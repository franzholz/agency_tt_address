<?php
if (!defined ('TYPO3_MODE')) {
    die ('Access denied.');
}

$result = false;

$emClass = '\\TYPO3\\CMS\\Core\\Utility\\ExtensionManagementUtility';

if ( // Direct Mail tables exist but Direct Mail shall not be used
    !$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][AGENCY_TT_ADDRESS_EXT]['enableDirectMail'] ||
    call_user_func($emClass . '::isLoaded', 'direct_mail')
) {
    return $result;
}


$table = 'sys_dmail_ttaddress_category_mm';

$queryResult =
    $GLOBALS['TYPO3_DB']->admin_query(
        'SELECT * FROM INFORMATION_SCHEMA.TABLES ' .
        'WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME=\'' . $table . '\''
    );
$tableExists = $GLOBALS['TYPO3_DB']->sql_num_rows($queryResult) > 0;
if (!$tableExists) {
    return $result;
}


$result = array (
    'ctrl' => array (
        'title' => 'LLL:EXT:' . AGENCY_TT_ADDRESS_EXT . '/locallang_db.xml:' . $table ,
        'label' => 'uid_local',
        'tstamp' => 'tstamp',
        'delete' => 'deleted',
        'enablecolumns' => array (
            'disabled' => 'hidden'
        ),
        'prependAtCopy' => DIV2007_LANGUAGE_LGL . 'prependAtCopy',
        'crdate' => 'crdate',
        'iconfile' => PATH_AGENCYTTADDRESS_ICON_TABLE_REL . 'icon_tx_directmail_category.gif',
        'hideTable' => TRUE,
    ),
    'interface' => array (
        'showRecordFieldList' => 'uid_local,uid_foreign'
    ),
    'columns' => array (
        'uid_local' => array (
            'label' => 'LLL:EXT:' . AGENCY_TT_ADDRESS_EXT . '/locallang_db.xml:' . $table . '.uid_local',
            'config' => array (
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'tt_address',
                'maxitems' => 1
            )
        ),
        'uid_foreign' => array (
            'label' => 'LLL:EXT:' . AGENCY_TT_ADDRESS_EXT . '/locallang_db.xml:' . $table . '.uid_foreign',
            'config' => array (
                'type' => 'select',
                'renderType' => 'selectSingle',
                'foreign_table' => 'sys_dmail_category',
                'maxitems' => 1
            )
        ),
        'sorting' => array (
            'config' => array (
                'type' => 'passthrough',
            )
        ),
        'articlesort' => array (
            'config' => array (
                'type' => 'passthrough',
            )
        ),
    ),
    'types' => array(
        '0' => array(
            'showitem' => ''
        )
    )
);


return $result;
