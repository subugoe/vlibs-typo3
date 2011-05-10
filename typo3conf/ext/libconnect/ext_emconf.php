<?php

########################################################################
# Extension Manager/Repository config file for ext "libconnect".
#
# Auto generated 10-05-2011 09:56
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Extension zur Anbindung von EZB und DBIS',
	'description' => 'Mit dieser Extension lassen sich Ergebnisse aus den Informationssystemen EZB und DBIS der UniversitÃ¤t Regensburg direkt in das TYPO3-System einbinden.',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '2.0.0',
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
	'author' => 'Avonis - Agentur fÃ¼r neue Medien',
	'author_email' => 'agency@avonis.com',
	'author_company' => '',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '',
			'lib' => '',
			'div' => '',
			'smarty' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:49:{s:12:"ext_icon.gif";s:4:"b57c";s:17:"ext_localconf.php";s:4:"101f";s:14:"ext_tables.php";s:4:"4a31";s:14:"ext_tables.sql";s:4:"acad";s:28:"ext_typoscript_constants.txt";s:4:"8d93";s:24:"ext_typoscript_setup.txt";s:4:"305d";s:13:"locallang.xml";s:4:"41dd";s:16:"locallang_db.xml";s:4:"539b";s:7:"tca.php";s:4:"11a3";s:56:"configurations/dbis/class.tx_libconnect_dbis_wizicon.php";s:4:"089a";s:32:"configurations/dbis/flexform.xml";s:4:"5397";s:54:"configurations/ezb/class.tx_libconnect_ezb_wizicon.php";s:4:"2636";s:31:"configurations/ezb/flexform.xml";s:4:"1af9";s:52:"controllers/class.tx_libconnect_controllers_dbis.php";s:4:"85f1";s:51:"controllers/class.tx_libconnect_controllers_ezb.php";s:4:"9069";s:54:"controllers/class.tx_libconnect_controllers_search.php";s:4:"c0a4";s:22:"icons/icon_subject.png";s:4:"0ba8";s:18:"icons/wiz_icon.gif";s:4:"38d5";s:35:"lib/ezb_dbis/classes/class_DBIS.php";s:4:"9311";s:34:"lib/ezb_dbis/classes/class_EZB.php";s:4:"0678";s:42:"models/class.tx_libconnect_models_dbis.php";s:4:"8c27";s:41:"models/class.tx_libconnect_models_ezb.php";s:4:"45b3";s:25:"templates/dbis_detail.tpl";s:4:"6114";s:23:"templates/dbis_form.tpl";s:4:"0430";s:23:"templates/dbis_list.tpl";s:4:"4fde";s:27:"templates/dbis_miniform.tpl";s:4:"8225";s:27:"templates/dbis_overview.tpl";s:4:"0f25";s:25:"templates/dbis_search.tpl";s:4:"3775";s:26:"templates/dbis_toplist.tpl";s:4:"7b25";s:24:"templates/ezb_detail.tpl";s:4:"0e16";s:22:"templates/ezb_form.tpl";s:4:"31fb";s:22:"templates/ezb_list.tpl";s:4:"af13";s:26:"templates/ezb_miniform.tpl";s:4:"2319";s:26:"templates/ezb_overview.tpl";s:4:"d9cb";s:24:"templates/ezb_search.tpl";s:4:"4baf";s:29:"templates/img/dbis-list_1.png";s:4:"4ca5";s:29:"templates/img/dbis-list_2.png";s:4:"fffe";s:29:"templates/img/dbis-list_3.png";s:4:"efff";s:29:"templates/img/dbis-list_4.png";s:4:"2000";s:29:"templates/img/dbis-list_5.png";s:4:"1a6a";s:29:"templates/img/dbis-list_6.png";s:4:"5b3e";s:29:"templates/img/dbis-list_7.png";s:4:"8ff8";s:35:"templates/img/dbis-list_germany.png";s:4:"5f7e";s:28:"templates/img/ezb-list_1.png";s:4:"4ca5";s:28:"templates/img/ezb-list_2.png";s:4:"fffe";s:28:"templates/img/ezb-list_4.png";s:4:"5b3e";s:28:"templates/img/ezb-list_6.png";s:4:"9401";s:31:"templates/img/ezb-list_euro.png";s:4:"76f7";s:42:"views/class.tx_libconnect_views_smarty.php";s:4:"1c7b";}',
);

?>