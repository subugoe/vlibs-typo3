<?php
if (!defined ('TYPO3_MODE')) {
	die('Access denied.');
}


if (TYPO3_MODE == 'BE') {
	// get extension configuration
	$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3jquery']);
	if (! $confArr['integrateFromCDN']) {
		t3lib_extMgm::addModule('tools', 'txt3jqueryM1', '', t3lib_extMgm::extPath($_EXTKEY).'mod1/');
	}
}

?>