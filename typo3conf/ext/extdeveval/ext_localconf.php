<?php
# TYPO3 CVS ID: $Id: ext_localconf.php 43504 2011-02-12 13:31:28Z ohader $

if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

define('PATH_tx_extdeveval', t3lib_extMgm::extPath('extdeveval'));

if (TYPO3_MODE=='BE')	{
	$TYPO3_CONF_VARS['SC_OPTIONS']['typo3/alt_topmenu_dummy.php']['fetchContentTopmenu'][] = 'EXT:extdeveval/class.tx_extdeveval_fetchContentTopMenu.php:tx_extdeveval_altTopMenuDummy';

	$TYPO3_CONF_VARS['SC_OPTIONS']['ext/extdeveval/class.ux_sc_alt_topmenu_dummy.php']['links']=array(
			// Backend
		array('t3lib/', t3lib_extMgm::extRelPath($_EXTKEY).'apidocs/t3lib_api.html'),
		array('div', t3lib_extMgm::extRelPath($_EXTKEY).'apidocs/t3lib_div.html'),
		array('extMgm', t3lib_extMgm::extRelPath($_EXTKEY).'apidocs/t3lib_extmgm.html'),
		array('BEfunc', t3lib_extMgm::extRelPath($_EXTKEY).'apidocs/t3lib_befunc.html'),
		array('DB', t3lib_extMgm::extRelPath($_EXTKEY).'apidocs/t3lib_db.html'),
		array('template', t3lib_extMgm::extRelPath($_EXTKEY).'apidocs/typo3_template.html'),
		array('lang', t3lib_extMgm::extRelPath($_EXTKEY).'apidocs/typo3_lang.html'),

			// Frontend:
		array('pibase', t3lib_extMgm::extRelPath($_EXTKEY).'apidocs/tslib_pibase_api.html'),
		array('cObj', t3lib_extMgm::extRelPath($_EXTKEY).'apidocs/tslib_content_api.html'),
		array('TSref', 'http://typo3.org/documentation/document-library/references/doc_core_tsref/current/view/'),

			// TYPO3.org
		array('TYPO3.org', 'http://typo3.org/'),
	);


	//integration in new backend ver 4.2
	if(t3lib_div::int_from_ver(TYPO3_version) >= 4002000) {
		$GLOBALS['TYPO3_CONF_VARS']['typo3/backend.php']['additionalBackendItems'][] = t3lib_extMgm::extPath('extdeveval').'class.tx_extdeveval_additionalBackendItems.php';
	}

}


?>
