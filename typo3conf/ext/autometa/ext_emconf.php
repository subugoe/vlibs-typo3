<?php

########################################################################
# Extension Manager/Repository config file for ext "autometa".
#
# Auto generated 30-11-2010 10:03
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Auto Meta',
	'description' => 'Automaticly generates meta keywords and description out of fully rendered page text (respects TYPO3SEARCH comments).',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.1.1',
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
	'author' => 'Sigfried Arnold',
	'author_email' => 's.arnold@rebell.at',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'conflicts' => array(
			'mc_autokeywords' => '',
			'pmkautokeywords' => '',
			'autokeywords' => '',
			'autokeywordz' => '',
			'metatags' => '',
		),
		'depends' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:11:{s:9:"ChangeLog";s:4:"4e5a";s:28:"class.tx_autometa_fehook.php";s:4:"78f6";s:21:"ext_conf_template.txt";s:4:"d1c2";s:12:"ext_icon.gif";s:4:"3bb3";s:17:"ext_localconf.php";s:4:"278e";s:28:"ext_typoscript_constants.txt";s:4:"836b";s:24:"ext_typoscript_setup.txt";s:4:"08dd";s:29:"pi1/class.tx_autometa_pi1.php";s:4:"1004";s:13:"res/stopwords";s:4:"4776";s:16:"res/stopwords.de";s:4:"bbfc";s:16:"res/stopwords.en";s:4:"a5d8";}',
);

?>