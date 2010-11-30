<?php
/**
 * Copyright notice
 * 
 * Copyright (c) 2007 Joerg Schoppet
 * All rights reserved
 * 
 * This script is part of the TYPO3 project. The TYPO3 project is 
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license 
 * from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */
/**
 * Execution class of this plugin
 * 
 * This class does the real work, it taks the TS-Config, processes it
 * and sets the headerData.
 * 
 * @package		TYPO3
 * @subpackage	jsmanager
 * @author		Joerg Schoppet <joerg@schoppet.de>
 * @version		SVN: $Id: tx_jsmanager_Executor.php 7932 2008-01-17 07:45:34Z derjoerg $
 */
class tx_jsmanager_Executor {

	/**
	 * holds the extension key
	 *
	 * @var	string	$extKey
	 */
	protected $extKey = 'jsmanager';

	/**
	 * holds the complete configuration passed from TYPO3
	 *
	 * @var	array	$configuration
	 */
	protected $configuration = array();

	/**
	 * holds all js-lib-extensions, which should be processed regarding the config
	 *
	 * @var	array	$libraries
	 */
	protected $libraries = array();

	/**
	 * holds the text, which should be inserted into the page
	 *
	 * @var	array	$data
	 */
	protected $data = array();

	/**
	 * holds the information, where the js-information should be included
	 * can be either 'header' or 'body'
	 * 
	 * @var	string	$place
	 */
	protected $place = 'header';


	/**
	 * Processes the Manager object and finally put everything ino the page
	 * 
	 * @param	string	$content		Content passed from TYPO3
	 * @param	array	$configuration	Configuration passed from TYPO3
	 * @return	string	The content that should be displayed
	 */
	public function main($content, array $configuration) {
		$this->configuration = $configuration;

		try {
			$this->checkConfiguration();
			$this->getData();
			$content .= $this->setData();
		} catch (tx_jsmanager_Exception $e) {
			return 'Exception: ' . $e->getMessage();
		} // catch (tx_jsmanager_Exception $e)

		return $content;
	} // public function main($content, $configuration)

	/**
	 * Checks if the TS-config has the right syntax and if the requested
	 * js-libs are registered.
	 * 
	 * @return	bool|tx_jsmanager_Exception
	 */
	protected function checkConfiguration() {

		if (array_key_exists('libs.', $this->configuration) && array_key_exists('order', $this->configuration['libs.'])) {
			$this->libraries = explode(',', $this->configuration['libs.']['order']);

			foreach ($this->libraries as $library) {

				if (tx_jsmanager_Manager::isRegistered($library)) {

					if (array_key_exists('config.', $this->configuration['libs.']) && array_key_exists($library . '.', $this->configuration['libs.']['config.'])) {
						$specialConfiguration = $this->configuration['libs.']['config.'][$library . '.'];
					} else {
						$specialConfiguration = array();
					} // if (array_key_exists('config.', $this->configuration['libs.']))

					tx_jsmanager_Manager::retrieve($library)->checkConfiguration($specialConfiguration);
				} else {
					throw new tx_jsmanager_Exception('The library ' . $library . ' is not registered.');
				} // if (tx_jsmanager_Manager::isRegistered($library))

			} // foreach ($this->libraries as $library)

		} else {
			throw new tx_jsmanager_Exception('The basic syntax of the TS-Config is not correct.');
		} // if (array_key_exists('libs.', $this->configuration) && array_key_exists('order', $this->configuration['libs.']))

		if (array_key_exists('place', $this->configuration['libs.'])) {

			if (in_array($this->configuration['libs.']['place'], array('header', 'body'))) {
				$this->place = $this->configuration['libs.']['place'];
			} // if (in_array($this->configuration['libs.']['place'], array('header', 'body')))

		} // if (array_key_exists('place', $this->configuration['libs.']))

	} // protected function checkConfiguration(array $configuration)

	/**
	 * Iterates over all js-lib-extensions and gets the specific hdata from each
	 *
	 * @return	void
	 */
	protected function getData() {

		foreach ($this->libraries as $library) {

			if (!tx_jsmanager_Manager::retrieve($library)->checkIsIncluded()) {
				$this->data[$library] = tx_jsmanager_Manager::retrieve($library)->getData();
			} // if (!tx_jsmanager_Manager::retrieve($library)->checkIsIncluded())

		} // foreach ($this->libraries as $library)

	} // protected function setData()

	/**
	 * Includes the data to the TYPO3-page
	 * 
	 * @return	void
	 */
	protected function setData() {

		if (!tx_jsmanager_Manager::$included) {
			$content = '';

			if ('header' == $this->place) {
				$GLOBALS['TSFE']->additionalHeaderData[$this->extKey] = implode("\n", $this->data);
			} else {
				$content = implode("\n", $this->data);
			} // if ('header' == $this->place)

			$includedLibs = array_keys($this->data);

			foreach ($includedLibs as $lib) {
				tx_jsmanager_Manager::retrieve($lib)->setIsIncluded(TRUE);
			} // foreach ($includedLibs as $libs)

			tx_jsmanager_Manager::$included = TRUE;
		} // if (!tx_jsmanager_Manager::$included)

		return $content;
	} // protected function setData()

} // class tx_jsmanager_Executor

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jsmanager/tx_jsmanager_Executor.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jsmanager/tx_jsmanager_Executor.php']);
}

?>