<?php

namespace MoveElevator\MeBackendSecurity\LoginProvider;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Factory\ExtensionConfigurationFactory;
use TYPO3\CMS\Backend\Controller\LoginController;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as ExtensionConfigurationUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @package MoveElevator\MeBackendSecurity\LoginProvider
 */
class UsernamePasswordLoginProvider extends \TYPO3\CMS\Backend\LoginProvider\UsernamePasswordLoginProvider
{
    protected const EXTKEY = 'me_backend_security';
    protected const PARAMETER_IDENTIFIER = 'tx_mebackendsecurity';

    /**
     * @var ExtensionConfiguration $extensionConfiguration
     */
    protected $extensionConfiguration;

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var ExtensionConfigurationUtility $extensionConfigurationUtility */
        $extensionConfigurationUtility = $objectManager->get(ExtensionConfigurationUtility::class);

        $this->extensionConfiguration = ExtensionConfigurationFactory::create(
            $extensionConfigurationUtility->get(self::EXTKEY)
        );
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function render(StandaloneView $view, PageRenderer $pageRenderer, LoginController $loginController): void
    {
        parent::render($view, $pageRenderer, $loginController);

        if ($this->isResetFormRequired() === false) {
            return;
        }

        $view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(
                'EXT:me_backend_security/Resources/Private/Templates/LoginProvider/PasswordResetLoginForm.html'
            )
        );

        $view->assign('configuration', $this->extensionConfiguration);
        $view->assign('presetUsername', GeneralUtility::_GP('u'));

        $errors = GeneralUtility::_GP('e');

        if (empty($errors) === false) {
            $view->assign(
                'errors',
                unserialize(base64_decode(urldecode($errors)), ['allowed_classes' => false])
            );
        }

        $messages = GeneralUtility::_GP('m');

        if (empty($messages) === false) {
            $view->assign(
                'messages',
                unserialize(base64_decode(urldecode($messages)), ['allowed_classes' => false])
            );
        }

        $pageRenderer->loadRequireJsModule(
            'TYPO3/CMS/MeBackendSecurity/PasswordValidator'
        );
    }

    /**
     * @return bool
     *
     * @codeCoverageIgnore
     */
    private function isResetFormRequired(): bool
    {
        $resetForm = GeneralUtility::_GP('r');
        $resetFormVars = GeneralUtility::_GP(self::PARAMETER_IDENTIFIER);

        return empty($resetForm) === false || empty($resetFormVars) === false;
    }
}
