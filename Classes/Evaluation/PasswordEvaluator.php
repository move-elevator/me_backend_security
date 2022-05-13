<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Evaluation;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Factory\CompositeValidatorFactory;
use MoveElevator\MeBackendSecurity\Factory\ExtensionConfigurationFactory;
use MoveElevator\MeBackendSecurity\Factory\PasswordChangeRequestFactory;
use MoveElevator\MeBackendSecurity\Service\FlashMessageService;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as ExtensionConfigurationUtility;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Object\ObjectManager;

class PasswordEvaluator
{
    protected const EXTKEY = 'me_backend_security';

    protected ObjectManager $objectManager;
    protected PasswordHashFactory $passwordHashFactory;
    protected FlashMessageService $flashMessageService;

    /**
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->passwordHashFactory = GeneralUtility::makeInstance(PasswordHashFactory::class);
        $this->flashMessageService = GeneralUtility::makeInstance(FlashMessageService::class);
    }

    /**
     * @codeCoverageIgnore
     */
    public function returnFieldJS(): string
    {
        return 'return value;';
    }

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     *
     * @codeCoverageIgnore
     */
    public function evaluateFieldValue(string $value, string $isIn, bool &$set): string
    {
        $requestParameters = ['password' => $value, 'password2' => $value];

        /** @var ExtensionConfigurationUtility $extensionConfigurationUtility */
        $extensionConfigurationUtility = GeneralUtility::makeInstance(ExtensionConfigurationUtility::class);

        /** @var ExtensionConfiguration $extensionConfiguration */
        $extensionConfiguration = ExtensionConfigurationFactory::create(
            $extensionConfigurationUtility->get(self::EXTKEY)
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

        /** @var PasswordChangeRequest $passwordChangeRequest */
        $passwordChangeRequest = PasswordChangeRequestFactory::create($requestParameters);

        $validationResult = $compositeValidator->validate($passwordChangeRequest);

        if (true === $validationResult->hasErrors()) {
            $this->flashMessageService->addPasswordErrorFlashMessage($validationResult);
            $set = false;

            return '';
        }

        return $value;
    }
}
