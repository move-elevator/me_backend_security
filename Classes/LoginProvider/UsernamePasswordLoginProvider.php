<?php

namespace MoveElevator\MeBackendSecurity\LoginProvider;

use TYPO3\CMS\Backend\Controller\LoginController;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;
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
        $templatePathAndFilename = GeneralUtility::getFileAbsFileName(
            'EXT:me_backend_security/Resources/Private/Templates/LoginProvider/PasswordResetLoginForm.html'
        );

        parent::render($view, $pageRenderer, $loginController);

        if ($this->isResetFormRequired()) {
            $view->setTemplatePathAndFilename(
                $templatePathAndFilename
            );

            $view->assign('error', GeneralUtility::_GP('e'));
        }
    }

    /**
     * @return bool
     */
    private function isResetFormRequired()
    {
        $resetForm = GeneralUtility::_GP('r');
        $resetFormVars = GeneralUtility::_GP('tx_mebackendsecurity');

        if (empty($resetForm) === false || empty($resetFormVars) === false) {
            return true;
        }

        return false;
    }
}
