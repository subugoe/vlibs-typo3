<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Christian Bülter <buelter@kennziffer.com>
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

class tx_kestats_filecounter {
	public $messages = array(
		'backend_tabname' => 'Downloads',
		'file_not_found' => 'File not found'
	);

	/**
	 * __construct
	 *
	 * @access public
	 * @return void
	 */
	public function __construct() {
		$this->initTYPO3();
	}

	/**
	 * initTYPO3
	 *
	 * init the TYPO3 Frontend
	 *
	 * @access protected
	 * @return void
	 */
	 function initTYPO3() {

			// *********************
			// Libraries included
			// *********************
		require_once(PATH_tslib.'class.tslib_fe.php');
		require_once(PATH_tslib.'class.tslib_content.php');
		require_once(PATH_t3lib.'class.t3lib_page.php');
		require_once(PATH_t3lib.'class.t3lib_userauth.php');
		require_once(PATH_tslib.'class.tslib_feuserauth.php');
		require_once(PATH_t3lib.'class.t3lib_tstemplate.php');
		require_once(PATH_t3lib.'class.t3lib_cs.php');


			// ***********************************
			// Create $TSFE object (TSFE = TypoScript Front End)
			// Connecting to database
			// ***********************************
		$temp_TSFEclassName = t3lib_div::makeInstanceClassName('tslib_fe');
		$TSFE = new $temp_TSFEclassName(
			$TYPO3_CONF_VARS,
			t3lib_div::_GP('id'),
			t3lib_div::_GP('type'),
			t3lib_div::_GP('no_cache'),
			t3lib_div::_GP('cHash'),
			t3lib_div::_GP('jumpurl'),
			t3lib_div::_GP('MP'),
			t3lib_div::_GP('RDCT')
		);

			// initialize the database
		$TSFE->connectToDB();

			// initialize the TCA
		$TSFE->includeTCA();

			// init fe user
		$this->feUserObj = tslib_eidtools::initFeUser(); // Initialize FE user object

			// create "fake TSFE" so that enable fields can use user group settings
		$GLOBALS['TSFE'] = $TSFE;
		$GLOBALS['TSFE']->gr_list = $this->feUserObj->user['usergroup'];

			// init page
		$this->page = t3lib_div::makeInstance('t3lib_pageSelect');

			// extension configuration
		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ke_stats']);
	}

	/**
	 *
	 */
	public function countFile() {
		$file = realpath($_SERVER['DOCUMENT_ROOT'] . $_SERVER['REQUEST_URI']);

			// check if file exists
		if (is_file($file)) {
			$fileinfo = pathinfo($file);
			$filename = $fileinfo['basename'];
			$fileextension = strtolower($fileinfo['extension']);

				// Must be set in order to use ke_stats
			$GLOBALS['TSFE']->config['config']['language'] = 0;

			if (t3lib_extMgm::isLoaded('ke_stats')) {
				$keStatsObj = t3lib_div::getUserObj('EXT:ke_stats/pi1/class.tx_kestats_pi1.php:tx_kestats_pi1');
				$keStatsObj->initApi();

				$category = $this->messages['backend_tabname'];
				$compareFieldList = 'element_uid,element_title,year,month';
				$element_title = htmlspecialchars(strip_tags($filename));
				$element_uid = 0;
				$element_pid = $this->extConf['fileAccessCountOnPage'] ? intval($this->extConf['fileAccessCountOnPage']) : 0;
				$element_language = $GLOBALS['TSFE']->sys_page->sys_language_uid;
				$element_type = 0;
				$stat_type = 'extension';
				$amount = 0;
				$parent_uid = 0;
				$additionalData = '';
				$counter = 1;

				$keStatsObj->increaseCounter(
					$category,
					$compareFieldList,
					$element_title,
					$element_uid,
					$element_pid,
					$element_language,
					$element_type,
					$stat_type,
					$parent_uid,
					$additionalData,
					$counter
				);
				unset($keStatsObj);
			}

			header('HTTP/1.1 200 OK');
			header('Status: 200 OK');

			// Download Bug IE SSL
			header('Pragma: anytextexeptno-cache', true);

			header('Content-Type: application/' . $fileextension);
			header('Content-Disposition: inline; filename="' . $filename . '"');

			readfile($file);
		} else {
			header("HTTP/1.0 404 Not Found");
			echo $this->messages['file_not_found'];
		}
    }
}
