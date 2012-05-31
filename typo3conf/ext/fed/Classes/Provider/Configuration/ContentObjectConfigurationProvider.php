<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Claus Due <claus@wildside.dk>, Wildside A/S
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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
 * ************************************************************* */

/**
 * @package Fed
 * @subpackage Provider
 */
class Tx_Fed_Provider_Configuration_ContentObjectConfigurationProvider extends Tx_Flux_Provider_AbstractContentObjectConfigurationProvider implements Tx_Flux_Provider_ContentObjectConfigurationProviderInterface {

	/**
	 * @var string
	 */
	protected $tableName = 'tt_content';

	/**
	 * @var string
	 */
	protected $fieldName = '';

	/**
	 * @var string
	 */
	protected $extensionKey = 'fed';

	/**
	 * @var string
	 */
	protected $contentObjectType = 'fed_fce';

	/**
	 * @var string
	 */
	protected $configurationSectionName = 'Configuration';

	/**
	 * @var Tx_Fed_Configuration_ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @param Tx_Fed_Configuration_ConfigurationManager $configurationManager
	 */
	public function injectConfigurationManager(Tx_Fed_Configuration_ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * @param array $row
	 * @return string
	 */
	public function getTemplatePathAndFilename(array $row) {
		$templatePathAndFilename = $row['tx_fed_fcefile'];
		list ($extensionName, $filename) = explode(':', $templatePathAndFilename);
		$paths = $this->configurationManager->getContentConfiguration($extensionName);
		$templatePathAndFilename = Tx_Fed_Utility_Path::translatePath($paths['templateRootPath'] . $filename);
		return $templatePathAndFilename;
	}

	/**
	 * @param array $row
	 * @return array
	 */
	public function getTemplateVariables(array $row) {
		$flexFormUtility = $this->objectManager->get('Tx_Fed_Utility_FlexForm');
		$templatePathAndFilename = $row['tx_fed_fcefile'];
		list ($extensionName, $filename) = explode(':', $templatePathAndFilename);
		$paths = $this->configurationManager->getContentConfiguration($extensionName);
		$templatePathAndFilename = Tx_Fed_Utility_Path::translatePath($paths['templateRootPath'] . $filename);
		$view = $this->objectManager->get('Tx_Flux_MVC_View_ExposedStandaloneView');
		$view->setTemplatePathAndFilename($templatePathAndFilename);
		$flexFormUtility->setContentObjectData($row);
		$flexform = $flexFormUtility->getAll();
		$view->assignMultiple($flexform);
		$view->assignMultiple($this->flexFormService->setContentObjectData($row)->getAll());
		try {
			$stored = $view->getStoredVariable('Tx_Flux_ViewHelpers_FlexformViewHelper', 'storage', 'Configuration');
			$stored['sheets'] = array();
			foreach ($stored['fields'] as $field) {
				$groupKey = $field['sheets']['name'];
				$groupLabel = $field['sheets']['label'];
				if (is_array($stored['sheets'][$groupKey]) === FALSE) {
					$stored['sheets'][$groupKey] = array(
						'name' => $groupKey,
						'label' => $groupLabel,
						'fields' => array()
					);
				}
				array_push($stored['sheets'][$groupKey]['fields'], $field);
			}
			return $stored;
		} catch (Exception $e) {
			t3lib_div::sysLog('FED Flexible Content Element error: ' . $e->getMessage(), 'fed');
			return NULL;
		}
	}

	/**
	 * @param array $row
	 * @return array
	 */
	public function getTemplatePaths(array $row) {
		$templatePathAndFilename = $row['tx_fed_fcefile'];
		list ($extensionName, $filename) = explode(':', $templatePathAndFilename);
		$paths = array();
		$paths = $this->configurationManager->getContentConfiguration($extensionName);
		return $paths;
	}

}

?>