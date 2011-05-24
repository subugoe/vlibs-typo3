<?php

if (!defined ('TYPO3_MODE')) {	die ('Access denied.'); }

t3lib_extMgm::addStaticFile($_EXTKEY, '/configurations/TS', 'libconnect');

$TCA["tx_libconnect_subject"] = array(
    "ctrl" => array(
        'title' => 'Fachgebiet',
        'label' => 'title',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'default_sortby' => "ORDER BY title",
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
        ),
        'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY) . 'tca.php',
        'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY) . 'icons/icon_subject.png',
    ),
    "feInterface" => array(
        "fe_admin_fieldList" => "hidden, title, dbis_id, ezb_notation",
    )
);



t3lib_div::loadTCA('tt_content');

$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_dbis'] = 'layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_dbis'] = 'pi_flexform';
t3lib_extMgm::addPlugin(array('Plugin DBIS', 'libconnect_dbis'));
t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_dbis', 'FILE:EXT:libconnect/configurations/dbis/flexform.xml');

$TCA['tt_content']['types']['list']['subtypes_excludelist'][$_EXTKEY . '_ezb'] = 'layout,select_key,pages,recursive';
$TCA['tt_content']['types']['list']['subtypes_addlist'][$_EXTKEY . '_ezb'] = 'pi_flexform';
t3lib_extMgm::addPlugin(array('Plugin EZB', 'libconnect_ezb'));
t3lib_extMgm::addPiFlexFormValue($_EXTKEY . '_ezb', 'FILE:EXT:libconnect/configurations/ezb/flexform.xml');

if (TYPO3_MODE == "BE") {
    $TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_libconnect_dbis_wizicon"] = t3lib_extMgm::extPath($_EXTKEY) . 'configurations/dbis/class.tx_libconnect_dbis_wizicon.php';
    $TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_libconnect_ezb_wizicon"] = t3lib_extMgm::extPath($_EXTKEY) . 'configurations/ezb/class.tx_libconnect_ezb_wizicon.php';
}
?>