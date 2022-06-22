<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Hook;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\LoginProviderRedirect;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Domain\Repository\BackendUserRepository;
use MoveElevator\MeBackendSecurity\Factory\CompositeValidatorFactory;
use MoveElevator\MeBackendSecurity\Factory\ExtensionConfigurationFactory;
use MoveElevator\MeBackendSecurity\Factory\PasswordChangeRequestFactory;
use MoveElevator\MeBackendSecurity\Service\BackendUserService;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use TYPO3\CMS\Backend\FrontendBackendUserAuthentication;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as ExtensionConfigurationUtility;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashInterface;
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
    protected const PARAMETER_IDENTIFIER = 'tx_mebackendsecurity';
    protected const PASSWORD_IDENTIFIER = 'userident';

    protected BackendUserAuthentication $backendUserAuthentication;
    protected BackendUserRepository $backendUserRepository;
    protected BackendUserService $backendUserService;
    protected ObjectManager $objectManager;
    protected PasswordHashFactory $passwordHashFactory;

    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->backendUserRepository = GeneralUtility::makeInstance(BackendUserRepository::class);
        $this->passwordHashFactory = GeneralUtility::makeInstance(PasswordHashFactory::class);
    }

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    public function postUserLookUp($params, $pObj): void
    {
        if (false === $pObj instanceof FrontendBackendUserAuthentication) {
            return;
        }

        if (false === $pObj instanceof BackendUserAuthentication) {
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
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     */
    private function initializeObjects(BackendUserAuthentication $pObj): void
    {
        $this->backendUserAuthentication = $pObj;

        /** @var ExtensionConfigurationUtility $extensionConfigurationUtility */
        $extensionConfigurationUtility = GeneralUtility::makeInstance(ExtensionConfigurationUtility::class);

        /** @var ExtensionConfiguration $extensionConfiguration */
        $extensionConfiguration = ExtensionConfigurationFactory::create(
            $extensionConfigurationUtility->get(ExtensionConfiguration::EXT_KEY)
        );

        /** @var ConfigurationManagerInterface $configurationManager */
        $configurationManager = GeneralUtility::makeInstance(ConfigurationManagerInterface::class);

        /** @var CompositeValidator $compositeValidator */
        $compositeValidator = CompositeValidatorFactory::create(
            $this->objectManager,
            $extensionConfiguration,
            $configurationManager->getConfiguration(
                ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT
            )
        );

        /** @var PasswordHashInterface $passwordHashInstance */
        $passwordHashInstance = GeneralUtility::makeInstance(PasswordHashFactory::class)->getDefaultHashInstance('BE');

        $this->backendUserService = new BackendUserService(
            $this->backendUserAuthentication,
            $this->backendUserRepository,
            $extensionConfiguration,
            $compositeValidator,
            $passwordHashInstance
        );
    }

    private function initializeLanguageService(): void
    {
        if (false === empty($GLOBALS['LANG'])) {
            return;
        }

        $GLOBALS['LANG'] = $this->objectManager->get(LanguageService::class);

        if (true === empty($this->backendUserAuthentication->user['uc'])) {
            return;
        }

        $userUc = unserialize($this->backendUserAuthentication->user['uc'], ['allowed_classes' => false]);
        $GLOBALS['LANG']->init($userUc['lang']);
    }

    private function processPasswordChange(): void
    {
        $requestParameters = GeneralUtility::_GP(self::PARAMETER_IDENTIFIER);
        $currentPassword = GeneralUtility::_GP(self::PASSWORD_IDENTIFIER);

        if (true === empty($requestParameters) ||
            true === empty($currentPassword)
        ) {
            return;
        }

        /** @var PasswordChangeRequest $passwordChangeRequest */
        $passwordChangeRequest = PasswordChangeRequestFactory::create(
            $requestParameters,
            $currentPassword
        );

        $result = $this->backendUserService->handlePasswordChangeRequest($passwordChangeRequest);

        if (true === $result instanceof LoginProviderRedirect) {
            $this->handleRedirect($result);
        }
    }

    private function processPasswordLifeTimeCheck(): void
    {
        $result = $this->backendUserService->checkPasswordLifeTime();

        if (true === $result instanceof LoginProviderRedirect) {
            $this->handleRedirect($result);
        }
    }

    private function handleRedirect(LoginProviderRedirect $loginProviderRedirect): void
    {
        $this->backendUserAuthentication->logoff();

        HttpUtility::redirect($loginProviderRedirect->getUrl());
    }
}
