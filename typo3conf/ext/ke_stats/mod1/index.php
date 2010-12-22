<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2009 Christian Bülter <buelter@kennziffer.com>
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

define('UPDATED_UNTIL_DATEFORMAT', 'd.m.Y, H:i:s');

	// DEFAULT initialization of a module [BEGIN]
unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');

$LANG->includeLLFile('EXT:ke_stats/mod1/locallang.xml');
require_once(PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]

require_once('backendmenu.class.php');
require_once('../inc/constants.inc.php');

// Classes for access to the frontend TSconfig
require_once(PATH_t3lib.'class.t3lib_page.php');
require_once(PATH_t3lib.'class.t3lib_tstemplate.php');
require_once(PATH_t3lib.'class.t3lib_tsparser_ext.php');

// shared library
require_once(PATH_BE_KESTATS.'lib/class.tx_kestats_lib.php');

/**
 * Module 'Statistics' for the 'ke_stats' extension.
 *
 * @author	Christian Bülter <buelter@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_kestats
 */
class  tx_kestats_module1 extends t3lib_SCbase {
	var $pageinfo;
	var $tablename = 'tx_kestats_statdata';
	var $tablenameCache = 'tx_kestats_cache';
	var $tablenameQueue = 'tx_kestats_queue';
	var $maxLengthURLs = 80;
	var $maxLengthTableContent = 80;
	var $showTrackingResultNumbers = array(10 => '10', 50 => '50', 100 => '100', 200 => '200');
	var $csvContent = array();
	var $currentRowNumber = 0;
	var $currentColNumber = 0;

	var $csvDateFormat = 'd.m.Y';
	var $decimalChar = ',';
	var $overviewPageData = array();

	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{/*{{{*/
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();

			// instantiate the shared library
		$this->kestatslib = t3lib_div::makeInstance('tx_kestats_lib');

			// introduce the backend module to the shared library
		$this->kestatslib->backendModule_obj = $this;

			// get the subpages list
		if ($this->id) {
			$this->kestatslib->pagelist = strval($this->id);
			$this->kestatslib->getSubPages($this->id, $this->pagelist);
		}

			// load the frontend TSconfig
		$this->loadFrontendTSconfig($this->id, 'tx_kestats_pi1');

			// init the first csv-content row
		$this->csvContent[0] = array();

			// check, if we should render a csv-table
		$this->csvOutput = (t3lib_div::_GET('format') == 'csv') ? true : false;

		// get the module TSconfig
		// $this->modConf = t3lib_BEfunc::getModTSconfig($this->id);

		/*
		if (t3lib_div::_GP('clear_all_cache'))	{
			$this->include_once[] = PATH_t3lib.'class.t3lib_tcemain.php';
		}
		*/
	}/*}}}*/

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()	{/*{{{*/
		global $LANG;
		$this->MOD_MENU = Array (
			'function' => Array (
				'1' => $LANG->getLL('function1'),
				'2' => $LANG->getLL('function2'),
				'3' => $LANG->getLL('function3'),
			)
		);
		parent::menuConfig();
	}/*}}}*/

	/**
	 * Main function of the module.
	 *
	 * @return	string
	 */
	function main()	{/*{{{*/
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

			// Access check!
			// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;

			// || ($BE_USER->user['admin'] && !$this->id)
		if (($this->id && $access))	{

				// Draw the header.
			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;

				// initialize tab menu
			$this->tabmenu = t3lib_div::makeInstance('backendMenu');
			$this->doc->inDocStylesArray['tab_menu'] = $this->tabmenu->getStyleSheet();

				// initialize table
			$this->doc->inDocStylesArray['tables'] = $this->getTableCSS();

				// Add css: Use the the available space in the backend
			$this->doc->inDocStyles = 'div.typo3-mediumDoc { width:90%; }';

				// include prototype and flotr for chart rendering
			$this->doc->JScode .= '<script language="javascript" type="text/javascript" src="../flotr/lib/prototype-1.6.0.2.js"></script>';
			$this->doc->JScode .= '<!--[if IE]><script language="javascript" type="text/javascript" src="../flotr/lib/excanvas.js"></script><![endif]-->';
			$this->doc->JScode .= '<!--[if IE]><script language="javascript" type="text/javascript" src="../flotr/lib/base64.js"></script><![endif]-->';
			$this->doc->JScode .= '<script language="javascript" type="text/javascript" src="../flotr/lib/canvas2image.js"></script>';
			$this->doc->JScode .= '<script language="javascript" type="text/javascript" src="../flotr/lib/canvastext.js"></script>';
			$this->doc->JScode .= '<script language="javascript" type="text/javascript" src="../flotr/flotr-0.2.0-alpha.js"></script>';

				// set some additional styles
			//$this->doc->form='<form action="" method="POST">';

			// JavaScript
			/*
			   $this->doc->JScode = '
			   <script language="javascript" type="text/javascript">
			   script_ended = 0;
			   function jumpToUrl(URL)	{
			   document.location = URL;
			   }
			   </script>
			   ';
			   $this->doc->postCode='
			   <script language="javascript" type="text/javascript">
			   script_ended = 1;
			   if (top.fsMod) top.fsMod.recentIds["web"] = 0;
			   </script>
			   ';
			 */

			$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

				// Init tab menus
			$this->tabmenu->initMenu('type','overview');
			$now = time();
			$this->tabmenu->initMenu('month',date('n',$now));
			$this->tabmenu->initMenu('year',date('Y',$now));
			$this->tabmenu->initMenu('element_language',-1);
			$this->tabmenu->initMenu('element_type',-1);

				// hook for custom initializations
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_stats']['backendModuleInit'])) {
				foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_stats']['backendModuleInit'] as $_classRef) {
					$_procObj = & t3lib_div::getUserObj($_classRef);
					$_procObj->backendModuleInit($this);
				}
			}

				// render chart in overview mode
			if ($this->tabmenu->getSelectedValue('type') == 'overview') {

					// get the for the overview page data
				$this->overviewPageData = $this->kestatslib->refreshOverviewPageData($this->id);

					// render chart using flotr library
					// http://solutoire.com/flotr/
				$this->doc->JScode .= '
				   <script language="javascript" type="text/javascript">

						function monthTickFormatter(inputNumber) {
							output = inputNumber;
							switch (inputNumber) {
								case "0": output = \'' . $this->overviewPageData['pageviews_and_visits'][0]['element_title'] . '\'; break;
								case "1": output = \'' . $this->overviewPageData['pageviews_and_visits'][1]['element_title'] . '\'; break;
								case "2": output = \'' . $this->overviewPageData['pageviews_and_visits'][2]['element_title'] . '\'; break;
								case "3": output = \'' . $this->overviewPageData['pageviews_and_visits'][3]['element_title'] . '\'; break;
								case "4": output = \'' . $this->overviewPageData['pageviews_and_visits'][4]['element_title'] . '\'; break;
								case "5": output = \'' . $this->overviewPageData['pageviews_and_visits'][5]['element_title'] . '\'; break;
								case "6": output = \'' . $this->overviewPageData['pageviews_and_visits'][6]['element_title'] . '\'; break;
								case "7": output = \'' . $this->overviewPageData['pageviews_and_visits'][7]['element_title'] . '\'; break;
								case "8": output = \'' . $this->overviewPageData['pageviews_and_visits'][8]['element_title'] . '\'; break;
								case "9": output = \'' . $this->overviewPageData['pageviews_and_visits'][9]['element_title'] . '\'; break;
								case "10": output = \'' . $this->overviewPageData['pageviews_and_visits'][10]['element_title'] . '\'; break;
								case "11": output = \'' . $this->overviewPageData['pageviews_and_visits'][11]['element_title'] . '\'; break;
							}
							return output;
						}

						document.observe(\'dom:loaded\', function(){' . "\n";

					// Pageviews
				$this->doc->JScode .= 'var pageviews = [';
				$flotrDataArray = array();
				for ($i = 0; $i<12 ; $i++) {
					$flotrDataArray[$i] = '[' . $i . ', ' . $this->overviewPageData['pageviews_and_visits'][$i]['pageviews'] . ']';
				}
				$this->doc->JScode .= implode(',', $flotrDataArray);
				$this->doc->JScode .= ' ];' . "\n";

					// Visits
				$this->doc->JScode .= 'var visits = [';
				$flotrDataArray = array();
				for ($i = 0; $i<12 ; $i++) {
					$flotrDataArray[$i] = '[' . $i . ', ' . $this->overviewPageData['pageviews_and_visits'][$i]['visits'] . ']';
				}
				$this->doc->JScode .= implode(',', $flotrDataArray);
				$this->doc->JScode .= ' ];' . "\n";

					// Render
				$this->doc->JScode .= '
							var f = Flotr.draw($(\'container\'), [
								{ data:pageviews, label:\'' . $LANG->getLL('category_pages_all') . '\', color: \'#0000ff\', points:{show: true} },
								{ data:visits, label:\'' . $LANG->getLL('category_visits') . '\', color: \'#009933\', points:{show: true} }
							],
							{
								legend: { backgroundOpacity:0 },
								lines: { show:true, fill:true },
								xaxis: { tickFormatter: monthTickFormatter, tickDecimals: 0 },
								yaxis: { min:0 }
							}
							);

						});
				</script>';
			}

			$this->content .= $this->doc->startPage($LANG->getLL('title'));
			$this->content .= $this->doc->header($LANG->getLL('title'));
			//$this->content .= $this->doc->spacer(5);
			//$this->content .= $this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
			$this->content .= $this->doc->divider(5);

				// get the extension-manager configuration
			$this->extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['ke_stats']);
			$this->extConf['enableIpLogging'] = $this->extConf['enableIpLogging'] ? 1 : 0;
			$this->extConf['enableTracking'] = $this->extConf['enableTracking'] ? 1 : 0;
			$this->extConf['ignoreBackendUsers'] = $this->extConf['ignoreBackendUsers'] ? 1 : 0;
			$this->extConf['asynchronousDataRefreshing'] = $this->extConf['asynchronousDataRefreshing'] ? 1 : 0;

				// find out what types we have statistics for
				// extension elements are filtered by their pid
				//
				// C. B., 11.Jul.2008:
				// this is very slow, so we assume having every type available here
			/*
			$typesArray = array();
			$where = '('.$this->tablename.'.type=\'extension\' AND '.$this->tablename.'.element_pid IN ('.$this->kestatslib->pagelist.')'. ')';
			$where .= ' OR ('.$this->tablename.'.type!=\'extension\' AND '.$this->tablename.'.element_uid IN ('.$this->kestatslib->pagelist.')'. ')';
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('type',$this->tablename,$where,'type');

			if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$typesArray[$row['type']] = $LANG->getLL('type_'.$row['type']) ? $LANG->getLL('type_'.$row['type']) : $row['type'];
				}
			}

				// put "extensions" to the end of the array
				// (just for optical reasons)
			if ($typesArray['extension']) {
				$value = $typesArray['extension'];
				unset($typesArray['extension']);
				$typesArray['extension'] = $value;
			}
			*/

				// this is a lot faster, it just means that you get an empty table
				// on pages where you click on "extension" and there are no
				// elements
			$typesArray = array(
				'overview' => $LANG->getLL('overview'),
				STAT_TYPE_PAGES => $LANG->getLL('type_' . STAT_TYPE_PAGES),
				STAT_TYPE_EXTENSION => $LANG->getLL('type_' . STAT_TYPE_EXTENSION),
				'csvdownload' => $LANG->getLL('csvdownload')
			);

				// Put "Tracking" tab at the end display it only if tracking is activated
			if ($this->extConf['enableTracking']) {
				$typesArray[STAT_TYPE_TRACKING] = $LANG->getLL('type_' . STAT_TYPE_TRACKING);
			}

				// the query to filter the elements based on the selected page in the pagetree
				// extension elements are filtered by their pid
			if (strlen($this->kestatslib->pagelist) > 0) {
				if ($this->tabmenu->getSelectedValue('type') == STAT_TYPE_EXTENSION) {
					$this->subpages_query = ' AND '.$this->tablename.'.element_pid IN ('.$this->kestatslib->pagelist.')';
				} else {
					$this->subpages_query = ' AND '.$this->tablename.'.element_uid IN ('.$this->kestatslib->pagelist.')';
				}
			} else {
				$this->subpages_query = '';
			}

				// render tab menu: types
			$this->content .= $this->tabmenu->generateTabMenu($typesArray,'type');

				// Render menus only if we are not in the csvdownload-section
			if ($this->tabmenu->getSelectedValue('type') != 'overview' && $this->tabmenu->getSelectedValue('type') != 'csvdownload' && !$this->csvOutput) {

				if ($this->tabmenu->getSelectedValue('type') == STAT_TYPE_PAGES) {

						// Init tab menus
					$this->tabmenu->initMenu('list_type','list_monthly_process');
					$this->tabmenu->initMenu('list_type_category','category_pages');
					$this->tabmenu->initMenu('list_type_category_monthly','category_monthly_pages');
					$this->tabmenu->initMenu('category_pages',CATEGORY_PAGES);
					$this->tabmenu->initMenu('category_referers',CATEGORY_REFERERS_EXTERNAL_WEBSITES);
					$this->tabmenu->initMenu('category_time_type','category_time_hits');
					$this->tabmenu->initMenu('category_time_hits',CATEGORY_PAGES_OVERALL_DAY_OF_MONTH);
					$this->tabmenu->initMenu('category_time_visits',CATEGORY_VISITS_OVERALL_DAY_OF_MONTH);
					$this->tabmenu->initMenu('category_time_visits_feusers',CATEGORY_VISITS_OVERALL_FEUSERS_DAY_OF_MONTH);
					$this->tabmenu->initMenu('category_user_agents',CATEGORY_BROWSERS);
					$this->tabmenu->initMenu('category_other',CATEGORY_OPERATING_SYSTEMS);

						// render tab menu: monthly or details of one month
					$this->content .= $this->tabmenu->generateTabMenu(array(
								'list_monthly_process' => $LANG->getLL('list_full'),
								'list_details_of_a_month' => $LANG->getLL('list_details'),
								),'list_type');

					if ($this->tabmenu->getSelectedValue('list_type') == 'list_monthly_process') {
							// render tab menu: category
						$this->content .= $this->tabmenu->generateTabMenu(array(
									'category_monthly_pages' => $LANG->getLL('category_monthly_pages'),
									'category_monthly_pages_fe_users' => $LANG->getLL('category_monthly_pages_fe_users'),
									'category_monthly_visits' => $LANG->getLL('category_monthly_visits'),
									'category_monthly_visits_fe_users' => $LANG->getLL('category_monthly_visits_fe_users')
									),'list_type_category_monthly');
					} else if ($this->tabmenu->getSelectedValue('list_type') == 'list_details_of_a_month') {
							// render tab menu: category
						$this->content .= $this->tabmenu->generateTabMenu(array(
									'category_pages' => $LANG->getLL('category_pages'),
									'category_time' => $LANG->getLL('category_time'),
									'category_referers' => $LANG->getLL('category_referers'),
									'category_user_agents' => $LANG->getLL('category_user_agents'),
									'category_other' => $LANG->getLL('category_other')
									),'list_type_category');
						if ($this->tabmenu->getSelectedValue('list_type_category') == 'category_pages') {
								// render tab menu: pages
							$this->content .= $this->tabmenu->generateTabMenu(array(
										CATEGORY_PAGES => $LANG->getLL('category_pages_all'),
										CATEGORY_PAGES_FEUSERS => $LANG->getLL('category_pages_feusers')
										),'category_pages');
						}
						if ($this->tabmenu->getSelectedValue('list_type_category') == 'category_referers') {
								// render tab menu: referers
							$this->content .= $this->tabmenu->generateTabMenu(array(
										CATEGORY_REFERERS_EXTERNAL_WEBSITES => $LANG->getLL('category_referers_websites'),
										CATEGORY_REFERERS_SEARCHENGINES => $LANG->getLL('category_referers_search_engines'),
										CATEGORY_SEARCH_STRINGS => $LANG->getLL('category_search_strings')
										),'category_referers');
						}
						if ($this->tabmenu->getSelectedValue('list_type_category') == 'category_time') {
								// render tab menu: time
							$this->content .= $this->tabmenu->generateTabMenu(array(
										'category_time_hits' => $LANG->getLL('category_time_hits'),
										'category_time_visits' => $LANG->getLL('category_time_visits'),
										'category_time_visits_feusers' => $LANG->getLL('category_time_visits_feusers'),
										),'category_time_type');
							if ($this->tabmenu->getSelectedValue('category_time_type') == 'category_time_hits') {
									// render tab menu: time hits
								$this->content .= $this->tabmenu->generateTabMenu(array(
											CATEGORY_PAGES_OVERALL_DAY_OF_MONTH => $LANG->getLL('category_day_of_month'),
											CATEGORY_PAGES_OVERALL_DAY_OF_WEEK => $LANG->getLL('category_day_of_week'),
											CATEGORY_PAGES_OVERALL_HOUR_OF_DAY => $LANG->getLL('category_hour_of_day'),
											),'category_time_hits');
							} else if ($this->tabmenu->getSelectedValue('category_time_type') == 'category_time_visits') {
									// render tab menu: time visits
								$this->content .= $this->tabmenu->generateTabMenu(array(
											CATEGORY_VISITS_OVERALL_DAY_OF_MONTH => $LANG->getLL('category_visits_day_of_month'),
											CATEGORY_VISITS_OVERALL_DAY_OF_WEEK => $LANG->getLL('category_visits_day_of_week'),
											CATEGORY_VISITS_OVERALL_HOUR_OF_DAY => $LANG->getLL('category_visits_hour_of_day'),
											),'category_time_visits');
							} else if ($this->tabmenu->getSelectedValue('category_time_type') == 'category_time_visits_feusers') {
									// render tab menu: time visits logged-in
								$this->content .= $this->tabmenu->generateTabMenu(array(
											CATEGORY_VISITS_OVERALL_FEUSERS_DAY_OF_MONTH => $LANG->getLL('category_visits_day_of_month_feusers'),
											CATEGORY_VISITS_OVERALL_FEUSERS_DAY_OF_WEEK => $LANG->getLL('category_visits_day_of_week_feusers'),
											CATEGORY_VISITS_OVERALL_FEUSERS_HOUR_OF_DAY => $LANG->getLL('category_visits_hour_of_day_feusers'),
											),'category_time_visits_feusers');
							}
						}
						if ($this->tabmenu->getSelectedValue('list_type_category') == 'category_user_agents') {
								// render tab menu: user agents
							$this->content .= $this->tabmenu->generateTabMenu(array(
										CATEGORY_BROWSERS => $LANG->getLL('category_browsers'),
										CATEGORY_ROBOTS => $LANG->getLL('category_robots'),
										CATEGORY_UNKNOWN_USER_AGENTS => $LANG->getLL('category_unknown_user_agents'),
										),'category_user_agents');
						}
						if ($this->tabmenu->getSelectedValue('list_type_category') == 'category_other') {
								// render tab menu: other
							$this->content .= $this->tabmenu->generateTabMenu(array(
										CATEGORY_OPERATING_SYSTEMS => $LANG->getLL('category_operating_systems'),
										CATEGORY_IP_ADRESSES => $LANG->getLL('category_ip_addresses'),
										'category_hosts' => $LANG->getLL('category_hosts')
										),'category_other');
						}
					}
				} else if ($this->tabmenu->getSelectedValue('type') == STAT_TYPE_EXTENSION) {
						// render tabs for the different extensions
						// find out what extensions we have statistics for (db field "category")
					$extensionTypesArray = array();
					$this->allowedExtensionTypes = array();
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('category',$this->tablename,'type=\''.STAT_TYPE_EXTENSION.'\''.$this->subpages_query,'category');
					if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
						while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
								// get the tabname for the extension from page TSconfig
								// if it is not set, get it from Locallang or from the database itself
							$tabname = $LANG->getLL('extension_'.$row['category']) ? $LANG->getLL('extension_'.$row['category']) : $row['category'];
							$extensionTypesArray[$row['category']] = $tabname;
							$this->allowedExtensionTypes[] = $row['category'];
						}
					}

						// Init tab menus
					$this->tabmenu->initMenu('extension_type','');

						// render the extension types tabs
					$this->content .= $this->tabmenu->generateTabMenu($extensionTypesArray,'extension_type');
				}
			}

				// Add info about the statistic type to the csv table
				// Current page title
			if ($this->id) {
				$row = t3lib_BEfunc::getRecord('pages',$this->id);
				$pagetitle = t3lib_BEfunc::getRecordTitle('pages',$row,1);
				$this->content .= '<p style=\'margin-top:10px;\'>'.$LANG->getLL('statistics_for').' <strong>'.$pagetitle.'</strong> '.$LANG->getLL('and_subpages').'</p>';
				$this->addCsvCol($LANG->getLL('statistics_for').' '.$pagetitle.' '.$LANG->getLL('and_subpages'));
			} else {
				$this->content .= '<p style=\'margin-top:10px;\'>'.$LANG->getLL('all_pages').'</p>';
				$this->addCsvCol($LANG->getLL('all_pages'));
			}

				// description of the statistic type
			$description = t3lib_div::_GET('descr');

			if ($this->tabmenu->getSelectedValue('list_type') == 'list_details_of_a_month') {
				if (!empty($description)) {
					$description .= ' - ';
				}
				$description .= $GLOBALS['LANG']->getLL('csvdownload_statistics_for_month') . $monthArray[$month] = $GLOBALS['LANG']->getLL('month_'.$this->tabmenu->getSelectedValue('month')) . ' ' . $this->tabmenu->getSelectedValue('year');
				$description .= ', ' . $GLOBALS['LANG']->getLL('csvdownload_generated_on') . ' ' . date($this->csvDateFormat);
			}

			$this->addCsvCol($description);
			$this->addCsvRow();

				// Render links for CSV-Download
			if ($this->tabmenu->getSelectedValue('type') == 'csvdownload' && !$this->csvOutput) {
				$this->content .= '<div style=\'clear:both;\'>&nbsp;</div>';
				$this->content .= '<h2>' . $GLOBALS['LANG']->getLL('list_full_csv') . '</h2>';

				/*
				$content .= '<a ';
				$content .= 'href="index.php?id='.t3lib_div::_GET('id').'&type=pages&list_type_category_monthly=category_monthly_pages&type=pages&format=csv';
				$content .= '<h2>' . $GLOBALS['LANG']->getLL('list_details_csv') . '</h2>';
				$content .= '">';
				$content .= '</a>';
				*/

				$this->content .= $this->tabmenu->generateLinkMenu(
						array(
							'category_monthly_pages' => $LANG->getLL('category_monthly_pages'),
							'category_monthly_pages_fe_users' => $LANG->getLL('category_monthly_pages_fe_users'),
							'category_monthly_visits' => $LANG->getLL('category_monthly_visits'),
							'category_monthly_visits_fe_users' => $LANG->getLL('category_monthly_visits_fe_users')
						),
						'list_type_category_monthly',
						'&type=pages&format=csv&list_type=list_monthly_process'
					);

				$this->content .= '<div style="clear:both; margin-top:10px;">&nbsp;</div>';

				$this->content .= '<h2>' . $GLOBALS['LANG']->getLL('list_details_csv') . '</h2>';

					// Render the dropdown for selecting month and year
					// we use STAT_TYPE_PAGES here, which is certainly not correct for all statistic types, but will do the job
				$this->content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_PAGES);
				$this->content .= '<div style="clear:both;">&nbsp;</div>';

					// render menu: pages
				$this->content .= '<h3 style="clear:both;">' . $GLOBALS['LANG']->getLL('csvdownload_pages') . '</h3>';
				$defaultParams = '&type=pages&format=csv&list_type=list_details_of_a_month';
				$labelPrefix = $GLOBALS['LANG']->getLL('csvdownload_pages') . ' - ';
				$this->content .= $this->tabmenu->generateLinkMenu(
					array(
						CATEGORY_PAGES => $labelPrefix . $LANG->getLL('category_pages_all'),
						CATEGORY_PAGES_FEUSERS => $labelPrefix . $LANG->getLL('category_pages_feusers')
					),
					'category_pages',
					$defaultParams . '&list_type_category=category_pages'
				);

					// render tab menu: referers
				$this->content .= '<h3 style="clear:both;">' . $GLOBALS['LANG']->getLL('csvdownload_referer') . '</h3>';
				$labelPrefix = $GLOBALS['LANG']->getLL('csvdownload_referer') . ' - ';
				$this->content .= $this->tabmenu->generateLinkMenu(
					array(
						CATEGORY_REFERERS_EXTERNAL_WEBSITES => $labelPrefix . $LANG->getLL('category_referers_websites'),
						CATEGORY_REFERERS_SEARCHENGINES => $labelPrefix . $LANG->getLL('category_referers_search_engines'),
						CATEGORY_SEARCH_STRINGS => $labelPrefix . $LANG->getLL('category_search_strings')
					),
					'category_referers',
					$defaultParams . '&list_type_category=category_referers'
				);

					// render tab menu: time hits
				$this->content .= '<h3 style="clear:both;">' . $GLOBALS['LANG']->getLL('csvdownload_list_time_hits') . '</h3>';
				$labelPrefix = $GLOBALS['LANG']->getLL('csvdownload_list_time_hits') . ' - ';
				$this->content .= $this->tabmenu->generateLinkMenu(
					array(
						CATEGORY_PAGES_OVERALL_DAY_OF_MONTH =>  $labelPrefix . $LANG->getLL('category_day_of_month'),
						CATEGORY_PAGES_OVERALL_DAY_OF_WEEK => $labelPrefix . $LANG->getLL('category_day_of_week'),
						CATEGORY_PAGES_OVERALL_HOUR_OF_DAY => $labelPrefix . $LANG->getLL('category_hour_of_day'),
					),
					'category_time_hits',
					$defaultParams . '&list_type_category=category_time&category_time_type=category_time_hits'
				);

					// render tab menu: time visits
				$this->content .= '<h3 style="clear:both;">' . $GLOBALS['LANG']->getLL('csvdownload_list_time_visits') . '</h3>';
				$labelPrefix = $GLOBALS['LANG']->getLL('csvdownload_list_time_visits') . ' - ';
				$this->content .= $this->tabmenu->generateLinkMenu(
					array(
						CATEGORY_VISITS_OVERALL_DAY_OF_MONTH => $labelPrefix . $LANG->getLL('category_visits_day_of_month'),
						CATEGORY_VISITS_OVERALL_DAY_OF_WEEK => $labelPrefix . $LANG->getLL('category_visits_day_of_week'),
						CATEGORY_VISITS_OVERALL_HOUR_OF_DAY => $labelPrefix . $LANG->getLL('category_visits_hour_of_day'),
					),
					'category_time_visits',
					$defaultParams . '&list_type_category=category_time&category_time_type=category_time_visits'
				);

					// render tab menu: time visits logged-in
				$this->content .= '<h3 style="clear:both;">' . $GLOBALS['LANG']->getLL('csvdownload_list_time_visits_feusers') . '</h3>';
				$labelPrefix = $GLOBALS['LANG']->getLL('csvdownload_list_time_visits_feusers') . ' - ';
				$this->content .= $this->tabmenu->generateLinkMenu(
					array(
						CATEGORY_VISITS_OVERALL_FEUSERS_DAY_OF_MONTH => $labelPrefix . $LANG->getLL('category_visits_day_of_month_feusers'),
						CATEGORY_VISITS_OVERALL_FEUSERS_DAY_OF_WEEK => $labelPrefix . $LANG->getLL('category_visits_day_of_week_feusers'),
						CATEGORY_VISITS_OVERALL_FEUSERS_HOUR_OF_DAY => $labelPrefix . $LANG->getLL('category_visits_hour_of_day_feusers'),
					),
					'category_time_visits_feusers',
					$defaultParams . '&list_type_category=category_time&category_time_type=category_time_visits_feusers'
				);

				$this->content .= '<h3 style="clear:both;">' . $GLOBALS['LANG']->getLL('csvdownload_user_agents') . '</h3>';

					// render tab menu: user agents
				$this->content .= $this->tabmenu->generateLinkMenu(
					array(
						CATEGORY_BROWSERS => $LANG->getLL('category_browsers'),
						CATEGORY_ROBOTS => $LANG->getLL('category_robots'),
						CATEGORY_UNKNOWN_USER_AGENTS => $LANG->getLL('category_unknown_user_agents'),
						),
					'category_user_agents',
					$defaultParams . '&list_type_category=category_user_agents'
				);

				$this->content .= '<h3 style="clear:both;">' . $GLOBALS['LANG']->getLL('csvdownload_more_statistics') . '</h3>';

					// render tab menu: other

					// display ip related options only if ip-logging is enabled
				$linkArray = array( CATEGORY_OPERATING_SYSTEMS => $LANG->getLL('category_operating_systems'));
				if ($this->extConf['enableIpLogging']) {
					$linkArray[CATEGORY_IP_ADRESSES ] = $LANG->getLL('category_ip_addresses');
					$linkArray['category_hosts'] = $LANG->getLL('category_hosts');
				}
				$this->content .= $this->tabmenu->generateLinkMenu(
					$linkArray,
					'category_other',
					$defaultParams . '&list_type_category=category_other'
				);
			}

				// Render content
			$this->content .= $this->moduleContent();

				// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
			}

			$this->content.=$this->doc->spacer(10);
		} else {
				// If no access or if ID == zero

			$this->doc = t3lib_div::makeInstance('mediumDoc');
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$LANG->getLL('please_select_page');
			$this->content.=$this->doc->spacer(10);
		}
	}/*}}}*/

	/**
	 * renderOverviewPage
	 *
	 * Renders the overview for the current page.
	 * Wrapper for the function in kestatslib.
	 *
	 * @access public
	 * @return void
	 */
	function renderOverviewPage() {/*{{{*/
		$content = '';

		// div for chart rendering (using flotr)
		$content .=  "<div id=\"container\" style=\"width:600px;height:300px;\"></div>";
		$content .= $this->overviewPageData['info'];

		// monthly progress, combined table
		$content .= $this->renderTable($GLOBALS['LANG']->getLL('overview_pageviews_and_visits_monthly'), 'element_title,pageviews,visits,pages_per_visit', $this->overviewPageData['pageviews_and_visits'], 'no_line_numbers', '', '');

		// for future versions:
		/*
		// pageviews of current month, top 10
		$content .= $this->renderTable($GLOBALS['LANG']->getLL('overview_pageviews_current_month'), 'element_title,element_uid,counter', $this->overviewPageData['pageviews_current_month'], '', '', '', 10);

		// referers, external websites, top 10
		$content .= $this->renderTable($GLOBALS['LANG']->getLL('overview_referers_external_websites'), 'element_title,counter', $this->overviewPageData['referers_external_websites'], 'url', '', '', 10);

		// search words, top 10
		$content .= $this->renderTable($GLOBALS['LANG']->getLL('overview_search_words'), 'element_title,counter', $this->overviewPageData['search_words'], '', '', '', 10);
		*/

		return $content;
	}/*}}}*/

	/**
	 * Returns a selectorboxes for month/year/language/type for the given data
	 *
	 * @param array $statType
	 * @param array $statCategory
	 * @return string
	 */
	function renderSelectorMenu($statType,$statCategory) {/*{{{*/
		$content = '';
		$fromToArray = $this->getFirstAndLastEntries($statType,$statCategory);

		// generate the year and the month-array
		// generate a list of allowed values for the years an the months

			// render all years for which data exists
		$yearArray = array();
		$this->allowedYears = array();
		for ($year = $fromToArray['from_year']; $year<=$fromToArray['to_year']; $year++) {
			$yearArray[$year] = $year;
			$this->allowedYears[] = $year;
		}

			// render only months for which data exists
		$monthArray = array();
		$this->allowedMonths = array();
		for ($month = 1; $month<=12; $month++) {
			if ($this->tabmenu->getSelectedValue('year') == $fromToArray['from_year'] && $fromToArray['from_year']== $fromToArray['to_year']) {
				if ($month >= $fromToArray['from_month'] && $month <= $fromToArray['to_month']) {
					$monthArray[$month] = $GLOBALS['LANG']->getLL('month_'.$month);
					$this->allowedMonths[] = $month;
				}
			} else if ($this->tabmenu->getSelectedValue('year') == $fromToArray['from_year']) {
				if ($month >= $fromToArray['from_month']) {
					$monthArray[$month] = $GLOBALS['LANG']->getLL('month_'.$month);
					$this->allowedMonths[] = $month;
				}
			} else if ($this->tabmenu->getSelectedValue('year') == $fromToArray['to_year']) {
				if ($month <= $fromToArray['to_month']) {
					$monthArray[$month] = $GLOBALS['LANG']->getLL('month_'.$month);
					$this->allowedMonths[] = $month;
				}
			} else {
					// we are in a year in-between, so we display all months
				$monthArray[$month] = $GLOBALS['LANG']->getLL('month_'.$month);
				$this->allowedMonths[] = $month;
			}
		}

			// is there more than one element type?
		$where_clause = 'type=\''.$statType.'\'';
		$where_clause .= ' AND category=\''.$statCategory.'\'';
		$where_clause .= $this->subpages_query;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('element_type',$this->tablename,$where_clause,'element_type');
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 1) {
			$this->elementTypesArray = array();
			$this->elementTypesArray[-1] = $GLOBALS['LANG']->getLL('selector_type_all');
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$this->elementTypesArray[$row['element_type']] = $GLOBALS['LANG']->getLL('selector_type').' '.$row['element_type'];
			}
		}

			// is there more than one element language?
		$where_clause = 'type=\''.$statType.'\'';
		$where_clause .= ' AND category=\''.$statCategory.'\'';
		$where_clause .= $this->subpages_query;
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('element_language',$this->tablename,$where_clause,'element_language');
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 1) {
			$this->elementLanguagesArray = array();
			$this->elementLanguagesArray[-1] = $GLOBALS['LANG']->getLL('selector_language_all');
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				$this->elementLanguagesArray[$row['element_language']] = $this->getLanguageName($row['element_language']);
			}
		}

			// do the menu rendering
		$content .= $this->tabmenu->generateDropDownMenu($yearArray,'year');
		$content .= $this->tabmenu->generateDropDownMenu($monthArray,'month');
		if (is_array($this->elementTypesArray)) {
			$content .= $this->tabmenu->generateDropDownMenu($this->elementTypesArray,'element_type');
		}
		if (is_array($this->elementLanguagesArray)) {
			$content .= $this->tabmenu->generateDropDownMenu($this->elementLanguagesArray,'element_language');
		}

			// hook for additional menus
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_stats']['modifySelectorMenu'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['ke_stats']['modifySelectorMenu'] as $_classRef) {
				$_procObj = & t3lib_div::getUserObj($_classRef);
				$content = $_procObj->modifySelectorMenu($content, $this);
			}
		}

		return $content;
	}/*}}}*/

	/**
	 * returns the cleartext name of a language uid
	 *
	 * @param integer $sys_language_uid
	 * @return string
	 */
	function getLanguageName($sys_language_uid) {/*{{{*/
		// get the language name from sys_language
		if ($sys_language_uid == 0) {
			return $GLOBALS['LANG']->getLL('language_default');
		} else {
			$resLanguage = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title','sys_language','hidden=0 AND uid='.$sys_language_uid);
			$rowLanguage = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resLanguage);
			return $rowLanguage['title'];
		}
	}/*}}}*/

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{/*{{{*/
		$this->content.=$this->doc->endPage();
		if ($this->csvOutput) {
			$this->outputCSV();
		} else {
			echo $this->content;
		}
	}/*}}}*/

	/**
	 * Generates the main content (renders the statistics)
	 * returns the html content
	 *
	 * @return	string
	 */
	function moduleContent() {/*{{{*/
		$content = '';
		switch($this->tabmenu->getSelectedValue('type')) {

			// the overview page
			case 'overview':
				$content .= $this->renderOverviewPage();
			break;

			// default statistics for pages
			case STAT_TYPE_PAGES:
				switch($this->tabmenu->getSelectedValue('list_type')) {
					case 'list_details_of_a_month':
						switch($this->tabmenu->getSelectedValue('list_type_category')) {
							case 'category_pages':
								switch($this->tabmenu->getSelectedValue('category_pages')) {
									case CATEGORY_PAGES:
										$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_PAGES);
										$columns = 'element_title,element_uid,counter';
										$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_PAGES,$columns);
										$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages'),$columns,$resultArray);
										break;
									case CATEGORY_PAGES_FEUSERS:
										$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_PAGES_FEUSERS);
										$columns = 'element_title,element_uid,counter';
										$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_PAGES_FEUSERS,$columns);
										$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages'),$columns,$resultArray);
										break;
								}
							break;
							case 'category_time':
								switch($this->tabmenu->getSelectedValue('category_time_type')) {
									// PAGEVIEWS
									case 'category_time_hits':
										switch($this->tabmenu->getSelectedValue('category_time_hits')) {
											case CATEGORY_PAGES_OVERALL_DAY_OF_MONTH:
												$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_PAGES_OVERALL_DAY_OF_MONTH);
												$columns = 'element_title,counter';
												$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_PAGES_OVERALL_DAY_OF_MONTH,$columns,STAT_COMPLETE_LIST,'element_title');
												$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_day_of_month'),$columns,$resultArray,'no_line_numbers');
												break;
											case CATEGORY_PAGES_OVERALL_DAY_OF_WEEK:
												$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_PAGES_OVERALL_DAY_OF_WEEK);
												$columns = 'element_title,counter';
												$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_PAGES_OVERALL_DAY_OF_WEEK,$columns,STAT_COMPLETE_LIST,'element_title');
												$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_day_of_week'),$columns,$resultArray,'no_line_numbers,day_of_week');
												break;
											case CATEGORY_PAGES_OVERALL_HOUR_OF_DAY:
												$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_PAGES_OVERALL_HOUR_OF_DAY);
												$columns = 'element_title,counter';
												$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_PAGES_OVERALL_HOUR_OF_DAY,$columns,STAT_COMPLETE_LIST,'element_title');
												$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_hour_of_day'),$columns,$resultArray,'no_line_numbers,hour_of_day');
												break;
										}
										break;
									// VISITS
									case 'category_time_visits':
										switch($this->tabmenu->getSelectedValue('category_time_visits')) {
											case CATEGORY_VISITS_OVERALL_DAY_OF_MONTH:
												$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_VISITS_OVERALL_DAY_OF_MONTH);
												$columns = 'element_title,counter';
												$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_VISITS_OVERALL_DAY_OF_MONTH,$columns,STAT_COMPLETE_LIST,'element_title');
												$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_visits_day_of_month'),$columns,$resultArray,'no_line_numbers');
												break;
											case CATEGORY_VISITS_OVERALL_DAY_OF_WEEK:
												$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_VISITS_OVERALL_DAY_OF_WEEK);
												$columns = 'element_title,counter';
												$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_VISITS_OVERALL_DAY_OF_WEEK,$columns,STAT_COMPLETE_LIST,'element_title');
												$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_visits_day_of_week'),$columns,$resultArray,'no_line_numbers,day_of_week');
												break;
											case CATEGORY_VISITS_OVERALL_HOUR_OF_DAY:
												$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_VISITS_OVERALL_HOUR_OF_DAY);
												$columns = 'element_title,counter';
												$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_VISITS_OVERALL_HOUR_OF_DAY,$columns,STAT_COMPLETE_LIST,'element_title');
												$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_visits_hour_of_day'),$columns,$resultArray,'no_line_numbers,hour_of_day');
												break;
										}
										break;
									// VISITS OF LOGGED-IN USERS
									case 'category_time_visits_feusers':
										switch($this->tabmenu->getSelectedValue('category_time_visits_feusers')) {
											case CATEGORY_VISITS_OVERALL_FEUSERS_DAY_OF_MONTH:
												$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_VISITS_OVERALL_FEUSERS_DAY_OF_MONTH);
												$columns = 'element_title,counter';
												$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_VISITS_OVERALL_FEUSERS_DAY_OF_MONTH,$columns,STAT_COMPLETE_LIST,'element_title');
												$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_visits_day_of_month_feusers'),$columns,$resultArray,'no_line_numbers');
												break;
											case CATEGORY_VISITS_OVERALL_FEUSERS_DAY_OF_WEEK:
												$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_VISITS_OVERALL_FEUSERS_DAY_OF_WEEK);
												$columns = 'element_title,counter';
												$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_VISITS_OVERALL_FEUSERS_DAY_OF_WEEK,$columns,STAT_COMPLETE_LIST,'element_title');
												$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_visits_day_of_week_feusers'),$columns,$resultArray,'no_line_numbers,day_of_week');
												break;
											case CATEGORY_VISITS_OVERALL_FEUSERS_HOUR_OF_DAY:
												$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_VISITS_OVERALL_FEUSERS_HOUR_OF_DAY);
												$columns = 'element_title,counter';
												$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_VISITS_OVERALL_FEUSERS_HOUR_OF_DAY,$columns,STAT_COMPLETE_LIST,'element_title');
												$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_visits_hour_of_day_feusers'),$columns,$resultArray,'no_line_numbers,hour_of_day');
												break;
										}
										break;
								}
							break;
							case 'category_referers':
								switch($this->tabmenu->getSelectedValue('category_referers')) {
									case CATEGORY_REFERERS_EXTERNAL_WEBSITES:
										$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_REFERERS_EXTERNAL_WEBSITES);
										$columns = 'element_title,counter';
										$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_REFERERS_EXTERNAL_WEBSITES,$columns,STAT_COMPLETE_LIST);
										$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_referers_websites'),$columns,$resultArray,'url');
										break;
									case CATEGORY_REFERERS_SEARCHENGINES:
										$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_REFERERS_SEARCHENGINES);
										$columns = 'element_title,counter';
										$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_REFERERS_SEARCHENGINES,$columns,STAT_COMPLETE_LIST);
										$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_referers_search_engines'),$columns,$resultArray);
										break;
									case CATEGORY_SEARCH_STRINGS:
										$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_SEARCH_STRINGS);
										$columns = 'element_title,counter';
										$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_SEARCH_STRINGS,$columns,STAT_COMPLETE_LIST,'counter DESC','',1);
										$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_referers_searchwords'),$columns,$resultArray,'none');
									break;
								}
							break;
							case 'category_user_agents':
								switch($this->tabmenu->getSelectedValue('category_user_agents')) {
									case CATEGORY_BROWSERS:
										$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_BROWSERS);
										$columns = 'element_title,counter';
										$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_BROWSERS,$columns,STAT_COMPLETE_LIST);
										$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_user_agents_browsers'),$columns,$resultArray);
									break;
									case CATEGORY_ROBOTS:
										$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_ROBOTS);
										$columns = 'element_title,counter';
										$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_ROBOTS,$columns,STAT_COMPLETE_LIST);
										$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_user_agents_robots'),$columns,$resultArray);
									break;
									case CATEGORY_UNKNOWN_USER_AGENTS:
										$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_UNKNOWN_USER_AGENTS);
										$columns = 'element_title,counter';
										$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_UNKNOWN_USER_AGENTS,$columns,STAT_COMPLETE_LIST);
										$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_user_agents_unknown'),$columns,$resultArray);
									break;
								}
							break;
							case 'category_other':
								switch($this->tabmenu->getSelectedValue('category_other')) {
									case CATEGORY_OPERATING_SYSTEMS:
										$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_OPERATING_SYSTEMS);
										$columns = 'element_title,counter';
										$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_OPERATING_SYSTEMS,$columns,STAT_COMPLETE_LIST);
										$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_operating_systems'),$columns,$resultArray);
									break;
									case CATEGORY_IP_ADRESSES:
										// display note, if ip-logging is disabled
										if (!$this->extConf['enableIpLogging']) {
											$content .= '<p style="font-weight:bold;">' . $GLOBALS['LANG']->getLL('iplogging_is_disabled') . '</p>';
										}
										$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_IP_ADRESSES);
										$columns = 'element_title,counter';
										$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_IP_ADRESSES,$columns,STAT_COMPLETE_LIST);
										$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_ip_addresses'),$columns,$resultArray);
									break;
									case 'category_hosts':
										// display note, if ip-logging is disabled
										if (!$this->extConf['enableIpLogging']) {
											$content .= '<p style="font-weight:bold;">' . $GLOBALS['LANG']->getLL('iplogging_is_disabled') . '</p>';
										}
										$content .= $this->renderSelectorMenu(STAT_TYPE_PAGES,CATEGORY_IP_ADRESSES);
										$columns = 'element_title,counter';
										$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_IP_ADRESSES,$columns,STAT_COMPLETE_LIST);
										$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_hosts'),$columns,$resultArray,'hosts');
									break;
								}
							break;
						}
						break;
					default:
					case 'list_monthly_process':
						switch($this->tabmenu->getSelectedValue('list_type_category_monthly')) {
							case 'category_monthly_pages':
								$columns = 'element_title,counter';
								$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_PAGES,$columns,STAT_ONLY_SUM);
								$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_monthly'),$columns,$resultArray,'no_line_numbers','counter','');
							break;
							case 'category_monthly_pages_fe_users':
								$columns = 'element_title,counter';
								$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_PAGES_FEUSERS,$columns,STAT_ONLY_SUM);
								$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_monthly_fe_users'),$columns,$resultArray,'no_line_numbers','counter','');
							break;
							case 'category_monthly_visits':
								$columns = 'element_title,counter';
								$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_VISITS_OVERALL,$columns,STAT_ONLY_SUM);
								$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_visits_monthly'),$columns,$resultArray,'no_line_numbers','counter','');
							break;
							case 'category_monthly_visits_fe_users':
								$columns = 'element_title,counter';
								$resultArray = $this->getStatResults(STAT_TYPE_PAGES,CATEGORY_VISITS_OVERALL_FEUSERS,$columns,STAT_ONLY_SUM);
								$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_pages_visits_monthly_fe_users'),$columns,$resultArray,'no_line_numbers','counter','');
							break;
						}
				}
				$content .= $this->renderUpdateInformation();
				//$this->content.=$this->doc->section($GLOBALS['LANG']->getLL('type_pages'),$content,0,1);
			break;
			// user tracking statistics
			case STAT_TYPE_TRACKING:
				// init tab menus
				$this->tabmenu->initMenu('tracking_results_number',10);

				// render the selector menu
				foreach ($this->showTrackingResultNumbers as $key => $value) {
					$this->showTrackingResultNumbers[$key] = $value.' '.$GLOBALS['LANG']->getLL('show_entries_number');

				}

				// display note, if tracking is disabled
				if (!$this->extConf['enableTracking']) {
					$content .= '<p style="font-weight:bold;">' . $GLOBALS['LANG']->getLL('tracking_is_disabled') . '</p>';
				} else {
					$content .= $this->tabmenu->generateDropDownMenu($this->showTrackingResultNumbers,'tracking_results_number');

					// render the refresh link
					$content .= '<a href="JavaScript:location.reload(true);" class="buttonlink">'.$GLOBALS['LANG']->getLL('refresh').'</a>';

					// get the initial entries
					$where_clause = 'type='.$GLOBALS['TYPO3_DB']->fullQuoteStr(STAT_TYPE_TRACKING, $this->tablename);
					$where_clause .= ' AND category='.$GLOBALS['TYPO3_DB']->fullQuoteStr(CATEGORY_TRACKING_INITIAL, $this->tablename);
					$where_clause .= $this->subpages_query;

					// get the number of entries to display
					$number_of_entries = $this->tabmenu->getSelectedValue('tracking_results_number') ? $this->tabmenu->getSelectedValue('tracking_results_number') : 10;

					// Todo: make the time format string configurable
					$time_format_date = "%d.%m.%y";
					$time_format_time = "%R";

					// get the initial entries from the database
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->tablename,$where_clause,'','tstamp DESC',$number_of_entries);

					// loop through the entries and display the details
					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
						$startTime = $row['crdate'];

						// compile the header
						$tableHeader = '';

						$headerRowCounter = 0;
						$tableInfoList = array(
												CATEGORY_TRACKING_BROWSER,
												CATEGORY_TRACKING_OPERATING_SYSTEM,
												CATEGORY_TRACKING_IP_ADRESS,
												CATEGORY_TRACKING_REFERER,
												CATEGORY_TRACKING_SEARCH_STRING
												);

						foreach ($tableInfoList as $category) {
							$where_clause = 'type=' . $GLOBALS['TYPO3_DB']->fullQuoteStr(STAT_TYPE_TRACKING, $this->tablename);
							$where_clause .= ' AND category=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($category, $this->tablename);
							$where_clause .= ' AND parent_uid='.$row['uid'];
							$resDetail = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->tablename,$where_clause);
							$rowDetail = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resDetail);
							$headerRowCounter++;
							if ($rowDetail['element_title']) {
								if ($headerRowCounter > 1 && $headerRowCounter < 4) {
									$tableHeader .= ' / ';
								} else if ($headerRowCounter >= 4) {
									$tableHeader .= ' <br /> ';
								}
								switch ($category) {
									case CATEGORY_TRACKING_REFERER:
										$tableHeader .= $GLOBALS['LANG']->getLL('referer').': ';
										break;
									case CATEGORY_TRACKING_SEARCH_STRING:
										$tableHeader .= $GLOBALS['LANG']->getLL('searchstring').': ';
										break;
								}
								$tableHeader .= $rowDetail['element_title'];
							}
						}

						// get the details
						$where_clause = 'type=' . $GLOBALS['TYPO3_DB']->fullQuoteStr(STAT_TYPE_TRACKING, $this->tablename);
						$where_clause .= ' AND category=' . $GLOBALS['TYPO3_DB']->fullQuoteStr(CATEGORY_TRACKING_PAGES, $this->tablename);
						$where_clause .= ' AND parent_uid=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($row['uid'], $this->tablename);
						$resDetail = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->tablename,$where_clause,'','crdate');

						$printRows = array();
						$lastRow = array();
						while ($rowDetail = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resDetail)) {
							// compile the data which will be printed
							$printRow['date'] = strftime($time_format_date, $rowDetail['crdate']);
							$printRow['time'] = strftime($time_format_time, $rowDetail['crdate']);
							$printRow['duration'] = $rowDetail['crdate'] - $startTime;
							$printRow['element_title'] = $rowDetail['element_title'];
							$printRow['element_uid'] = $rowDetail['element_uid'];
							$printRow['element_language'] = $this->getLanguageName($rowDetail['element_language']);

							// do some formating for the printRow
							if ($printRow['duration'] > 60) {
								$printRow['duration'] = round($printRow['duration']/60).' '.$GLOBALS['LANG']->getLL('min');
							} else {
								$printRow['duration'] = $printRow['duration'].' '.$GLOBALS['LANG']->getLL('sec');
							}
							if (strlen($printRow['element_title']) > $this->maxLengthTableContent) {
								$printRow['element_title'] = substr($printRow['element_title'],0,$this->maxLengthTableContent).'...';
							}

							// print some values only if they differ from the last values
							$cleanUpFiels = 'element_language,element_uid,date';
							if (sizeof($lastRow)==0) {
								$lastRow = $printRow;
							} else {
								foreach (explode(',',$cleanUpFiels) as $key) {
									if ($printRow[$key] == $lastRow[$key]) {
										$printRow[$key] = '';
									} else {
										$lastRow[$key] = $printRow[$key];
									}
								}
							}

							// add this row to the result
							$printRows[] = $printRow;
						}
						$content .= $this->renderTable($tableHeader,'crdate,time,duration,element_title,element_uid,element_language',$printRows,'no_line_numbers','counter','');
						unset($printRows);
						unset($lastRow);
					}
				}
			break;
			// display extension statistics
			// works more or less like the normal page statistics
			case STAT_TYPE_EXTENSION:
				$category = $this->tabmenu->getSelectedValue('extension_type',$this->allowedExtensionTypes);
				$content .= $this->renderSelectorMenu(STAT_TYPE_EXTENSION,$category);
				$columns = 'element_title,element_uid,counter';
				$resultArray = $this->getStatResults(STAT_TYPE_EXTENSION,$category,$columns);
				$content .= $this->renderTable($GLOBALS['LANG']->getLL('type_extension'),$columns,$resultArray,$this->tabmenu->getSelectedValue('extension_type',$this->allowedExtensionTypes));
				$content .= $this->renderUpdateInformation();
			break;
		}

		return $content;
	}/*}}}*/

	/**
	 * renderUpdateInformation
	 *
	 * Print information about to what time the update has been made (only if
	 * asynchronousDataRefreshing is activated)
	 *
	 * @access public
	 * @return void
	 */
	function renderUpdateInformation() {/*{{{*/
		$content = '';
		if ($this->extConf['asynchronousDataRefreshing']) {
			$oldestEntry = $this->kestatslib->getOldestQueueEntry();
			if ($oldestEntry) {
				$content .= '<p class="update_information">' . $GLOBALS['LANG']->getLL('updated_until') . date(UPDATED_UNTIL_DATEFORMAT, $oldestEntry['tstamp']) . '<p>';
			}
		}
		return $content;
	}/*}}}*/

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
	 * @return array
	 */
	function getStatResults($statType='pages',$statCategory,$columns,$onlySum=0,$orderBy='counter DESC',$groupBy='',$encode_title_to_utf8=0, $fromToArray=array()) {/*{{{*/
		$columns = $this->addTypeAndLanguageToColumns($columns);

		// find out the time period, if it is not given as a parameter
		if (!count($fromToArray)) {
			if ($onlySum) {
				// the whole time period, for which data exits
				$fromToArray = $this->getFirstAndLastEntries($statType,$statCategory);
			} else {
				// only the month given in the parameters
				$fromToArray['from_year'] = $this->tabmenu->getSelectedValue('year',$this->allowedYears);
				$fromToArray['to_year'] = $this->tabmenu->getSelectedValue('year',$this->allowedYears);
				$fromToArray['from_month'] = $this->tabmenu->getSelectedValue('month',$this->allowedMonths);
				$fromToArray['to_month'] = $this->tabmenu->getSelectedValue('month',$this->allowedMonths);
			}
		}

		$element_language = intval($this->tabmenu->getSelectedValue('element_language'));
		$element_type = intval($this->tabmenu->getSelectedValue('element_type'));

		return $this->kestatslib->getStatResults($statType, $statCategory, $columns, $onlySum, $orderBy, $groupBy, $encode_title_to_utf8, $fromToArray, $element_language, $element_type);

		// KENNZIFFER, C. B., 05.Jun.2009:
		// Now in kestatslib ...
		/*
		$yearArray = $this->kestatslib->getDateArray($fromToArray['from_year'],$fromToArray['from_month'],$fromToArray['to_year'],$fromToArray['to_month']);

		// read the stat data into an array
		$lineCounter = 0;
		foreach($yearArray as $year => $monthArray){
			foreach($monthArray as $month => $daysPerMonth){

				// if we are dealing with data of a month in the past, we may use the cache
				if ($year < date('Y') || ($year == date('Y') && $month < date('m'))) {
					$useCache = true;
				} else {
					$useCache = false;
				}

				$where_clause = 'type=\''.$statType.'\'';
				$where_clause .= ' AND category=\''.$statCategory.'\'';
				$where_clause .= ' AND year='.$year.'';
				$where_clause .= ' AND month='.$month.'';
				if (intval($this->tabmenu->getSelectedValue('element_language')) >= 0) {
					$where_clause .= ' AND element_language='.intval($this->tabmenu->getSelectedValue('element_language')).'';
				}
				if (intval($this->tabmenu->getSelectedValue('element_type')) >= 0) {
					$where_clause .= ' AND element_type='.intval($this->tabmenu->getSelectedValue('element_type')).'';
				}
				$where_clause .= $this->subpages_query;

				if ($useCache) {
					// is there a cache entry?
					// if yes, use this instead of really querying the stats-table
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->tablenameCache,
					'whereclause=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($where_clause, $this->tablenameCache)
					. ' AND groupby=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($groupBy, $this->tablenameCache)
					. ' AND orderby=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($orderBy, $this->tablenameCache) );

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

						$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*',$this->tablename,$where_clause,$groupBy,$orderBy);

						// write the result to the cache
						if (count($rows)) {
							$result = t3lib_div::array2xml($rows);

							// DEBUG
							// cache entries may get quite big ...
							// print_r($result);
							// echo round(strlen($result) / 1024) . ' KB';
							$GLOBALS['TYPO3_DB']->exec_INSERTquery($this->tablenameCache,array(
										'whereclause' => $where_clause,
										'groupby' => $groupBy,
										'orderby' => $orderBy,
										'result' => $result
										));
						}
					}
				}

				if (!$useCache) {
					$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*',$this->tablename,$where_clause,$groupBy,$orderBy);
				}

				$sum = 0;

				// render brackets around the year in CSV mode (otherwise excel doesn't like it)
				$rowIndex = $GLOBALS['LANG']->getLL('month_'.$month);
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
							for ($i = 0; $i<=$lineCounter; $i++) {
								if ($resultArray[$i]['element_title'] == $row['element_title']) {
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
								foreach (explode(',',$columns) as $field) {
									$resultArray[$lineCounter][$field] = $row[$field];
								}
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
		*/
	}/*}}}*/

	/**
	 * addTypeAndLanguageToColumns
	 * Add a column for the type and the language, if more than one type
	 * (language) exists and none is yet selected
	 *
	 * @param string $columns
	 * @access public
	 * @return string
	 */
	function addTypeAndLanguageToColumns($columns='') {/*{{{*/
		if (sizeof($this->elementTypesArray)>0 && $this->tabmenu->getSelectedValue('element_type')==-1) {
			$columns = str_replace('element_title','element_title,element_type',$columns);
		}
		if (sizeof($this->elementLanguagesArray)>0 && $this->tabmenu->getSelectedValue('element_language')==-1) {
			$columns = str_replace('element_title','element_title,element_language',$columns);
		}
		return $columns;
	}/*}}}*/

	/**
	 * returns year and month of the first and the last entry of given statistic types / categories
	 *
	 * @param string $statType
	 * @param string $statCategory
	 * @return array
	 */
	function getFirstAndLastEntries($statType,$statCategory) {/*{{{*/
		$fromToArray = array();
		$fromToArray['from_month'] = 0;
		$fromToArray['from_year'] = 0;
		$fromToArray['to_month'] = 0;
		$fromToArray['to_year'] = 0;

		$where_clause = 'type=\''.$statType.'\'';
		$where_clause .= ' AND category=\''.$statCategory.'\'';
			// ignore faulty entries
		$where_clause .= ' AND year>0';
		$where_clause .= $this->subpages_query;

		// get first entry
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->tablename,$where_clause,'','uid','1');
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$fromToArray['from_month'] = $row['month'];
			$fromToArray['from_year'] = $row['year'];
		} else {
			return $fromToArray;
		}

		// get last entry
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',$this->tablename,$where_clause,'','uid DESC','1');
		if ($GLOBALS['TYPO3_DB']->sql_num_rows($res) > 0) {
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$fromToArray['to_month'] = $row['month'];
			$fromToArray['to_year'] = $row['year'];
		} else {
			return $fromToArray;
		}

		return $fromToArray;
	}/*}}}*/

	/**
	 * returns a html table, rendered from the array $dataRows.
	 * $dataRows must contains one row for each row in the table.
	 * Each row is an array associative containing the data for the row.
	 *
	 * @param string $caption: Table-caption
	 * @param string $columns: comma-separated list of column-names used in the table (corrsponding to the array-keys in each row)
	 * @param array $dataRows: data array
	 * @param string $special: special rendering options
	 * @param string $columnWithSum: name of the column for which a sum shall be calculated
	 * @param string $columnWithPercent: name of the column for which a sum shall be calculated
	 * @param string $maxrows: Max. rows to render. 0 --> render all rows.
	 * @return string
	 */
	function renderTable($caption='Table',$columns='element_title,element_uid,counter',$dataRows=array(),$special='',$columnWithSum='counter',$columnWithPercent='counter',$maxrows=0) {/*{{{*/

		$columns = $this->addTypeAndLanguageToColumns($columns);
		$content = '';
		$columnsArray = explode(',',$columns);

		// is there a language column and which one is it?
		$language_column = -1;
		$i = 0;
		foreach ($columnsArray as $column) {
			if ($column == 'element_language') {
				$language_column = $i;
			}
			$i++;
		}

		// first we calculate the sum for each column
		$sumRow = array();
		if (count($dataRows) > 0) {
			foreach ($dataRows as $label => $dataRow) {
				$column_number = 0;
				foreach ($dataRow as $data) {
					if (!isset($sumRow[$column_number])) {
						$sumRow[$column_number] = 0;
					}
					$sumRow[$column_number] += intval($data);
					$column_number++;
				}
			}
		}

		// how many data columns will we have?
		if (count($dataRows) > 0) {
			reset($dataRows);
			$numberOfDataColumns = sizeof(current($dataRows));
			// add one for the percentage column
			if (!empty($columnWithPercent)) {
				$numberOfDataColumns += sizeof($columnWithPercent);
			}
		}

		// hack: we do have at least two colums!
		if ($numberOfDataColumns < 2) {
			$numberOfDataColumns = 2;
		}

		// render table
		$content .= '<table class="ke-stats-table" summary="'.$caption.'">';
		$content .= '<caption>'.$caption.'</caption>';

		// render the head
		$content .= '<thead>';
		$content .= '<tr>';

		// first we render a line number column
		if (!strstr($special,'no_line_numbers')) {
			$content .= '<th>'.$GLOBALS['LANG']->getLL('header_line_number').'</th>';
			$this->addCsvCol($GLOBALS['LANG']->getLL('header_line_number'));
		}

		// render a header column for each data column
		foreach ($columnsArray as $data) {
			$content .= '<th>'.$GLOBALS['LANG']->getLL('header_'.$data).'</th>';
			$this->addCsvCol($GLOBALS['LANG']->getLL('header_'.$data));
		}
		if (!empty($columnWithPercent)) {
			for ($column_number=0; $column_number<$numberOfDataColumns; $column_number++) {
				if ($columnsArray[$column_number-1] == $columnWithPercent) {
					$content .= '<th>'.$GLOBALS['LANG']->getLL('header_percent').'</th>';
					$this->addCsvCol($GLOBALS['LANG']->getLL('header_percent'));
				}
			}
		}
		$content .= '</tr>';
		$content .= '</thead>';
		$oddRow = 0;
		$rowCount = 0;

		// print the data rows
		if (count($dataRows) > 0) {
			$content .= '<tbody>';
			foreach ($dataRows as $key => $dataRow) {

				// skip empty rows with empty title and emtpy uid
				$skipRow = false;
				if (empty($dataRow['element_title']) && empty($dataRow['element_uid'])) {
					$skipRow = true;
				} else {
					$rowCount++;
				}

				// render row if we not reached the limit $maxrows
				if (!$maxrows || $rowCount <= $maxrows && !$skipRow) {
					$content .= '<tr';
					if ($oddRow) {
						$content .= ' class="odd"';
					}
					$content .= '>';
					$oddRow = 1-$oddRow;
					$column_number = 0;

					// start a new csv row
					$this->addCsvRow();

					// print the line number (which is the key in the data array)
					if (!strstr($special,'no_line_numbers')) {
						$content .= '<td>'.$key.'</td>';
						$this->addCsvCol($key);
					}
					foreach ($dataRow as $data) {
						// print the label of this row
						if ($column_number == 0) {
							if (strstr($special,'day_of_week')) {
								$formatted_data = $GLOBALS['LANG']->getLL('weekday_'.$data);
							} else if (strstr($special,'hosts')) {
								$formatted_data = gethostbyaddr($data);
							} else if (strstr($special,'url')) {
								$formatted_data = $data;
								if (substr($formatted_data,0,strlen('http://')) == 'http://') {
									$formatted_data = substr($formatted_data,strlen('http://'));
								}
								if (strlen($formatted_data) > $this->maxLengthURLs) {
									$formatted_data = substr($formatted_data,0,$this->maxLengthURLs).'...';
								}
								// sanitize the output
								// since 5.5.2008 data is already sanitized in the frontend
								// plugin, but maybe there are older values in the
								// databases that need to be sanitized
								$formatted_data = '<a target="_blank" href="'.htmlspecialchars($data, ENT_QUOTES).'">'.htmlspecialchars($formatted_data, ENT_QUOTES).'</a>';
							} else if (strstr($special,'naw_securedl')) {
								// Data from extension "naw_securedl"
								$formatted_data = '<a title="'.htmlspecialchars($data, ENT_QUOTES).'" alt="'.htmlspecialchars($data, ENT_QUOTES).'">'.basename(htmlspecialchars($data, ENT_QUOTES)).'</a>';
							} else if (strstr($special,'none')) {
								$formatted_data = $data;
								$formatted_data = htmlspecialchars($formatted_data, ENT_QUOTES);
							} else if (strstr($special,'hour_of_day')) {
								$formatted_data = $data.' - '.sprintf('%02d',intval($data+1));
							} else {
								$formatted_data = $data;
								if (strlen($formatted_data) > $this->maxLengthTableContent) {
									$formatted_data = substr($formatted_data,0,$this->maxLengthTableContent).'...';
								}
							}
							$this->addCsvCol(strip_tags($formatted_data));
						} else {
							// print the data
							// if this the row with the language, print the cleartext language name
							if ($column_number == $language_column) {
								$formatted_data = $this->getLanguageName($data);
							} else {
								// do some formatting
								switch ($special) {
									default:
										$formatted_data = $data;
										// number format for integer fields
										if (strval(intval($formatted_data)) == $formatted_data) {
											$formatted_data = number_format(intval($formatted_data),0,'.',' ');
										}
									break;
								}
							}
							// remove spaces from numbers
							$this->addCsvCol(strip_tags(str_replace(' ', '', $formatted_data)));
						}

						// add the data to the output
						$content .= '<td>'.$formatted_data.'</td>';

						// render the percent column
						if ($columnsArray[$column_number] == $columnWithPercent) {
							if (!empty($sumRow[$column_number])) {
								//$percent = round(100 * intval($data) / $sumRow[$column_number],2);
								$percent = 100 * intval($data) / $sumRow[$column_number];
								$percent = number_format($percent, 2, $this->decimalChar, ' ');
							} else {
								$percent = '-';
							}
							$content.='<td>'.$percent.' %</td>';
							$this->addCsvCol($percent . ' %');
						}
						$column_number++;
					}
					$content .= '</tr>';
				}
			}
			$content .= '</tbody>';

			// start a new csv row
			$this->addCsvRow();

			// make the sum row
			if (strlen($columnWithSum) > 0) {
				$content .= '<tfoot>';
				$content .= '<tr>';
				// This columns normally contais the line number, so wie have to disable it, if we have no line numbers
				if (!strstr($special,'no_line_numbers')) {
					$content .= '<td>'.$GLOBALS['LANG']->getLL('sum').'</td>';
					$this->addCsvCol($GLOBALS['LANG']->getLL('sum'));
				}
				for ($column_number=0; $column_number<$numberOfDataColumns; $column_number++) {
					if ($columnsArray[$column_number] == $columnWithSum) {
						$content .= '<td>'.$sumRow[$column_number].'</td>';
						$this->addCsvCol($sumRow[$column_number]);
					} else {
						if ($column_number>0 && $columnsArray[$column_number-1] == $columnWithPercent) {
							$content .= '<td>100 %</td>';
							$this->addCsvCol('100 %');
						} else {
							$content .= '<td>&nbsp;</td>';
							$this->addCsvCol('');
						}
					}
				}
				$content .= '</tr>';
				$content .= '</tfoot>';
			} else {

				// render an empty footer row
				$content .= '<tfoot>';
				$content .= '<tr>';
				$footerColumns = ($special == 'no_line_numbers') ? $numberOfDataColumns : $numberOfDataColumns + 1;
				for ($column_number=0; $column_number<$footerColumns; $column_number++) {
					$content .= '<td>&nbsp;</td>';
				}
				$content .= '</tr>';
				$content .= '</tfoot>';
			}
		}
		$content .= '</table>';
		return $content;
	}/*}}}*/

	/**
	 * addCsvCol
	 *
	 * @param string $content
	 * @access public
	 * @return void
	 */
	function addCsvCol($content='') {/*{{{*/
		$this->csvContent[$this->currentRowNumber][$this->currentColNumber] = $content;
		$this->currentColNumber++;
	}/*}}}*/

	/**
	 * addCsvRow
	 *
	 * @access public
	 * @return void
	 */
	function addCsvRow() {/*{{{*/
		$this->currentRowNumber++;
		$this->currentColNumber = 0;
		$this->csvContent[$this->currentRowNumber] = array();
	}/*}}}*/

	/**
	 * outputCSV
	 *
	 * @access public
	 * @return void
	 */
	function outputCSV() {/*{{{*/
		// Set Excel as default application
		header('Pragma: private');
		header('Cache-control: private, must-revalidate');
		header("Content-Type: application/vnd.ms-excel");

		// Set file name
		header('Content-Disposition: attachment; filename="' . str_replace('###DATE###', date('Y-m-d-H-i'), $GLOBALS['LANG']->getLL('csvdownload_filename') . '"'));

		$content = '';
		foreach ($this->csvContent as $row) {
			//function csvValues($row,$delim=',',$quote='"')
			$content .= t3lib_div::csvValues($row) . "\n";
		}

		// I'm not sure if this is necessary for all programs you are importing to, tested with OpenOffice.org
		if ($GLOBALS['LANG']->charSet == 'utf-8') {
			$content = utf8_decode($content);
		}

		echo $content;
		exit();
	}/*}}}*/

	/**
	 * returns the css for the result tables
	 *
	 * @return string
	 */
	function getTableCSS() {/*{{{*/
		return '

.buttonlink {
	float:left;
	margin: .5em 3px 0 0;
	font-size: 10px;
	font-weight: bold;
	border: 1px solid gray;
	display:block;
	padding:2px;
}

.update_information {
	font-style: italic;
	text-align: right;
	font-size:9px;
}

table.ke-stats-table {
	color: #7F7F7F;
	font-size: 10px;
	border-collapse: collapse;
	text-align:left;
	clear:left;
}

table.ke-stats-table,
table.ke-stats-table caption {
	width:100%;
	border-right: 1px solid #CCC;
	border-left: 1px solid #CCC
}

table.ke-stats-table caption {
	margin-top: 15px;
}

table.ke-stats-table caption,
table.ke-stats-table th,
table.ke-stats-table td {
	border-left: 0;
	padding: 2px
}

table.ke-stats-table caption,
table.ke-stats-table thead th,
table.ke-stats-table tfoot th,
table.ke-stats-table tfoot td {
	background-color: #B7B7CC;
	color: #FFF;
	font-weight: bold;
	text-transform: uppercase;
}

table.ke-stats-table thead td,
table.ke-stats-table thead th {
	background-color: #E2E2E9;
}

table.ke-stats-table tbody td,
table.ke-stats-table tbody th {
	padding: 4px 2px;
    white-space: nowrap;
    border-right: 1px solid #CCC;
}

table.ke-stats-table tbody tr.odd {
	background-color: #F7F7F7;
	color: #666;
}

table.ke-stats-table tbody a {
	padding: 1px 2px;
	text-decoration: none;
	border-bottom: 1px dotted #ccc;
}


table.ke-stats-table tbody a:link,
table.ke-stats-table tbody a:visited,
table.ke-stats-table tbody a:hover,
table.ke-stats-table tbody a:focus,
table.ke-stats-table tbody a:focus {
	color:black;
	border-bottom: 1px dotted #333333;
}

h3 {
	padding-left:0px;
}

table.ke-stats-table tbody tr:hover {
	background-color: #EEE;
}


table.ke-stats-table tbody a:visited:after {
	/*content: "\00A0\221A"*/
}
		';
	}/*}}}*/


	/**
	 * loadFrontendTSconfig
	 *
	 * gives access to the frontend TSconfig
	 * loads the TSconfig for the given page-uid
	 * and the plugin-TSconfig on this page for $plugin_name
	 *
	 * @param mixed $pageUid
	 * @param string $plugin_name
	 * @access public
	 * @return void
	 */
	function loadFrontendTSconfig($pageUid=0,$plugin_name='') {/*{{{*/
		if ($pageUid>0) {
			$sysPageObj = t3lib_div::makeInstance('t3lib_pageSelect');
			$rootLine = $sysPageObj->getRootLine($pageUid);
			$TSObj = t3lib_div::makeInstance('t3lib_tsparser_ext');
			$TSObj->tt_track = 0;
			$TSObj->init();
			$TSObj->runThroughTemplates($rootLine);
			$TSObj->generateConfig();
			$this->conf = $TSObj->setup;
			if (!empty($plugin_name)) {
				$this->extConf = $TSObj->setup['plugin.'][$plugin_name.'.'];
			}
		}
	}/*}}}*/
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_stats/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/ke_stats/mod1/index.php']);
}

// Make instance:
$SOBE = t3lib_div::makeInstance('tx_kestats_module1');
$SOBE->init();

// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();

?>
