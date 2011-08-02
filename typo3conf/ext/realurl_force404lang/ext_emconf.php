<?php

########################################################################
# Extension Manager/Repository config file for ext "realurl_force404lang".
#
# Auto generated 02-08-2011 11:20
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Force 404 language',
	'description' => 'Sets GET-parameter for language id even when realurl throws 404 error. Calls TYPO3 $TYPO3_CONF_VARS[\'FE\'][\'pageNotFound_handling\'] afterwards.',
	'category' => 'fe',
	'shy' => 0,
	'version' => '0.2.0',
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
	'author' => 'Vladimir Falcon Piva',
	'author_email' => 'falcon@cps-it.de',
	'author_company' => 'CPS-IT GmbH (http://www.cps-it.de)',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'realurl' => '0.2.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:7:{s:9:"ChangeLog";s:4:"463c";s:32:"class.tx_realurlforce404lang.php";s:4:"e046";s:21:"ext_conf_template.txt";s:4:"e9c8";s:12:"ext_icon.gif";s:4:"c142";s:17:"ext_localconf.php";s:4:"8367";s:13:"locallang.xml";s:4:"b9d8";s:14:"doc/manual.sxw";s:4:"a127";}',
);

?>