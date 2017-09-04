<?php

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$typoVersion = tx_div2007_core::getTypoVersion();
if (!isset($_EXTKEY)) {
    $_EXTKEY = AGENCY_TT_ADDRESS_EXT;
}

if (
	TYPO3_MODE == 'BE' &&
	!$loadTcaAdditions
) {
	t3lib_extMgm::addStaticFile($_EXTKEY, 'Configuration/TypoScript/PluginSetup/', 'Agency Registration for tt_address');

    if (version_compare(TYPO3_version, '6.2.0', '<')) {
        call_user_func($emClass . '::addStaticFile', $_EXTKEY, 'Configuration/TypoScript/PluginSetup/Compatibility4.5/', 'Agency Registration for tt_address compatibility TYPO3 4.5');
    }

	if ($typoVersion < 6002000) {

		t3lib_div::loadTCA('tt_content');
	}

	$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY] = 'layout,select_key';
	$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY] = 'pi_flexform';
	t3lib_extMgm::addPiFlexFormValue($_EXTKEY, 'FILE:EXT:' . $_EXTKEY . '/pi/flexform_ds_pi.xml');
	t3lib_extMgm::addPlugin(Array('LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tt_content.plugin', $_EXTKEY), 'list_type');
}

if (!t3lib_extMgm::isLoaded('sr_email_subscribe')) {

	$addressTable = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['addressTable'];

	if ($addressTable == 'tt_address') {

		/**
		* Setting up country, country subdivision, preferred language in tt_address table
		* Adjusting some maximum lengths to the values as corresponding fields in fe_users as set by extension agency
		*/
		if ($typoVersion < 6002000) {
			t3lib_div::loadTCA('tt_address');
		}

		if (
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['useImageFolder'] &&
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['imageFolder'] != ''
		) {
			$GLOBALS['TCA']['tt_address']['columns']['image']['config']['uploadfolder'] = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['imageFolder'];
		}

		t3lib_extMgm::addTCAcolumns('tt_address', Array(
			'static_info_country' => Array (
				'exclude' => 0,
				'label' => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tt_address.static_info_country',
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
				'label' => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tt_address.zone',
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
				'label' => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tt_address.language',
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
				'label' => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tt_address.date_of_birth',
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
				'label' => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tt_address.comments',
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

		if ($typoVersion < 6002000) {

			$GLOBALS['TCA']['tt_address']['feInterface']['fe_admin_fieldList'] =
				preg_replace('/(^|,)\s*country\s*(,|$)/', '$1zone,static_info_country,country,language,comments$2', $GLOBALS['TCA']['tt_address']['feInterface']['fe_admin_fieldList']);
			$GLOBALS['TCA']['tt_address']['feInterface']['fe_admin_fieldList'] .= ',date_of_birth';

			if (strstr($GLOBALS['TCA']['tt_address']['feInterface']['fe_admin_fieldList'], 'image') === FALSE) {
				$GLOBALS['TCA']['tt_address']['feInterface']['fe_admin_fieldList'] .= ',image';
			}
		}

		t3lib_extMgm::addToAllTCAtypes('tt_address', 'comments');
		$GLOBALS['TCA']['tt_address']['palettes']['3']['showitem'] =
			preg_replace('/(^|,)\s*country\s*(,|$)/', '$1zone,static_info_country,country,language$2', $GLOBALS['TCA']['tt_address']['palettes']['3']['showitem']);

			// tt_address modified
		if (
			$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['enableDirectMail'] &&
			!t3lib_extMgm::isLoaded('direct_mail')
		) {
			if (!$GLOBALS['TCA']['sys_dmail_category']['columns']) {
				$GLOBALS['TCA']['sys_dmail_category'] = array(
					'ctrl' => array(
						'title' => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:sys_dmail_category',
						'label' => 'category',
						'tstamp' => 'tstamp',
						'crdate' => 'crdate',
						'cruser_id' => 'cruser_id',
						'languageField' => 'sys_language_uid',
						'transOrigPointerField' => 'l18n_parent',
						'transOrigDiffSourceField' => 'l18n_diffsource',
						'sortby' => 'sorting',
						'delete' => 'deleted',
						'enablecolumns' => array(
							'disabled' => 'hidden',
						),
						'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'icon_tx_directmail_category.gif',
						)
				);

				// ******************************************************************
				// Categories
				// ******************************************************************
				$GLOBALS['TCA']['sys_dmail_category'] = Array (
					'ctrl' => $TCA['sys_dmail_category']['ctrl'],
					'interface' => Array (
							'showRecordFieldList' => 'hidden,category'
					),
					'feInterface' => $TCA['sys_dmail_category']['feInterface'],
					'columns' => Array (
						'sys_language_uid' => Array (
							'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.language',
							'config' => Array (
								'type' => 'select',
								'foreign_table' => 'sys_language',
								'foreign_table_where' => 'ORDER BY sys_language.title',
								'items' => Array(
									Array('LLL:EXT:lang/locallang_general.xml:LGL.allLanguages',-1),
									Array('LLL:EXT:lang/locallang_general.xml:LGL.default_value',0)
								)
							)
						),
						'l18n_parent' => Array (
							'displayCond' => 'FIELD:sys_language_uid:>:0',
							'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.l18n_parent',
							'config' => Array (
								'type' => 'select',
								'items' => Array (
									Array('', 0),
								),
								'foreign_table' => 'sys_dmail_category',
								'foreign_table_where' => 'AND sys_dmail_category.pid=###CURRENT_PID### AND sys_dmail_category.sys_language_uid IN (-1,0)',
							)
						),
						'l18n_diffsource' => Array (
							'config' => Array (
									'type' => 'passthrough'
							)
						),
						'hidden' => Array (
							'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
							'config' => Array (
								'type' => 'check',
								'default' => '0'
							)
						),
						'category' => Array (
							'label' => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:sys_dmail_category.category',
							'config' => Array (
								'type' => 'input',
								'size' => '30',
							)
						),
						'old_cat_number' => Array (
							'label' => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:sys_dmail_category.old_cat_number',
							'l10n_mode' => 'exclude',
							'config' => Array (
								'type' => 'input',
								'size' => '2',
								'eval' => 'trim',
								'max' => '2',
							)
						),
					),
					'types' => Array (
						'0' => Array('showitem' => 'sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource,hidden;;1;;1-1-1, category')
					),
					'palettes' => Array (
						'1' => Array('showitem' => '')
					)
				);
			}

			$tempCols = Array(
				'module_sys_dmail_category' => Array(
					'label'=>'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tt_address.module_sys_dmail_category',
					'exclude' => '1',
					'config' => array(
						'type' => 'select',
						'allowed' => 'sys_dmail_category',
						'MM' => 'sys_dmail_ttaddress_category_mm',
						'foreign_table' => 'sys_dmail_category',
						'foreign_table_where' =>
							'AND sys_dmail_category.pid IN ' .
							'(###PAGE_TSCONFIG_IDLIST###) ORDER BY sys_dmail_category.sorting',
						'size' => 10,
						'selectedListStyle' => 'width:450px',
						'renderMode' => 'check',
						'minitems' => 0,
						'maxitems' => 1000,
						)

				),
				'module_sys_dmail_html' => Array(
					'label' => 'LLL:EXT:' . $_EXTKEY . '/locallang_db.xml:tt_address.module_sys_dmail_html',
					'exclude' => '1',
					'config' => Array(
						'type' => 'check'
						)
					)
				);

			t3lib_extMgm::addTCAcolumns('tt_address', $tempCols);
			t3lib_extMgm::addToAllTCATypes('tt_address','--div--;Direct mail,module_sys_dmail_category;;;;1-1-1,module_sys_dmail_html');

			if ($typoVersion < 6002000) {
				$GLOBALS['TCA']['tt_address']['feInterface']['fe_admin_fieldList'] .= ',module_sys_dmail_category,module_sys_dmail_html';
			}
		}
	}
}

