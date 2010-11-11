<?PHP
if (!defined('TYPO3_MODE')) {
	die ('Access denied!');
}

$TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/realurl/class.tx_realurl_advanced.php'] = t3lib_extMgm::extPath('p2_realurl').'class.ux_tx_realurl_advanced.php';

?>
