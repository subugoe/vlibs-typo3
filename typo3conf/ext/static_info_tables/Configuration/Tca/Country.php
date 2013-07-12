<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$extensionResourcesLanguagePath = 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:';
$GLOBALS['TCA']['static_countries'] = array(
	'ctrl' => $GLOBALS['TCA']['static_countries']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'cn_iso_2,cn_iso_3,cn_iso_nr,cn_official_name_local,cn_official_name_en,cn_capital,cn_tldomain,cn_currency_iso_3,cn_currency_iso_nr,cn_phone,cn_uno_member,cn_eu_member,cn_address_format,cn_short_en'
	),
	'columns' => array(
		'deleted' => array(
			'readonly' => 1,
			'label' => $extensionResourcesLanguagePath . 'deleted',
			'config' => array(
				'type' => 'check'
			)
		),
		'cn_iso_2' => array(
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_iso_2',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '4',
				'max' => '2',
				'eval' => '',
				'default' => ''
			)
		),
		'cn_iso_3' => array(
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_iso_3',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '5',
				'max' => '3',
				'eval' => '',
				'default' => ''
			)
		),
		'cn_iso_nr' => array(
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_iso_nr',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '7',
				'max' => '7',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'cn_parent_territory_uid' => array(
			'exclude' => 0,
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_parent_territory_uid',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'static_territories',
				'foreign_table_where' => 'ORDER BY static_territories.tr_name_en',
				'itemsProcFunc' => 'SJBR\StaticInfoTables\Hook\Backend\Form\ElementRenderingHelper->translateTerritoriesSelector',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'cn_parent_tr_iso_nr' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'cn_official_name_local' => array(
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_official_name_local',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '25',
				'max' => '128',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cn_official_name_en' => array(
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_official_name_en',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '25',
				'max' => '50',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cn_capital' => array(
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_capital',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '15',
				'max' => '45',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cn_tldomain' => array(
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_tldomain',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '5',
				'max' => '',
				'eval' => '',
				'default' => ''
			)
		),
		'cn_currency_uid' => array(
			'exclude' => 0,
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_currency_uid',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('', 0),
				),
				'foreign_table' => 'static_currencies',
				'foreign_table_where' => 'ORDER BY static_currencies.cu_name_en',
				'itemsProcFunc' => 'SJBR\StaticInfoTables\Hook\Backend\Form\ElementRenderingHelper->translateCurrenciesSelector',
				'size' => 1,
				'minitems' => 0,
				'maxitems' => 1,
			)
		),
		'cn_currency_iso_nr' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'cn_currency_iso_3' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'cn_phone' => array(
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_phone',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '10',
				'max' => '20',
				'eval' => '',
				'default' => '0'
			)
		),
		'cn_eu_member' => array(
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_eu_member',
			'exclude' => '0',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'cn_uno_member' => array(
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_uno_member',
			'exclude' => '0',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'cn_address_format' => array(
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_address_format',
			'exclude' => '0',
			'config' => array(
				'type' => 'select',
				'items' => array(
					array('','0'),
					array($extensionResourcesLanguagePath . 'static_countries_item.cn_address_format_1','1'),
					array($extensionResourcesLanguagePath . 'static_countries_item.cn_address_format_2','2'),
					array($extensionResourcesLanguagePath . 'static_countries_item.cn_address_format_3','3'),
					array($extensionResourcesLanguagePath . 'static_countries_item.cn_address_format_4','4'),
					array($extensionResourcesLanguagePath . 'static_countries_item.cn_address_format_5','5'),
					array($extensionResourcesLanguagePath . 'static_countries_item.cn_address_format_6','6'),
					array($extensionResourcesLanguagePath . 'static_countries_item.cn_address_format_7','7'),
					array($extensionResourcesLanguagePath . 'static_countries_item.cn_address_format_8','8'),
					array($extensionResourcesLanguagePath . 'static_countries_item.cn_address_format_9','9'),
					),
				'default' => '0'
			)
		),
		'cn_zone_flag' => array(
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_zone_flag',
			'exclude' => '0',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'cn_short_local' => array(
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_short_local',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '25',
				'max' => '50',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cn_short_en' => array(
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_short_en',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '25',
				'max' => '50',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'cn_country_zones' => array(
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_country_zones',
			'exclude' => '0',
			'config' => array(
				'type' => 'inline',
				'foreign_table' => 'static_country_zones',
				'foreign_field' => 'zn_country_uid',
				'foreign_table_field' => 'zn_country_table',
				'foreign_default_sortby' => 'zn_name_local',
				'maxitems' => '100',
				'appearance' => array(
					'expandSingle' => 1,
					'newRecordLinkAddTitle' => 1
				)
			)
		)
	),
	'types' => array(
		'1' => array(
			'showitem' => 'cn_short_local,cn_official_name_local,cn_official_name_en,--palette--;;1;;,--palette--;;5;;,--palette--;;2;;,--palette--;;3;;,--palette--;;4;;,cn_short_en,cn_country_zones'
		)
	),
	'palettes' => array(
		'1' => array(
			'showitem' => 'cn_iso_nr,cn_iso_2,cn_iso_3', 'canNotCollapse' => '1'
		),
		'2' => array(
			'showitem' => 'cn_currency_uid,cn_currency_iso_nr,cn_currency_iso_3', 'canNotCollapse' => '1'
		),
		'3' => array(
			'showitem' => 'cn_capital,cn_uno_member,cn_eu_member,cn_phone,cn_tldomain', 'canNotCollapse' => '1'
		),
		'4' => array(
			'showitem' => 'cn_address_format,cn_zone_flag', 'canNotCollapse' => '1'
		),
		'5' => array(
			'showitem' => 'cn_parent_territory_uid,cn_parent_tr_iso_nr', 'canNotCollapse' => '1'
		)
	)
);
unset($extensionResourcesLanguagePath);
?>