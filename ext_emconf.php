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
    'version' => '3.0.1',
    'constraints' => [
        'depends' => [
            'php' => '8.0.0-8.9.99',
            'typo3' => '11.5.0-11.5.99',
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'MoveElevator\\MeBackendSecurity\\' => 'Classes',
        ],
    ],
];
