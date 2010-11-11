<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

/**
 * Extension of tx_rlmplanguagedetection_pi1:
 */


$TYPO3_CONF_VARS['FE']['XCLASS']['ext/rlmp_language_detection/pi1/class.tx_rlmplanguagedetection_pi1.php'] = t3lib_extMgm::extPath($_EXTKEY).'pi1/class.ux_tx_rlmplanguagedetection_pi1.php';
?>
