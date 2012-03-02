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
 * Solr Service
 *
 * Service for interacting with a Solr server. Re-uses configuration from TS added
 * by the extension "solr".
 *
 * @package Fed
 * @subpackage Service
 * @version
 */
class Tx_Fed_Service_Solr implements t3lib_Singleton {

	/**
	 * @var Tx_Fed_Service_Json
	 */
	protected $jsonService;

	/**
	 * @var Tx_Fed_Configuration_ConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @var array
	 */
	protected $solrConfiguration;

	/**
	 * Initialize object
	 */
	public function initializeObject() {
		$configType = Tx_Extbase_Configuration_ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT;
		$settings = $this->configurationManager->getConfiguration($configType);
		$settings = Tx_Extbase_Utility_TypoScript::convertTypoScriptArrayToPlainArray($settings['plugin.']['tx_solr.']);
		$this->solrConfiguration = $settings;
	}

	/**
	 * @param Tx_Fed_Service_Json $jsonService
	 */
	public function injectJsonService(Tx_Fed_Service_Json $jsonService) {
		$this->jsonService = $jsonService;
	}

	/**
	 * @param Tx_Fed_Configuration_ConfigurationManager $configurationManager
	 */
	public function injectConfigurationManager(Tx_Fed_Configuration_ConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Queries the Solr server
	 *
	 * @param string $q
	 * @param array $parameters
	 */
	public function query($q, $parameters=NULL) {
		if ($q != $parameters['q']) {
			$parameters['q'] = $q;
		}
		$connection = $this->solrConfiguration['solr'];
		$rows = $parameters['rows'] ? $parameters['rows'] : $this->solrConfiguration['search']['results']['resultsPerPage'];
		$queryString = 'facet=on&wt=json&json.nl=map&fl=*,score&q=' . urlencode($parameters['q']) . '&rows=' . $rows . '&start=' . $parameters['start'];
		foreach ($this->solrConfiguration['search']['faceting']['facets'] as $facet) {
			$queryString .= '&facet.field=' . $facet['field'];
		}
		foreach ((array) $parameters['facets'] as $facet) {
			$queryString .= '&fq=' . urlencode($facet['facetName'] . ':"' . $facet['facetValue'] . '"');
		}
		$queryString .= '&qf=' . implode('+', t3lib_div::trimExplode(',', $this->solrConfiguration['search']['query']['fields']));
		$hostString = $connection['scheme'] . '://' . $connection['host'] . ':' . $connection['port'] . $connection['path'];
		$url = $hostString . 'select/?' . $queryString;
		$result = file_get_contents($url);
		return $this->jsonService->decode($result);
	}

}

?>