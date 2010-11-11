<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TCA["tx_a21glossary_main"] = Array (
	"ctrl" => $TCA["tx_a21glossary_main"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "sys_language_uid,l18n_parent,l18n_diffsource,hidden,starttime,endtime,fe_group,short,shortcut,longversion,shorttype,language,description,link,exclude"
	),
	"feInterface" => $TCA["tx_a21glossary_main"]["feInterface"],
	"columns" => Array (
		'sys_language_uid' => Array (		
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.language',
			'config' => Array (
				'type' => 'select',
				'foreign_table' => 'sys_language',
				'foreign_table_where' => 'ORDER BY sys_language.title',
				'items' => Array(
					Array('LLL:EXT:lang/locallang_general.php:LGL.allLanguages',-1),
					Array('LLL:EXT:lang/locallang_general.php:LGL.default_value',0)
				)
			)
		),
		'l18n_parent' => Array (		
			'displayCond' => 'FIELD:sys_language_uid:>:0',
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.php:LGL.l18n_parent',
			'config' => Array (
				'type' => 'select',
				'items' => Array (
					Array('', 0),
				),
				'foreign_table' => 'tx_a21glossary_main',
				'foreign_table_where' => 'AND tx_a21glossary_main.pid=###CURRENT_PID### AND tx_a21glossary_main.sys_language_uid IN (-1,0)',
			)
		),
		'l18n_diffsource' => Array (		
			'config' => Array (
				'type' => 'passthrough'
			)
		),
		"hidden" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"starttime" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.starttime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"default" => "0",
				"checkbox" => "0"
			)
		),
		"endtime" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.endtime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0",
				"range" => Array (
					"upper" => mktime(0,0,0,12,31,2020),
					"lower" => mktime(0,0,0,date("m")-1,date("d"),date("Y"))
				)
			)
		),
		"fe_group" => Array (		
			"exclude" => 1,
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.fe_group",
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("", 0),
					Array("LLL:EXT:lang/locallang_general.php:LGL.hide_at_login", -1),
					Array("LLL:EXT:lang/locallang_general.php:LGL.any_login", -2),
					Array("LLL:EXT:lang/locallang_general.php:LGL.usergroups", "--div--")
				),
				"foreign_table" => "fe_groups"
			)
		),
		"short" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.short",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"eval" => "required",
			)
		),
		"shortcut" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.shortcut",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",
			)
		),
		"longversion" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.longversion",		
			"config" => Array (
				"type" => "input",	
				"size" => "48",
			)
		),
		"shorttype" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.shorttype",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.shorttype.I.0", "span"),
					Array("LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.shorttype.I.1", "dfn"),
					Array("LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.shorttype.I.2", "acronym"),
					Array("LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.shorttype.I.3", "abbr"),
				),
				"size" => 1,	
				"maxitems" => 1,
			)
		),
		"language" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.language",		
			"config" => Array (
				"type" => "select",
				"items" => Array (
					Array("LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.language.I.0", ""),
					Array("LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.language.I.1", "en"),
					Array("LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.language.I.2", "fr"),
					Array("LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.language.I.3", "de"),
					Array("LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.language.I.4", "it"),
					Array("LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.language.I.5", "es"),
				),
				"size" => 1,	
				"maxitems" => 1,
			)
		),
		"description" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.description",		
			"config" => Array (
				"type" => "text",
				"cols" => "48",
				"rows" => "5",
				"wizards" => Array(
					"_PADDING" => 2,
					"RTE" => Array(
						"notNewRecords" => 1,
						"RTEonly" => 1,
						"type" => "script",
						"title" => "Full screen Rich Text Editing|Formatteret redigering i hele vinduet",
						"icon" => "wizard_rte2.gif",
						"script" => "wizard_rte.php",
					),
				),
			)
		),
		"link" => Array (		
			"exclude" => 0,		
			"label" => "LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.link",		
			"config" => Array (
				"type" => "input",		
				"size" => "48",
				"max" => "255",
				"checkbox" => "",
				"eval" => "trim",
				"wizards" => Array(
					"_PADDING" => 2,
					"link" => Array(
						"type" => "popup",
						"title" => "Link",
						"icon" => "link_popup.gif",
						"script" => "browse_links.php?mode=wizard",
						"JSopenParams" => "height=300,width=500,status=0,menubar=0,scrollbars=1"
					)
				)
			)
		),
        "exclude" => Array (        
            "exclude" => 1,        
            "label" => "LLL:EXT:a21glossary/locallang_db.php:tx_a21glossary_main.exclude",        
            "config" => Array (
                "type" => "check",
            )
        ),
	),
	"types" => Array (
        "0" => Array("showitem" => "sys_language_uid;;;;1-1-1, l18n_parent, l18n_diffsource, hidden;;1, short, shortcut, longversion, shorttype, language, description;;;richtext[cut|copy|paste|formatblock|textcolor|bold|italic|underline|left|center|right|orderedlist|unorderedlist|outdent|indent|link|table|image|line|chMode]:rte_transform[mode=ts_css|imgpath=uploads/tx_a21glossary/rte/], link, exclude")	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime, endtime, fe_group"),
		"2" => Array("showitem" => "shortcut"),
	)
);
?>