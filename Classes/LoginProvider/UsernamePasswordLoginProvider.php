<?php

namespace MoveElevator\MeBackendSecurity\LoginProvider;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Factory\ExtensionConfigurationFactory;
use TYPO3\CMS\Backend\Controller\LoginController;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extensionmanager\Utility\ConfigurationUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @package MoveElevator\MeBackendSecurity\LoginProvider
 */
class UsernamePasswordLoginProvider extends \TYPO3\CMS\Backend\LoginProvider\UsernamePasswordLoginProvider
{
    const EXTKEY = 'me_backend_security';
    const PARAMETER_IDENTIFIER = 'tx_mebackendsecurity';

    /** @var ExtensionConfiguration $extensionConfiguration */
    protected $extensionConfiguration;

    /**
     * Determine extension configuration
     */
    public function __construct()
    {
        $objectManager = GeneralUtility::makeInstance(ObjectManager::class);

        /** @var ConfigurationUtility $configurationUtility */
        $configurationUtility = $objectManager->get(ConfigurationUtility::class);

        $this->extensionConfiguration = ExtensionConfigurationFactory::create(
            $configurationUtility->getCurrentConfiguration(self::EXTKEY)
        );
    }

    /**
     * {@inheritdoc}
     */
    public function render(StandaloneView $view, PageRenderer $pageRenderer, LoginController $loginController)
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

        $errors = GeneralUtility::_GP('e');

        if (empty($errors) === false) {
            $view->assign('errors', unserialize(base64_decode(urldecode($errors))));
        }

        $pageRenderer->loadRequireJsModule(
            'TYPO3/CMS/MeBackendSecurity/PasswordValidator'
        );
    }

    /**
     * @return bool
     */
    private function isResetFormRequired()
    {
        $resetForm = GeneralUtility::_GP('r');
        $resetFormVars = GeneralUtility::_GP(self::PARAMETER_IDENTIFIER);

        if (empty($resetForm) === false || empty($resetFormVars) === false) {
            return true;
        }

        return false;
    }
}
