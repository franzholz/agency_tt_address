<?php

########################################################################
# Extension Manager/Repository config file for ext "agency_tt_address".
#
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Agency Registration for tt_address',
	'description' => 'An address and newsletter subscription variant of the Agency Registration.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '0.1.0',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 1,
	'createDirs' => '',
	'modify_tables' => 'tt_address',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Franz Holzinger',
	'author_email' => 'franz@ttproducts.de',
	'author_company' => 'jambage.com',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
            'php' => '5.3.3-7.99.99',
            'typo3' => '4.5.0-8.99.99',
            'agency' => '0.4.0-',
            'div2007' => '1.7.10-0.0.0',
			'tt_address' => '2.2.0-',
		),
		'conflicts' => array(
			'sr_feuser_register' => '',
			'sr_email_subscribe' => '',
		),
		'suggests' => array(
			'sr_freecap' => '',
		),
	),
);

