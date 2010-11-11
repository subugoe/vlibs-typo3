<?php

########################################################################
# Extension Manager/Repository config file for ext "autometa".
#
# Auto generated 27-09-2010 15:12
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Auto Meta',
	'description' => 'Automaticly generates meta keywords and description out of fully rendered page text.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '1.0.1',
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
	'_md5_values_when_last_written' => 'a:11:{s:9:"ChangeLog";s:4:"bfa5";s:28:"class.tx_autometa_fehook.php";s:4:"0ae1";s:21:"ext_conf_template.txt";s:4:"d1c2";s:12:"ext_icon.gif";s:4:"3bb3";s:17:"ext_localconf.php";s:4:"74a1";s:28:"ext_typoscript_constants.txt";s:4:"9430";s:24:"ext_typoscript_setup.txt";s:4:"7070";s:29:"pi1/class.tx_autometa_pi1.php";s:4:"035f";s:13:"res/stopwords";s:4:"4baa";s:16:"res/stopwords.de";s:4:"01db";s:16:"res/stopwords.en";s:4:"a5d8";}',
);

?>