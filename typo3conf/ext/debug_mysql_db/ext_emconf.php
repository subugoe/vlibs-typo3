<?php

########################################################################
# Extension Manager/Repository config file for ext "debug_mysql_db".
#
# Auto generated 23-11-2010 15:47
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Debug Mysql DB',
	'description' => 'Extends t3lib_db (by xclassing it) to show Errors and Debug-Messages. Usefull for viewing and debugging of sql-queries generated using its methods. Shows error messages if they occur. Simply deinstall to remove all debug-output.',
	'category' => 'misc',
	'shy' => 0,
	'version' => '0.3.6',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Stefan Geith',
	'author_email' => 'typo3dev2010@geithware.de',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:7:{s:9:"ChangeLog";s:4:"643e";s:10:"README.txt";s:4:"8105";s:21:"class.ux_t3lib_db.php";s:4:"c0c4";s:21:"ext_conf_template.txt";s:4:"0b06";s:12:"ext_icon.gif";s:4:"8ea6";s:17:"ext_localconf.php";s:4:"0cde";s:14:"doc/manual.sxw";s:4:"8b0f";}',
);

?>