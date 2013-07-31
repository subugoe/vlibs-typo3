<?php
/*
 * Register necessary class names with autoloader
 */
$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('static_info_tables');
return array(
	'tx_staticinfotables_div' => $extensionPath . 'class.tx_staticinfotables_div.php',
	'tx_staticinfotables_pi1' => $extensionPath . 'pi1/class.tx_staticinfotables_pi1.php',
);
?>