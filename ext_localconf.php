<?php

use MoveElevator\MeBackendSecurity\Authentication\BackendUserAuthentication;
use MoveElevator\MeBackendSecurity\Authentication\PasswordReset;
use MoveElevator\MeBackendSecurity\Controller\ResetPasswordController;
use MoveElevator\MeBackendSecurity\Evaluation\PasswordEvaluator;
use MoveElevator\MeBackendSecurity\Hook\BackendUserTableHook;
use MoveElevator\MeBackendSecurity\Hook\UserAuthHook;
use MoveElevator\MeBackendSecurity\LoginProvider\UsernamePasswordLoginProvider;
use TYPO3\CMS\Backend\Authentication\PasswordReset as CorePasswordReset;
use TYPO3\CMS\Backend\Controller\ResetPasswordController as CoreResetPasswordController;
use TYPO3\CMS\Core\Authentication\BackendUserAuthentication as CoreBackendUserAuthentication;

defined('TYPO3') || die();

(static function () {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['loginProviders'][1433416747]['provider'] =
        UsernamePasswordLoginProvider::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['postUserLookUp'][] =
        UserAuthHook::class . '->postUserLookUp';

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][PasswordEvaluator::class] =
        'EXT:me_backend_security/Classes/Evaluation/PasswordEvaluator.php';

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['me_backend_security'] =
        BackendUserTableHook::class;

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][CorePasswordReset::class] = [
        'className' => PasswordReset::class,
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][CoreResetPasswordController::class] = [
        'className' => ResetPasswordController::class,
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][CoreBackendUserAuthentication::class] = [
        'className' => BackendUserAuthentication::class,
    ];
})();
