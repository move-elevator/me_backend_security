<?php

namespace MoveElevator\MeBackendSecurity\Hook;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Factory\DatabaseConnectionFactory;
use MoveElevator\MeBackendSecurity\Factory\ExtensionConfigurationFactory;
use MoveElevator\MeBackendSecurity\Service\BackendUserService;
use MoveElevator\MeBackendSecurity\Factory\PasswordChangeRequestFactory;
use MoveElevator\MeBackendSecurity\Validation\Validator\PasswordChangeRequestValidator;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;
use TYPO3\CMS\Lang\LanguageService;
use TYPO3\CMS\Rsaauth\RsaEncryptionDecoder;
use TYPO3\CMS\Saltedpasswords\Salt\SaltFactory;
use TYPO3\CMS\Saltedpasswords\Salt\SaltInterface;

/**
 * @package MoveElevator\MeBackendSecurity\Hook
 */
class UserAuthHook
{
    const EXTKEY = 'me_backend_security';
    const PARAMETER_IDENTIFIER = 'tx_mebackendsecurity';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var RsaEncryptionDecoder
     */
    protected $rsaEncryptionDecoder;

    /**
     * @var BackendUserAuthentication
     */
    protected $backendUserAuthentication;

    /**
     * @var BackendUserService
     */
    protected $backendUserService;

    /**
     * UserAuthHook constructor.
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->rsaEncryptionDecoder = $this->objectManager->get(RsaEncryptionDecoder::class);
    }

    /**
     * @param array $params
     * @param mixed $pObj
     */
    public function postUserLookUp($params, $pObj)
    {
        if ($pObj instanceof BackendUserAuthentication === false) {
            return;
        }

        if (empty($pObj->user)) {
            return;
        }

        /** @var DatabaseConnection $databaseConnection */
        $databaseConnection = DatabaseConnectionFactory::create($GLOBALS['TYPO3_CONF_VARS']['DB']);

        /** @var ConfigurationUtility $configurationUtility */
        $configurationUtility = $this->objectManager->get(ConfigurationUtility::class);

        /** @var ExtensionConfiguration $extensionConfiguration */
        $extensionConfiguration = ExtensionConfigurationFactory::create(
            $configurationUtility->getCurrentConfiguration(self::EXTKEY)
        );

        /** @var PasswordChangeRequestValidator $passwordChangeRequestValidator */
        $passwordChangeRequestValidator = $this->objectManager->get(
            PasswordChangeRequestValidator::class,
            ['extensionConfiguration' => $extensionConfiguration]
        );

        /** @var SaltInterface $saltingInstance */
        $saltingInstance = SaltFactory::getSaltingInstance(null, 'BE');

        $this->backendUserAuthentication = $pObj;
        $this->backendUserService = $this->objectManager->get(
            BackendUserService::class,
            $this->backendUserAuthentication,
            $databaseConnection,
            $extensionConfiguration,
            $passwordChangeRequestValidator,
            $saltingInstance
        );

        $this->initializeLanguageService();
        $this->processPasswordChange();
        $this->processPasswordLifeTimeCheck();
    }

    /**
     * @return void
     */
    private function initializeLanguageService()
    {
        if (empty($GLOBALS['LANG']) === false) {
            return;
        }

        if (empty($this->backendUserAuthentication->user['uc'])) {
            return;
        }

        $userUc = unserialize($this->backendUserAuthentication->user['uc']);
        $GLOBALS['LANG'] = $this->objectManager->get(LanguageService::class);
        $GLOBALS['LANG']->init($userUc['lang']);
    }

    /**
     * @return void
     */
    private function processPasswordChange()
    {
        $requestParameters = GeneralUtility::_GP(self::PARAMETER_IDENTIFIER);

        if (empty($requestParameters)) {
            return;
        }

        /** @var PasswordChangeRequest $passwordChangeRequest */
        $passwordChangeRequest = PasswordChangeRequestFactory::create(
            $requestParameters,
            $this->rsaEncryptionDecoder
        );

        $result = $this->backendUserService->handlePasswordChangeRequest($passwordChangeRequest);

        if ($result instanceof LoginProviderRedirect) {
            $this->handleRedirect($result);
        }
    }

    /**
     * @return void
     */
    private function processPasswordLifeTimeCheck()
    {
        $result = $this->backendUserService->checkPasswordLifeTime();

        if ($result instanceof LoginProviderRedirect) {
            $this->handleRedirect($result);
        }
    }

    /**
     * @param LoginProviderRedirect $loginProviderRedirect
     */
    private function handleRedirect(LoginProviderRedirect $loginProviderRedirect)
    {
        $this->backendUserAuthentication->logoff();

        HttpUtility::redirect($loginProviderRedirect->getUrl());
    }
}
