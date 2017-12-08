<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'm:e Backend Security',
    'description' => 'Erweiterte Sicherheit für das TYPO3-Backend',
    'version' => '1.0.1',
    'category' => 'services',
    'constraints' => [
        'depends' => [
            'typo3' => '7.6.0-8.6.99'
        ],
        'conflicts' => [],
        'suggests' => []
    ],
    'state' => 'stable',
    'uploadfolder' => false,
    'createDirs' => '',
    'clearCacheOnLoad' => true,
    'author' => 'move elevator GmbH',
    'author_email' => 'entwicklung@move-elevator.de',
    'author_company' => 'move elevator GmbH',
    'autoload' => []
];
