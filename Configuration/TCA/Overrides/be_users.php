<?php

defined('TYPO3_MODE') || die();

use MoveElevator\MeBackendSecurity\Evaluation\PasswordEvaluator;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

(static function () {
    $languageFile = 'LLL:EXT:me_backend_security/Resources/Private/Language/locallang.xml:';
    $column = [
        'tx_mebackendsecurity_lastpasswordchange' => [
            'exclude' => false,
            'label' => $languageFile . 'be_users.tx_mebackendsecurity_lastpasswordchange',
            'config' => [
                'type' => 'input',
                'renderType' => 'inputDateTime',
                'eval' => 'datetime',
                'readOnly' => true,
            ],
        ],
    ];

    $GLOBALS['TCA']['be_users']['types'][0]['columnsOverrides'] =
    $GLOBALS['TCA']['be_users']['types'][1]['columnsOverrides'] = [
        'password' => [
            'config' => [
                'eval' => 'required,' . PasswordEvaluator::class . ',saltedPassword,password',
            ],
        ],
    ];

    ExtensionManagementUtility::addTCAcolumns('be_users', $column);
    ExtensionManagementUtility::addToAllTCAtypes('be_users', 'tx_mebackendsecurity_lastpasswordchange', '', 'after:lastlogin');
})();
