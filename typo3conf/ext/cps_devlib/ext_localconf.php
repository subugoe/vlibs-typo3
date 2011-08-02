<?php
	// Auto load extension classes
	$extensionPath = t3lib_extMgm::extPath('cps_devlib') . 'lib/';

	$autoloadFiles = array(
		'tx_cpsdevlib_db' => $extensionPath.'class.tx_cpsdevlib_db.php',
		'tx_cpsdevlib_debug' => $extensionPath.'class.tx_cpsdevlib_debug.php',
		'tx_cpsdevlib_div' => $extensionPath.'class.tx_cpsdevlib_div.php',
		'tx_cpsdevlib_extmgm' => $extensionPath.'class.tx_cpsdevlib_extmgm.php',
		'tx_cpsdevlib_parser' => $extensionPath.'class.tx_cpsdevlib_parser.php',
	);

	foreach ($autoloadFiles as $key => $value) {
		if (!class_exists($key)) {
			if (file_exists($value)) {
				require_once($value);
			}
		}
	}
?>