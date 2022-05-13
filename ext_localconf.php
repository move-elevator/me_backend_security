<?php

defined('TYPO3') || die();

use MoveElevator\MeBackendSecurity\Authentication\PasswordReset;
use MoveElevator\MeBackendSecurity\Controller\LoginController;
use MoveElevator\MeBackendSecurity\Evaluation\PasswordEvaluator;
use MoveElevator\MeBackendSecurity\Hook\TableHook;
use MoveElevator\MeBackendSecurity\Hook\UserAuthHook;
use TYPO3\CMS\Backend\Authentication\PasswordReset as CorePasswordReset;
use TYPO3\CMS\Backend\Controller\LoginController as CoreLoginController;
use TYPO3\CMS\Backend\LoginProvider\UsernamePasswordLoginProvider;

(static function () {
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['loginProviders'][1433416747]['provider'] =
        UsernamePasswordLoginProvider::class;

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['postUserLookUp'][] =
        UserAuthHook::class . '->postUserLookUp';

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['tce']['formevals'][PasswordEvaluator::class] =
        'EXT:me_backend_security/Classes/Evaluation/PasswordEvaluator.php';

    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['me_backend_security'] =
        TableHook::class;

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][CorePasswordReset::class] = [
        'className' => PasswordReset::class
    ];

    $GLOBALS['TYPO3_CONF_VARS']['SYS']['Objects'][CoreLoginController::class] = [
        'className' => LoginController::class
    ];
})();
