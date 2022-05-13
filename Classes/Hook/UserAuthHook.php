<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Hook;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Factory\CompositeValidatorFactory;
use MoveElevator\MeBackendSecurity\Factory\ExtensionConfigurationFactory;
use MoveElevator\MeBackendSecurity\Factory\PasswordChangeRequestFactory;
use MoveElevator\MeBackendSecurity\Service\BackendUserService;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as ExtensionConfigurationUtility;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Localization\LanguageService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\HttpUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

/**
 * @codeCoverageIgnore
 */
class UserAuthHook
{
    protected const EXTKEY = 'me_backend_security';
    protected const PARAMETER_IDENTIFIER = 'tx_mebackendsecurity';
    protected const PASSWORD_IDENTIFIER = 'userident';
    protected const USERS_TABLE = 'be_users';

    /**
     * @var BackendUserAuthentication
     */
    protected $backendUserAuthentication;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var PasswordHashFactory
     */
    protected $passwordHashFactory;

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
        $this->passwordHashFactory = $this->objectManager->get(PasswordHashFactory::class);
    }

    /**
     * @param mixed $params
     * @param mixed $pObj
     *
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function postUserLookUp($params, $pObj): void
    {
        if ($pObj instanceof BackendUserAuthentication === false) {
            return;
        }

        if (true === empty($pObj->user)) {
            return;
        }

        if (false === empty($pObj->user['tx_igldapssoauth_dn'])) {
            return;
        }

        $this->initializeObjects($pObj);
        $this->initializeLanguageService();
        $this->processPasswordChange();
        $this->processPasswordLifeTimeCheck();
    }

    /**
     * @param BackendUserAuthentication $pObj
     *
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    private function initializeObjects(BackendUserAuthentication $pObj): void
    {
        $this->backendUserAuthentication = $pObj;

        /** @var ExtensionConfigurationUtility $extensionConfigurationUtility */
        $extensionConfigurationUtility = $this->objectManager->get(ExtensionConfigurationUtility::class);

        /** @var ExtensionConfiguration $extensionConfiguration */
        $extensionConfiguration = ExtensionConfigurationFactory::create(
            $extensionConfigurationUtility->get(self::EXTKEY)
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

        /** @var PasswordHashInterface $passwordHashInstance */
        $passwordHashInstance = $this->objectManager->get(PasswordHashFactory::class)->getDefaultHashInstance('BE');

        /** @var ConnectionPool $connectionPool */
        $connectionPool = $this->objectManager->get(ConnectionPool::class);

        $this->backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $connectionPool->getQueryBuilderForTable(self::USERS_TABLE),
            $extensionConfiguration,
            $compositeValidator,
            $passwordHashInstance
        );
    }

    private function initializeLanguageService(): void
    {
        if (empty($GLOBALS['LANG']) === false) {
            return;
        }

        $GLOBALS['LANG'] = $this->objectManager->get(LanguageService::class);

        if (empty($this->backendUserAuthentication->user['uc'])) {
            return;
        }

        $userUc = unserialize($this->backendUserAuthentication->user['uc'], ['allowed_classes' => false]);
        $GLOBALS['LANG']->init($userUc['lang']);
    }

    private function processPasswordChange(): void
    {
        $requestParameters = GeneralUtility::_GP(self::PARAMETER_IDENTIFIER);
        $currentPassword = GeneralUtility::_GP(self::PASSWORD_IDENTIFIER);

        if (empty($requestParameters) || empty($currentPassword)) {
            return;
        }

        /** @var PasswordChangeRequest $passwordChangeRequest */
        $passwordChangeRequest = PasswordChangeRequestFactory::create(
            $requestParameters,
            $currentPassword
        );

        $result = $this->backendUserService->handlePasswordChangeRequest($passwordChangeRequest);

        if ($result instanceof LoginProviderRedirect) {
            $this->handleRedirect($result);
        }
    }

    /**
     * @throws \Exception
     */
    private function processPasswordLifeTimeCheck(): void
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
