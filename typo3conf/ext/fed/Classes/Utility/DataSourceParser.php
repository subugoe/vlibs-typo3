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
***************************************************************/

/**
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage Utility
 */
class Tx_Fed_Utility_DataSourceParser implements t3lib_Singleton {

	const URLMETHOD_JSON = 0;
	const URLMETHOD_XML = 1;
	const URLMETHOD_URI = 2;

	/**
	 * @var Tx_Fed_Utility_JSON
	 */
	protected $jsonHandler;

	/**
	 * @param Tx_Fed_Utility_JSON $jsonHandler
	 */
	protected function injectJSONHandler(Tx_Fed_Utility_JSON $jsonHandler) {
		$this->jsonHandler = $jsonHandler;
	}

	/**
	 * @param array $sources
	 * @return array
	 * @api
	 */
	public function parseDataSources(array $sources) {
		foreach ($sources as $k=>$v) {
			$sources[$k] = $this->parseDataSource($v);
		}
		return $sources;
	}

	/**
	 * @param Tx_Fed_Domain_Model_DataSource $source
	 * @return Tx_Fed_Domain_Model_DataSource
	 * @api
	 */
	public function parseDataSource(Tx_Fed_Domain_Model_DataSource $source) {
		$data = $this->gatherData($source);
		$source->setData($data);
		return $source;
	}

	/**
	 * Gather data from source by running the type of data gathering. NOT marked
	 * as API method; use "parseSource" or "parseSources" - future filtering etc.
	 * will be added through these functions while this function will remain a
	 * "uncached raw data output" function which MAY be marked API in the future.
	 *
	 * @param Tx_Fed_Domain_Model_DataSource $source
	 * @return array
	 */
	public function gatherData(Tx_Fed_Domain_Model_DataSource $source) {
		$probeUrl = $source->getUrl();
		$probeQuery = $source->getQuery();
		$probeFunction = $source->getFunc();
		if ($probeFunction) {
			$data = $this->fetchDataByFunction($probeFunction);
		} else if ($probeQuery) {
			$data = $this->fetchDataByQuery($probeQuery);
		} else if ($probeUrl) {
			$probeUrlMethod = $source->getUrlMethod();
			$data = $this->fetchDataByUrl($probeUrl, $probeUrlMethod);
		} else {
			throw new Exception('Could not fetch data from DataSource - no usable source defined');
		}

		return (array) $data;
	}

	/**
	 * Fetch data by $query
	 *
	 * @param string $query
	 * @return array
	 */
	private function fetchDataByQuery($query) {
		$array = array();
		$result = $GLOBALS['TYPO3_DB']->sql_query($query);
		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)) {
			array_push($array, $row);
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($result);
		return (array) $array;
	}

	/**
	 * Fetch data by $url and $method
	 * @param string $url
	 * @param string $method
	 * @return array
	 */
	private function fetchDataByUrl($url, $method) {
		$contents = file_get_contents($url);
		switch ($methpd) {
			case self::URLMETHOD_JSON:
				return (array) $this->jsonHandler->decode($contents);
				break;
			case self::URLMETHOD_XML:
				return (array) simplexml_load_string($contents, 'stdClass');
				break;
			case self::URLMETHOD_URI:
				return (array) parse_url($content, PHP_URL_SCHEME);
				break;
			default:
				return (array) array();
				break;
		}
	}

	/**
	 * Fetch data by $function call
	 * @param string $function
	 * @return array
	 */
	private function fetchDataByFunction($function) {
		if (strpos($function, '::')) {
			list ($object, $function) = explode('::', $function);
			$object = $this->objectManager->get($object);
			return (array) $object->$function();
		} else {
			return (array) $function();
		}
	}

}