<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

if (!defined ('AGENCY_TT_ADDRESS_EXT')) {
	define('AGENCY_TT_ADDRESS_EXT', $_EXTKEY);
}

if (!defined ('PATH_BE_AGENCYTTADDRESS')) {
	define('PATH_BE_AGENCYTTADDRESS', t3lib_extMgm::extPath($_EXTKEY));
}

if (!defined ('PATH_BE_AGENCYTTADDRESS_REL')) {
	define('PATH_BE_AGENCYTTADDRESS_REL', t3lib_extMgm::extRelPath($_EXTKEY));
}

if (!defined ('PATH_FE_AGENCYTTADDRESS_REL')) {
	define('PATH_FE_AGENCYTTADDRESS_REL', t3lib_extMgm::siteRelPath($_EXTKEY));
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

t3lib_extMgm::addPItoST43($_EXTKEY, 'class.tx_agencyttaddress.php', '', 'list_type', 0);

$_EXTCONF = unserialize($_EXTCONF);    // unserializing the configuration so we can use it here:
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['imagefolder'] =
	$_EXTCONF['imageFolder'] ? $_EXTCONF['imageFolder'] : 'uploads/tx_agencyttaddress';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['useImageFolder'] =
	!empty($_EXTCONF['useImageFolder']) ? $_EXTCONF['useImageFolder'] : '0';
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['addressTable'] = $_EXTCONF['addressTable'];

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['enableDirectMail'] = $_EXTCONF['enableDirectMail'] ? $_EXTCONF['enableDirectMail'] : 0;

	// Save extension version and constraints
require_once(t3lib_extMgm::extPath($_EXTKEY) . 'ext_emconf.php');
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['version'] = $EM_CONF[$_EXTKEY]['version'];
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['constraints'] = $EM_CONF[$_EXTKEY]['constraints'];


if (t3lib_extMgm::isLoaded(DIV2007_EXT)) {
	if (!defined ('PATH_BE_div2007')) {
		define('PATH_BE_div2007', t3lib_extMgm::extPath(DIV2007_EXT));
	}
}

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

$addressTable = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['addressTable'];
if (!$addressTable) {
	if (t3lib_extMgm::isLoaded(PARTY_EXT)) {
		$addressTable = 'tx_wecpeople_addresses';
	} else if (t3lib_extMgm::isLoaded(PARTNER_EXT)) {
		$addressTable = 'tx_partner_main';
	} else if (t3lib_extMgm::isLoaded(TT_ADDRESS_EXT)) {
		$addressTable = 'tt_address';
	} else {
		$addressTable = 'fe_users';
	}
}
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['addressTable'] = $addressTable;

if (TYPO3_MODE == 'BE')	{

	if (defined('PATH_BE_div2007')) {
		// replace the output of the former CODE field with the flexform
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['list_type_Info'][$_EXTKEY][] =
			'EXT:' . $_EXTKEY . '/hooks/class.tx_agencyttaddress_hooks_cms.php:&tx_agencyttaddress_hooks_cms->pmDrawItem';
	}

	if (
		!defined($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['addTables']['fe_users']['MENU'])
		&& ($addressTable == 'tt_address')
	) {
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['LLFile'][$addressTable] = 'EXT:' . $_EXTKEY . '/locallang.xml';


		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['addTables'][$addressTable] = array (
			'default' => array(
				'MENU' => 'm_default',
				'fList' =>  'first_name,middle_name,last_name,title,address,zip,city,country,gender,image,uid',
				'icon' => TRUE
			),
			'ext' => array (
				'MENU' => 'm_ext',
				'fList' =>  'name,description,email,phone,mobile,fax,www,birthday',
				'icon' => TRUE
			),
			'company' => array (
				'MENU' => 'm_company',
				'fList' =>  'name,city,company,building,room,addressgroup',
				'icon' => TRUE
			),
		);
	}
}

if (TYPO3_MODE == 'FE') { // only needed before TYPO3 6.2
	if (t3lib_extMgm::isLoaded('tt_products')) {
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['tt_products']['extendingTCA'][] = $_EXTKEY;
	}
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['agency']['extendingTCA'][] = $_EXTKEY;

	if (t3lib_extMgm::isLoaded('direct_mail')) {
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['extendingTCA'][] = 'direct_mail';
	}
}


?>