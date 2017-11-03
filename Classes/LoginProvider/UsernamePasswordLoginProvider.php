<?php

namespace MoveElevator\MeBackendSecurity\LoginProvider;

use TYPO3\CMS\Backend\Controller\LoginController;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\ObjectManager;
use TYPO3\CMS\Extbase\SignalSlot\Dispatcher;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * @package MoveElevator\MeBackendSecurity\LoginProvider
 */
class UsernamePasswordLoginProvider extends \TYPO3\CMS\Backend\LoginProvider\UsernamePasswordLoginProvider
{
    /**
     * {@inheritdoc}
     */
    public function render(StandaloneView $view, PageRenderer $pageRenderer, LoginController $loginController)
    {
        parent::render($view, $pageRenderer, $loginController);

        if (empty(GeneralUtility::_GP('r')) === false && empty(GeneralUtility::_GP('u')) === false) {
            $view->setTemplatePathAndFilename(
                GeneralUtility::getFileAbsFileName(
                    'EXT:me_backend_security/Resources/Private/Templates/LoginProvider/PasswordResetLoginForm.html'
                )
            );
        }

        if (GeneralUtility::_GP('tx_mebackendsecurity_action') === 'changePassword') {
            $this->changePassword();

            return;
        }
    }

    private function changePassword()
    {
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump(GeneralUtility::_GP('tx_mebackendsecurity_old_password'));
    }
}
