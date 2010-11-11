<?php

########################################################################
# Extension Manager/Repository config file for ext "static_info_tables".
#
# Auto generated 22-09-2010 19:02
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Static Info Tables',
	'description' => 'API and data for countries, languages and currencies.',
	'category' => 'misc',
	'shy' => 0,
	'version' => '2.1.2',
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
	'author' => 'RenÃÂ© Fritz',
	'author_email' => 'r.fritz@colorcube.de',
	'author_company' => 'Colorcube - digital media lab, www.colorcube.de',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'typo3' => '4.0-0.0.0',
			'php' => '5.1.0-0.0.0',
		),
		'conflicts' => array(
			'sr_static_info' => '0.0.1-',
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:22:{s:9:"ChangeLog";s:4:"bca9";s:20:"class.ext_update.php";s:4:"09b5";s:33:"class.tx_staticinfotables_div.php";s:4:"56bc";s:38:"class.tx_staticinfotables_encoding.php";s:4:"03fd";s:41:"class.tx_staticinfotables_syslanguage.php";s:4:"4f57";s:21:"ext_conf_template.txt";s:4:"ec5b";s:12:"ext_icon.gif";s:4:"639f";s:17:"ext_localconf.php";s:4:"eb98";s:14:"ext_tables.php";s:4:"e112";s:14:"ext_tables.sql";s:4:"b3b8";s:25:"ext_tables_static+adt.sql";s:4:"8240";s:25:"icon_static_countries.gif";s:4:"2a46";s:26:"icon_static_currencies.gif";s:4:"a1e2";s:25:"icon_static_languages.gif";s:4:"639f";s:27:"icon_static_territories.gif";s:4:"aab5";s:13:"locallang.xml";s:4:"f121";s:16:"locallang_db.xml";s:4:"158c";s:7:"tca.php";s:4:"1496";s:14:"doc/manual.sxw";s:4:"92e3";s:37:"pi1/class.tx_staticinfotables_pi1.php";s:4:"0f4a";s:39:"static/static_info_tables/constants.txt";s:4:"aaf6";s:35:"static/static_info_tables/setup.txt";s:4:"82b7";}',
);

?>