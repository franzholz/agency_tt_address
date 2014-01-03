<?php
/*
 * Register necessary class names with autoloader
 *
 * $Id$
 */

$key = 'agency_tt_address';
$extensionPath = t3lib_extMgm::extPath($key, $script);

return array(
	'tx_agencyttaddress' => $extensionPath . 'class.tx_agencyttaddress.php',
	'tx_agencyttaddress_pi_base' => $extensionPath . 'pi/class.tx_agencyttaddress_pi_base.php',
);
?>