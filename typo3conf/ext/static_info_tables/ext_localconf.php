<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

if (!defined ('STATIC_INFO_TABLES_EXTkey')) {
	define('STATIC_INFO_TABLES_EXTkey',$_EXTKEY);
}

if (!defined ('PATH_BE_staticinfotables')) {
	define('PATH_BE_staticinfotables', t3lib_extMgm::extPath(STATIC_INFO_TABLES_EXTkey));
}

if (!defined ('PATH_BE_staticinfotables_rel')) {
	define('PATH_BE_staticinfotables_rel', t3lib_extMgm::extRelPath(STATIC_INFO_TABLES_EXTkey));
}

$_EXTCONF = unserialize($_EXTCONF);    // unserializing the configuration so we can use it here:

if (!isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['charset']))	{
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['charset'] = (isset($_EXTCONF) && is_array($_EXTCONF) && $_EXTCONF['charset'] ? $_EXTCONF['charset'] : 'utf-8');
}

$labelTable = array(
	'static_territories' => array(
		'label_fields' => array(	// possible label fields for different languages. Default as last.
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

if (t3lib_extMgm::isLoaded('static_info_tables_markets')) {
	$labelTable['static_markets'] = array(
		'label_fields' => array(
			'institution_description',
		),
		'isocode_field' => array(
			'institution_description',
		),
	);
}

if (isset($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['tables']) && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['tables']))	{

	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['tables'] = array_merge ($labelTable, $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['tables']);
} else {
	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][STATIC_INFO_TABLES_EXTkey]['tables'] = $labelTable;
}

require_once(t3lib_extMgm::extPath(STATIC_INFO_TABLES_EXTkey).'class.tx_staticinfotables_div.php');

if (TYPO3_MODE == 'BE' && isset($_EXTCONF) && is_array($_EXTCONF) && $_EXTCONF['usePatch1822'] &&
!defined($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['addTables']['static_countries']['MENU'])) {
	$tableArray = array('static_territories', 'static_countries', 'static_country_zones', 'static_currencies', 'static_languages');

	foreach ($tableArray as $theTable)	{
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['LLFile'][$theTable] = 'EXT:'.STATIC_INFO_TABLES_EXTkey.'/locallang.xml';
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['page0'][$theTable] = TRUE;
	}

	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['addTables']['static_territories'] = array (
		'default' => array(
			'MENU' => 'm_default',
			'fList' =>  'tr_name_en,tr_iso_nr,tr_parent_iso_nr',
			'icon' => TRUE
		),
	);


	if (t3lib_extMgm::isLoaded('static_info_tables_de'))	{
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['addTables']['static_territories']['langArray'] = array('de' => array('tr_name_en' => 'tr_name_de'));
	}

	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['addTables']['static_countries'] = array (
		'default' => array(
			'MENU' => 'm_default',
			'fList' =>  'cn_short_en,cn_iso_2,cn_iso_3,cn_iso_nr,cn_parent_tr_iso_nr,cn_official_name_local,cn_capital',
			'icon' => TRUE
		),
		'ext' => array(
			'MENU' => 'm_ext',
			'fList' =>  'cn_short_en,cn_tldomain,cn_currency_iso_3,cn_currency_iso_nr,cn_phone,cn_eu_member,cn_uno_member,cn_address_format,cn_zone_flag,cn_short_local,cn_official_name_en',
			'icon' => TRUE
		)
	);

	if (t3lib_extMgm::isLoaded('static_info_tables_de'))	{
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['addTables']['static_countries']['langArray'] = array('de' => array('cn_short_en' => 'cn_short_de'));
	}

	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['addTables']['static_country_zones'] = array (
		'default' => array(
			'MENU' => 'm_default',
			'fList' =>  'zn_name_local,zn_name_en,zn_country_iso_2,zn_country_iso_3,zn_country_iso_nr,zn_code',
			'icon' => TRUE
		)
	);

	if (t3lib_extMgm::isLoaded('static_info_tables_de'))	{
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['addTables']['static_country_zones']['langArray'] = array('de' => array('zn_name_en' => 'zn_name_de'));
	}

	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['addTables']['static_currencies'] = array (
		'default' => array(
			'MENU' => 'm_default',
			'fList' =>  'cu_name_en,cu_iso_3,cu_iso_nr,cu_symbol_left,cu_symbol_right,cu_thousands_point,cu_decimal_digits,cu_sub_name_en,cu_sub_divisor,cu_sub_symbol_left,cu_sub_symbol_right',
			'icon' => TRUE
		)
	);

	if (t3lib_extMgm::isLoaded('static_info_tables_de'))	{
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['addTables']['static_currencies']['langArray'] = array('de' => array('cu_name_en' => 'cu_name_de', 'cu_sub_name_en' => 'cu_sub_name_de'));
	}

	$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['addTables']['static_languages'] = array (
		'default' => array(
			'MENU' => 'm_default',
			'fList' =>  'lg_name_en,lg_iso_2,lg_name_local,lg_typo3,lg_country_iso_2,lg_collate_locale,lg_sacred,lg_constructed',
			'icon' => TRUE
		)
	);

	if (t3lib_extMgm::isLoaded('static_info_tables_de'))	{
		$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cms']['db_layout']['addTables']['static_languages']['langArray'] = array('de' => array('lg_name_en' => 'lg_name_de'));
	}

}

?>