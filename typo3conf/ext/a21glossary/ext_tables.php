<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

t3lib_extMgm::addStaticFile($_EXTKEY,'static/','Accessibility Glossary');

t3lib_extMgm::addPlugin(Array('LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main', 'a21glossary'));
t3lib_extMgm::allowTableOnStandardPages('tx_a21glossary_main');
t3lib_extMgm::addToInsertRecords('tx_a21glossary_main');

$TCA["tx_a21glossary_main"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main",	
		"label" => "short",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"versioning" => "1",	
		"languageField" => "sys_language_uid",	
		"transOrigPointerField" => "l18n_parent",	
		"transOrigDiffSourceField" => "l18n_diffsource",	
		"default_sortby" => "ORDER BY short",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",	
			"starttime" => "starttime",	
			"endtime" => "endtime",	
			"fe_group" => "fe_group",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_a21glossary_main.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "sys_language_uid, l18n_parent, l18n_diffsource, hidden, starttime, endtime, fe_group, short, shortcut, longversion, shorttype, language, description, link, exclude",
	)
);

?>