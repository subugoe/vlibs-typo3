<?php

########################################################################
# Extension Manager/Repository config file for ext "mc_autokeywords".
#
# Auto generated 27-09-2010 12:15
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Autogeneration of metakeywords',
	'description' => 'This extension generates metakeywords automatically on every update, delete or creation of a page or a content element',
	'category' => 'be',
	'shy' => 0,
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'pages',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author' => 'Michael Brauchl',
	'author_email' => 'mcyra@chello.at',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'version' => '2.3.0',
	'constraints' => array(
		'depends' => array(
			'typo3' => '3.5.0-0.0.0',
			'php' => '3.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:9:{s:27:"class.tx_mcautokeywords.php";s:4:"9ee8";s:12:"ext_icon.gif";s:4:"8e88";s:17:"ext_localconf.php";s:4:"67c4";s:14:"ext_tables.php";s:4:"de44";s:14:"ext_tables.sql";s:4:"376d";s:16:"locallang_db.php";s:4:"5a99";s:14:"doc/manual.sxw";s:4:"a142";s:19:"doc/wizard_form.dat";s:4:"6497";s:20:"doc/wizard_form.html";s:4:"a374";}',
);

?>