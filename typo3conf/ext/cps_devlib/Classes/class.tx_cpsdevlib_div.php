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

class tx_cpsdevlib_div {

	/**
	 * Function to call hooks from any extension
	 *
	 * @param string $extKey: Unique extension key
	 * @param string $theHook: Name of hook
	 * @param mixed $parameter: Parameter to assign to function
	 * @param mixed $ref: Reference to calling object
	 * @return void
	 *
	 */
	public static function callHookObjects($extKey, $theHook, &$parameter, &$ref) {
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extKey][$theHook])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extKey][$theHook] as $funcRef) {
				t3lib_div::callUserFunction($funcRef, $parameter, $ref);
			}
		}
	}

	/**
	 * Edit array values with multiple functions
	 *
	 * @param array $theArray: Array to edit
	 * @param boolean $useTrim: If true use trim function for each value
	 * @param boolean $removeEmptyValues: If true remove empty array items
	 * @param integer $theLimit: Defines limited array count
	 * @return array The edited array
	 *
	 */
	public static function removeArrayValues($theArray, $useTrim = true, $removeEmptyValues = true, $theLimit = 0) {
		$result = $theArray;

		if ($useTrim) $result = array_map('trim', $result);

		if ($removeEmptyValues) {
			$tempArray = array();
			foreach ($result as $value) {
				if ($value) {
					$tempArray[] = $value;
				}
			}
			$result = $tempArray;
		}

		if ($theLimit != 0) {
			if ($theLimit < 0) {
				$result = array_slice($result, $theLimit);
			} elseif (count($result) > $theLimit) {
				$result = array_slice($result, 0, $theLimit);
			}
		}

		return $result;
	}

	/**
	 * Splits the given string by delimeters. If set trim results and remove empty values.
	 *
	 * @param string $theString: String to split
	 * @param string $theDelims: Regular expression used to split first argument
	 * @param boolean $useTrim: If set trim function is used on each array item
	 * @param boolean $removeEmptyValues: If set all empty items are removed
	 * @param integer $theLimit: Defines the size of returned array
	 * @return array The exploded array
	 *
	 */
	public static function explode($theString, $theDelims = ',;\.:\-\+&\/', $useTrim = true, $removeEmptyValues = true, $theLimit = 0) {
		$pattern = '/[' . $theDelims . ']/';
		$result = preg_split($pattern, $theString);
		$result = self::removeArrayValues($result, $useTrim, $removeEmptyValues, $theLimit);

		return $result;
	}

	/**
	 * Converts the given mixed data into a list (array).
	 *
	 * @param mixed $theData: Data to convert to list
	 * @param string $theDelims: Character(s) used to split first argument
	 * @param boolean $useTrim: If set trim function is used to each array item
	 * @param boolean $removeEmptyValues: If set all empty items are removed
	 * @param boolean $useArrayKeys: If set array keys are used to get list
	 * @param integer $theLimit: Defines the size of returned array
	 * @return array An array list (one-dimensional)
	 *
	 */
	public static function toListArray($theData, $theDelims = ',;\.:\-\+&\/', $useTrim = true, $removeEmptyValues = true, $useArrayKeys = false, $theLimit = 0) {

		if (is_string($theData)) {
			return self::explode($theData, $theDelims, $useTrim, $removeEmptyValues, $theLimit);
		} elseif (is_array($theData)) {
			$result = array();
			foreach ($theData as $key => $value) {
				if (is_array($value)) {
					$result[] = $key;
					$result = array_merge($result, self::toListArray($value, $theDelims, $useTrim, $removeEmptyValues, $useArrayKeys));
				} else {
					if ($useArrayKeys) {
						$result[] = $key;
					} else {
						$result[] = $value;
					}
				}
			}
			$result = self::removeArrayValues($result, $useTrim, $removeEmptyValues, $theLimit);

			return $result;
		}

		return array();
	}

	/**
	 * Converts the given mixed data in an array with integer values
	 *
	 * @param mixed $theData: Data to convert to list
	 * @param string $theDelims: Character(s) used to split first argument
	 * @param bool $useTrim: If set trim function is used to each array item
	 * @param bool $removeEmptyValues: If set all empty items are removed
	 * @param bool $useArrayKeys: If set array keys are used to get list
	 * @param int $theLimit: Defines the size of returned array
	 * @return array An array list with integer values
	 */
	public static function toIntListArray($theData, $theDelims = ',;\.:\-\+&\/', $useTrim = true, $removeEmptyValues = true, $useArrayKeys = false, $theLimit = 0) {
		$theData = self::toListArray($theData, $theDelims, $useTrim, $removeEmptyValues, $useArrayKeys, $theLimit);

		foreach ($theData as $key => $value) {
			$theData[$key] = (int) $value;
		}
		unset($key, $value);

		return $theData;
	}

	/**
	 * Converts the given string (query string) in an array.
	 *
	 * @param string $theString: String to convert
	 * @param mixed $removeKeys: Mixed data to convert to array. Values are removed from query array
	 * @param string $theSeparator: Separator to split key/value pairs
	 * @param string $equalChar: Character to split key from value
	 * @param string $altSeparators: Comma separated list for alternative separators
	 * @return array The converted array
	 *
	 */
	public static function queryStringToArray($theString, $removeKeys = '', $theSeparator = '&', $equalChar = '=', $altSeparators = '&amp;') {

		$result = array();

		if ($theString != '') {
			// Generate an array with removeKeys values
			$removeKeys = self::toListArray($removeKeys);

			// Replace alternative separators
			$altSeparatorList = self::toListArray($altSeparators, ',', false, false, false, 0);
			foreach ($altSeparatorList as $altSeparator) {
				$theString = str_replace($altSeparator, $theSeparator, $theString);
			}
			unset($altSeparator);

			// Explode string to pairs
			$pairedArray = explode($theSeparator, $theString);
			foreach ($pairedArray as $key => $value) {
				// Explode pair to key and value
				list($k, $v) = explode($equalChar, $value);
				// If not in removeKeys
				if (!in_array($k, $removeKeys)) {
					// Check for array in key
					if (strpos($k, '[') === false) {
						$result[$k] = $v;
					} else {
						list($array, $arrayKey) = explode('[', $k);
						if (!is_array($result[$array])) {
							$result[$array] = array();
						}
						$result[$array][substr($arrayKey, 0, -1)] = $v;
						unset($array, $arrayKey);
					}
				}
				unset($k, $v);
			}
			unset($key, $value);
		}

		return $result;
	}

	/**
	 * Converts the given mixed data to list (string). If array you can use array keys for list.
	 *
	 * @param mixed $theData: Data to convert to list
	 * @param string $theGlue: Character(s) used to join list
	 * @param string $theDelims: Character(s) used to split first argument
	 * @param boolean $useTrim: If set trim function is used to each array item
	 * @param boolean $removeEmptyValues: If set all empty items are removed
	 * @param boolean $useArrayKeys: If set array keys are used to get list
	 * @param integer $theLimit: Defines the size of returned list
	 * @return string A list joined by second argument
	 *
	 */
	public static function toListString($theData, $theGlue = ',', $theDelims = ',;\.:\-\+&\/', $useTrim = true, $removeEmptyValues = true, $useArrayKeys = false, $theLimit = 0) {
		return implode($theGlue, self::toListArray($theData, $theDelims, $useTrim, $removeEmptyValues, $useArrayKeys, $theLimit));
	}

	/**
	 * Converts the given mixed data to a list with integer values. If array you can use array keys for list.
	 * @param mixed $theData: Data to convert to list
	 * @param string $theGlue: Character(s) used to join list
	 * @param string $theDelims: Character(s) used to split first argument
	 * @param bool $useTrim: If set trim function is used to each array item
	 * @param bool $removeEmptyValues: If set all empty items are removed
	 * @param bool $useArrayKeys: If set array keys are used to get list
	 * @param int $theLimit: Defines the size of returned list
	 * @return string A list of integer values joined by second argument
	 */
	public static function toIntListString($theData, $theGlue = ',', $theDelims = ',;\.:\-\+&\/', $useTrim = true, $removeEmptyValues = true, $useArrayKeys = false, $theLimit = 0) {
		return implode($theGlue, self::toIntListArray($theData, $theDelims, $useTrim, $removeEmptyValues, $useArrayKeys, $theLimit));
	}
}

?>