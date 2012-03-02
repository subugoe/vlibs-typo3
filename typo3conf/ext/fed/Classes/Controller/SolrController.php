<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * Controller
 *
 * @package Fed
 * @subpackage Controller
 */
class Tx_Fed_Controller_SolrController extends Tx_Fed_Core_AbstractController {

	/**
	 * @var Tx_Fed_Service_Solr
	 */
	protected $solrService;

	/**
	 * @param Tx_Fed_Service_Solr $solrService
	 */
	public function injectSolrService(Tx_Fed_Service_Solr $solrService) {
		$this->solrService = $solrService;
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
	 * Displays the search form
	 *
	 * @return string
	 */
	public function formAction() {
		return $this->view->render();
	}

	/**
	 * Queries the Solr server
	 *
	 * @return string
	 */
	public function searchAction(array $query) {
		$result = $this->solrService->query($query['q'], $query);
		if (!$result) {
			$message = array(
				'code' => 1324057374,
				'message' => 'Error during communication with Solr server - see Tomcat logs',
			);
			return $this->jsonService->encode($message);
		}
		return $this->jsonService->encode($result);
	}

}

?>