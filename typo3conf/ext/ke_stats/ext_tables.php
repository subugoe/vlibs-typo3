<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
$TCA["tx_kestats_statdata"] = array (
	"ctrl" => array (
		'title'     => 'LLL:EXT:ke_stats/locallang_db.xml:tx_kestats_statdata',		
		'label'     => 'uid',	
		'tstamp'    => 'tstamp',
		'crdate'    => 'crdate',
		'cruser_id' => 'cruser_id',
		'default_sortby' => "ORDER BY crdate",	
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_kestats_statdata.gif',
	),
	"feInterface" => array (
		"fe_admin_fieldList" => "type, category, element_uid, element_title, element_language, counter, year, month, day, hour",
	)
);

/*
$TCA["tx_kestats_cache"] = array (
	"ctrl" => array (
		'title'     => 'ke_stats cache',		
		'label'     => 'uid',	
		'default_sortby' => "ORDER BY uid",	
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'tca.php',
		'iconfile'          => t3lib_extMgm::extRelPath($_EXTKEY).'icon_tx_kestats_statdata.gif',
	),
);
*/

if (TYPO3_MODE == 'BE')	{
	t3lib_extMgm::addModule('web','txkestatsM1','',t3lib_extMgm::extPath($_EXTKEY).'mod1/');
}
?>
