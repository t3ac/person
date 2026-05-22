<?php
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use T3ac\Person\Controller\PersonController;

defined('TYPO3') || die();

(static function() {
    ExtensionUtility::configurePlugin(
        'Person',
        'pi1',
        [
            PersonController::class => 'index'
        ],
        // non-cacheable actions
        [
            PersonController::class => 'index'
        ],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );

    ExtensionUtility::configurePlugin(
        'Person',
        'pi2',
        [
            PersonController::class => 'list, show'
        ],
        // non-cacheable actions
        [
            
        ],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );

    ExtensionUtility::configurePlugin(
        'Person',
        'pi3',
        [
            PersonController::class => 'search, show'
        ],
        // non-cacheable actions
        [
            PersonController::class => 'search, show'
        ],
        ExtensionUtility::PLUGIN_TYPE_CONTENT_ELEMENT
        );
    
})();
    