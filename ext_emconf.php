<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'm:e Backend Security',
    'description' => 'Erweiterte Sicherheit für das TYPO3-Backend',
    'category' => 'services',
    'author' => 'move elevator GmbH',
    'author_email' => 'entwicklung@move-elevator.de',
    'author_company' => 'move elevator GmbH',
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'version' => '2.0.7',
    'constraints' => [
        'depends' => [
            'typo3' => '11.5.0-11.5.99',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'MoveElevator\\MeBackendSecurity\\' => 'Classes',
        ],
    ],
];
