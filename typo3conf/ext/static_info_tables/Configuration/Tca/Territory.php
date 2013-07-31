<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$extensionResourcesLanguagePath = 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:';
$GLOBALS['TCA']['static_territories'] = array(
	'ctrl' => $GLOBALS['TCA']['static_territories']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'tr_iso_nr,tr_parent_iso_nr,tr_name_en'
	),
	'columns' => array(
		'deleted' => array(
			'readonly' => 1,
			'label' => $extensionResourcesLanguagePath . 'deleted',
			'config' => array(
				'type' => 'check'
			)
		),
		'tr_iso_nr' => array(
			'label' => $extensionResourcesLanguagePath . 'static_territories_item.tr_iso_nr',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '7',
				'max' => '7',
				'eval' => 'int',
				'default' => '0'
			)
		),
		'tr_parent_territory_uid' => array(
			'exclude' => 0,
			'label' => $extensionResourcesLanguagePath . 'static_territories_item.tr_parent_territory_uid',
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
		'tr_parent_iso_nr' => array(
			'config' => array(
				'type' => 'passthrough',
			)
		),
		'tr_name_en' => array(
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
	),
	'types' => array(
		'1' => array(
			'showitem' => 'tr_iso_nr,tr_name_en,fk_billing_country,--palette--;;1;;'
		)
	),
	'palettes' => array(
		'1' => array(
			'showitem' => 'tr_parent_territory_uid,tr_parent_iso_nr', 'canNotCollapse' => '1'
		)
	)
);
unset($extensionResourcesLanguagePath);
?>