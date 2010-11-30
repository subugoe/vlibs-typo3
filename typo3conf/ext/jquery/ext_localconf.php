<?php

if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

require_once(t3lib_extMgm::extPath('jquery', 'class.tx_jquery.php'));
tx_jsmanager_Manager::register(new tx_jquery());
?>