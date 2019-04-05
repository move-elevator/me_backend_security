<?php

namespace MoveElevator\MeBackendSecurity\Service;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Factory\LoginProviderRedirectFactory;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Error\Result;

/**
 * @package MoveElevator\MeBackendSecurity\Service
 */
class BackendUserService
{
    const USERS_TABLE = 'be_users';
    const LASTCHANGE_COLUMN_NAME = 'tx_mebackendsecurity_lastpasswordchange';
    const LASTLOGIN_COLUMN_NAME = 'lastlogin';
    const USER_DONT_EXIST_ERROR_CODE = 1510742747;
    const FIRST_CHANGE_MESSAGE_CODE = 1513928250;

    /**
     * mixed
     */
    protected $backendUserAuthentication;

    /**
     * @var ConnectionPool
     */
    protected $queryBuilder;

    /**
     * @var array
     */
    protected $extensionConfiguration;

    /**
     * @var CompositeValidator
     */
    protected $compositeValidator;

    /**
     * @var PasswordHashInterface
     */
    protected $passwordHashInstance;

    /**
     * @param BackendUserAuthentication $backendUserAuthentication
     * @param ConnectionPool            $connectionPool
     * @param ExtensionConfiguration    $extensionConfiguration
     * @param CompositeValidator        $compositeValidator
     * @param PasswordHashInterface     $passwordHashInstance
     *
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        BackendUserAuthentication $backendUserAuthentication,
        ConnectionPool $connectionPool,
        ExtensionConfiguration $extensionConfiguration,
        CompositeValidator $compositeValidator,
        PasswordHashInterface $passwordHashInstance
    ) {
        $this->backendUserAuthentication = $backendUserAuthentication;
        $this->queryBuilder = $connectionPool->getQueryBuilderForTable(self::USERS_TABLE);
        $this->extensionConfiguration = $extensionConfiguration;
        $this->compositeValidator = $compositeValidator;
        $this->passwordHashInstance = $passwordHashInstance;
    }

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     *
     * @return LoginProviderRedirect|null
     */
    public function handlePasswordChangeRequest(PasswordChangeRequest $passwordChangeRequest)
    {
        /** @var Result $validationResults */
        $validationResults = $this->compositeValidator->validate($passwordChangeRequest);

        if ($validationResults->hasErrors()) {
            return LoginProviderRedirectFactory::create(
                $this->backendUserAuthentication->user['username'],
                $this->getErrorCodesWithArguments($validationResults)
            );
        }

        if ($this->isExistingUser() === false) {
            return LoginProviderRedirectFactory::create(
                $this->backendUserAuthentication->user['username'],
                [self::USER_DONT_EXIST_ERROR_CODE]
            );
        }

        $this->queryBuilder
            ->update(self::USERS_TABLE)
            ->where(
                $this->queryBuilder->expr()->eq('uid', $this->backendUserAuthentication->user['uid'])
            )
            ->set('password', $this->passwordHashInstance->getHashedPassword($passwordChangeRequest->getPassword()))
            ->set(self::LASTCHANGE_COLUMN_NAME, time() + date('Z'))
            ->execute();

        $this->backendUserAuthentication->user[self::LASTCHANGE_COLUMN_NAME] = time() + date('Z');

        return null;
    }

    /**
     * @return LoginProviderRedirect|null
     *
     * @throws \Exception
     */
    public function checkPasswordLifeTime()
    {
        $this->handleNewAccount();

        if ($this->isNonMigratedAccount()) {
            $this->migrateAccount();

            return null;
        }

        $lastPasswordChange = (int)$this->backendUserAuthentication->user[self::LASTCHANGE_COLUMN_NAME];

        if ($lastPasswordChange === 1) {
            return LoginProviderRedirectFactory::create(
                $this->backendUserAuthentication->user['username'],
                [],
                [self::FIRST_CHANGE_MESSAGE_CODE]
            );
        }

        $validUntil = $this->extensionConfiguration->getMaximumValidDays();

        $now = new \DateTime();
        $expireDeathLine = new \DateTime();
        $expireDeathLine->setTimestamp(
            $lastPasswordChange
        );
        $expireDeathLine->add(
            new \DateInterval('P' . $validUntil . 'D')
        );

        if ($now <= $expireDeathLine) {
            return null;
        }

        return LoginProviderRedirectFactory::create(
            $this->backendUserAuthentication->user['username']
        );
    }

    /**
     * @return void
     */
    private function handleNewAccount()
    {
        $lastLogin = (int)$this->backendUserAuthentication->user[self::LASTLOGIN_COLUMN_NAME];

        if ($lastLogin !== 0) {
            return;
        }

        $this->backendUserAuthentication->user[self::LASTLOGIN_COLUMN_NAME] = time();
        $this->backendUserAuthentication->user[self::LASTCHANGE_COLUMN_NAME] = 1;

        $this->queryBuilder
            ->update(self::USERS_TABLE)
            ->where(
                $this->queryBuilder->expr()->eq('uid', $this->backendUserAuthentication->user['uid'])
            )
            ->set(self::LASTLOGIN_COLUMN_NAME, time())
            ->set(self::LASTCHANGE_COLUMN_NAME, 1)
            ->execute();
    }

    /**
     * @return bool
     */
    private function isNonMigratedAccount()
    {
        $lastPasswordChange = (int)$this->backendUserAuthentication->user[self::LASTCHANGE_COLUMN_NAME];

        if ($lastPasswordChange !== 0) {
            return false;
        }

        return true;
    }

    /**
     * @return void
     */
    private function migrateAccount()
    {
        $this->queryBuilder
            ->update(self::USERS_TABLE)
            ->where(
                $this->queryBuilder->expr()->eq('uid', $this->backendUserAuthentication->user['uid'])
            )
            ->set(self::LASTCHANGE_COLUMN_NAME, time() + date('Z'))
            ->execute();
    }

    /**
     * @return bool
     */
    private function isExistingUser()
    {
        $userExists = $this->queryBuilder
            ->count('uid')
            ->from(self::USERS_TABLE)
            ->where(
                $this->queryBuilder->expr()->eq('uid', $this->backendUserAuthentication->user['uid']),
                $this->queryBuilder->expr()->eq('username', $this->backendUserAuthentication->user['username'])
            )
            ->execute()
            ->fetchColumn(0);

        if ($userExists === false) {
            return false;
        }

        return true;
    }

    /**
     * @param Result $validationResults
     *
     * @return array
     */
    private function getErrorCodesWithArguments($validationResults)
    {
        $errors = [];

        /** @var Error $error */
        foreach ($validationResults->getErrors() as $error) {
            $errors[] = [
                'errorCode' => $error->getCode(),
                'arguments' => $error->getArguments()
            ];
        }

        return $errors;
    }
}
