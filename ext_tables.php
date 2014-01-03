<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}


if (!t3lib_extMgm::isLoaded('sr_email_subscribe')) {

	t3lib_extMgm::addStaticFile(AGENCY_TT_ADDRESS_EXT, 'static/', 'Agency Registration for tt_address');

	t3lib_div::loadTCA('tt_content');
	$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][AGENCY_TT_ADDRESS_EXT] = 'layout,select_key';
	$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][AGENCY_TT_ADDRESS_EXT] = 'pi_flexform';
	t3lib_extMgm::addPiFlexFormValue(AGENCY_TT_ADDRESS_EXT, 'FILE:EXT:' . AGENCY_TT_ADDRESS_EXT . '/pi/flexform_ds_pi.xml');
	t3lib_extMgm::addPlugin(Array('LLL:EXT:' . AGENCY_TT_ADDRESS_EXT . '/locallang_db.xml:tt_content.plugin', AGENCY_TT_ADDRESS_EXT), 'list_type');

	$addressTable = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][AGENCY_TT_ADDRESS_EXT]['addressTable'];

	if ($addressTable == 'tt_address') {

		/**
		* Setting up country, country subdivision, preferred language in tt_address table
		* Adjusting some maximum lengths to the values as corresponding fields in fe_users as set by extension sr_feuser_register
		*/
		t3lib_div::loadTCA('tt_address');

		if (
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][AGENCY_TT_ADDRESS_EXT]['useImageFolder'] &&
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][AGENCY_TT_ADDRESS_EXT]['imageFolder'] != ''
		) {
			$GLOBALS['TCA']['tt_address']['columns']['image']['config']['uploadfolder'] = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][AGENCY_TT_ADDRESS_EXT]['imageFolder'];
		}

		t3lib_extMgm::addTCAcolumns('tt_address', Array(
			'static_info_country' => Array (
				'exclude' => 0,
				'label' => 'LLL:EXT:agency_tt_address/locallang_db.xml:tt_address.static_info_country',
				'config' => Array (
					'type' => 'input',
					'size' => '5',
					'max' => '3',
					'eval' => '',
					'default' => ''
				)
			),
			'zone' => Array (
				'exclude' => 0,
				'label' => 'LLL:EXT:agency_tt_address/locallang_db.xml:tt_address.zone',
				'config' => Array (
					'type' => 'input',
					'size' => '20',
					'max' => '40',
					'eval' => 'trim',
					'default' => ''
				)
			),
			'language' => Array (
				'exclude' => 0,
				'label' => 'LLL:EXT:agency_tt_address/locallang_db.xml:tt_address.language',
				'config' => Array (
					'type' => 'input',
					'size' => '4',
					'max' => '2',
					'eval' => '',
					'default' => ''
				)
			),
			'date_of_birth' => Array (
				'exclude' => 0,
				'label' => 'LLL:EXT:agency_tt_address/locallang_db.xml:tt_address.date_of_birth',
				'config' => Array (
					'type' => 'input',
					'size' => '10',
					'max' => '20',
					'eval' => 'date',
					'checkbox' => '0',
					'default' => ''
				)
			),
			'comments' => Array (
				'exclude' => 0,
				'label' => 'LLL:EXT:agency_tt_address/locallang_db.xml:tt_address.comments',
				'config' => Array (
					'type' => 'text',
					'rows' => '5',
					'cols' => '48'
				)
			),
		));

		$GLOBALS['TCA']['tt_address']['interface']['showRecordFieldList'] = preg_replace('/(^|,)\s*country\s*(,|$)/', '$1zone,static_info_country,country,language$2', $GLOBALS['TCA']['tt_address']['interface']['showRecordFieldList']);
		$GLOBALS['TCA']['tt_address']['interface']['showRecordFieldList'] = preg_replace('/(^|,)\s*title\s*(,|$)/', '$1date_of_birth,title$2', $GLOBALS['TCA']['tt_address']['interface']['showRecordFieldList']);
		$GLOBALS['TCA']['tt_address']['interface']['showRecordFieldList'] = preg_replace('/(^|,)\s*www\s*(,|$)/', '$1www,comments$2', $GLOBALS['TCA']['tt_address']['interface']['showRecordFieldList']);

		$GLOBALS['TCA']['tt_address']['feInterface']['fe_admin_fieldList'] = preg_replace('/(^|,)\s*country\s*(,|$)/', '$1zone,static_info_country,country,language,comments$2', $GLOBALS['TCA']['tt_address']['feInterface']['fe_admin_fieldList']);
		$GLOBALS['TCA']['tt_address']['feInterface']['fe_admin_fieldList'] .= ',date_of_birth';

		if (strstr($GLOBALS['TCA']['tt_address']['feInterface']['fe_admin_fieldList'], 'image') === FALSE) {
			$GLOBALS['TCA']['tt_address']['feInterface']['fe_admin_fieldList'] .= ',image';
		}

		t3lib_extMgm::addToAllTCAtypes('tt_address', 'comments');
		$GLOBALS['TCA']['tt_address']['palettes']['3']['showitem'] = preg_replace('/(^|,)\s*country\s*(,|$)/', '$1zone,static_info_country,country,language$2', $GLOBALS['TCA']['tt_address']['palettes']['3']['showitem']);

			// tt_address modified
		if (!t3lib_extMgm::isLoaded('direct_mail')) {
			$tempCols = Array(
				'module_sys_dmail_html' => Array(
					'label'=>'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tt_address.module_sys_dmail_html',
					'exclude' => '1',
					'config'=>Array(
						'type'=>'check'
						)
					)
			);
			t3lib_extMgm::addTCAcolumns('tt_address', $tempCols);
			t3lib_extMgm::addToAllTCATypes('tt_address', '--div--;Direct mail,module_sys_dmail_html;;;;1-1-1');
			$GLOBALS['TCA']['tt_address']['feInterface']['fe_admin_fieldList'] .= ',module_sys_dmail_html';
		}
	}
}

?>