<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

// Configure extension static template
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript/Static', 'Static Info Tables');

$typo3Version = \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version);
$extensionResourcesLanguagePath = 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_db.xlf:';
$extensionConfigurationTcaPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) . 'Configuration/Tca/';
$extensionResourcesIconsPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath($_EXTKEY) . 'Resources/Public/Images/Icons/';

// Country reference data from ISO 3166-1
$GLOBALS['TCA']['static_countries'] = array(
	'ctrl' => array(
		'label' => 'cn_short_en',
		'label_alt' => 'cn_iso_2',
		'label_alt_force' => 1,
		'label_userFunc' => 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\ElementRenderingHelper->addIsoCodeToLabel',
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'readOnly' => 1,
		'default_sortby' => 'ORDER BY cn_short_en',
		'delete' => 'deleted',
		'title' => $extensionResourcesLanguagePath . 'static_countries.title',
		'dynamicConfigFile' => $extensionConfigurationTcaPath . 'Country.php',
		'iconfile' => $extensionResourcesIconsPath . 'icon_static_countries.gif',
	),
	'interface' => array(
		'showRecordFieldList' => 'cn_iso_2,cn_iso_3,cn_iso_nr,cn_official_name_local,cn_official_name_en,cn_capital,cn_tldomain,cn_currency_iso_3,cn_currency_iso_nr,cn_phone,cn_uno_member,cn_eu_member,cn_address_format,cn_short_en'
	)
);

// Country subdivision reference data from ISO 3166-2
$GLOBALS['TCA']['static_country_zones'] = array(
	'ctrl' => array(
		'label' => 'zn_name_local',
		'label_alt' => 'zn_name_local,zn_code',
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'readOnly' => 1,
		'default_sortby' => 'ORDER BY zn_name_local',
		'delete' => 'deleted',
		'title' => $extensionResourcesLanguagePath . 'static_country_zones.title',
		'dynamicConfigFile' => $extensionConfigurationTcaPath . 'CountryZone.php',
		'iconfile' => $extensionResourcesIconsPath . 'icon_static_countries.gif',
	),
	'interface' => array(
		'showRecordFieldList' => 'zn_country_iso_nr,zn_country_iso_3,zn_code,zn_name_local,zn_name_en'
	)
);

// Currency reference data from ISO 4217
$GLOBALS['TCA']['static_currencies'] = array(
	'ctrl' => array(
		'label' => 'cu_name_en',
		'label_alt' => 'cu_iso_3',
		'label_alt_force' => 1,
		'label_userFunc' => 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\ElementRenderingHelper->addIsoCodeToLabel',
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'readOnly' => 1,
		'default_sortby' => 'ORDER BY cu_name_en',
		'delete' => 'deleted',
		'title' => $extensionResourcesLanguagePath . 'static_currencies.title',
		'dynamicConfigFile' => $extensionConfigurationTcaPath . 'Currency.php',
		'iconfile' => $extensionResourcesIconsPath . 'icon_static_currencies.gif',
	),
	'interface' => array(
		'showRecordFieldList' => 'cu_iso_3,cu_iso_nr,cu_name_en,cu_symbol_left,cu_symbol_right,cu_thousands_point,cu_decimal_point,cu_decimal_digits,cu_sub_name_en,cu_sub_divisor,cu_sub_symbol_left,cu_sub_symbol_right'
	)
);

// Language reference data from ISO 639-1
$GLOBALS['TCA']['static_languages'] = array(
	'ctrl' => array(
		'label' => 'lg_name_en',
		'label_alt' => 'lg_iso_2',
		'label_alt_force' => 1,
		'label_userFunc' => 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\ElementRenderingHelper->addIsoCodeToLabel',
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'readOnly' => 1,
		'default_sortby' => 'ORDER BY lg_name_en',
		'delete' => 'deleted',
		'title' => $extensionResourcesLanguagePath . 'static_languages.title',
		'dynamicConfigFile' => $extensionConfigurationTcaPath . 'Language.php',
		'iconfile' => $extensionResourcesIconsPath . 'icon_static_languages.gif',
	),
	'interface' => array(
		'showRecordFieldList' => 'lg_name_local,lg_name_en,lg_iso_2,lg_typo3,lg_country_iso_2,lg_collate_locale,lg_sacred,lg_constructed'
	)
);

// UN Territory reference data 
$GLOBALS['TCA']['static_territories'] = array(
	'ctrl' => array(
		'label' => 'tr_name_en',
		'label_alt' => 'tr_iso_nr',
		'label_alt_force' => 1,
		'label_userFunc' => 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\ElementRenderingHelper->addIsoCodeToLabel',
		'adminOnly' => 1,
		'rootLevel' => 1,
		'is_static' => 1,
		'readOnly' => 1,
		'default_sortby' => 'ORDER BY tr_name_en',
		'delete' => 'deleted',
		'title' => $extensionResourcesLanguagePath . 'static_territories.title',
		'dynamicConfigFile' => $extensionConfigurationTcaPath . 'Territory.php',
		'iconfile' => $extensionResourcesIconsPath . 'icon_static_territories.gif',
	),
	'interface' => array(
		'showRecordFieldList' => 'tr_name_en,tr_iso_nr'
	)
);

unset($extensionResourcesLanguagePath);
unset($extensionConfigurationTcaPath);
unset($extensionResourcesIconsPath);

// Configure static language field of sys_language table
if ($typo3Version < 6001000) {
	\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA('sys_language');
}
$GLOBALS['TCA']['sys_language']['columns']['static_lang_isocode']['config'] = array(
	'type' => 'select',
	'items' => array(
		array('',0),
	),
	'foreign_table' => 'static_languages',
	'foreign_table_where' => 'AND static_languages.pid=0 ORDER BY static_languages.lg_name_en',
	'itemsProcFunc' => 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\ElementRenderingHelper->translateLanguagesSelector',
	'size' => 1,
	'minitems' => '0',
	'maxitems' => 1,
	'wizards' => array(
		'suggest' => array(
			'type' => 'suggest',
			'default' => array(
				'receiverClass' => 'SJBR\\StaticInfoTables\\Hook\\Backend\\Form\\SuggestReceiver'
			)
		)
	)
);

// Add data handling hook to manage ISO codes redundancies on records
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'SJBR\\StaticInfoTables\\Hook\\Core\\DataHandling\\ProcessDataMap';

if (TYPO3_MODE == 'BE' && !(TYPO3_REQUESTTYPE & TYPO3_REQUESTTYPE_INSTALL)) {
	/**
	 * Registers the Static Info Tables Manager backend module, if enabled
	 */
	if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$_EXTKEY]['enableManager']) {
		\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerModule(
			$_EXTKEY,
			// Make module a submodule of 'tools'
			'tools',
			// Submodule key
			'Manager',
			// Position
			'',
			// An array holding the controller-action combinations that are accessible
			array(
				'Manager' => 'information,newLanguagePack,createLanguagePack,testForm,testFormResult,sqlDumpNonLocalizedData'
			),
			array(
				'access' => 'user,group',
				'icon' => 'EXT:' . $_EXTKEY . '/Resources/Public/Images/Icons/moduleicon.gif',
				'labels' => 'LLL:EXT:' . $_EXTKEY . '/Resources/Private/Language/locallang_mod.xlf'
			)
		);
		// Add module configuration setup
		\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTypoScript($_EXTKEY, 'setup', '<INCLUDE_TYPOSCRIPT: source="FILE:EXT:' . $_EXTKEY . '/Configuration/TypoScript/Manager/setup.txt">');
		
		// Enable editing Static Info Tables
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables'])) {
			$tableNames = array_keys($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables']);
			foreach ($tableNames as $tableName) {
				if ($typo3Version < 6001000) {
					\SJBR\StaticInfoTables\Utility\TcaUtility::loadTCA($tableName);
				}
				$GLOBALS['TCA'][$tableName]['ctrl']['readOnly'] = 0;
			}
		}
	}
}
?>