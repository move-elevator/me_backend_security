<?php

declare(strict_types=1);

namespace MoveElevator\MeBackendSecurity\Controller;

use MoveElevator\MeBackendSecurity\Domain\Model\ExtensionConfiguration;
use MoveElevator\MeBackendSecurity\Factory\ExtensionConfigurationFactory;
use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Backend\Controller\ResetPasswordController as CoreResetPasswordController;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration as ExtensionConfigurationUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ResetPasswordController extends CoreResetPasswordController
{
    protected function initializeResetPasswordView(ServerRequestInterface $request): void
    {
        parent::initializeResetPasswordView($request);

        /** @var ExtensionConfigurationUtility $extensionConfigurationUtility */
        $extensionConfigurationUtility = GeneralUtility::makeInstance(ExtensionConfigurationUtility::class);
        $backendSecurityConfiguration = ExtensionConfigurationFactory::create(
            $extensionConfigurationUtility->get(ExtensionConfiguration::EXT_KEY)
        );

        $this->view->setTemplatePathAndFilename(
            GeneralUtility::getFileAbsFileName(
                'EXT:me_backend_security/Resources/Private/Templates/Login/ResetPasswordForm.html'
            )
        );
        $this->view->assign('configuration', $backendSecurityConfiguration);
    }
}
