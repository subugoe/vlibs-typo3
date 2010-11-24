<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


$_EXTCONF = unserialize($_EXTCONF);    // unserializing the configuration so we can use it here:

if (isset($_EXTCONF) && is_array($_EXTCONF))	{

	$dbgMode = $_EXTCONF['TYPO3_MODE'] ? strtoupper($_EXTCONF['TYPO3_MODE']) : 'OFF';
	if (TYPO3_MODE==$dbgMode || $dbgMode=='ALL') {

		$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_db.php'] = t3lib_extMgm::extPath($_EXTKEY) . 'class.ux_t3lib_db.php';
	}
}
?>
