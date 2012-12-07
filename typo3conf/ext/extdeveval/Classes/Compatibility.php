<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2012 Oliver Hader <oliver.hader@typo3.org>
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

class Tx_Extdeveval_Compatibility {
	/**
	 * @param array $array
	 */
	public static function viewArray($array) {
		if (class_exists('t3lib_utility_Debug') && is_callable('t3lib_utility_Debug::viewArray')) {
			t3lib_utility_Debug::viewArray($array);
		} else {
			t3lib_div::view_array($array);
		}
	}

	/**
	 * @param string $versionNumber
	 * @return integer
	 */
	public static function convertVersionNumberToInteger($versionNumber) {
		if (class_exists('t3lib_utility_VersionNumber') && is_callable('t3lib_utility_VersionNumber::convertVersionNumberToInteger')) {
			return t3lib_utility_VersionNumber::convertVersionNumberToInteger($versionNumber);
		} else {
			return t3lib_div::int_from_ver($versionNumber);
		}
	}
}
?>