<?php

namespace MoveElevator\MeBackendSecurity\Hook;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Factory\CompositeValidatorFactory;
use MoveElevator\MeBackendSecurity\Factory\DatabaseConnectionFactory;
use MoveElevator\MeBackendSecurity\Factory\ExtensionConfigurationFactory;
use MoveElevator\MeBackendSecurity\Service\BackendUserService;
use MoveElevator\MeBackendSecurity\Factory\PasswordChangeRequestFactory;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Database\DatabaseConnection;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;
use TYPO3\CMS\Lang\LanguageService;
use TYPO3\CMS\Rsaauth\RsaEncryptionDecoder;
use TYPO3\CMS\Saltedpasswords\Salt\SaltFactory;
use TYPO3\CMS\Saltedpasswords\Salt\SaltInterface;

/**
 * @package MoveElevator\MeBackendSecurity\Hook
 *
 * @codeCoverageIgnore
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
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
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function postUserLookUp($params, $pObj)
    {
        if ($pObj instanceof BackendUserAuthentication === false) {
            return;
        }

        if (empty($pObj->user)) {
            return;
        }

        if (empty($pObj->user['tx_igldapssoauth_dn']) === false) {
            return;
        }

        $this->initializeObjects($pObj);
        $this->initializeLanguageService();
        $this->processPasswordChange();
        $this->processPasswordLifeTimeCheck();
    }

    /**
     * @param $pObj
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function initializeObjects($pObj)
    {
        /** @var DatabaseConnection $databaseConnection */
        $databaseConnection = DatabaseConnectionFactory::create(
            $this->objectManager,
            $GLOBALS['TYPO3_CONF_VARS']['DB']
        );

        /** @var ConfigurationUtility $configurationUtility */
        $configurationUtility = $this->objectManager->get(ConfigurationUtility::class);

        /** @var ExtensionConfiguration $extensionConfiguration */
        $extensionConfiguration = ExtensionConfigurationFactory::create(
            $configurationUtility->getCurrentConfiguration(self::EXTKEY)
        );

        /** @var ConfigurationManagerInterface $configurationManager */
        $configurationManager = $this->objectManager->get(ConfigurationManagerInterface::class);

        /** @var CompositeValidator $compositeValidator */
        $compositeValidator = CompositeValidatorFactory::create(
            $this->objectManager,
            $extensionConfiguration,
            $configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            )
        );

        /** @var SaltInterface $saltingInstance */
        $saltingInstance = SaltFactory::getSaltingInstance(null, 'BE');

        $this->backendUserAuthentication = $pObj;
        $this->backendUserService = $this->objectManager->get(
            BackendUserService::class,
            $this->backendUserAuthentication,
            $databaseConnection,
            $extensionConfiguration,
            $compositeValidator,
            $saltingInstance
        );
    }

    /**
     * @return void
     *
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    private function initializeLanguageService()
    {
        if (empty($GLOBALS['LANG']) === false) {
            return;
        }

        $GLOBALS['LANG'] = $this->objectManager->get(LanguageService::class);

        if (empty($this->backendUserAuthentication->user['uc'])) {
            return;
        }

        $userUc = unserialize($this->backendUserAuthentication->user['uc']);
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
