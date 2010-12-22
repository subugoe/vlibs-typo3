<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA["tx_kestats_statdata"] = array (
	"ctrl" => $TCA["tx_kestats_statdata"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => "type,category,element_uid,element_pid,element_title,element_language,counter,year,month"
	),
	"feInterface" => $TCA["tx_kestats_statdata"]["feInterface"],
	"columns" => array (
		"type" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:ke_stats/locallang_db.xml:tx_kestats_statdata.type",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"category" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:ke_stats/locallang_db.xml:tx_kestats_statdata.category",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"element_uid" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:ke_stats/locallang_db.xml:tx_kestats_statdata.element_uid",		
			"config" => Array (
				"type"     => "input",
				"size"     => "4",
				"max"      => "4",
				"eval"     => "int",
				"checkbox" => "0",
				"range"    => Array (
					"lower" => "-1"
				),
				"default" => -1
			)
		),
		"element_pid" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:ke_stats/locallang_db.xml:tx_kestats_statdata.element_pid",		
			"config" => Array (
				"type"     => "input",
				"size"     => "4",
				"max"      => "4",
				"eval"     => "int",
				"checkbox" => "0",
				"range"    => Array (
					"lower" => "0"
				),
				"default" => 0 
			)
		),
		"element_title" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:ke_stats/locallang_db.xml:tx_kestats_statdata.element_title",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"element_language" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:ke_stats/locallang_db.xml:tx_kestats_statdata.element_language",		
			"config" => Array (
				"type"     => "input",
				"size"     => "4",
				"max"      => "4",
				"eval"     => "int",
				"checkbox" => "0",
				"range"    => Array (
					"lower" => "-1"
				),
				"default" => -1
			)
		),
		"counter" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:ke_stats/locallang_db.xml:tx_kestats_statdata.counter",		
			"config" => Array (
				"type"     => "input",
				"size"     => "4",
				"max"      => "4",
				"eval"     => "int",
				"checkbox" => "0",
				"range"    => Array (
					"lower" => "0"
				),
				"default" => 0
			)
		),
		"year" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:ke_stats/locallang_db.xml:tx_kestats_statdata.year",		
			"config" => Array (
				"type"     => "input",
				"size"     => "4",
				"max"      => "4",
				"eval"     => "int",
				"checkbox" => "0",
				"range"    => Array (
					"lower" => "-1"
				),
				"default" => -1
			)
		),
		"month" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:ke_stats/locallang_db.xml:tx_kestats_statdata.month",		
			"config" => Array (
				"type"     => "input",
				"size"     => "4",
				"max"      => "4",
				"eval"     => "int",
				"checkbox" => "0",
				"range"    => Array (
					"lower" => "-1"
				),
				"default" => -1
			)
		),
		"parent_uid" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:ke_stats/locallang_db.xml:tx_kestats_statdata.parent_uid",        
            "config" => Array (
                "type" => "select",    
                "items" => Array (
                    Array("",0),
                ),
                "foreign_table" => "tx_kestats_statdata",    
                "foreign_table_where" => "AND tx_kestats_statdata.pid=###CURRENT_PID### ORDER BY tx_kestats_statdata.uid",    
                "size" => 1,    
                "minitems" => 0,
                "maxitems" => 1,
            )
        ),
	),
	"types" => array (
		"0" => array("showitem" => "type;;;;1-1-1, category, element_uid, element_pid, element_title, element_language, counter, year, month, day, day_of_week, hour")
	),
	"palettes" => array (
		"1" => array("showitem" => "")
	)
);

/*
$TCA["tx_kestats_cache"] = array (
	"ctrl" => $TCA["tx_kestats_cache"]["ctrl"],
	"interface" => array (
		"showRecordFieldList" => ""
	),
	"columns" => array (
		"whereclause" => Array (		
			"exclude" => 0,		
			"label" => "whereclause",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"groupby" => Array (		
			"exclude" => 0,		
			"label" => "orderby",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"orderby" => Array (		
			"exclude" => 0,		
			"label" => "orderby",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"result" => Array (		
			"exclude" => 0,		
			"label" => "result",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
	),
	"types" => array (
		"0" => array("showitem" => "whereclause,orderby,groupby,result")
	),
);
*/
?>
