<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
 *
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
 * SOLR Widget Controller
 *
 * Handles proxying of AJAX requests from the Fluid Widget.
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers/Widget
 */
class Tx_Fed_ViewHelpers_Widget_Controller_SolrController extends Tx_Fluid_Core_Widget_AbstractWidgetController {

	/**
	 * @var Tx_Fed_Service_Json
	 */
	protected $jsonService;

	/**
	 * @var array
	 */
	protected $solrConfiguration;

	/**
	 * @param Tx_Fed_Service_Json $jsonService
	 */
	public function injectJsonService(Tx_Fed_Service_Json $jsonService) {
		$this->jsonService = $jsonService;
	}

	/**
	 * Initialize action
	 */
	public function initializeAction() {
		$configType = Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT;
		$settings = $this->configurationManager->getConfiguration($configType);
		$settings = Tx_Extbase_Utility_TypoScript::convertTypoScriptArrayToPlainArray($settings['plugin.']['tx_solr.']);
		$this->solrConfiguration = $settings;
	}

	/**
	 * @return string
	 */
	public function indexAction() {
		if (!$this->widgetConfiguration['id']) {
			$this->widgetConfiguration['id'] = uniqid('fedsolr');
		}
		if ($this->widgetConfiguration['templatePathAndFilename']) {
			$templatePathAndFilename = $this->widgetConfiguration['templatePathAndFilename'];
			$templatePathAndFilename = Tx_Fed_Utility_Path::translatePath($templatePathAndFilename);
			$this->view->setTemplatePathAndFilename($templatePathAndFilename);
		}
		if ($this->widgetConfiguration['layoutRootPath']) {
			$layoutRootPath = $this->widgetConfiguration['layoutRootPath'];
			$layoutRootPath = Tx_Fed_Utility_Path::translatePath($layoutRootPath);
			$this->view->setLayoutRootPath($layoutRootPath);
		}
		if ($this->widgetConfiguration['partialRootPath']) {
			$partialRootPath = $this->widgetConfiguration['partialRootPath'];
			$partialRootPath = Tx_Fed_Utility_Path::translatePath($partialRootPath);
			$this->view->setPartialRootPath($partialRootPath);
		}
		$titles = array();
		foreach ($this->solrConfiguration['search']['faceting']['facets'] as $facet) {
			$titles[$facet['field']] = $facet['label'];
		}
		$this->widgetConfiguration['options']['facetTitles'] = $titles;
		$this->widgetConfiguration['options']['fields'] = (array) t3lib_div::trimExplode(',', $this->solrConfiguration['search']['query']['fields']);
		$this->widgetConfiguration['resultsPerPageOptions'] = $this->solrConfiguration['search']['results']['resultsPerPageSwitchOptions'];
		$this->view->assign('arguments', $this->widgetConfiguration);
		$this->view->assign('options', $this->jsonService->encode($this->widgetConfiguration['options']));
		return $this->view->render();
	}
}

?>