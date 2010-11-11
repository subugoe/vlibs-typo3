<?php

########################################################################
# Extension Manager/Repository config file for ext "metaext".
#
# Auto generated 27-09-2010 15:01
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Config, Metatags & SEO Features',
	'description' => 'This extension provides you with an editable set of all the important \'config\' parameters including multilanguage settings, adds additional metatag fields to the page module and provides global settings for some of them (those who make sense to be filled globaly). It also comes with a configurable html post processor which is able to remove html comments (if not inside script/style tag) & redundant whitespace, making a nice indentation and even reorder the tags within the html header (eg. pushing title & metatags on top).',
	'category' => 'be',
	'shy' => 0,
	'version' => '0.5.1',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => 'pages,pages_language_overlay',
	'clearcacheonload' => 1,
	'lockType' => '',
	'author' => '\'Iggy\' - sensomedia.de',
	'author_email' => 'info@sensomedia.de',
	'author_company' => 'sensomedia.de',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.2.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:19:{s:9:"ChangeLog";s:4:"2fbc";s:10:"README.txt";s:4:"ee2d";s:21:"ext_conf_template.txt";s:4:"0b04";s:12:"ext_icon.gif";s:4:"5a59";s:17:"ext_localconf.php";s:4:"240d";s:14:"ext_tables.php";s:4:"09b2";s:14:"ext_tables.sql";s:4:"6c42";s:17:"locallang_csh.xml";s:4:"79aa";s:16:"locallang_db.xml";s:4:"1e91";s:14:"doc/manual.sxw";s:4:"a5e2";s:36:"lib/class.tx_metaext_postprocess.php";s:4:"b3de";s:32:"lib/class.tx_metaext_sitemap.php";s:4:"c60c";s:35:"lib/class.ux_t3lib_tsparser_ext.php";s:4:"8a6f";s:27:"lib/class.ux_tslib_menu.php";s:4:"e650";s:38:"modfunc1/class.tx_metaext_modfunc1.php";s:4:"984e";s:22:"modfunc1/locallang.xml";s:4:"5e1e";s:20:"static/constants.txt";s:4:"578e";s:13:"static/lib.ts";s:4:"8e2e";s:16:"static/setup.txt";s:4:"9928";}',
);

?>