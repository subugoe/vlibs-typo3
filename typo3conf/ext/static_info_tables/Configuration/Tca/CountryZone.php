<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$extensionResourcesLanguagePath = 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:';
$GLOBALS['TCA']['static_country_zones'] = array(
	'ctrl' => $GLOBALS['TCA']['static_country_zones']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'zn_country_iso_nr,zn_country_iso_2,zn_country_iso_3,zn_code,zn_name_local,zn_name_en'
	),
	'columns' => array(
		'deleted' => array(
			'readonly' => 1,
			'label' => $extensionResourcesLanguagePath . 'deleted',
			'config' => array(
				'type' => 'check'
			)
		),
		'zn_country_uid' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'zn_country_table' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'zn_country_iso_nr' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'zn_country_iso_2' => array(
			'config' => array(
				'type' => 'passthrough',
			)		
		),
		'zn_country_iso_3' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'zn_code' => array(
			'label' => $extensionResourcesLanguagePath . 'static_country_zones_item.zn_code',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '18',
				'max' => '45',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'zn_name_local' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.name',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '18',
				'max' => '45',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'zn_name_en' => array(
			'label' => $extensionResourcesLanguagePath . 'static_country_zones_item.zn_name_en',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '18',
				'max' => '45',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
	),
	'types' => array(
		'1' => array(
			'showitem' => 'zn_name_local,zn_code,--palette--;;1;;,zn_name_en'
		)
	),
	'palettes'	=> array(
		'1' => array(
			'showitem' => 'zn_country_uid,zn_country_iso_nr,zn_country_iso_2,zn_country_iso_3', 'canNotCollapse' => '1'
		)
	)
);
unset($extensionResourcesLanguagePath);
?>