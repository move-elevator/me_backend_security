<?php

namespace MoveElevator\MeBackendSecurity\Service;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Factory\LoginProviderRedirectFactory;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;
use TYPO3\CMS\Saltedpasswords\Salt\SaltInterface;

/**
 * @package MoveElevator\MeBackendSecurity\Service
 */
class BackendUserService
{
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
     * @var SaltInterface
     */
    protected $saltingInstance;

    /**
     * @param mixed                  $backendUserAuthentication
     * @param DatabaseConnection     $databaseConnection
     * @param ExtensionConfiguration $extensionConfiguration
     * @param SaltInterface          $saltingInstance
     */
    public function __construct(
        BackendUserAuthentication $backendUserAuthentication,
        DatabaseConnection $databaseConnection,
        ExtensionConfiguration $extensionConfiguration,
        SaltInterface $saltingInstance
    ) {
        $this->backendUserAuthentication = $backendUserAuthentication;
        $this->databaseConnection = $databaseConnection;
        $this->extensionConfiguration = $extensionConfiguration;
        $this->saltingInstance = $saltingInstance;
    }

    /**
     * @return LoginProviderRedirect|null
     */
    public function handlePasswordChangeRequest(PasswordChangeRequest $passwordChange)
    {
        if ($this->isActiveBackendUserAuthentication() === false) {
            return null;
        }

        $userExists = $this->databaseConnection->exec_SELECTcountRows(
            'uid',
            'be_users',
            'username=' .
            $this->databaseConnection->fullQuoteStr($this->backendUserAuthentication->user['username'], 'be_users')
        );

        if ($userExists) {
            $hashedPassword = $this->saltingInstance->getHashedPassword($passwordChange->getPassword());

            $this->databaseConnection->exec_UPDATEquery(
                'be_users',
                'uid=' . $this->backendUserAuthentication->user['uid'],
                [
                    'password' => $hashedPassword,
                    'tx_mebackendsecurity_lastpasswordchange' => $GLOBALS['EXEC_TIME']
                ]
            );

            $this->backendUserAuthentication->user['tx_mebackendsecurity_lastpasswordchange'] = $GLOBALS['EXEC_TIME'];

            return null;
        }

        return LoginProviderRedirectFactory::create(
            $this->backendUserAuthentication->user['username']
        );
    }

    /**
     * @return LoginProviderRedirect|null
     */
    public function checkPasswordLifeTime()
    {
        if ($this->isActiveBackendUserAuthentication() === false) {
            return null;
        }

        $validUntil = $this->extensionConfiguration->getValidUntil();

        $now = new \DateTime();
        $expireDeathLine = new \DateTime();
        $expireDeathLine->setTimestamp(
            intval($this->backendUserAuthentication->user['tx_mebackendsecurity_lastpasswordchange'])
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
     * @return bool
     */
    private function isActiveBackendUserAuthentication()
    {
        if ($this->backendUserAuthentication instanceof BackendUserAuthentication === false) {
            return false;
        }

        if (empty($this->backendUserAuthentication->user)) {
            return false;
        }

        return true;
    }
}
