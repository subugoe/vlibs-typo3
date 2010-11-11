<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
$tempColumns = Array (
	"tx_mcautokeywords_keyword_change" => Array (		
		"exclude" => 1,		
		"label" => "LLL:EXT:mc_autokeywords/locallang_db.php:pages.tx_mcautokeywords_keyword_change",		
		"config" => Array (
			"type" => "check",
			"default" => 0,
		)
	),
);


t3lib_div::loadTCA("pages");
t3lib_extMgm::addTCAcolumns("pages",$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes("pages","tx_mcautokeywords_keyword_change;;;;1-1-1",'2','before:keywords');
?>