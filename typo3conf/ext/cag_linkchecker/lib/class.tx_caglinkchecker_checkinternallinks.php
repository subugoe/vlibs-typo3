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
 * 'Check Internal Links' for the 'cag_linkchecker' extension.
 *
 * @author	Dimitri König <dk@cabag.ch>
 */

class tx_caglinkchecker_checkinternallinks {

	function checkLink($url, $reference) {
		$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
			'uid, deleted, title',
			'pages',
			'uid = ' . intval($url)
		);
		if($rows[0]) {
			if($rows[0]['deleted'] == '1') {
				$response = $GLOBALS['LANG']->getLL('list.report.pagedeleted');
				$response = str_replace('###title###', $rows[0]['title'], $response); 
				return $response;
			}
		} else {
			return $GLOBALS['LANG']->getLL('list.report.pagenotexisting');
		}

		return 1;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cag_linkchecker/lib/class.tx_caglinkchecker_checkinternallinks.php'])  {
        include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cag_linkchecker/lib/class.tx_caglinkchecker_checkinternallinks.php']);
}

?>
