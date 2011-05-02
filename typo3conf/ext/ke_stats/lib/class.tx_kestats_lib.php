<?php

/* * *************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2011 Christian Bülter <buelter@kennziffer.com>
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

/**
 * Shared library 'ke_stats' extension.
 *
 * @author	Christian Bülter <buelter@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_kestats
 */
class tx_kestats_lib {

	var $statData = array();
	var $timeFields = 'year,month';
	var $pagelist = '';
	var $backendModule_obj;

	/**
	 * tx_kestats_lib
	 *
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	function tx_kestats_lib() {/* {{{ */
		$this->now = time();
		$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ke_stats']);
		$this->extConf['asynchronousDataRefreshing'] = $this->extConf['asynchronousDataRefreshing'] ? 1 : 0;

		// enable backend module caching by default. If you upgrade from
		// older versions, this option may not be set in the extension manager.
		if (!isset($this->extConf['enableBackendModuleCaching'])) {
			$this->extConf['enableBackendModuleCaching'] = 1;
		}
		$this->extConf['enableBackendModuleCaching'] = $this->extConf['enableBackendModuleCaching'] ? 1 : 0;
	}

	/* }}} */

	/**
	 * Increases a statistics counter for the given $category.
	 * $compareFieldList is a comma-separated list.
	 *
	 * Takes into account if asynchronous data refreshing is activated and
	 * stores the data either in a queue table or updates it directly.
	 *
	 * @param string $category
	 * @param string $compareFieldList
	 * @param string $element_title
	 * @param int $element_uid
	 * @param int $element_pid
	 * @param int $element_language
	 * @param int $element_type
	 * @param string $stat_type
	 * @param int $parent_uid
	 * @param string $additionalData Additional data, must be processed by a custom hook.
	 * @param int $counter By what number should the statistic counter be increased? Default is 1.
	 * @access public
	 * @return void
	 */
	function increaseCounter($category, $compareFieldList, $element_title='', $element_uid=0, $element_pid=0, $element_language=0, $element_type=0, $stat_type=STAT_TYPE_PAGES, $parent_uid=0, $additionalData='', $counter = 1) {/* {{{ */

		// hook for individual modifications of the statistical data
		// before submitting it to the queue or updatign it directly
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_stats']['modifyStatDataBeforeQueue'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_stats']['modifyStatDataBeforeQueue'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->modifyStatDataBeforeQueue(
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
						$this
				);
			}
		}

		// if asynchronous data refreshing is activated, store the the data
		// which should be counted at this point into a queue table. If not,
		// process the data (update the counter).
		if (!$this->extConf['asynchronousDataRefreshing']) {

			$this->updateStatisticsTable(
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
					$counter);
		} else {

			// compile data for the queue
			$dataArray = array(
				'category' => $category,
				'compareFieldList' => $compareFieldList,
				'element_title' => $element_title,
				'element_uid' => $element_uid,
				'element_pid' => $element_pid,
				'element_language' => $element_language,
				'element_type' => $element_type,
				'stat_type' => $stat_type,
				'parent_uid' => $parent_uid,
				'additionalData' => $additionalData,
				'counter' => $counter
			);

			// insert into queue
			$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery(
							'tx_kestats_queue',
							array(
								'tstamp' => $this->now,
								'data' => serialize($dataArray),
								'generaldata' => serialize($this->statData)
							)
			);
		}
	}

	/* }}} */

	/**
	 * refreshOverviewPageData
	 *
	 * In future versions there will be an overview page with more data. This
	 * page will then stay in a cache and will be updated by a cron-cli-script.
	 * Right now, there are only visitors and pageviews in order to keep the
	 * rendering time fast.
	 *
	 * @param int $pageUid
	 * @access public
	 * @return void
	 */
	function refreshOverviewPageData($pageUid=0) {/* {{{ */
		$overviewPageData = array();

		// all languages and types will be shown in the overview page
		$element_language = -1;
		$element_type = -1;

		// get the subpages list
		if ($$pageUid) {
			$this->pagelist = strval($pageUid);
			$this->getSubPages($pageUid, $this->pagelist);
		}

		if ($pageUid) {
			$startTime = t3lib_div::milliseconds();

			// in the overview page we display 12 month
			$fromToArray['from_year'] = date('Y') - 1;
			$fromToArray['to_year'] = date('Y');
			$fromToArray['from_month'] = date('n');
			$fromToArray['to_month'] = date('n') - 1;

			// monthly process of pageviews
			$columns = 'element_title,counter';
			$pageviews = $this->getStatResults(STAT_TYPE_PAGES, CATEGORY_PAGES, $columns, STAT_ONLY_SUM, 'counter DESC', '', 0, $fromToArray, $element_language, $element_type);
			//$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_monthly'),$columns,$resultArray,'no_line_numbers','counter','');
			// monthly process of visitors
			$visits = $this->getStatResults(STAT_TYPE_PAGES, CATEGORY_VISITS_OVERALL, $columns, STAT_ONLY_SUM, 'counter DESC', '', 0, $fromToArray, $element_language, $element_type);

			// combine visits and pageviews
			$resultArray = array();
			for ($i = 0; $i < 13; $i++) {
				$pages_per_visit = $visits[$i]['counter'] ? round(floatval($pageviews[$i]['counter'] / $visits[$i]['counter']), 1) : '';
				$resultArray[$i] = array(
					'element_title' => $pageviews[$i]['element_title'],
					'pageviews' => $pageviews[$i]['counter'],
					'visits' => $visits[$i]['counter'],
					'pages_per_visit' => $pages_per_visit
				);
			}

			$overviewPageData['pageviews_and_visits'] = $resultArray;
			unset($pageviews);
			unset($visits);
			unset($resultArray);

			// For future versions:
			/*

			  // in the overview page we display the current month for the detailed listing
			  $fromToArray['from_year'] = date('Y');
			  $fromToArray['to_year'] = date('Y');
			  $fromToArray['from_month'] = date('n');
			  $fromToArray['to_month'] = date('n');

			  // pageviews of the current month
			  $columns = 'element_title,element_uid,counter';
			  $overviewPageData['pageviews_current_month'] = $this->getStatResults(STAT_TYPE_PAGES, CATEGORY_PAGES, $columns, 0, 'counter DESC', '', 0, $fromToArray, $element_language, $element_type);

			  // referers, external websites
			  $columns = 'element_title,counter';
			  $overviewPageData['referers_external_websites'] = $this->getStatResults(STAT_TYPE_PAGES, CATEGORY_REFERERS_EXTERNAL_WEBSITES, $columns, 0, 'counter DESC', '', 0, $fromToArray, $element_language, $element_type);

			  // search words
			  $columns = 'element_title,counter';
			  $overviewPageData['search_words'] = $this->getStatResults(STAT_TYPE_PAGES, CATEGORY_SEARCH_STRINGS, $columns, 0, 'counter DESC', '', 0, $fromToArray, $element_language, $element_type);

			  // extensions
			  // get extension types
			  $extensionTypes= array();
			  $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('category','tx_kestats_statdata','type=\''.STAT_TYPE_EXTENSION.'\' AND year='. date('Y') . ' AND month=' . date('m'), 'category');
			  if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
			  while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			  $extensionTypes[] = $row['category'];
			  }
			  }

			  // save extension list
			  $overviewPageData['extensionlist'] = implode(',', $extensionTypes);

			  // get extension data
			  $columns = 'element_title,counter';
			  foreach ($extensionTypes as $extensionType) {
			  //$overviewPageData['extension_' . $extensionType] = $this->getStatResults(STAT_TYPE_EXTENSION, $extensionType, $columns, 0, 'counter DESC', '', 0, $fromToArray, $element_language, $element_type);
			  }

			 */

			// some time information ...
			$runningTime = round((t3lib_div::milliseconds() - $startTime) / 1000, 1);
			// $overviewPageData['info'] = '<p class="update_information">' . $GLOBALS['LANG']->getLL('last_update') . date(UPDATED_UNTIL_DATEFORMAT);
			// $overviewPageData['info'] .= ' in ' . $runningTime . ' s.<p>';
			$overviewPageData['tstamp'] = time();
		}
		return $overviewPageData;
	}

	/* }}} */

	/**
	 * Returns an array with statistical data of a certain time period.
	 *
	 * @param string $statType: type of the statistic. default ist pages, but may also be for example an extension key.
	 * @param string $statCategory: category, used to determine further differences with in the statistic type
	 * @param string $indexField: field, which makes up the index, should be unique
	 * @param string $columns: fields to display in the list
	 * @param string $groupBy: group fields (commalist of database field names)
	 * @param string $encode_title_to_utf8: set to 1 if the title in the result table has to be encoded to utf-8. The function checks for itself, if the backend is set to utf-8 and only then encodes the value.
	 * @param int $onlySum: display only the sum of each month or the whole list for a certain time period (which is normally a single month)?
	 * @param array $fromToArray: contains the time period for which the statistical data shoud be generated (year and month from and to). If empty, it will be populated automatically within the function.
	 * @param int $element_language
	 * @param int $element_type
	 * @return array
	 */
	function getStatResults($statType='pages', $statCategory, $columns, $onlySum=0, $orderBy='counter DESC', $groupBy='', $encode_title_to_utf8=0, $fromToArray=array(), $element_language=0, $element_type=0) {/* {{{ */
		$resultArray = array();
		$yearArray = $this->getDateArray($fromToArray['from_year'], $fromToArray['from_month'], $fromToArray['to_year'], $fromToArray['to_month']);

		// read the stat data into an array
		$lineCounter = 0;
		foreach ($yearArray as $year => $monthArray) {
			foreach ($monthArray as $month => $daysPerMonth) {

				// if we are dealing with data of a month in the past, we may use the cache
				if ($year < date('Y') || ($year == date('Y') && $month < date('m'))) {
					$useCache = true;
				} else {
					$useCache = false;
				}

				// enable backend cache only if set in extension manager configuration
				if (!$this->extConf['enableBackendModuleCaching']) {
					$useCache = false;
				}

				$where_clause = 'type=\'' . $statType . '\'';
				$where_clause .= ' AND category=\'' . $statCategory . '\'';
				$where_clause .= ' AND year=' . $year . '';
				$where_clause .= ' AND month=' . $month . '';
				if ($element_language >= 0) {
					$where_clause .= ' AND element_language=' . $element_language;
				}
				if ($element_type >= 0) {
					$where_clause .= ' AND element_type=' . $element_type;
				}

				// the query to filter the elements based on the selected page in the pagetree
				// extension elements are filtered by their pid
				if (strlen($this->pagelist) > 0) {
					if ($statType == STAT_TYPE_EXTENSION) {
						$subpages_query = ' AND tx_kestats_statdata.element_pid IN (' . $this->pagelist . ')';
					} else {
						$subpages_query = ' AND tx_kestats_statdata.element_uid IN (' . $this->pagelist . ')';
					}
				} else {
					$subpages_query = '';
				}
				$where_clause .= $subpages_query;

				// hook for custom db query
				if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_stats']['getStatResultsDBQuery'])) {
					foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_stats']['getStatResultsDBQuery'] as $_classRef) {
						$_procObj = & t3lib_div::getUserObj($_classRef);
						$where_clause = $_procObj->getStatResultsDBQuery($where_clause, $this);
					}
				}

				if ($useCache) {
					// is there a cache entry?
					// if yes, use this instead of really querying the stats-table
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_kestats_cache',
									'whereclause=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($where_clause, 'tx_kestats_cache')
									. ' AND groupby=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($groupBy, 'tx_kestats_cache')
									. ' AND orderby=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($orderBy, 'tx_kestats_cache'));

					if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
						$cacheRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
						$rows = t3lib_div::xml2array($cacheRow['result']);

						// found cache
						if (!is_array($rows)) {

							// cache is invalid
							$useCache = false;
						}
						unset($cacheRow);
					} else {

						$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tx_kestats_statdata', $where_clause, $groupBy, $orderBy);

						// write the result to the cache
						if (count($rows) && $this->extConf['enableBackendModuleCaching']) {
							$result = t3lib_div::array2xml($rows);

								// cache entries may get quite big ...
							$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_kestats_cache', array(
								'whereclause' => $where_clause,
								'groupby' => $groupBy,
								'orderby' => $orderBy,
								'result' => $result
							));
						}
					}
				}

				if (!$useCache) {
					$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'tx_kestats_statdata', $where_clause, $groupBy, $orderBy);
				}

				$sum = 0;

				// render brackets around the year in CSV mode (otherwise excel doesn't like it)
				$rowIndex = $GLOBALS['LANG']->getLL('month_' . $month);
				if ($this->csvOutput) {
					$rowIndex .= ' (' . $year . ')';
				} else {
					$rowIndex .= ' ' . $year;
				}

				if (!$onlySum) {
					$lineCounter = 0;
				}

				if (count($rows)) {
					foreach ($rows as $row) {

						// do we want only the sum of all fields?
						if ($onlySum) {
							$sum += $row['counter'];
						} else {

							// check, if the title matches a title we had already before,
							// then just increase that row.
							// This happens for example, when we have entries for one hour, which occured on different days.
							// In this case, we have more than one entry in the database for the same row in the result table.
							// We always have the two columns element_title and counter.
							// So we can access them here directly.
							$element_already_counted = 0;
							for ($i = 0; $i <= $lineCounter; $i++) {
								if ($resultArray[$i]['element_title'] == $row['element_title']
										&& $resultArray[$i]['element_language'] == $row['element_language']
										&& $resultArray[$i]['element_type'] == $row['element_type']
								) {
									$resultArray[$i]['counter'] += $row['counter'];
									$element_already_counted = 1;
								}
							}

							// Add all columns we want to display to the result
							// table (this will be at least element_title and column)
							if (!$element_already_counted) {
								$lineCounter++;
								// UTF-8 for search words
								if (strtolower($GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset']) == 'utf-8' && $encode_title_to_utf8) {
									$row['element_title'] = utf8_encode($row['element_title']);
								}
								foreach (explode(',', $columns) as $field) {
									$resultArray[$lineCounter][$field] = $row[$field];
								}
								$resultArray[$lineCounter]['element_language'] = $row['element_language'];
								$resultArray[$lineCounter]['element_type'] = $row['element_type'];
							}
						}
					}
				}

				if ($onlySum) {
					$resultArray[$lineCounter]['element_title'] = $rowIndex;
					$resultArray[$lineCounter]['counter'] = $sum;
					$lineCounter++;
				}
			}
		}

		return $resultArray;
	}

	/* }}} */

	/**
	 * getSubPages
	 *
	 * returns commalist of all subpages of a given page
	 * works recursive
	 * Does explicitly not check for hidden pages and restricted access!
	 *
	 * @param int $page_uid
	 * @access public
	 * @return void
	 */
	function getSubPages($page_uid=0) {/* {{{ */
		if ($page_uid) {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'pages', 'pid=' . intval($page_uid));
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				if (strlen($this->pagelist) > 0) {
					$this->pagelist .= ',';
				}
				$this->pagelist .= $row['uid'];
				$this->getSubPages($row['uid']);
			}
		} else {
			$this->pagelist = '';
		}
	}

	/* }}} */

	/**
	 * getDateArray
	 *
	 * returns an array with the years, month and day of the given period
	 *
	 * @param int $from_year
	 * @param int $from_month
	 * @param int $to_year
	 * @param int $to_month
	 * @access public
	 * @return array
	 * @author Christoph Bl�mer <info@christoph-bloemer.de>
	 */
	function getDateArray($from_year, $from_month, $to_year, $to_month) {/* {{{ */
		$fromToArray = array();
		$fromToArray['from_year'] = $from_year;
		$fromToArray['to_year'] = $to_year;
		$fromToArray['from_month'] = $from_month;
		$fromToArray['to_month'] = $to_month;

		for ($j = $fromToArray['from_year']; $j <= $fromToArray['to_year']; $j++) {
			$fromToArray['from_year'] == $j ? $monat = $fromToArray['from_month'] : $monat = 1;
			$fromToArray['to_year'] == $j ? $monat2 = $fromToArray['to_month'] : $monat2 = 12;
			$dayPerMonth[$j] = array();
			if ($fromToArray['from_year'] == $fromToArray['to_year'] && $fromToArray['from_month'] == $fromToArray['to_month']) {
				$dayPerMonth[$fromToArray['from_year']][$fromToArray['from_month']] = date('t', mktime(0, 0, 0, $fromToArray['from_month'], 1, $fromToArray['from_year']));
				break;
			}
			for ($m = $monat; $m <= $monat2; $m++) {
				//Anzahl Tage des Monats
				$days = date('t', mktime(0, 0, 0, $m, 1, $j));
				//echo $days."<br />";
				//Wenn erster Monat
				if (date('Y', $time1) == $j && date('n', $time1) == $m) {
					$dayPerMonth[$j][$m] = $days - date('j', $time1) + 1;
					//echo "1<br />";
				} elseif (date('Y', $time2) == $j && date('n', $time2) == $m) {
					if (date('j', $time2) - 1 != 0) {
						$dayPerMonth[$j][$m] = date('j', $time2);
						//$dayPerMonth[$j][$m] = date('j',$time2)-1;
						//echo "2<br />";
					}
				} else {
					$dayPerMonth[$j][$m] = $days;
					//echo "3<br />";
				}
			}
		}

		return $dayPerMonth;
	}

	/* }}} */

	/**
	 * Increases a statistics counter.
	 * If no counter exists that matches all fields the $compareFieldList, a new one is created.
	 *
	 * @param mixed $category
	 * @param mixed $compareFieldList
	 * @param int $element_uid
	 * @param int $element_pid
	 * @param string $element_title
	 * @param int $element_language
	 * @param int $element_type
	 * @param string $stat_type
	 * @param int $parent_uid
	 * @param string $additionalData Additional data, must be processed by a custom hook.
	 * @param int $counter By what number should the statistic counter be increased? Default is 1.
	 * @access public
	 * @return void
	 */
	function updateStatisticsTable($category, $compareFieldList, $element_title='', $element_uid=0, $element_pid=0, $element_language=0, $element_type=0, $stat_type=STAT_TYPE_PAGES, $parent_uid=0, $additionalData='', $counter = 1) {

		// check if there is already an entry for this combination of
		// statistical data. Takes compareFieldList into account
		$statEntry = $this->getStatEntry(
						$category,
						$compareFieldList,
						$element_uid,
						$element_pid,
						$element_title,
						$element_language,
						$element_type,
						$stat_type,
						$parent_uid,
						$additionalData
		);

		// create a new entry if the data is unique, or this entry referers to another (user tracking)
		if (count($statEntry) == 0 || $parent_uid > 0) {

			// generate new counter
			$insertFields = array();
			$insertFields['type'] = $stat_type;
			$insertFields['category'] = $category;
			$insertFields['element_uid'] = $element_uid;
			$insertFields['element_pid'] = $element_pid;
			$insertFields['element_title'] = $element_title;
			$insertFields['element_language'] = $element_language;
			$insertFields['element_type'] = $element_type;
			$insertFields['parent_uid'] = $parent_uid;
			$insertFields['tstamp'] = $this->now;
			$insertFields['crdate'] = $this->now;
			$insertFields['counter'] = $counter;

			// Set only the time fields which are necessary for this
			// category (those which are in the $compareFieldList)
			foreach (explode(',', $this->timeFields) as $field) {
				if (in_array($field, explode(',', $compareFieldList))) {
					$insertFields[$field] = $this->statData[$field];
				} else {
					$insertFields[$field] = -1;
				}
			}

			// hook for individual modifications of the statistical data
			// before creating a new counter (e.g. processing the additional data)
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_stats']['modifyStatDataBeforeNewCounter'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_stats']['modifyStatDataBeforeNewCounter'] as $_classRef) {
					$_procObj = & t3lib_div::getUserObj($_classRef);
					$insertFields = $_procObj->modifyStatDataBeforeNewCounter($insertFields, $additionalData, $this);
				}
			}

			$res = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_kestats_statdata', $insertFields);
			unset($insertFields);
		} else {

			// increase existing counter
			$updateFields = array();
			$updateFields['counter'] = $statEntry['counter'] + $counter;
			$updateFields['tstamp'] = $this->now;
			$where_clause = 'uid = ' . $statEntry['uid'];
			$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_kestats_statdata', $where_clause, $updateFields);
			unset($updateFields);
		}
	}

	/**
	 * Returns the UID of an enty in the data table matching the $compareFieldList (comma-separated list).
	 * If there is no matching Entry, it returns -1.
	 *
	 * @param mixed $category
	 * @param mixed $compareFieldList
	 * @param int $element_uid
	 * @param int $element_pid
	 * @param string $element_title
	 * @param int $element_language
	 * @param int $element_type
	 * @param string $stat_type
	 * @param string $additionalData Additional data, must be processed by a custom hook.
	 * @return void
	 */
	function getStatEntry($category, $compareFieldList, $element_uid, $element_pid, $element_title, $element_language, $element_type, $stat_type, $parent_uid, $additionalData) {/* {{{ */
		$statEntry = array();
		$compareData = $this->statData;
		$compareData['element_uid'] = $element_uid;
		$compareData['element_pid'] = $element_pid;
		$compareData['element_title'] = $element_title;
		$compareData['element_language'] = $element_language;
		$compareData['element_type'] = $element_type;

		$where_clause = ' type=\'' . $stat_type . '\'';
		$where_clause .= ' AND category=\'' . $category . '\'';

		// hook for individual modifications of the data
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_stats']['modifyGetStatEntryWhereClause'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_stats']['modifyGetStatEntryWhereClause'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$_procObj->modifyGetStatEntryWhereClause(
						$compareFieldList,
						$parent_uid,
						$additionalData,
						$where_clause,
						$compareData,
						$this
				);
			}
		}

		// loop through the fields defined in compareFieldList
		foreach (explode(',', $compareFieldList) as $field) {
			// is the field a string field, or an integer?
			if (in_array($field, array('element_title', 'type'))) {
				// string field
				$where_clause .= ' AND ' . $field . '=\'' . $compareData[$field] . '\'';
			} else {
				// integer field
				$where_clause .= ' AND ' . $field . '=' . $compareData[$field];
			}
		}
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,counter', 'tx_kestats_statdata', $where_clause);

		// any results?
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)) {
			$statEntry = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		}

		return $statEntry;
	}

	/* }}} */

	/**
	 * getOldestQueueEntry
	 * find and return the oldest entry in the queue table
	 *
	 * @access public
	 * @return array or false
	 */
	function getOldestQueueEntry() {/* {{{ */
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_kestats_queue', '1=1', '', 'uid ASC', '1');
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
			$result = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
		} else {
			$result = false;
		}
		return $result;
	}

	/* }}} */

	/**
	 * debugMail
	 *
	 * Sends a html mail with debug information
	 *
	 * @param string $email
	 * @param string $content
	 * @param string $subject
	 * @access public
	 * @return void
	 */
	function debugMail($email='', $content='', $subject = 'TYPO3 tx_kestats_lib DEBUG') {/* {{{ */
		if (is_array($content)) {
			$content = t3lib_div::view_array($content);
		}

		$header = "MIME-Version: 1.0\n";
		$header .= "Content-type: text/html; charset=utf-8\n";
		$header .= "From: ke_stats DEBUG\n";

		mail($email, $subject, $content, $header);
	}

	/* }}} */
}

?>
