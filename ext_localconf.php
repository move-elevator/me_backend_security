<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['postUserLookUp'][] =
    MoveElevator\MeBackendSecurity\Hook\UserAuthHook::class . '->postUserLookUp';

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['loginProviders'][1433416747]['provider'] =
    \MoveElevator\MeBackendSecurity\LoginProvider\UsernamePasswordLoginProvider::class;
