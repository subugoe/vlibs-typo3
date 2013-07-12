<?php

/***************************************************************
 * Extension Manager/Repository config file for ext "devlog".
 *
 * Auto generated 09-06-2013 13:56
 *
 * Manual updates:
 * Only the data in the array - everything else is removed by next
 * writing. "version" and "dependencies" must not be touched!
 ***************************************************************/

$EM_CONF[$_EXTKEY] = array (
	'title' => 'Developer Log',
	'description' => 'The Developer log extension provides development logging/debugging functionality for usage of t3lib_div::devlog() and a BE module to browse the logs.',
	'category' => 'misc',
	'shy' => 0,
	'version' => '2.10.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => 'mod1',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Rene Fritz, Francois Suter',
	'author_email' => 'typo3@cobweb.ch',
	'author_company' => '',
	'CGLcompliance' => NULL,
	'CGLcompliance_note' => NULL,
	'constraints' => 
	array (
		'depends' => 
		array (
			'typo3' => '4.5.0-6.1.99',
			'cms' => '',
		),
		'conflicts' => '',
		'suggests' => 
		array (
		),
	),
);

?>