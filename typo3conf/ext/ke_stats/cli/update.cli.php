#! /usr/bin/php
<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007-2011 Christian B�lter <buelter@kennziffer.com>
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

	// time in milliseconds this script should stop after
define(MAX_EXECUTION_TIME, 90000);

	// Defining circumstances for CLI mode:
define('TYPO3_cliMode', TRUE);

	// Defining PATH_thisScript here: Must be the ABSOLUTE path of this script.
if (!empty($_ENV['_']) || !empty($_SERVER['_'])) {
		// script is called from the shell
		// This will work as long as the script is called by it's
		// absolute path from the shell!
	define('PATH_thisScript', $_ENV['_'] ? $_ENV['_'] : $_SERVER['_']);
} else if (!empty($_ENV['SCRIPT_FILENAME']) || !empty($_SERVER['SCRIPT_FILENAME'])) {
		// script is called via browser
	define('PATH_thisScript', $_ENV['SCRIPT_FILENAME'] ? $_ENV['SCRIPT_FILENAME'] : $_SERVER['SCRIPT_FILENAME']);
} else {
	die ('Error: Could not determine absolute path to current script.');
}

	// Include configuration file:
require(dirname(PATH_thisScript).'/conf.php');

	// Include init file:
require(dirname(PATH_thisScript).'/'.$BACK_PATH.'init.php');

	// find the extension directory
$EXT_DIR = dirname(dirname(PATH_thisScript));

	// include the shared library
require_once($EXT_DIR.'/lib/class.tx_kestats_lib.php');

	// instantiate the shared library
$kestatslib = t3lib_div::makeInstance('tx_kestats_lib');

$startTime = t3lib_div::milliseconds();
$oldestEntry = false;
$counter = 0;
$counter_invalid = 0;

do {

		// get oldest entry
	$oldestEntry = $kestatslib->getOldestQueueEntry();

		// process it and delete it
	if ($oldestEntry) {
		$dataArray = unserialize($oldestEntry['data']);

			// compatibility with older versions
		$dataArray['counter'] = $dataArray['counter'] ? $dataArray['counter'] : 1;

		$kestatslib->statData = unserialize($oldestEntry['generaldata']);

			// make sure we only process valid data
		if ($dataArray['category'] && $dataArray['stat_type']) {
			$kestatslib->updateStatisticsTable(
					$dataArray['category'],
					$dataArray['compareFieldList'],
					$dataArray['element_title'],
					$dataArray['element_uid'],
					$dataArray['element_pid'],
					$dataArray['element_language'],
					$dataArray['element_type'],
					$dataArray['stat_type'],
					$dataArray['parent_uid'],
					$dataArray['additionalData'],
					$dataArray['counter']
					);
			$counter++;
		} else {
			$counter_invalid++;
		}

		$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_kestats_queue', 'uid=' . $oldestEntry['uid']);
	}

	$runningTime = t3lib_div::milliseconds() - $startTime;

} while ($oldestEntry && ($runningTime < MAX_EXECUTION_TIME));

$output =  'Processed ' . $counter . ' entries in ' . ($runningTime / 1000) . ' seconds.' . "\n";
$output .=  'Ignored ' . $counter_invalid . ' invalid entries.' . "\n";

// DEBUG
// echo $output;
// mail('admin@mysite.com', 'ke_stats CLI', $output);
?>
