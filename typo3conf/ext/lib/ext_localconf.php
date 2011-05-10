<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

require_once(t3lib_extMgm::extPath('div') . 'class.tx_div.php');

if (1) {
	require_once(t3lib_extMgm::extPath('lib') . 'spl/class.tx_lib_spl_arrayObject.php');
	require_once(t3lib_extMgm::extPath('lib') . 'spl/class.tx_lib_spl_arrayIterator.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_selfAwareness.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_object.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_viewBase.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_formBase.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_configurations.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_controller.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_image.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_link.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_parameters.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_pearLoader.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_phpFormEngine.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_phpTemplateEngine.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_smartyView.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_switch.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_t3Loader.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_translator.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_validator.php');
	require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_captcha.php');
}
?>
