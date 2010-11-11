<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');
$TYPO3_CONF_VARS["BE"]['XCLASS']['ext/belog/mod/index.php']=t3lib_extMgm::extPath($_EXTKEY) . 'class.ux_SC_mod_tools_log_index.php';
?>