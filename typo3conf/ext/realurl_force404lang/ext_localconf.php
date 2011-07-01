<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}

	// Store old script in extension configuration
	$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['realurl_force404lang']);
	$confArr['pageNotFound_handling'] = $GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling'];
	$GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['realurl_force404lang'] = serialize($confArr);

	// Set own extension script as handler
	$GLOBALS['TYPO3_CONF_VARS']['FE']['pageNotFound_handling'] = 'USER_FUNCTION:EXT:realurl_force404lang/class.tx_realurlforce404lang.php:&tx_realurlforce404lang->getRealUrlPreVars';
?>