<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\LoginProvider;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Factory\ExtensionConfigurationFactory;
use TYPO3\CMS\Backend\Controller\LoginController;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationExtensionNotConfiguredException;
use TYPO3\CMS\Core\Configuration\Exception\ExtensionConfigurationPathDoesNotExistException;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as ExtensionConfigurationUtility;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

class UsernamePasswordLoginProvider extends \TYPO3\CMS\Backend\LoginProvider\UsernamePasswordLoginProvider
{
    protected const PARAMETER_IDENTIFIER = 'tx_mebackendsecurity';
    protected const TEMPLATE_PATH = 'EXT:me_backend_security/Resources/Private/Templates/LoginProvider/PasswordResetLoginForm.html';

    protected ExtensionConfiguration $extensionConfiguration;

    /**
     * @throws ExtensionConfigurationExtensionNotConfiguredException
     * @throws ExtensionConfigurationPathDoesNotExistException
     *
     * @codeCoverageIgnore
     */
    public function __construct()
    {
        /** @var ExtensionConfigurationUtility $extensionConfigurationUtility */
        $extensionConfigurationUtility = GeneralUtility::makeInstance(ExtensionConfigurationUtility::class);

        $this->extensionConfiguration = ExtensionConfigurationFactory::create(
            $extensionConfigurationUtility->get(ExtensionConfiguration::EXT_KEY)
        );
    }

    /**
     * {@inheritdoc}
     *
     * @codeCoverageIgnore
     */
    public function render(
        StandaloneView $view,
        PageRenderer $pageRenderer,
        LoginController $loginController
    ): void {
        parent::render($view, $pageRenderer, $loginController);

        if (false === $this->isResetFormRequired()) {
            return;
        }

        $view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(self::TEMPLATE_PATH)
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

        $x = GeneralUtility::_GP('x');

        if (empty($x) === false) {
            $view->assign(
                'mfaToken',
                unserialize(base64_decode(urldecode($x)), ['allowed_classes' => false])
            );
        }

        $pageRenderer->loadRequireJsModule(
            'TYPO3/CMS/MeBackendSecurity/PasswordValidator'
        );
    }

    /**
     * @codeCoverageIgnore
     */
    private function isResetFormRequired(): bool
    {
        $resetForm = GeneralUtility::_GP('r');
        $resetFormVars = GeneralUtility::_GP(self::PARAMETER_IDENTIFIER);

        return empty($resetForm) === false || empty($resetFormVars) === false;
    }
}
