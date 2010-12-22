<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Christian Bülter <buelter@kennziffer.com>
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
 * @author	Christian Bülter <buelter@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_kestats
 */

// Constants

define(UNKNOWN_USER_AGENT,'unknown');
define(UNKNOWN_OPERATING_SYSTEM,'unknown');
define(EMPTY_USER_AGENT,'empty');

define(STAT_TYPE_PAGES,'pages');
define(STAT_TYPE_TRACKING,'tracking');
define(STAT_TYPE_EXTENSION,'extension');

define(CATEGORY_PAGES,'pages');
define(CATEGORY_PAGES_FEUSERS,'pages_feusers');

define(CATEGORY_VISITS_OVERALL,'visits_overall');
define(CATEGORY_VISITS_OVERALL_FEUSERS,'visits_overall_feusers');

define(CATEGORY_PAGES_OVERALL_DAY_OF_MONTH,'pages_overall_day_of_month');
define(CATEGORY_PAGES_OVERALL_DAY_OF_WEEK,'pages_overall_day_of_week');
define(CATEGORY_PAGES_OVERALL_HOUR_OF_DAY,'pages_overall_hour_of_day');

define(CATEGORY_VISITS_OVERALL_DAY_OF_MONTH,'visits_overall_day_of_month');
define(CATEGORY_VISITS_OVERALL_DAY_OF_WEEK,'visits_overall_day_of_week');
define(CATEGORY_VISITS_OVERALL_HOUR_OF_DAY,'visits_overall_hour_of_day');

define(CATEGORY_VISITS_OVERALL_FEUSERS_DAY_OF_MONTH,'visits_overall_feusers_day_of_month');
define(CATEGORY_VISITS_OVERALL_FEUSERS_DAY_OF_WEEK,'visits_overall_feusers_day_of_week');
define(CATEGORY_VISITS_OVERALL_FEUSERS_HOUR_OF_DAY,'visits_overall_feusers_hour_of_day');

define(CATEGORY_BROWSERS,'browsers');
define(CATEGORY_OPERATING_SYSTEMS,'operating_systems');
define(CATEGORY_IP_ADRESSES,'ip_addresses');
define(CATEGORY_UNKNOWN_USER_AGENTS,'unknown_user_agents');
define(CATEGORY_REFERERS_SEARCHENGINES,'referers_searchengines');
define(CATEGORY_SEARCH_STRINGS,'search_strings');
define(CATEGORY_REFERERS_EXTERNAL_WEBSITES,'referers_external_websites');
define(CATEGORY_ROBOTS,'robots');

define(CATEGORY_TRACKING_INITIAL,'initial');
define(CATEGORY_TRACKING_PAGES,'pages');
define(CATEGORY_TRACKING_BROWSER,'browser');
define(CATEGORY_TRACKING_OPERATING_SYSTEM,'os');
define(CATEGORY_TRACKING_IP_ADRESS,'ip');
define(CATEGORY_TRACKING_REFERER,'referer');
define(CATEGORY_TRACKING_SEARCH_STRING,'searchstring');

define(STAT_ONLY_SUM,1);
define(STAT_COMPLETE_LIST,0);
define(STAT_CALCULATE_SUM,1);
define(STAT_CALCULATE_PERCENT,1);
define(STAT_DONT_CALCULATE_SUM,0);
define(STAT_DONT_CALCULATE_PERCENT,0);
?>
