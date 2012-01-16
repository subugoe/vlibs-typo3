<?php

########################################################################
# Extension Manager/Repository config file for ext "a21glossary".
#
# Auto generated 11-01-2012 14:51
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'A21 Glossary',
	'description' => 'A21 Glossary - automatical conversion of all abbreviations and acronyms in the special tags for accessibility issues',
	'category' => 'fe',
	'shy' => 0,
	'version' => '0.9.4',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => 'uploads/tx_a21glossary/rte/',
	'modify_tables' => '',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => 'Ronny Vorpahl',
	'author_email' => 'info@artplan21.de',
	'author_company' => 'Artplan21 GmbH. - www.artplan21.de',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.1.0-0.0.0',
			'typo3' => '4.1.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:17:{s:9:"ChangeLog";s:4:"def7";s:10:"README.txt";s:4:"9b32";s:24:"class.tx_a21glossary.php";s:4:"8828";s:21:"ext_conf_template.txt";s:4:"76ef";s:12:"ext_icon.gif";s:4:"dff2";s:17:"ext_localconf.php";s:4:"b780";s:14:"ext_tables.php";s:4:"382e";s:14:"ext_tables.sql";s:4:"f3ae";s:28:"icon_tx_a21glossary_main.gif";s:4:"a4ed";s:13:"locallang.xml";s:4:"efca";s:16:"locallang_db.xml";s:4:"4280";s:21:"Configuration/Tca.php";s:4:"36e1";s:52:"Configuration/TypoScript/Accessibility/constants.txt";s:4:"8dbf";s:48:"Configuration/TypoScript/Accessibility/setup.txt";s:4:"b954";s:14:"doc/manual.sxw";s:4:"f0a4";s:20:"static/constants.txt";s:4:"26fe";s:16:"static/setup.txt";s:4:"ee27";}',
);

?>