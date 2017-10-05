<?php

namespace JambageCom\AgencyTtAddress\Configuration;

/***************************************************************
*  Copyright notice
*
*  (c) 2017 Stanislas Rolland (typo3(arobas)sjbr.ca)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
*
* Part of the agency_tt_address (Email Newsletter Registration) extension.
*
* Check the configuration and extension requirements
*
* @author	Stanislas Rolland <typo3(arobas)sjbr.ca>
* @author	Franz Holzinger <franz@ttproducts.de>
* @maintainer	Franz Holzinger <franz@ttproducts.de>
*
*
*/

use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;
use TYPO3\CMS\Core\Utility\GeneralUtility;


class ConfigurationCheck {

    /* Checks requirements for this plugin
    *
    * @return string Error message, if error found, empty string otherwise
    */
    static public function checkRequirements ($conf, $extensionKey) {
        $content = '';
        $requiredExtensions = array();

            // Check if all required extensions are available
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['constraints']['depends'])) {
            $requiredExtensions =
                array_diff(
                    array_keys($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['constraints']['depends']),
                    array('php', 'typo3')
                );

        }

        foreach ($requiredExtensions as $extension) {
            if (!ExtensionManagementUtility::isLoaded($extension)) {
                $message = sprintf($GLOBALS['TSFE']->sL('LLL:EXT:' . $extensionKey . '/pi/locallang.xml:internal_required_extension_missing'), $extension);
                GeneralUtility::sysLog($message, $extensionKey, GeneralUtility::SYSLOG_SEVERITY_ERROR);
                $content .= sprintf($GLOBALS['TSFE']->sL('LLL:EXT:' . $extensionKey . '/pi/locallang.xml:internal_check_requirements_frontend'), $message);
            }
        }

            // Check if any conflicting extension is available
        if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['constraints']['conflicts'])) {
            $conflictingExtensions =
                array_keys($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['constraints']['conflicts']);
        }

        if (
            isset($conflictingExtensions) &&
            is_array($conflictingExtensions)
        ) {
            foreach ($conflictingExtensions as $extension) {
                if (ExtensionManagementUtility::isLoaded($extension)) {
                    $message = sprintf($GLOBALS['TSFE']->sL('LLL:EXT:' . AGENCY_EXT . '/pi/locallang.xml:internal_conflicting_extension_installed'), $extension);
                    GeneralUtility::sysLog($message, $extensionKey, GeneralUtility::SYSLOG_SEVERITY_ERROR);
                    $content .= sprintf($GLOBALS['TSFE']->sL('LLL:EXT:' . $extensionKey . '/pi/locallang.xml:internal_check_requirements_frontend'), $message);
                }
            }
        }

        return $content;
    }
}

