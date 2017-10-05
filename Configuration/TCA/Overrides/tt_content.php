<?php

if (!defined ('TYPO3_MODE')) {
    die ('Access denied.');
}

$table = 'tt_content';


$GLOBALS['TCA'][$table]['types']['list']['subtypes_excludelist'][AGENCY_TT_ADDRESS_EXT] = 'layout,select_key';
$GLOBALS['TCA'][$table]['types']['list']['subtypes_addlist'][AGENCY_TT_ADDRESS_EXT] = 'pi_flexform';

