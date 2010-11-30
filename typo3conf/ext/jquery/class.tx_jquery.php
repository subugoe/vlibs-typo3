<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007 Joerg Schoppet (joerg@schoppet.de)
 *  All rights reserved
 *
 *  This script is part of the Typo3 project. The Typo3 project is
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
 * Plugin 'jQuery' for the 'jquery' extension.
 *
 * This class performs the inclusion of the required files for the jquery lib
 * 
 * @package		TYPO3
 * @subpackage	jquery
 * @author		Joerg Schoppet <joerg@schoppet.de>
 * @version		SVN: $Id$
 */
class tx_jquery  implements tx_jsmanager_ManagerInterface {

	/**
	 * holds the state, if jsmanager has included this ext
	 * 
	 * @var	bool
	 */
	protected $isIncluded = FALSE;

	/**
	 * holds the TS-config, provided by jsmanager
	 * 
	 * @var	array
	 */
	protected $configuration = array();

	/**
	 * @deprecated	deprecated since dependency of jsmanager
	 */
	var $prefixId = 'tx_jquery';		// Same as class name

	/**
	 * @deprecated	deprecated since dependency of jsmanager
	 */
	var $scriptRelPath = 'class.tx_jquery.php';	// Path to this script relative to the extension dir.

	/**
	 * @deprecated	deprecated since dependency of jsmanager
	 */
	var $extKey = 'jquery';	// The extension key.

	/**
	 * dummy method, jquery needs no config, but can have one
	 * 
	 * @return	bool
	 */
	public function checkConfiguration(array $configuration) {
		$this->configuration = $configuration;
		return true;
	} // public function checkConfiguration(array $configuration)

	/**
	 * main method, which processes the TS-Config and returns the
	 * data
	 * 
	 * @return	string
	 */
	public function getData() {
		// Which version should be included?
		// Fallback is the latest version
		$availableVersions = $this->getVersions();
		$version = $availableVersions[count($availableVersions)-1];

		if (array_key_exists('version', $this->configuration)) {

			if (strcasecmp($this->configuration['version'], 'max') == 0) {
				$version = $availableVersions[count($availableVersions)-1];
			} else {

				if (in_array($this->configuration['version'], $availableVersions)) {
					$version = $this->configuration['version'];
				} else {
					$version = $availableVersions[count($availableVersions)-1];
				} // if (in_array($this->configuration['version'], $availableVersions))

			} // if (strcasecmp($this->configuration['version'], 'max') == 0)

		} // if (array_key_exists('version', $this->configuration))

		// Which variant should be used?
		// Fallback is the normal variant, then the minimized and then the packed
		$variant = '';
		$variantFile = '';
		$availableVariants = $this->getVariants($version);

		if (array_key_exists('variant', $this->configuration)) {

			if (array_key_exists($this->configuration['variant'], $availableVariants)) {
				$variant = $this->configuration['variant'];
				$variantFile = $availableVariants[$this->configuration['variant']];
			} // if (array_key_exists($this->configuration['variant'], $availableVariants))

		} // if (array_key_exists('variant', $this->configuration))

		if (strlen($variant) == 0) {
			$variant = 'normal';

			if (array_key_exists($variant, $availableVariants)) {
				$variantFile = $availableVariants[$variant];
			} // if (array_key_exists($varaint, $availableVariants))

			if (strlen($variant) == 0) {
				$variant = 'minimized';

				if (array_key_exists($variant, $availableVariants)) {
					$variantFile = $availableVariants[$variant];
				} // if (array_key_exists($variant, $availableVariants))

				if (strlen($variant) == 0) {
					$variant = 'packed';

					if (array_key_exists($variant, $availableVariants)) {
						$variantFile = $availableVariants[$variant];
					} // if (array_key_exists($variant, $availableVariants))

				} // if (strlen($variant) == 0)

			} // if (strlen($variant) == 0)

		} // if (strlen($variant) == 0)

		// Should plugins be included?
		$pluginFiles = array();

		if (array_key_exists('plugins', $this->configuration) && strlen($this->configuration['plugins']) > 0) {
			$plugins = explode(',', $this->configuration['plugins']);
			$pattern = "|{([a-zA-Z0-9]*)}|";

			foreach ($plugins as $plugin) {
				$file = '';

				if (array_key_exists('plugins.', $this->configuration) && array_key_exists($plugin, $this->configuration['plugins.'])) {
					$file = preg_replace($pattern . 'e', '$\1', $this->configuration['plugins.'][$plugin]);

					if (strcmp(substr($file, 0, 4), 'EXT:') == 0) {
						list($extKey, $filePart) = explode('/', substr($file, 4), 2);

						if (strcmp($extKey, '') != 0 && t3lib_extMgm::isLoaded($extKey) && strcmp($filePart, '') != 0) {

							if (file_exists(t3lib_extMgm::extPath($extKey) . $filePart)) {
								$file = t3lib_extMgm::siteRelPath($extKey) . $filePart;
							} // if (file_exists(t3lib_extMgm::extPath($extKey) . $filePart))

						} // if (strcmp($extKey, '') != 0 && t3lib_extMgm::isLoaded($extKey) && strcmp($filePart, '') != 0)

					} else {

						if (!file_exists(PATH_site . $file)) {
							continue;
						} // if (!file_exists(PATH_site . $file))

					} // if (strcmp(substr($file, 0, 4), 'EXT:') == 0)

					$pluginFiles[] = $file;
				} // if (array_key_exists('plugins.', $this->configuration) && array_key_exists($plugin, $this->configuration['plugins.']))

			} // foreach ($plugins as $plugin)

		} // if (array_key_exists('plugins', $this->configuration) && strlen($this->configuration['plugins']) > 0)

		// Now we can process everything
		$data = '';
		// 1. Main js-file
		$data .= '<script type="text/javascript" src="' . t3lib_extMgm::siteRelPath('jquery') . 'versions/' . $version . '/source/' . $variantFile . '"></script>' . "\n";

		// 2. plugins
		if (count($pluginFiles) > 0) {

			foreach ($pluginFiles as $file) {
				$data .= '<script type="text/javascript" src="' . $file . '"></script>' . "\n";
			} // foreach ($pluginFiles as $file)

		} // if (count($pluginFiles) > 0)

		return $data;
	} // public function getHeaderData()

	/**
	 * getter-method for the isIncluded-var
	 * 
	 * @return	bool
	 */
	public function checkIsIncluded() {
		return $this->isIncluded;
	} // public function checkIsIncluded()

	/**
	 * setter-method for the isIncluded-var
	 * 
	 * @param	bool	$isIncluded
	 * @return	void
	 */
	public function setIsIncluded($isIncluded = TRUE) {
		$this->isIncluded = $isIncluded;
	} // public function setIsIncluded($isIncluded = TRUE)

	/**
	 * Returns all available versions of jquery.
	 * To achieve this, it iterates over the versions-directory
	 * 
	 * @return	array
	 */
	protected function getVersions() {
		$versions = array();
		$directory = new DirectoryIterator(t3lib_extMgm::extPath('jquery', 'versions'));

		foreach ($directory as $entry) {

			if ($entry->isDir() && !$entry->isDot() && is_numeric(substr($entry->getFilename(), 0, 1))) {
				$versions[] = $entry->getFilename();
			} // if ($entry->isDir() && !$entry->isDot() && is_numeric(substr($entry->getFilename(), 0, 1)))

		} // foreach ($directory as $entry)

		sort($versions);
		return $versions;
	} // protected function getVersions()

	/**
	 * Returns all available variants of the jquery-class (jquery)
	 * To achieve this, it iterates over the source-directory.
	 * 
	 * @param	string	$version	Version, to know, in which directory to search
	 * @return	array
	 */
	protected function getVariants($version) {
		$possibleVariants = array(
			'normal' => '.',
			'minimized' => '.min.',
			'packed' => '.pack.',
		);
		$variants = array();
		$directory = new DirectoryIterator(t3lib_extMgm::extPath('jquery', 'versions/' . $version . '/source'));

		foreach ($directory as $entry) {

			if ($entry->isFile()) {
				$fileName = $entry->getFilename();
				$fileName = str_replace('jquery', '', $fileName);
				$fileName = str_replace('js', '', $fileName);
				$variant = array_search($fileName, $possibleVariants);

				if ($variant) {
					$variants[$variant] = $entry->getFilename();
				} // if ($variant)

			} // if ($entry->isFile())

		} // foreach ($directory as $entry)

		return $variants;
	} // protected function getVariants($version)

	/**
	 * @deprecated	deprecated since dependency of jsmanager
	 */
	function main($content,$conf)	{
	}


	/**
	 * include the library and other data for page rendering
	 * any configuration has to be done before with the set-functions
	 * 
	 * @deprecated	deprecated since dependency of jsmanager
	 */
	function includeLib()	{
		// first we look, wich kind of plugins should be loaded (compressed or uncompressed)
		if (!isset($GLOBALS['tx_jquery']['compressed']))	{

			if (isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jquery.']['compressed']))	{

				if (strCaseCmp($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jquery.']['compressed'], 'true') == 0)	{
					tx_jquery::setCompressed(TRUE);
				} else {
					tx_jquery::setCompressed(FALSE);
				}

			} else {
				tx_jquery::setCompressed();
			}

		}

		// add jquery to page content
		if (!$GLOBALS['tx_jquery']['tx_jquery_base_inc']) {
			// add jquery to page header
			$GLOBALS['TSFE']->additionalHeaderData['tx_jquery_base_inc'] = '<script type="text/javascript" src="' . t3lib_extMgm::siteRelPath('jquery') . ($GLOBALS['tx_jquery']['compressed']==false?'uncompressed_':'') . 'src/jquery.js"></script>';
			$GLOBALS['tx_jquery']['tx_jquery_base_inc'] = TRUE;
		}

		// the config is parsed the following way
		// 1. has the user set something within an application
		// 2. is something defined by TS
		// 3. use default values
		if (!isset($GLOBALS['tx_jquery']['base_only']))	{

			if (isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jquery.']['base_only']))	{

				if (strCaseCmp($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jquery.']['base_only'], 'true') == 0)	{
					tx_jquery::setBaseOnly(TRUE);
				} else {
					tx_jquery::setBaseOnly(FALSE);
				}

			} else {
				tx_jquery::setBaseOnly();
			}

		}

		if (!isset($GLOBALS['tx_jquery']['plugins']))	{
			$aPlugins = array();

			if (isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jquery.']['plugins']))	{

				if ($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jquery.']['plugins'] != '')	{
					$aPlugins = explode(',', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jquery.']['plugins']);
				}

			}

			tx_jquery::setPlugins($aPlugins);
		}

		if (!isset($GLOBALS['tx_jquery']['compatibility'])) {

			if (isset($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jquery.']['compatibility'])) {

				if (strCaseCmp($GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_jquery.']['compatibility'], 'true') == 0) {
					tx_jquery::setCompatibility(TRUE);
				} else {
					tx_jquery::setCompatibility(FALSE);
				}

			} else {
				tx_jquery::setCompatibility();
			}

		}

		// add jquery-plugins to page content
		if ($GLOBALS['tx_jquery']['base_only'] === FALSE)	{

			if (!$GLOBALS['tx_jquery']['tx_jquery_plugins_inc']) {

				if (count($GLOBALS['tx_jquery']['plugins']) > 0)	{

					foreach ($GLOBALS['tx_jquery']['plugins'] as $sFile)	{
						$GLOBALS['TSFE']->additionalHeaderData['tx_jquery_plugins_inc' . $sFile] = '<script type="text/javascript" src="' . t3lib_extMgm::siteRelPath('jquery') . ($GLOBALS['tx_jquery']['compressed']==false?'uncompressed_':'') . 'plugins/' . $sFile . '.js"></script>';
					}

				}

				$GLOBALS['tx_jquery']['tx_jquery_plugins_inc'] = TRUE;
			}

		}


		if (!$GLOBALS['tx_jquery']['tx_jquery_compat_inc']) {

			if ($GLOBALS['tx_jquery']['compatibility']) {
				$GLOBALS['TSFE']->additionalHeaderData['tx_jquery_compat_inc'] = '<script type="text/javascript" src="' . t3lib_extMgm::siteRelPath('jquery') . ($GLOBALS['tx_jquery']['compressed']==false?'uncompressed_':'') . 'plugins/jquery.compat-1.1.js"></script>';
			}

			$GLOBALS['tx_jquery']['tx_jquery_compat_inc'] = TRUE;
		}

	}


	/**
	 * set value if only jquery should be included
	 * 
	 * @deprecated	deprecated since dependency of jsmanager
	 */
	function setBaseOnly($var=FALSE) {
		$GLOBALS['tx_jquery']['base_only'] = (bool)$var;
	}


	/**
	 * set value for special plugin load
	 * 
	 * @deprecated	deprecated since dependency of jsmanager
	 */
	function setPlugins($plugins=array()) {

		if (is_array($plugins)) {
			$GLOBALS['tx_jquery']['plugins'] = $plugins;
		}

	}


	/**
	 * set value if compressed scripts should be included
	 * 
	 * @deprecated	deprecated since dependency of jsmanager
	 */
	function setCompressed($var=TRUE) {
		$GLOBALS['tx_jquery']['compressed'] = (bool)$var;
	}


	/**
	 * set value if compatibility-plugin for 1.1 should be included
	 * 
	 * @deprecated	deprecated since dependency of jsmanager
	 */
	function setCompatibility($var=TRUE) {
		$GLOBALS['tx_jquery']['compatibility'] = (bool)$var;
	}


} // class tx_jquery  implements tx_jsmanager_ManagerInterface

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jquery/class.tx_jquery.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jquery/class.tx_jquery.php']);
}

?>