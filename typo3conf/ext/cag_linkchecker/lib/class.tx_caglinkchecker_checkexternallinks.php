<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 - 2009 Jochen Rieger (j.rieger@connecta.ag)
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
 * 'Check External Links' for the 'cag_linkchecker' extension.
 *
 * @author	Dimitri König <dk@cabag.ch>
 */

class tx_caglinkchecker_checkexternallinks {

	var $url_reports = array();
	
	/**
	 * Checks a given URL + /path/filename.ext for validity
	 */
	function checkLink($url, $reference) {
		if($this->url_reports[$url]) {
			return $this->url_reports[$url]; 
		}
		// remove possible anchor from the url
		if (strrpos($url, '#') !== false) {
			$url = substr($url, 0, strrpos($url, '#'));
		}

		// try to fetch the content of the URL (just fetching of headers doesn't work!)
		$report = array();
		t3lib_div::getURL($url, 1, false, $report);

		$ret = 1;
		
		// analyze the response
		if ($report['error']) {
			//$ret = $report['lib'] . ': (' . $report['error'] . ') ' . $report['message'];
			$ret = $GLOBALS['LANG']->getLL('list.report.noresponse');
		}

		$this->url_reports[$url] = $ret;
		return $ret;
	}

	function fetchType($value, $type) {
		preg_match_all('/((?:http|https|ftp|ftps))(?::\/\/)(?:[^\s<>]+)/i', $value['tokenValue'], $urls, PREG_PATTERN_ORDER);

		if(!empty($urls[0][0])) {
			$type = "external";
		}

		return $type;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cag_linkchecker/lib/class.tx_caglinkchecker_checkexternallinks.php'])  {
        include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cag_linkchecker/lib/class.tx_caglinkchecker_checkexternallinks.php']);
}

?>
