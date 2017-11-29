<?php

namespace MoveElevator\MeBackendSecurity\Hook;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Domain\Model\PasswordChangeRequest;
use MoveElevator\MeBackendSecurity\Factory\ExtensionConfigurationFactory;
use MoveElevator\MeBackendSecurity\Factory\PasswordChangeRequestFactory;
use MoveElevator\MeBackendSecurity\Factory\CompositeValidatorFactory;
use MoveElevator\MeBackendSecurity\Validation\Validator\CompositeValidator;
use TYPO3\CMS\Core\Messaging\FlashMessage;
use TYPO3\CMS\Core\Messaging\FlashMessageQueue;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface;
use TYPO3\CMS\Extbase\Error\Error;
use TYPO3\CMS\Extbase\Error\Result;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;
use TYPO3\CMS\Rsaauth\RsaEncryptionDecoder;
use TYPO3\CMS\Setup\Controller\SetupModuleController;

/**
 * @package MoveElevator\MeBackendSecurity\Hook
 *
 * @codeCoverageIgnore
 */
class UserEditHook
{
    const EXTKEY = 'me_backend_security';

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var RsaEncryptionDecoder
     */
    protected $rsaEncryptionDecoder;

    /**
     * @var FlashMessageQueue
     */
    protected $messageQueue;

    /**
     * UserAuthHook constructor.
     */
    public function __construct()
    {
        $this->objectManager = GeneralUtility::makeInstance(ObjectManager::class);
        $this->rsaEncryptionDecoder = $this->objectManager->get(RsaEncryptionDecoder::class);
        $this->messageQueue = $this->objectManager->get(FlashMessageQueue::class, 'core.template.flashMessages');
    }

    /**
     * @param array                 $params
     * @param SetupModuleController $parentObject
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function modifyUserDataBeforeSave(array &$params, SetupModuleController &$parentObject)
    {
        if (empty($params['be_user_data']['password']) &&
            empty($params['be_user_data']['password2'])
        ) {
            return;
        }

        $requestParameters = $params['be_user_data'];

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

        /** @var PasswordChangeRequest $passwordChangeRequest */
        $passwordChangeRequest = PasswordChangeRequestFactory::create(
            $requestParameters,
            $this->rsaEncryptionDecoder
        );

        $validationResult = $compositeValidator->validate($passwordChangeRequest);

        if ($validationResult->hasErrors() === false) {
            return;
        }

        $this->addFlashMessage($validationResult);

        $params['be_user_data']['password'] = '';
        $params['be_user_data']['password2'] = '';
    }

    /**
     * @param Result $validationResult
     */
    protected function addFlashMessage($validationResult)
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
