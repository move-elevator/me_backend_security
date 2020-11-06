<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'm:e Backend Security',
    'description' => 'Erweiterte Sicherheit für das TYPO3-Backend',
    'version' => '3.0.0',
    'category' => 'services',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-10.4.99',
        ],
    ],
    'state' => 'stable',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearCacheOnLoad' => true,
    'author' => 'move elevator GmbH',
    'author_email' => 'entwicklung@move-elevator.de',
    'author_company' => 'move elevator GmbH',
    'autoload' => [
        'psr-4' => [
            'MoveElevator\\MeBackendSecurity\\' => 'Classes',
        ],
    ],
];
