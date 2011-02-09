<?php
/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Nils K. Windisch <windisch@sub.uni-goettingen.de>
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
 * ************************************************************* */
require_once(PATH_tslib . 'class.tslib_pibase.php');

class tx_nkwlib extends tslib_pibase {

	var $extKey;
	var $conf;
	var $language;

	/**
	 * Returns the first letter of a String parameter
	 * @param <String> $str
	 * @return <Char>
	 */
	public function getFirstLetter($str) {
		$str = strtoupper(mb_substr($str, 0, 1, 'UTF-8'));
		return $str;
	}

	/**
	 * Geocode an address using the Google Maps API
	 * @param <type> $str
	 * @return <type>
	 * @todo TESTEN
	 */
	function geocodeAddress($str) {
		$getThis = 'http://maps.google.com/maps/api/geocode/json?address=' . urlencode($str) . '&sensor=false';
		$json = file_get_contents($getThis);
		$tmp = json_decode($json, true);
		$return = $tmp;
		return $return;
	}

	/**
	 * Extracts and returns the URL of the current Installation
	 * @param <type> $clean
	 * @return <type>
	 */
	function getPageUrl($clean = false) {
		$url = $GLOBALS['TSFE']->baseUrl . $GLOBALS['TSFE']->anchorPrefix;
		if ($clean) {
			$tmp = explode('?', $url);
			$url = $tmp[0];
		}
		return $url;
	}

	/**
	 * Sets the language
	 * @param <type> $str
	 */
	function setLanguage($str = false) {
		if ($GLOBALS['TSFE']->sys_page->sys_language_uid == true) {
			$this->language = $GLOBALS['TSFE']->sys_page->sys_language_uid;
		} else {
			$this->language = $str;
		}
	}

	/**
	 * Returns the Syslanguage UID
	 * @return <int>
	 */
	function getLanguage() {
		$lang = $GLOBALS['TSFE']->sys_page->sys_language_uid;
		return $lang;
	}

	/**
	 * Returns the current page UID
	 * @return <int>
	 */
	function getPageUID() {
		$pageUID = $GLOBALS['TSFE']->id;
		return $pageUID;
	}

	/**
	 * Returns a language String
	 * Convention is that language UID = 0 is german and UID = 1 english
	 * @param <type> $lang
	 * @return <type>
	 */
	function getLanguageStr($lang) {
		$lang = intval($lang);
		if ($lang === 0) {
			return 'de';
		} else if ($lang === 1) {
			return 'en';
		}
	}

	/**
	 * Get Keywords for a page
	 * @param <int> $id
	 * @param <int> $lang
	 * @param <type> $mode
	 * @return string 
	 */
	function keywordsForPage($id, $lang, $mode = false, $landingpage = FALSE) {
	
		if ($lang == 0) {
			$sep = '_de';
		} else if ($lang == 1) {
			$sep = '_en';
		}
		$pageInfo = $this->pageInfo($id, $lang);
		if (!empty($pageInfo['tx_nkwkeywords_keywords'])) {
			if ($mode == 'header') {
				$tmp = explode(',', $pageInfo['tx_nkwkeywords_keywords']);
				foreach ($tmp AS $key => $value) {
					$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
									'*',
									'tx_nkwkeywords_keywords',
									"uid = '" . $value . "'",
									'',
									'',
									'');
					while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
						$tmpList .= $row1['title' . $sep] . ',';
					}
				}
				$str .= substr($tmpList, 0, -1);
			} else if ($mode == 'infobox') {
				$tmp = explode(',', $pageInfo['tx_nkwkeywords_keywords']);
				foreach ($tmp AS $key => $value) {
					$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
									'*',
									'tx_nkwkeywords_keywords',
									'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($value, 'tx_nkwkeywords_keywords'),
									'',
									'',
									'');
					while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
						$str .= '<li>';

						$this->cObj->typoLink(
							$row1['title' . $sep], 
							array(
								'parameter' => $landingpage,
								'useCacheHash' => true,
								'additionalParams' => '&tx_nkwkeywords[id]=' . $value
								)
							);
						$str .= '<a title="' . $row1['title' . $sep] . '" href="' . $this->cObj->lastTypoLinkUrl . '">' . $row1['title' . $sep] . '</a>';
						$str .= '</li>';
					}
				}
			}
		}
		return $str;
	}

	/**
	 * Get the page title of a page in your desired language
	 * @param <int> $id
	 * @param <int> $lang
	 * @return <type> 
	 */
	function getPageTitle($id, $lang = 0) {
		if ($lang > 0) {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'title',
							'pages_language_overlay',
							'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'pages_language_overlay'),
							'',
							'',
							'');
			while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
				$title = $row1['title'];
			}
		}
		if ($lang == 0 || ($lang > 0 && !$title)) {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'*',
							'pages',
							'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'pages'),
							'',
							'',
							'');
			while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
				$title = $row1['title'];
			}
		}
		return $title;
	}

	/**
	 * Get page informations about uid, pid, title, keywords, ...
	 * @param <int> $id
	 * @param <int> $lang
	 * @return <array>
	 */
	function pageInfo($id, $lang = false) {
		if ($lang > 0) {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'*',
							'pages_language_overlay',
							'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'pages_language_overlay'),
							'',
							'',
							'');
		} else {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'*',
							'pages',
							'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'pages'),
							'',
							'',
							'');
		}
		while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			$pageInfo['uid'] = $row1['uid'];
			$pageInfo['pid'] = $row1['pid'];
			$pageInfo['title'] = $row1['title'];
			$pageInfo['keywords'] = $row1['keywords'];
			$pageInfo['tx_nkwsubmenu_knot'] = $row1['tx_nkwsubmenu_knot'];
			$pageInfo['tx_nkwkeywords_keywords'] = $row1['tx_nkwkeywords_keywords'];
		}
		if ($lang > 0) {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'tx_nkwkeywords_keywords',
							'pages',
							'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($pageInfo['pid'], 'pages'),
							'',
							'',
							'');
			while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
				$pageInfo['tx_nkwkeywords_keywords'] = $row1['tx_nkwkeywords_keywords'];
			}
		}
		return $pageInfo;
	}

	/**
	 *
	 * @param <int> $id
	 * @return <type> 
	 */
	function knotID($id) {
		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'uid, pid, tx_nkwsubmenu_knot',
						'pages',
						'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'pages'),
						'',
						'',
						'');
		while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			if ($row1['tx_nkwsubmenu_knot']) {
				return $row1['uid'];
			} else if ($row1['pid'] != 3) {
				return $this->knotID($row1['pid']);
			}
		}
	}

	/**
	 * Get the IDS of a pagetree as Array
	 * @param <type> $startId
	 * @return Array
	 */
	function getPageTreeIds($startId) {
		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'uid',
						'pages',
						'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($startId, 'pages')
						. ' AND deleted = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0, 'pages')
						. ' AND hidden = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0, 'pages'),
						'',
						'sorting ASC',
						'');
		while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			$children = $this->getPageTreeIds($row1['uid']);
			if ($children) {
				$tree[$row1['uid']]['children'] = $this->getPageTreeIds($row1['uid']);
			} else {
				$tree[$row1['uid']]['children'] = 0;
			}
		}
		return $tree;
	}

	/**
	 * Get child page of a page UID
	 * @param <int> $id
	 * @return <Array>
	 */
	function getPageChildIds($id) {
		$i = 0;
		$arr = array();
		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'uid',
						'pages',
						'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'pages')
						. ' AND deleted = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0, 'pages')
						. ' AND hidden = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0, 'pages'),
						'',
						'sorting ASC',
						'');

		$arr = array();
		while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			$arr[$i]['uid'] = $row1['uid'];
			$i++;
		}
		if ($i > 0) {
			return $arr;
		} else {
			return false;
		}
	}

	/**
	 * Checks if a page has child records
	 * @todo Should maybe only return true or false and not false or Array
	 * @param <int> $id
	 * @param <int> $lang
	 * @return <boolean or Array>
	 */
	function pageHasChild($id, $lang = 0) {
		$i = 0;
		$arr = array();
		if ($lang > 0) {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'*',
							'pages_language_overlay',
							'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'pages_language_overlay')
							. ' AND deleted = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0, 'pages_language_overlay')
							. ' AND hidden = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0, 'pages_language_overlay')
							. ' AND sys_language_uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($lang, 'pages_language_overlay'),
							'',
							'',
							'');
		} else {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'*',
							'pages',
							'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'pages')
							. ' AND deleted = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0, 'pages')
							. ' AND hidden = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0, 'pages'),
							'',
							'sorting ASC',
							'');
		}
		while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			$arr[$i]['uid'] = $row1['uid'];
			$arr[$i]['title'] = $row1['title'];
			$arr[$i]['tx_nkwsubmenu_in_menu'] = $row1['tx_nkwsubmenu_in_menu'];
			$i++;
		}
		if ($i > 0) {
			return $arr;
		} else {
			return false;
		}
	}

	/**
	 * Needs a key-value pair and extracts the first Letter of the value, checks
	 * if it is unique and sorts it alphabetically
	 * @param <Array> $arr
	 * @return <Array>
	 */
	function alphaListFromArray($arr) {
		$list = array();
		foreach ($arr AS $key => $value) {
			if ($value) {
				$letter = strtoupper($value);
				array_push($list, $letter{0});
			}
		}
		$list = array_unique($list);
		return $list;
	}

	/**
	 * check if a page uses the content of another page "content_from_pid"
	 * @todo Maybe check if we should only return true or false and not false or an id
	 * @param <type> $id
	 * @return <type> 
	 */
	function checkForAlienContent($id) {
		$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
						'uid, content_from_pid',
						'pages',
						'uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'pages'),
						'',
						'',
						'');
		while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			$content_from_pid = $row1['content_from_pid'];
		}
		if ($content_from_pid) {
			$id = $content_from_pid;
		} else {
			return false;
		}
		return $id;
	}

	/**
	 * Fragment of an SQL String to determine something is visible at the moment
	 * @param <String> $table
	 * @return string 
	 */
	function queryStartEndTime($table = 'tt_content') {
		$str = '((starttime < ' . time() . ' AND endtime > ' . time() . ')'
				. ' OR (starttime = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0, $table)
				. ' AND endtime = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0, $table) . '))';
		return $str;
	}

	/**
	 * Returns an Array Containing the UID and header field of content elements of a page
	 * If no content element it returns false @todo maybe improve return values
	 * @param <int> $id
	 * @param <int> $lang
	 * @return <Array>
	 */
	function pageContent($id, $lang = false) {
		$i = 0;
		$arr = array();
		if ($lang > 0) {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'uid, header, colPos',
							'tt_content',
							'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'tt_content')
							. ' AND deleted = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0, 'tt_content')
							. ' AND hidden = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0, 'tt_content')
							. ' AND sys_language_uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($lang, 'tt_content')
							. ' AND t3ver_wsid != ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(-1, 'tt_content'),
							'',
							'sorting ASC',
							'');
		} else {
			$res1 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
							'uid, header, colPos',
							'tt_content',
							'pid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($id, 'tt_content')
							. ' AND deleted = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0, 'tt_content')
							. ' AND hidden = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0, 'tt_content')
							. ' AND sys_language_uid = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(0, 'tt_content')
							. ' AND t3ver_wsid != ' . $GLOBALS['TYPO3_DB']->fullQuoteStr(-1, 'tt_content')
							. ' AND ' . $this->queryStartEndTime('tt_content'),
							'',
							'sorting ASC',
							'');
		}
		while ($row1 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res1)) {
			$arr[$i]['uid'] = $row1['uid'];
			$arr[$i]['header'] = $row1['header'];
			$arr[$i]['colPos'] = $row1['colPos'];
			$i++;
		}
		if ($i > 0) {
			return $arr;
		} else {
			return false;
		}
	}

	/**
	 * Get an array of all Keywords added in the TYPO3 page field of a page
	 * @param <int> $id
	 * @param <int> $lang
	 * @return <Array>
	 */
	function pageKeywordsList($id, $lang = false) {
		$pageInfo = $this->pageInfo($id, $lang);
		$keywords = explode(',', $pageInfo['keywords']);
		if (is_array($keywords)) {
			return $keywords;
		} else {
			return false;
		}
	}

	/**
	 * Replace Ampersand by equivalent HTML entity
	 * @param <String> $str
	 * @return <String>
	 */
	function formatString($str) {
		$str = preg_replace('/&/', '&amp;', $str);
		return $str;
	}

	/**
	 * Returns today's unix time stamp (day start)
	 * @return <type>
	 */
	function hTime() {
		$time = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
		return $time;
	}

	/**
	 * Get a humanreadable date format
	 * @param <String> $time
	 * @param <int> $lang
	 * @return string
	 */
	function hReturnFormatDate($time, $lang = false) {
		$date = date('d', $time) . '.' . date('m', $time) . '.' . date('Y', $time);
		if ($lang != 0) {
			$date = date('Y', $time) . '-' . date('m', $time) . '-' . date('d', $time);
		}
		return $date;
	}

	/**
	 * Returns ISO compatible Date
	 * @param <string> $time
	 * @return string
	 */
	function hReturnFormatDateSortable($time) {
		$date = date('Y', $time) . '-' . date('m', $time) . '-' . date('d', $time);
		return $date;
	}

	/**
	 * Get the Plugin Configuration for a Plugin
	 * @param string $pluginName
	 * @return Array
	 */
	function getPluginConf($pluginName) {
		$pluginName .= '.';
		$array = $GLOBALS['TSFE']->tmpl->setup['plugin.'][$pluginName];
		return $array;
	}

	/**
	 * debug output
	 * @param <type> $str
	 */
	function dPrint($str) {
		echo '<pre style="font-size: 11px; line-height: 0.8em; background-color: grey; color: white;">';
		print_r($str);
		echo '</pre>';
	}

}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/nkwlib/class.tx_nkwlib.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/nkwlib/class.tx_nkwlib.php']);
}
?>