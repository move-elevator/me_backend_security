<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$column = [
    'tx_mebackendsecurity_lastpasswordchange' => [
        'exclude' => 0,
        'label' =>
            'LLL:EXT:me_backend_security/Resources/Private/Language/locallang.xml:' .
            'be_users.tx_mebackendsecurity_lastpasswordchange',
        'config' => [
            'type' => 'input',
            'size' => 12,
            'eval' => 'datetime',
        ]
    ],
];

ExtensionManagementUtility::addTCAcolumns('be_users', $column);
ExtensionManagementUtility::addToAllTCAtypes('be_users', 'tx_mebackendsecurity_lastpasswordchange;;;;1-1-1');
