<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Nicole Cordes <cordes@cps-it.de>
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

class tx_cpsdevlib_db {

	/**
	 * Gets rootline of a table downwards
	 *
	 * @param string $theTable: Database table
	 * @param string $parentField: Database field to check with third parameter
	 * @param mixed $uids: Uids of items
	 * @return string An rootline array
	 *
	 */
	public static function getRootLineDownwards($theTable, $parentField, $uids) {
		if (!is_array($uids)) $uids = tx_cpsdevlib_div::explode($uids);
		$rootLine = array();
		foreach ($uids as $uid) {
			$uidRootLine = array();
			$result = t3lib_BEfunc::getRecordsByField($theTable, $parentField, $uid);
			if (count($result)) {
				foreach ($result as $row) {
					$rL = self::getRootLineDownwards($theTable, $parentField, $row['uid']);
					$uidRootLine[$row['uid']] = $rL[$row['uid']];
				}
			}
			$rootLine[$uid] = $uidRootLine;
		}
		return $rootLine;
	}

	/**
	 * Gets rootline of a table downwards
	 *
	 * @param string $theTable: Database table
	 * @param string $parentField: Database field to check with third parameter
	 * @param mixed $uids: Uids of (different) parents
	 * @return string An rootline array
	 *
	 */
	public static function getRootLineUpwards($theTable, $parentField, $uids) {
		if (!is_array($uids)) $uids = tx_cpsdevlib_div::explode($uids);
		$rootLine = array();
		foreach ($uids as $uid) {
			$result = t3lib_BEfunc::getRecordsByField($theTable, 'uid', $uid);
			$rL = array();
			if (count($result)) {
				$rL = self::getRootLineUpwards($theTable, $parentField, $result[0][$parentField]);
			}
			$rootLine[$uid] = $rL;
		}

		return $rootLine;
	}
}

?>