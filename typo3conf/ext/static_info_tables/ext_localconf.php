<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (!defined ('STATIC_INFO_TABLES_EXTkey')) {
	define('STATIC_INFO_TABLES_EXTkey', $_EXTKEY);
}

if (!defined ('PATH_BE_staticinfotables')) {
	define('PATH_BE_staticinfotables', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY));
}

if (!defined ('PATH_BE_staticinfotables_rel')) {
	define('PATH_BE_staticinfotables_rel', \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY));
}
// Unserializing the configuration so we can use it here
$_EXTCONF = unserialize($_EXTCONF);

// Including Extbase configuration
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScriptSetup('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/Configuration/TypoScript/Extbase/setup.txt">');

// Register cache static_info_tables
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$_EXTKEY])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$_EXTKEY] = array();
}
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$_EXTKEY]['frontend'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$_EXTKEY]['frontend'] = 'TYPO3\\CMS\\Core\\Cache\\Frontend\\PhpFrontend';
}
if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$_EXTKEY]['backend'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations'][$_EXTKEY]['backend'] = 'TYPO3\\CMS\\Core\\Cache\\Backend\\FileBackend';
}

// Configure clear cache post processing for extended domain model
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][$_EXTKEY] = 'EXT:' . $_EXTKEY . '/Classes/Cache/ClassCacheManager.php:SJBR\StaticInfoTables\Cache\ClassCacheManager->reBuild';

// Names of static entities
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['entities'] = array(
	'Country',
	'CountryZone',
	'Currency',
	'Language',
	'Territory'
);

// Regiter cached domain model classes autoloader
\SJBR\StaticInfoTables\Cache\CachedClassLoader::registerAutoloader();

// Possible label fields for different languages. Default as last.
$labelTable = array(
	'static_territories' => array(
		'label_fields' => array(
			'tr_name_##', 'tr_name_en',
		),
		'isocode_field' => array(
			'tr_iso_##',
		),
	),
	'static_countries' => array(
		'label_fields' => array(
			'cn_short_##', 'cn_short_en',
		),
		'isocode_field' => array(
			'cn_iso_##',
		),
	),
	'static_country_zones' => array(
		'label_fields' => array(
			'zn_name_##', 'zn_name_local',
		),
		'isocode_field' => array(
			'zn_code', 'zn_country_iso_##',
		),
	),
	'static_languages' => array(
		'label_fields' => array(
			'lg_name_##', 'lg_name_en',
		),
		'isocode_field' => array(
			'lg_iso_##', 'lg_country_iso_##',
		),
	),
	'static_currencies' => array(
		'label_fields' => array(
			'cu_name_##', 'cu_name_en',
		),
		'isocode_field' => array(
			'cu_iso_##',
		),
	),
);

if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['tables']) && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['tables'])) {
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['tables'] = array_merge($labelTable, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['tables']);
} else {
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['tables'] = $labelTable;
}
unset($labelTable);

// Enabling the Static Info Tables Manager module
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['enableManager'] = isset($_EXTCONF['enableManager']) ? $_EXTCONF['enableManager'] : '0';

// Make the extension version and constraints available when creating language packs and to other extensions
require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'ext_emconf.php');
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['version'] = $EM_CONF[$_EXTKEY]['version'];
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['constraints'] = $EM_CONF[$_EXTKEY]['constraints'];

?>