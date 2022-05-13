<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Evaluation;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Factory\CompositeValidatorFactory;
use MoveElevator\MeBackendSecurity\Factory\ExtensionConfigurationFactory;
use MoveElevator\MeBackendSecurity\Factory\PasswordChangeRequestFactory;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as ExtensionConfigurationUtility;
use TYPO3\CMS\Core\Crypto\PasswordHashing\InvalidPasswordHashException;
use TYPO3\CMS\Core\Crypto\PasswordHashing\PasswordHashFactory;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class PasswordEvaluator
{
    protected const EXTKEY = 'me_backend_security';
    protected const USERS_TABLE = 'be_users';
    protected const LASTCHANGE_COLUMN_NAME = 'tx_mebackendsecurity_lastpasswordchange';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var PasswordHashFactory
     */
    protected $passwordHashFactory;

    /**
     * @var FlashMessageQueue
     */
    protected $messageQueue;

    /**
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->passwordHashFactory = $this->objectManager->get(PasswordHashFactory::class);
        $this->messageQueue = $this->objectManager->get(FlashMessageQueue::class, 'core.template.flashMessages');
    }

    /**
     * @return string
     *
     * @codeCoverageIgnore
     */
    public function returnFieldJS(): string
    {
        return 'return value;';
    }

    /**
     * @param string $value
     * @param string $is_in
     * @param bool $set
     *
     * @return string
     *
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     * @throws InvalidPasswordHashException
     *
     * @codeCoverageIgnore
     */
    public function evaluateFieldValue(string $value, string $is_in, bool &$set): string
    {
        $requestParameters = ['password' => $value, 'password2' => $value];

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

        /** @var PasswordChangeRequest $passwordChangeRequest */
        $passwordChangeRequest = PasswordChangeRequestFactory::create(
            $requestParameters,
            null
        );

        $validationResult = $compositeValidator->validate($passwordChangeRequest);

        if ($validationResult->hasErrors()) {
            $this->addFlashMessage($validationResult);
            $set = false;
            return '';
        }

        return $value;
    }

    /**
     * @param Result $validationResult
     *
     * @codeCoverageIgnore
     */
    protected function addFlashMessage(Result $validationResult): void
    {
        $errorMessages = [];
        $messageTitle = LocalizationUtility::translate(
            'error.title',
            'me_backend_security'
        );

        /** @var Error $error */
        foreach ($validationResult->getErrors() as $error) {
            $errorMessages[] = $error->getMessage();
        }

        $flashMessage = new FlashMessage(
            implode(' | ', $errorMessages),
            $messageTitle,
            FlashMessage::ERROR,
            true
        );

        $this->messageQueue->addMessage(
            $flashMessage
        );
    }
}
