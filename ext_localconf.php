<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_userauth.php']['postUserLookUp'][] =
    MoveElevator\MeBackendSecurity\Hook\UserAuthHook::class . '->postUserLookUp';

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['backend']['loginProviders'][1507295520] = [
    'provider' => \MoveElevator\MeBackendSecurity\LoginProvider\UsernamePasswordLoginProvider::class,
    'sorting' => 200,
    'icon-class' => 'fa-lock',
    'label' => 'LLL:EXT:me_backend_security/Resources/Private/Language/locallang.xlf:login.link'
];
