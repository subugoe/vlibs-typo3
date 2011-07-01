<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Vladimir Falcon Piva <falcon@cps-it.de>
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Class to get language parameter from realurl and store it globally
 *
 * @author	Vladimir Falcon Piva <falcon@cps-it.de>
 * @package TYPO3
 * @subpackage realurl_force404lang
 */
	require_once(t3lib_extMgm::extPath('realurl').'class.tx_realurl.php'); // load framework class

	class tx_realurlforce404lang extends tx_realurl {

		public function __construct() {
			parent::__construct();
		}

		public function getRealUrlPreVars(&$params, &$pObj){

			$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['realurl_force404lang']);

			// Setting parent object reference (which is $GLOBALS['TSFE'])
			$this->pObj = $pObj;

			// Initializing config / request URL:
			$this->setConfig();
			$this->adjustConfigurationByHost('decode');
			$this->adjustRootPageId();
			$speakingURIpath = $this->pObj->siteScript{0} == '/' ? substr($this->pObj->siteScript, 1) : $this->pObj->siteScript;

			// Convert URL to segments
			$pathParts = explode('/', $speakingURIpath);
			array_walk($pathParts, create_function('&$value', '$value = rawurldecode($value);'));

			// Setting "preVars":
			$pre_GET_VARS = $this->decodeSpURL_settingPreVars($pathParts, $this->extConf['preVars']);

			// Set global language parameter
			if (isset($pre_GET_VARS[$confArr['languageParam']])) {
				$_GET[$confArr['languageParam']] = $pre_GET_VARS[$confArr['languageParam']];
			}

			// Call TYPO3 error handler with stored script
			$this->pObj->pageErrorHandler($confArr['pageNotFound_handling'], '', $params['reasonText']);
		}
	}
?>