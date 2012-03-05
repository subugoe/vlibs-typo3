<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Class that renders a selection field for Fluid FCE template selection
 *
 * @package	TYPO3
 * @subpackage	fed
 */
class Tx_Fed_Backend_FCESelector {

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 *
	 * @var Tx_Fed_Configuration_ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @var Tx_Fed_Utility_FlexForm
	 */
	protected $flexform;

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		$this->objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->configurationManager = $this->objectManager->get('Tx_Fed_Configuration_ConfigurationManager');
		$this->flexform = $this->objectManager->get('Tx_Fed_Utility_FlexForm');
	}

	/**
	 * Render a Flexible Content Element type selection field
	 *
	 * @param array $parameters
	 * @param mixed $parentObject
	 * @return string
	 */
	public function renderField(array &$parameters, &$parentObject) {
		$allTemplatePaths = $this->configurationManager->getContentConfiguration();
		$name = $parameters['itemFormElName'];
		$value = $parameters['itemFormElValue'];
		$select = '<div><select name="' . htmlspecialchars($name) . '"  class="formField select" onchange="if (confirm(TBE_EDITOR.labels.onChangeAlert) && TBE_EDITOR.checkSubmit(-1)){ TBE_EDITOR.submitForm() };">' . LF;
		$select .= '<option value="">' . $GLOBALS['LANG']->sL('LLL:EXT:fed/Resources/Private/Language/locallang.xml:fce.selection', TRUE) . '</option>' . LF;
		foreach ($allTemplatePaths as $key => $templatePathSet) {
			$files = Tx_Fed_Utility_Path::getFiles($templatePathSet['templateRootPath'], TRUE);
			if (count($files) > 0) {
				$groupLabel = '';
				if (!t3lib_extMgm::isLoaded($key)) {
					$groupLabel = ucfirst($key);
				} else {
					$emConfigFile = t3lib_extMgm::extPath($key, 'ext_emconf.php');
					require $emConfigFile;
					$groupLabel = empty($EM_CONF['']['title']) ? ucfirst($key) : $EM_CONF['']['title'];
				}
				$select .= '<optgroup label="' . htmlspecialchars($groupLabel) . '">' . LF;
				foreach ($files as $fileRelPath) {
					$templateFilename = $templatePathSet['templateRootPath'] . DIRECTORY_SEPARATOR . $fileRelPath;
					$view = $this->objectManager->get('Tx_Flux_MVC_View_ExposedStandaloneView');
					$view->setTemplatePathAndFilename($templateFilename);
					try {
						$config =  $view->getStoredVariable('Tx_Flux_ViewHelpers_FlexformViewHelper', 'storage', 'Configuration');
						$enabled = $config['enabled'];
						$label = $config['label'];
						if ($enabled !== FALSE) {
							$optionValue = $key . ':' . $fileRelPath;
							if (!$label) {
								$label = $fileRelPath;
							}
							$translatedLabel = Tx_Extbase_Utility_Localization::translate($label, $key);
							if ($translatedLabel !== NULL) {
								$label = $translatedLabel;
							}
							$selected = ($optionValue === $value ? ' selected="selected"' : '');
							$select .= '<option value="' . htmlspecialchars($optionValue) . '"' . $selected . '>' . htmlspecialchars($label) . '</option>' . LF;
						}
					} catch (Exception $e) {
						$select .= "<option value=''>INVALID: " . $fileRelPath . " (Exception # " . $e->getMessage() . ")</option>" . LF;
					}
				}
				$select .= '</optgroup>' . LF;
			}
		}
		$select .= '</select></div>' . LF;
		return $select;
	}


}

?>