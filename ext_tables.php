<?php

if (!defined ('TYPO3_MODE')) {
    die ('Access denied.');
}

$emClass = '\\TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility';

if (
    class_exists($emClass) &&
    method_exists($emClass, 'extPath')
) {
    // nothing
} else {
    $emClass = 't3lib_extMgm';
}

$divClass = '\\TYPO3\\CMS\\Core\\Utility\\GeneralUtility';

if (
    TYPO3_MODE == 'BE' &&
    !$loadTcaAdditions
) {
    call_user_func($emClass . '::addStaticFile', $_EXTKEY,
    'Configuration/TypoScript/PluginSetup/', 'Agency Registration for tt_address');

    call_user_func($emClass . '::addPiFlexFormValue', $_EXTKEY,
     'FILE:EXT:' . $_EXTKEY . '/pi/flexform_ds_pi.xml');
    call_user_func($emClass . '::addPlugin', array('LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tt_content.plugin', $_EXTKEY), 'list_type');
}

