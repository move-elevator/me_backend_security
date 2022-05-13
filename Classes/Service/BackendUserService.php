<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Service;

use DateInterval;
use DateTime;
use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Factory\LoginProviderRedirectFactory;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;
use TYPO3\CMS\Core\Database\Query\QueryBuilder;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Error\Result;

class BackendUserService
{
    protected const USERS_TABLE = 'be_users';
    protected const LASTCHANGE_COLUMN_NAME = 'tx_mebackendsecurity_lastpasswordchange';
    protected const LASTLOGIN_COLUMN_NAME = 'lastlogin';
    protected const USER_DONT_EXIST_ERROR_CODE = 1510742747;
    protected const FIRST_CHANGE_MESSAGE_CODE = 1513928250;

    protected mixed $backendUserAuthentication;
    protected QueryBuilder $queryBuilder;
    protected ExtensionConfiguration $extensionConfiguration;
    protected CompositeValidator $compositeValidator;
    protected PasswordHashInterface $passwordHashInstance;

    public function __construct(
        BackendUserAuthentication $backendUserAuthentication,
        QueryBuilder $queryBuilder,
        ExtensionConfiguration $extensionConfiguration,
        CompositeValidator $compositeValidator,
        PasswordHashInterface $passwordHashInstance
    ) {
        $this->backendUserAuthentication = $backendUserAuthentication;
        $this->queryBuilder = $queryBuilder;
        $this->extensionConfiguration = $extensionConfiguration;
        $this->compositeValidator = $compositeValidator;
        $this->passwordHashInstance = $passwordHashInstance;
    }

    public function handlePasswordChangeRequest(PasswordChangeRequest $passwordChangeRequest): ?LoginProviderRedirect
    {
        $validationResults = $this->compositeValidator->validate($passwordChangeRequest);

        if (true === $validationResults->hasErrors()) {
            return LoginProviderRedirectFactory::create(
                $this->backendUserAuthentication->user['username'],
                $this->getErrorCodesWithArguments($validationResults)
            );
        }

        if (false === $this->isExistingUser()) {
            return LoginProviderRedirectFactory::create(
                $this->backendUserAuthentication->user['username'],
                [self::USER_DONT_EXIST_ERROR_CODE]
            );
        }

        $this->queryBuilder
            ->update(self::USERS_TABLE)
            ->where(
                $this->queryBuilder->expr()->eq('uid', (int)$this->backendUserAuthentication->user['uid'])
            )
            ->set('password', $this->passwordHashInstance->getHashedPassword($passwordChangeRequest->getPassword()))
            ->set(self::LASTCHANGE_COLUMN_NAME, time() + (int)date('Z'))
            ->execute();

        $this->backendUserAuthentication->user[self::LASTCHANGE_COLUMN_NAME] = time() + (int)date('Z');

        return null;
    }

    public function checkPasswordLifeTime(): ?LoginProviderRedirect
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

        $now = new DateTime();
        $expireDeathLine = new DateTime();
        $expireDeathLine->setTimestamp(
            $lastPasswordChange
        );
        $expireDeathLine->add(
            new DateInterval('P' . $validUntil . 'D')
        );

        if ($now <= $expireDeathLine) {
            return null;
        }

        return LoginProviderRedirectFactory::create(
            $this->backendUserAuthentication->user['username']
        );
    }

    private function handleNewAccount(): void
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

    private function isNonMigratedAccount(): bool
    {
        return 0 === (int)$this->backendUserAuthentication->user[self::LASTCHANGE_COLUMN_NAME];
    }

    private function migrateAccount(): void
    {
        $this->queryBuilder
            ->update(self::USERS_TABLE)
            ->where(
                $this->queryBuilder->expr()->eq('uid', $this->backendUserAuthentication->user['uid'])
            )
            ->set(self::LASTCHANGE_COLUMN_NAME, time() + (int)date('Z'))
            ->execute();
    }

    private function isExistingUser(): bool
    {
        $userExists = $this->queryBuilder
            ->count('uid')
            ->from(self::USERS_TABLE)
            ->where(
                $this->queryBuilder->expr()->eq('uid', $this->backendUserAuthentication->user['uid']),
                $this->queryBuilder->expr()->eq('username', ':u')
            )
            ->setParameter('u', $this->backendUserAuthentication->user['username'])
            ->execute()
            ->fetchOne();

        return false !== $userExists;
    }

    private function getErrorCodesWithArguments(Result $validationResults): array
    {
        $errors = [];

        foreach ($validationResults->getErrors() as $error) {
            $errors[] = [
                'errorCode' => $error->getCode(),
                'arguments' => $error->getArguments()
            ];
        }

        return $errors;
    }
}
