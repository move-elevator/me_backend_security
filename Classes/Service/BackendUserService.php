<?php

namespace MoveElevator\MeBackendSecurity\Service;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Factory\LoginProviderRedirectFactory;
use MoveElevator\MeBackendSecurity\Validation\Validator\PasswordChangeRequestValidator;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Saltedpasswords\Salt\SaltInterface;

/**
 * @package MoveElevator\MeBackendSecurity\Service
 */
class BackendUserService
{
    const USERS_TABLE_NAME = 'be_users';
    const LASTCHANGE_COLUMN_NAME = 'tx_mebackendsecurity_lastpasswordchange';

    /**
     * mixed
     */
    protected $backendUserAuthentication;

    /**
     * @var DatabaseConnection
     */
    protected $databaseConnection;

    /**
     * @var array
     */
    protected $extensionConfiguration;

    /**
     * @var PasswordChangeRequestValidator
     */
    protected $passwordChangeRequestValidator;

    /**
     * @var SaltInterface
     */
    protected $saltingInstance;

    /**
     * @param BackendUserAuthentication      $backendUserAuthentication
     * @param DatabaseConnection             $databaseConnection
     * @param ExtensionConfiguration         $extensionConfiguration
     * @param PasswordChangeRequestValidator $passwordChangeRequestValidator
     * @param SaltInterface                  $saltingInstance
     */
    public function __construct(
        BackendUserAuthentication $backendUserAuthentication,
        DatabaseConnection $databaseConnection,
        ExtensionConfiguration $extensionConfiguration,
        PasswordChangeRequestValidator $passwordChangeRequestValidator,
        SaltInterface $saltingInstance
    ) {
        $this->backendUserAuthentication = $backendUserAuthentication;
        $this->databaseConnection = $databaseConnection;
        $this->extensionConfiguration = $extensionConfiguration;
        $this->passwordChangeRequestValidator = $passwordChangeRequestValidator;
        $this->saltingInstance = $saltingInstance;
    }

    /**
     * @param PasswordChangeRequest $passwordChangeRequest
     *
     * @return LoginProviderRedirect|null
     */
    public function handlePasswordChangeRequest(PasswordChangeRequest $passwordChangeRequest)
    {
        /** @var Result $validationResults */
        $validationResults = $this->passwordChangeRequestValidator->validate($passwordChangeRequest);

        if ($validationResults->hasErrors()) {
            return LoginProviderRedirectFactory::create(
                $this->backendUserAuthentication->user['username'],
                $this->getErrorCodes($validationResults)
            );
        }

        $userExists = $this->databaseConnection->exec_SELECTcountRows(
            'uid',
            self::USERS_TABLE_NAME,
            'uid=' . $this->databaseConnection->fullQuoteStr(
                $this->backendUserAuthentication->user['uid'],
                self::USERS_TABLE_NAME
            ) . 'AND username=' . $this->databaseConnection->fullQuoteStr(
                $this->backendUserAuthentication->user['username'],
                self::USERS_TABLE_NAME
            )
        );

        if ($userExists === false) {
            return LoginProviderRedirectFactory::create(
                $this->backendUserAuthentication->user['username'],
                [1510742747]
            );
        }

        $this->databaseConnection->exec_UPDATEquery(
            self::USERS_TABLE_NAME,
            'uid=' . $this->databaseConnection->fullQuoteStr(
                $this->backendUserAuthentication->user['uid'],
                self::USERS_TABLE_NAME
            ),
            [
                'password' => $this->saltingInstance->getHashedPassword($passwordChangeRequest->getPassword()),
                self::LASTCHANGE_COLUMN_NAME => time() + date('Z')
            ]
        );

        return null;
    }

    /**
     * @return LoginProviderRedirect|null
     */
    public function checkPasswordLifeTime()
    {
        $validUntil = $this->extensionConfiguration->getValidUntil();

        $now = new \DateTime();
        $expireDeathLine = new \DateTime();
        $expireDeathLine->setTimestamp(
            intval($this->backendUserAuthentication->user[self::LASTCHANGE_COLUMN_NAME])
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
     * @param Result $validationResults
     *
     * @return array
     */
    private function getErrorCodes($validationResults)
    {
        $errorCodes = [];

        /** @var Error $error */
        foreach ($validationResults->getErrors() as $error) {
            $errorCodes[] = $error->getCode();
        }

        return $errorCodes;
    }
}
