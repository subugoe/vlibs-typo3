<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "static_info_tables".
 *
 * Auto generated 31-07-2013 14:06
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Static Info Tables',
	'description' => 'Data and API for countries, languages and currencies.',
	'category' => 'misc',
	'shy' => 0,
	'version' => '6.0.5',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Stanislas Rolland/Rene Fritz',
	'author_email' => 'typo3@sjbr.ca',
	'author_company' => 'SJBR',
	'CGLcompliance' => NULL,
	'CGLcompliance_note' => NULL,
	'constraints' => 
	array (
		'depends' => 
		array (
			'typo3' => '6.0.6-6.1.99',
			'php' => '5.3.7-0.0.0',
		),
		'conflicts' => 
		array (
			'sr_static_info' => '0.0.0-99.99.99',
			'cc_infotablesmgm' => '0.0.0-99.99.99',
		),
		'suggests' => 
		array (
		),
	),
);

?>