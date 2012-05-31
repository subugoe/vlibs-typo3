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
class Tx_Fed_Provider_Configuration_PageConfigurationProvider extends Tx_Flux_Provider_AbstractConfigurationProvider implements Tx_Flux_Provider_ConfigurationProviderInterface {

	/**
	 * @var string
	 */
	protected $tableName = 'pages';

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
	protected $configurationSectionName = 'Configuration';

	/**
	 * @param array $row
	 * @return string
	 */
	public function getTemplatePathAndFilename(array $row) {
		$configurationManager = $this->objectManager->get('Tx_Fed_Configuration_ConfigurationManager');
		$pageService = $this->objectManager->get('Tx_Fed_Service_Page');
		$configuration = $pageService->getPageTemplateConfiguration($row['uid']);
		$templatePathAndFilename = NULL;
		if ($configuration['tx_fed_page_controller_action']) {
			$action = $configuration['tx_fed_page_controller_action'];
			list ($extensionName, $action) = explode('->', $action);
			$paths = $configurationManager->getPageConfiguration($extensionName);
			$templatePathAndFilename = $paths['templateRootPath'] . '/Page/' . $action . '.html';
		} else if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableFallbackFluidPageTemplate']) {
			$templatePathAndFilename = $pageService->getFallbackPageTemplatePathAndFilename();
		}
		return $templatePathAndFilename;
	}

	/**
	 * @param array $row
	 * @return array
	 */
	public function getTemplateVariables(array $row) {
		$flexFormUtility = $this->objectManager->get('Tx_Fed_Utility_FlexForm');
		$flexFormUtility->setContentObjectData($row['tx_fed_page_flexform']);
		$flexform = $flexFormUtility->getAll();
		$templatePathAndFilename = $row['tx_fed_fcefile'];
		list ($extensionName, $filename) = explode(':', $templatePathAndFilename);
		$paths = array();
		$configurationManager = $this->objectManager->get('Tx_Fed_Configuration_ConfigurationManager');
		$pageService = $this->objectManager->get('Tx_Fed_Service_Page');
		$paths = $configurationManager->getPageConfiguration($extensionName);
		$templatePathAndFilename = Tx_Fed_Utility_Path::translatePath($paths['templateRootPath'] . $filename);
		$configuration = $pageService->getPageTemplateConfiguration($row['uid']);
		if ($configuration['tx_fed_page_controller_action']) {
			$action = $configuration['tx_fed_page_controller_action'];
			list ($extensionName, $action) = explode('->', $action);
			$paths = $configurationManager->getPageConfiguration($extensionName);
			$templatePathAndFilename = $paths['templateRootPath'] . '/Page/' . $action . '.html';
		} else if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableFallbackFluidPageTemplate']) {
			$templatePathAndFilename = $pageService->getFallbackPageTemplatePathAndFilename();
		} else {
			return array();
		}

		$view = $this->objectManager->get('Tx_Flux_MVC_View_ExposedStandaloneView');
		$view->setTemplatePathAndFilename($templatePathAndFilename);
		$view->assignMultiple($flexform);
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
	}

	/**
	 * @param array $row
	 * @return array
	 */
	public function getTemplatePaths(array $row) {
		$configuration = $this->pageService->getPageTemplateConfiguration($row['uid']);
		if ($configuration['tx_fed_page_controller_action']) {
			$action = $configuration['tx_fed_page_controller_action'];
			list ($extensionName, $action) = explode('->', $action);
		} else {
			$extensionName = 'fed';
		}
		$paths = $this->configurationManager->getPageConfiguration($extensionName);
		return $paths;
	}

}

?>