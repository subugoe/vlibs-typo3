<?php

########################################################################
# Extension Manager/Repository config file for ext "realurl_force404lang".
#
# Auto generated 30-06-2011 18:07
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
	'version' => '0.1.2',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'beta',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 1,
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
	'_md5_values_when_last_written' => 'a:6:{s:9:"ChangeLog";s:4:"4780";s:32:"class.tx_realurlforce404lang.php";s:4:"e046";s:21:"ext_conf_template.txt";s:4:"e9c8";s:12:"ext_icon.gif";s:4:"c142";s:17:"ext_localconf.php";s:4:"0d43";s:13:"locallang.xml";s:4:"b9d8";}',
);

?>