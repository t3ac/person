<?php

declare(strict_types=1);

$EM_CONF[$_EXTKEY] = [
    'title' => 'Person',
    'description' => '',
    'category' => 'plugin',
    'author' => 'Michael Lang',
    'author_email' => 'info@t3ac.de',
    'state' => 'alpha',
    'version' => '1.0.0',
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-13.4.99',
            'extbase' => '13.4.0-13.4.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
];
