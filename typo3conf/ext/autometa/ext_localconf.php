<?php
	if (!defined ('TYPO3_MODE')) {
		die ('Access denied.');
	}

	t3lib_extMgm::addPItoST43($_EXTKEY, 'pi1/class.tx_autometa_pi1.php', '_pi1', '', 1);
	$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output'][] = 'EXT:autometa/class.tx_autometa_fehook.php:&tx_autometa_fehook->intPages'; 
	$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all'][]    = 'EXT:autometa/class.tx_autometa_fehook.php:&tx_autometa_fehook->noIntPages'; 
?>
