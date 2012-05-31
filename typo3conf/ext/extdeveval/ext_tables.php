<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

if (TYPO3_MODE=='BE')	{
	t3lib_extMgm::addModule('tools', 'txextdevevalM1', '',t3lib_extMgm::extPath($_EXTKEY) . 'mod1/');

	// register top module
	$GLOBALS['TYPO3_CONF_VARS']['typo3/backend.php']['additionalBackendItems'][] = t3lib_extMgm::extPath('extdeveval') . 'registerToolbarItem.php';
}
?>