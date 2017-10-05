<?php

namespace JambageCom\AgencyTtAddress\Configuration;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Backend\Utility\BackendUtility;

/**
 * Static class.
 * Functions in this class are used by more than one modules.
 *
 * @author		Kasper Sk�rh�j <kasper@typo3.com>
 * @author  	Jan-Erik Revsbech <jer@moccompany.com>
 * @author  	Stanislas Rolland <stanislas.rolland(arobas)fructifor.ca>
 * @author		Ivan-Dharma Kartolo	<ivan.kartolo@dkd.de>
 *
 * @package 	TYPO3
 * @subpackage	tx_directmail
 */
class DirectMailUtility
{
    /**
     * Import from t3lib_page in order to create backend version
     * Creates language-overlay for records in general
     * (where translation is found in records from the same table)
     *
     * @param string $table Table name
     * @param array $row Record to overlay. Must contain uid, pid and languageField
     * @param int $sys_language_content Language ID of the content
     * @param string $OLmode Overlay mode. If "hideNonTranslated" then records without translation will not be returned un-translated but unset (and return value is false)
     *
     * @return mixed Returns the input record, possibly overlaid with a translation. But if $OLmode is "hideNonTranslated" then it will return false if no translation is found.
     */
    public static function getRecordOverlay($table, array $row, $sys_language_content, $OLmode = '')
    {
        if ($row['uid']>0 && $row['pid']>0) {
            if ($GLOBALS["TCA"][$table] && $GLOBALS["TCA"][$table]['ctrl']['languageField'] && $GLOBALS["TCA"][$table]['ctrl']['transOrigPointerField']) {
                if (!$GLOBALS["TCA"][$table]['ctrl']['transOrigPointerTable']) {
                    // Will try to overlay a record only
                    // if the sys_language_content value is larger that zero.
                    if ($sys_language_content > 0) {
                        // Must be default language or [All], otherwise no overlaying:
                        if ($row[$GLOBALS["TCA"][$table]['ctrl']['languageField']]<=0) {
                            // Select overlay record:
                            $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                                '*',
                                $table,
                                'pid=' . intval($row['pid']) .
                                    ' AND ' . $GLOBALS["TCA"][$table]['ctrl']['languageField'] . '=' . intval($sys_language_content) .
                                    ' AND ' . $GLOBALS["TCA"][$table]['ctrl']['transOrigPointerField'] . '=' . intval($row['uid']) .
                                    BackendUtility::BEenableFields($table) .
                                    BackendUtility::deleteClause($table),
                                '',
                                '',
                                '1'
                                );
                            $olrow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
                            $GLOBALS["TYPO3_DB"]->sql_free_result($res);

                                // Merge record content by traversing all fields:
                            if (is_array($olrow)) {
                                foreach ($row as $fN => $fV) {
                                    if ($fN!='uid' && $fN!='pid' && isset($olrow[$fN])) {
                                        if ($GLOBALS["TCA"][$table]['l10n_mode'][$fN]!='exclude' && ($GLOBALS["TCA"][$table]['l10n_mode'][$fN]!='mergeIfNotBlank' || strcmp(trim($olrow[$fN]), ''))) {
                                            $row[$fN] = $olrow[$fN];
                                        }
                                    }
                                }
                            } elseif ($OLmode === 'hideNonTranslated' && $row[$GLOBALS["TCA"][$table]['ctrl']['languageField']] == 0) {
                                // Unset, if non-translated records should be hidden.
                                // ONLY done if the source record really is default language and not [All] in which case it is allowed.
                                unset($row);
                            }

                            // Otherwise, check if sys_language_content is different from the value of the record
                            // that means a japanese site might try to display french content.
                        } elseif ($sys_language_content!=$row[$GLOBALS["TCA"][$table]['ctrl']['languageField']]) {
                            unset($row);
                        }
                    } else {
                        // When default language is displayed,
                        // we never want to return a record carrying another language!:
                        if ($row[$GLOBALS["TCA"][$table]['ctrl']['languageField']]>0) {
                            unset($row);
                        }
                    }
                }
            }
        }

        return $row;
    }

}
