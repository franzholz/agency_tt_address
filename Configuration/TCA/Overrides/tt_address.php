<?php

if (!defined ('TYPO3_MODE')) {
    die ('Access denied.');
}

$table = 'tt_address';

if (
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][AGENCY_TT_ADDRESS_EXT]['useImageFolder'] &&
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][AGENCY_TT_ADDRESS_EXT]['imageFolder'] != ''
) {
    $GLOBALS['TCA'][$table]['columns']['image']['config']['uploadfolder'] = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][AGENCY_TT_ADDRESS_EXT]['imageFolder'];
}

$GLOBALS['TCA'][$table]['interface']['showRecordFieldList'] = preg_replace('/(^|,)\s*country\s*(,|$)/', '$1zone,static_info_country,country,language$2', $GLOBALS['TCA'][$table]['interface']['showRecordFieldList']);
$GLOBALS['TCA'][$table]['interface']['showRecordFieldList'] = preg_replace('/(^|,)\s*title\s*(,|$)/', '$1date_of_birth,title$2', $GLOBALS['TCA'][$table]['interface']['showRecordFieldList']);
$GLOBALS['TCA'][$table]['interface']['showRecordFieldList'] = preg_replace('/(^|,)\s*www\s*(,|$)/', '$1www,comments$2', $GLOBALS['TCA'][$table]['interface']['showRecordFieldList']);


$temporaryColumns = array(
    'static_info_country' => array (
        'exclude' => 0,
        'label' => 'LLL:EXT:' . AGENCY_TT_ADDRESS_EXT . '/locallang_db.xml:tt_address.static_info_country',
        'config' => array (
            'type' => 'input',
            'size' => '5',
            'max' => '3',
            'eval' => '',
            'default' => ''
        )
    ),
    'zone' => array (
        'exclude' => 0,
        'label' => 'LLL:EXT:' . AGENCY_TT_ADDRESS_EXT . '/locallang_db.xml:tt_address.zone',
        'config' => array (
            'type' => 'input',
            'size' => '20',
            'max' => '40',
            'eval' => 'trim',
            'default' => ''
        )
    ),
    'language' => array (
        'exclude' => 0,
        'label' => 'LLL:EXT:' . AGENCY_TT_ADDRESS_EXT . '/locallang_db.xml:tt_address.language',
        'config' => array (
            'type' => 'input',
            'size' => '4',
            'max' => '2',
            'eval' => '',
            'default' => ''
        )
    ),
    'date_of_birth' => array (
        'exclude' => 0,
        'label' => 'LLL:EXT:' . AGENCY_TT_ADDRESS_EXT . '/locallang_db.xml:tt_address.date_of_birth',
        'config' => array (
            'type' => 'input',
            'size' => '10',
            'max' => '20',
            'eval' => 'date',
            'checkbox' => '0',
            'default' => ''
        )
    ),
    'comments' => array (
        'exclude' => 0,
        'label' => 'LLL:EXT:' . AGENCY_TT_ADDRESS_EXT . '/locallang_db.xml:tt_address.comments',
        'config' => array (
            'type' => 'text',
            'rows' => '5',
            'cols' => '48'
        )
    )
);


if ( // Direct Mail tables exist but Direct Mail shall not be used
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][AGENCY_TT_ADDRESS_EXT]['enableDirectMail'] &&
    !\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('direct_mail')
) {
    // tt_address modified
    $directMailTemporaryColumns = array(
        'module_sys_dmail_category' => array(
            'label' => 'LLL:EXT:' . AGENCY_TT_ADDRESS_EXT . '/locallang_db.xml:tt_address.module_sys_dmail_category',
            'exclude' => '1',
            'config' => array(
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_dmail_category',
                'foreign_table_where' => 'AND sys_dmail_category.l18n_parent=0 AND sys_dmail_category.pid IN (###PAGE_TSCONFIG_IDLIST###) ORDER BY sys_dmail_category.sorting',
                'itemsProcFunc' => 'JambageCom\\AgencyTtAddress\\Configuration\\SelectCategories->get_localized_categories',
                'itemsProcFunc_config' => array(
                    'table' => 'sys_dmail_category',
                    'indexField' => 'uid',
                ),
                'size' => 5,
                'minitems' => 0,
                'maxitems' => 60,
                'renderMode' => 'checkbox',
                'MM' => 'sys_dmail_ttaddress_category_mm',
            )
        ),
        'module_sys_dmail_html' => array(
            'label' => 'LLL:EXT:' . AGENCY_TT_ADDRESS_EXT . '/locallang_db.xml:tt_address.module_sys_dmail_html',
            'exclude' => '1',
            'config' => array(
                'type' => 'check'
            )
        )
    );

    $temporaryColumns = array_merge($temporaryColumns, $directMailTemporaryColumns);
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns($table, $temporaryColumns);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    $table,
    'date_of_birth,comments'
);

if ( // Direct Mail tables exist but Direct Mail shall not be used
    $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][AGENCY_TT_ADDRESS_EXT]['enableDirectMail'] &&
    !\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('direct_mail')
) {
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
        $table,
        '--div--;Direct mail, module_sys_dmail_category, module_sys_dmail_html'
    );
}

$searchFields = explode(',', $GLOBALS['TCA'][$table]['ctrl']['searchFields'] . ',date_of_birth,comments');
$searchFields = array_unique($searchFields);
$GLOBALS['TCA'][$table]['ctrl']['searchFields'] = implode(',', $searchFields);

$GLOBALS['TCA'][$table]['palettes']['3']['showitem'] =
    preg_replace('/(^|,)\s*country\s*(,|$)/', '$1zone,static_info_country,country,language$2', $GLOBALS['TCA'][$table]['palettes']['3']['showitem']);

