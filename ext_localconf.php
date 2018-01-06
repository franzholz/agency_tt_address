<?php

$emClass = '\\TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility';

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

if (!defined ('AGENCY_TT_ADDRESS_EXT')) {
    define('AGENCY_TT_ADDRESS_EXT', $_EXTKEY);
}

if (!defined ('PATH_BE_AGENCYTTADDRESS')) {
    define('PATH_BE_AGENCYTTADDRESS', call_user_func($emClass . '::extPath', $_EXTKEY));
}

if (!defined ('PATH_BE_AGENCYTTADDRESS_REL')) {
    define('PATH_BE_AGENCYTTADDRESS_REL', call_user_func($emClass . '::extRelPath', $_EXTKEY));
}

if (!defined ('PATH_FE_AGENCYTTADDRESS_REL')) {
    define('PATH_FE_AGENCYTTADDRESS_REL', call_user_func($emClass . '::siteRelPath', $_EXTKEY));
}

if (!defined ('PATH_AGENCYTTADDRESS_ICON_TABLE_REL')) {
    define('PATH_AGENCYTTADDRESS_ICON_TABLE_REL', 'EXT:' . $_EXTKEY . '/Resources/Public/Icons/');
}

if (!defined ('AGENCY_EXT')) {
    define('AGENCY_EXT','agency');
}

if (!defined ('DIV2007_EXT')) {
    define('DIV2007_EXT', 'div2007');
}

if (!defined ('TT_ADDRESS_EXT')) {
    define('TT_ADDRESS_EXT', 'tt_address');
}

if (!defined ('PARTNER_EXT')) {
    define('PARTNER_EXT', 'partner');
}

if (!defined ('PARTY_EXT')) {
    define('PARTY_EXT', 'party');
}

call_user_func($emClass . '::addPItoST43', $_EXTKEY, 'class.tx_agencyttaddress.php', '', 'list_type', 0);

$_EXTCONF = unserialize($_EXTCONF);    // unserializing the configuration so we can use it here:
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['imagefolder'] =
    $_EXTCONF['imageFolder'] ? $_EXTCONF['imageFolder'] : 'uploads/tx_agencyttaddress';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['useImageFolder'] =
    !empty($_EXTCONF['useImageFolder']) ? $_EXTCONF['useImageFolder'] : '0';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['addressTable'] = $_EXTCONF['addressTable'];

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['enableDirectMail'] = $_EXTCONF['enableDirectMail'] ? $_EXTCONF['enableDirectMail'] : 0;

    // Save extension version and constraints

require_once(PATH_BE_AGENCYTTADDRESS . 'ext_emconf.php');
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['version'] = $EM_CONF[$_EXTKEY]['version'];
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['constraints'] = $EM_CONF[$_EXTKEY]['constraints'];

if (call_user_func($emClass . '::isLoaded', DIV2007_EXT)) {
    if (!defined ('PATH_BE_DIV2007')) {
        $bePath = call_user_func($emClass . '::extPath', DIV2007_EXT);
        define('PATH_BE_DIV2007', $bePath);
    }
}

if (
    version_compare(TYPO3_version, '7.6.0', '>')
) {
    // Configure captcha hooks
    if (!is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['captcha'])) {
        $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['captcha'] = [];
    }
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['captcha'][] = 'JambageCom\\Div2007\\Captcha\\Captcha';
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['captcha'][] = 'JambageCom\\Div2007\\Captcha\\Freecap';
} else {

        // Captcha marker hook
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['registrationProcess'][] =
        'EXT:' . AGENCY_EXT . '/hooks/captcha/class.tx_agency_captcha.php:&tx_agency_captcha';
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['model'][] =
        'EXT:' . AGENCY_EXT . '/hooks/captcha/class.tx_agency_captcha.php:&tx_agency_captcha';
        // Freecap marker hook
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['registrationProcess'][] =
        'EXT:' . AGENCY_EXT . '/hooks/freecap/class.tx_agency_freecap.php:&tx_agency_freecap';
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['model'][] =
        'EXT:' . AGENCY_EXT . '/hooks/freecap/class.tx_agency_freecap.php:&tx_agency_freecap';
}

$addressTable = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['addressTable'];
if (!$addressTable) {
    if (call_user_func($emClass . '::isLoaded', PARTY_EXT)) {
        $addressTable = 'tx_party_addresses';
    } else if (call_user_func($emClass . '::isLoaded', PARTNER_EXT)) {
        $addressTable = 'tx_partner_main';
    } else if (call_user_func($emClass . '::isLoaded', TT_ADDRESS_EXT)) {
        $addressTable = 'tt_address';
    } else {
        $addressTable = 'fe_users';
    }
}

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['addressTable'] = $addressTable;

if (
    TYPO3_MODE == 'BE'
) {
    if (call_user_func($emClass . '::isLoaded', DIV2007_EXT)) {
        // replace the output of the former CODE field with the flexform
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$_EXTKEY . '_pi'][] =
            'JambageCom\\AgencyTtAddress\\Hooks\\CmsBackend->pmDrawItem';
    }
}

