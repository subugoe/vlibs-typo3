<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi1']='layout,select_key';


t3lib_extMgm::addPlugin(array('LLL:EXT:its_langmenu/locallang_db.xml:tt_content.list_type_pi1', $_EXTKEY.'_pi1'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","Langmenu");


t3lib_div::loadTCA('tt_content');
$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY.'_pi2']='layout,select_key';


t3lib_extMgm::addPlugin(array('LLL:EXT:its_langmenu/locallang_db.xml:tt_content.list_type_pi2', $_EXTKEY.'_pi2'),'list_type');


t3lib_extMgm::addStaticFile($_EXTKEY,"pi2/static/","Langmenu nc");

$tempColumns = array (
	'tx_itslangmenu_disable_in_menu' => array (
		'exclude' => 0,
		'label' => 'LLL:EXT:its_langmenu/locallang_db.xml:sys_language.tx_itslangmenu_disable_in_menu',
		'config' => array (
			'type' => 'check',
		)
	),
);


t3lib_div::loadTCA('sys_language');
t3lib_extMgm::addTCAcolumns('sys_language',$tempColumns,1);
t3lib_extMgm::addToAllTCAtypes('sys_language','tx_itslangmenu_disable_in_menu;;;;1-1-1');
?>