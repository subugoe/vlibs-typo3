<?php

if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_libconnect_subject"] = array (
	"ctrl" => $TCA["tx_libconnect_subject"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "hidden,title,dbis_id,ezb_notation"
	),
	"feInterface" => $TCA["tx_libconnect_subject"]["feInterface"],
	"columns" => array (
		'hidden' => array (		
			'exclude' => 1,
			'label'   => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config'  => array (
				'type'    => 'check',
				'default' => '0'
			)
		),
		"title" => Array (		
			"exclude" => 0,		
			"label" => "Name des Fachgebiets",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"dbis_id" => Array (		
			"exclude" => 0,		
			"label" => "Notation im DBIS-System",		
			"config" => Array (
				"type" => "input",	
				"size" => "5",	
			)
		),
		"ezb_notation" => Array (		
			"exclude" => 0,		
			"label" => "Notation im EZB-System",		
			"config" => Array (
				"type" => "input",	
				"size" => "5",	
			)
		),

	),
	"types" => array (
		"0" => array("showitem" => "hidden;;1;;1-1-1, title, dbis_id, ezb_notation")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);