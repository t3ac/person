<?php

defined('TYPO3') or die;

$ll = 'LLL:EXT:person/Resources/Private/Language/locallang.xlf:';

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

$tmp_person_columns = [

    'department' => [
        'exclude' => true,
        'label' => $ll . 'department',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim',
            'default' => ''
        ],
    ],
    'office' => [
        'exclude' => 0,
        'label' => $ll . 'office',
        'config' => [
            'type' => 'input',
            'size' => '10',
            'eval' => 'trim',
        ]
    ],
    'position' => [
        'exclude' => 0,
        'label' => $ll . 'position',
        'config' => [
            'type' => 'input',
            'size' => '10',
            'eval' => 'trim',
        ]
    ],
    'area' => [
        'exclude' => 1,
        'label' => $ll . 'area',
        'config' => [
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim'
        ]
    ],
    'gender' => [
        'exclude' => 1,
        'label' => $ll . 'gender',
        'config' => [
            'type' => 'select',
            'renderType' => 'selectSingle',
            'items' => [
                ['label' => $ll . 'select', 'value' => '--div--'],
                ['label' => $ll . 'none', 'value' => 0],               
                ['label' => $ll . 'f', 'value' => 1],
                ['label' => $ll . 'm', 'value' => 2],
                ['label' => $ll . 'd', 'value' => 3],
            ],        
        ],
    ],
];

$fields = 'area,department,position,office,gender';

ExtensionManagementUtility::addTCAcolumns('fe_users', $tmp_person_columns);

ExtensionManagementUtility::addToAllTCAtypes(
    'fe_users',
    '--div--;LLL:EXT:person/Resources/Private/Language/locallang.xlf:fe_users.tab, ' . $fields
    );

$GLOBALS['TCA']['fe_users']['ctrl']['default_sortby'] = 'ORDER BY name';
$GLOBALS['TCA']['fe_users']['ctrl']['searchFields'] = 'department,username,name,first_name,last_name,middle_name,address,telephone,fax,email,title,office,zip,city,country,area';

