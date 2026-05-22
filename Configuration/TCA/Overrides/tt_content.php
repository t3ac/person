<?php

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;

defined('TYPO3') || die;

$pluginConfig = ['pi1', 'pi2', 'pi3'];
foreach ($pluginConfig as $pluginName) {
    $pluginNameForLabel = $pluginName;
    $pluginIdentifier = ExtensionUtility::registerPlugin(
        'person',
        $pluginName,
        //GeneralUtility::underscoredToUpperCamelCase($pluginName),
        'LLL:EXT:person/Resources/Private/Language/locallang.xlf:plugin.' . $pluginNameForLabel . '.title',
        'person-plugin-'.$pluginNameForLabel,
        'Person',
        'LLL:EXT:person/Resources/Private/Language/locallang.xlf:plugin.' . $pluginNameForLabel . '.description',
    );

    $contentTypeName = $pluginName;
    $flexformFileName = $pluginNameForLabel;

    ExtensionManagementUtility::addPiFlexFormValue(
        '*',
        'FILE:EXT:person/Configuration/FlexForms/flexform_' . $pluginName . '.xml',
        $pluginIdentifier
    );
    // Add the FlexForm to the show item list
    ExtensionManagementUtility::addToAllTCAtypes(
        'tt_content',
        '--div--;LLL:EXT:frontend/Resources/Private/Language/locallang_ttc.xlf:tabs.plugin, pi_flexform',
        $pluginIdentifier,
        'after:palette:headers'
    );

}