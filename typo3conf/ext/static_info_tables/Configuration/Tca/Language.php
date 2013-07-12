<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}
$extensionResourcesLanguagePath = 'LLL:EXT:static_info_tables/Resources/Private/Language/locallang_db.xlf:';
$GLOBALS['TCA']['static_languages'] = array(
	'ctrl' => $GLOBALS['TCA']['static_languages']['ctrl'],
	'interface' => array(
		'showRecordFieldList' => 'lg_name_local,lg_name_en,lg_iso_2,lg_typo3,lg_country_iso_2,lg_collate_locale,lg_sacred,lg_constructed'
	),
	'columns' => array(
		'deleted' => array(
			'readonly' => 1,
			'label' => $extensionResourcesLanguagePath . 'deleted',
			'config' => array(
				'type' => 'check'
			)
		),
		'lg_iso_2' => array(
			'label' => $extensionResourcesLanguagePath . 'static_languages_item.lg_iso_2',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '4',
				'max' => '2',
				'eval' => '',
				'default' => ''
			)
		),
		'lg_name_local' => array(
			'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.name',
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
		'lg_name_en' => array(
			'label' => $extensionResourcesLanguagePath . 'static_languages_item.lg_name_en',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '18',
				'max' => '40',
				'eval' => 'trim',
				'default' => '',
				'_is_string' => '1'
			)
		),
		'lg_typo3' => array(
			'label' => $extensionResourcesLanguagePath . 'static_languages_item.lg_typo3',
			'exclude' => '0',
			'config' => array(
				'type' => 'input',
				'size' => '3',
				'max' => '2',
				'eval' => '',
				'default' => ''
			)
		),
		'lg_country_iso_2' => Array (
			'label' => $extensionResourcesLanguagePath . 'static_countries_item.cn_iso_2',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '3',
				'max' => '2',
				'eval' => '',
				'default' => ''
			)
		),
		'lg_collate_locale' => Array (
			'label' => $extensionResourcesLanguagePath . 'static_languages_item.lg_collate_locale',
			'exclude' => '0',
			'config' => Array (
				'type' => 'input',
				'size' => '5',
				'max' => '5',
				'eval' => '',
				'default' => ''
			)
		),
		'lg_sacred' => array(
			'label' => $extensionResourcesLanguagePath . 'static_languages_item.lg_sacred',
			'exclude' => '0',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
		'lg_constructed' => array(
			'label' => $extensionResourcesLanguagePath . 'static_languages_item.lg_constructed',
			'exclude' => '0',
			'config' => array(
				'type' => 'check',
				'default' => '0'
			)
		),
	),
	'types' => array(
		'1' => array(
			'showitem' => 'lg_name_local,lg_name_en,lg_iso_2,lg_typo3,lg_country_iso_2,lg_collate_locale,lg_sacred,lg_constructed'
		)
	)
);
unset($extensionResourcesLanguagePath);
?>