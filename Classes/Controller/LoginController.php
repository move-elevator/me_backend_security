<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Controller;

use MoveElevator\MeBackendSecurity\Factory\ExtensionConfigurationFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Authentication\PasswordReset;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as ExtensionConfigurationUtility;
use TYPO3\CMS\Core\Context\Context;
use TYPO3\CMS\Core\Http\HtmlResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class LoginController extends \TYPO3\CMS\Backend\Controller\LoginController
{
    private const EXTKEY = 'me_backend_security';

    /**
     * Validates the link and show a form to enter the new password.
     *
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     */
    public function passwordResetAction(ServerRequestInterface $request): ResponseInterface
    {
        $context = GeneralUtility::makeInstance(Context::class);

        if ($context->getAspect('backend.user')->isLoggedIn()) {
            return $this->formAction($request);
        }

        $this->init($request);
        $passwordReset = GeneralUtility::makeInstance(PasswordReset::class);
        $extensionConfigurationUtility = GeneralUtility::makeInstance(ExtensionConfigurationUtility::class);
        $backendSecurityConfiguration = ExtensionConfigurationFactory::create(
            $extensionConfigurationUtility->get(self::EXTKEY)
        );

        $this->view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(
                'EXT:me_backend_security/Resources/Private/Templates/Login/ResetPasswordForm.html'
            )
        );
        $this->view->assign('enablePasswordReset', $passwordReset->isEnabled());
        $this->view->assign('configuration', $backendSecurityConfiguration);

        if (!$passwordReset->isValidResetTokenFromRequest($request)) {
            $this->view->assign('invalidToken', true);
        }

        $this->view->assign('token', $request->getQueryParams()['t'] ?? '');
        $this->view->assign('identity', $request->getQueryParams()['i'] ?? '');
        $this->view->assign('expirationDate', $request->getQueryParams()['e'] ?? '');
        $this->moduleTemplate->setContent($this->view->render());

        return new HtmlResponse($this->moduleTemplate->renderContent());
    }
}
