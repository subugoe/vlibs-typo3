<?php

########################################################################
# Extension Manager/Repository config file for ext "sr_language_detect".
#
# Auto generated 27-09-2010 17:54
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Language Detection - Extended',
	'description' => 'Detects the client preferred language, including the country subtag. Matches pages in Brazilian (pt-br) with TYPO3 language code br.',
	'category' => 'misc',
	'shy' => 0,
	'version' => '1.1.0',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Stanislas Rolland',
	'author_email' => 'stanislas.rolland@fructifor.ca',
	'author_company' => 'Fructifor Inc.',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'rlmp_language_detection' => '1.2.1',
			'static_info_tables' => '2.0.0-',
			'php' => '4.1.0-0.0.0',
			'typo3' => '4.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:3:{s:12:"ext_icon.gif";s:4:"ba72";s:17:"ext_localconf.php";s:4:"a802";s:45:"pi1/class.ux_tx_rlmplanguagedetection_pi1.php";s:4:"b77c";}',
);

?>