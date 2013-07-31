<?php
namespace SJBR\StaticInfoTables\Utility;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Sebastian Kurfürst <sebastian@typo3.org>
 *  (c) 2013 Stanislas Rolland <typo3@sjbr.ca>
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
 * Localization helper which should be used to fetch localized labels for static info entities.
 *
 */
class LocalizationUtility {

	/**
	 * Key of the language to use
	 *
	 * @var string
	 */
	protected static $languageKey = 'default';

	/**
	 * Pointer to alternative fall-back language to use
	 *
	 * @var array
	 */
	protected static $alternativeLanguageKeys = array();

	/**
	 * Returns the localized label for a static info entity
	 *
	 * @param array $identifiers An array with key 1- 'uid' containing a uid and/or 2- 'iso' containing one or two iso codes (i.e. country zone code and country code, or language code and country code)
	 * @param string $tableName The name of the table
	 * @param boolean local name only - if set local labels are returned
	 * @return string The value from the label field of the table
	 */
	public static function translate ($identifiers, $tableName, $local = FALSE) {

		$value = '';
		self::setLanguageKeys();
		$isoLanguage = self::getIsoLanguageKey(self::$languageKey);
		$value = self::getLabelFieldValue($identifiers, $tableName, $isoLanguage, $local);
		if ($value) {
			$value = self::convertCharset($value, 'utf-8');
		}
		return $value;
	}

	/**
	 * Get the localized value for the label field
	 *
	 * @param array $identifiers An array with key 1- 'uid' containing a uid and/or 2- 'iso' containing one or two iso codes (i.e. country zone code and country code, or language code and country code)
	 * @param string $tableName The name of the table
	 * @param string language ISO code
	 * @param boolean local name only - if set local labels are returned
	 * @return string the value for the label field
	 */
	public static function getLabelFieldValue ($identifiers, $tableName, $language, $local = FALSE) {
		$value = '';
		$labelFields = self::getLabelFields($tableName, $language, $local);
		if (count($labelFields)) {
			// Build the list of fields
			$prefixedLabelFields = array();
			foreach ($labelFields as $labelField) {
				$prefixedLabelFields[] = $tableName . '.' . $labelField;
			}
			$fields = $tableName . '.uid,' . implode(',', $prefixedLabelFields);
			//Build the where clause
			$whereClause = '';
			if ($identifiers['uid']) {
				$whereClause .= $tableName . '.uid = ' . intval($identifiers['uid']);
			} else if (!empty($identifiers['iso'])) {
				$isoCode = is_array($identifiers['iso']) ? $identifiers['iso'] : array($identifiers['iso']);
				foreach ($isoCode as $index => $code) {
					if ($code) {
						$field = self::getIsoCodeField($tableName, $code, $index);
						if ($field) {
							$whereClause .= ($whereClause ? ' AND ' : '') . $tableName . '.' . $field . ' = ' . $GLOBALS['TYPO3_DB']->fullQuoteStr($code, $tableName);
						}
					}
				}
			}
			// Get the entity
			if ($whereClause) {
				$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					$fields,
					$tableName,
					$whereClause . \SJBR\StaticInfoTables\Utility\TcaUtility::getEnableFields($tableName)
				);
				if (is_array($rows) && count($rows)) {
					foreach ($labelFields as $labelField) {
						if ($rows[0][$labelField]) {
							$value = $rows[0][$labelField];
							break;
						}
					}
				}
			}
		}
		return $value;
	}

	/**
	 * Returns the label fields for a given language
	 *
	 * @param string table name
	 * @param string ISO language code to be used
	 * @param boolean If set, we are looking for the "local" title field
	 * @return array field names
	 */
	public static function getLabelFields ($tableName, $lang, $local = FALSE) {
		if (\TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version) < 6001000) {
			TcaUtility::loadTca($tableName);
		}
		$labelFields = array();
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables'][$tableName]['label_fields'])) {
			$alternativeLanguages = array();
			if (count(self::$alternativeLanguageKeys)) {
				$alternativeLanguages = array_reverse(self::$alternativeLanguageKeys);
			}
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables'][$tableName]['label_fields'] as $field) {
				if ($local) {
					$labelField = str_replace ('##', 'local', $field);
				} else {
					$labelField = str_replace ('##', strtolower($lang), $field);
				}
				// Make sure the resulting field name exists in the table
				if (is_array($GLOBALS['TCA'][$tableName]['columns'][$labelField])) {
					$labelFields[] = $labelField;
				}
				// Add fields for alternative languages
				if (strpos($field, '##') !== FALSE && count($alternativeLanguages)) {
					foreach ($alternativeLanguages as $language) {
						$labelField = str_replace ('##', strtolower($language), $field);
						// Make sure the resulting field name exists in the table
						if (is_array($GLOBALS['TCA'][$tableName]['columns'][$labelField])) {
							$labelFields[] = $labelField;
						}
					}
					
				}
			}
		}
		return $labelFields;
	}

	/**
	 * Returns a iso code field for the passed table name, iso code and index
	 *
	 * @param string table name
	 * @param string iso code
	 * @param integer index in the table's isocode_field configuration array
	 * @return string field name
	 */
	protected static function getIsoCodeField ($table, $isoCode, $index = 0) {
		$isoCodeField = '';
		$isoCodeFieldTemplate = $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['tables'][$table]['isocode_field'][$index];
		if ($isoCode && $table && $isoCodeFieldTemplate) {
			$field = str_replace ('##', self::isoCodeType($isoCode), $isoCodeFieldTemplate);
			if (is_array($GLOBALS['TCA'][$table]['columns'][$field])) {
				$isoCodeField = $field;
			}
		}
		return $isoCodeField;
	}

	/**
	 * Returns the type of an iso code: nr, 2, 3
	 *
	 * @param	string		iso code
	 * @return	string		iso code type
	 */
	protected static function isoCodeType ($isoCode) {
		$type = '';
		$isoCodeAsInteger = \TYPO3\CMS\Core\Utility\MathUtility::canBeInterpretedAsInteger($isoCode);
		if ($isoCodeAsInteger) {
			$type = 'nr';
		} else if (strlen($isoCode) == 2) {
			$type = '2';
		} else if (strlen($isoCode) == 3) {
			$type = '3';
		}
		return $type;
	}

	/**
	 * Get the ISO language key corresponding to a TYPO3 language key
	 *
	 * @param string $key The TYPO3 language key
	 * @return string the ISO language key
	 */
	public static function getIsoLanguageKey($key) {
		return ($key === 'default' ? 'EN' : $key);
	}

	/**
	 * Get the current TYPO3 language
	 *
	 * @return string the TYP3 language key
	 */
	public static function getCurrentLanguage() {
		if (self::$languageKey === 'default') {
			self::setLanguageKeys();
		}
		return self::$languageKey;
	}

	/**
	 * Sets the currently active language/language_alt keys.
	 * Default values are "default" for language key and "" for language_alt key.
	 *
	 * @return void
	 */
	protected static function setLanguageKeys() {
		self::$languageKey = 'default';
		self::$alternativeLanguageKeys = array();
		if (TYPO3_MODE === 'FE') {
			if (isset($GLOBALS['TSFE']->config['config']['language'])) {
				self::$languageKey = $GLOBALS['TSFE']->config['config']['language'];
				if (isset($GLOBALS['TSFE']->config['config']['language_alt'])) {
					self::$alternativeLanguageKeys[] = $GLOBALS['TSFE']->config['config']['language_alt'];
				} else {
					/** @var $locales \TYPO3\CMS\Core\Localization\Locales */
					$locales = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Localization\\Locales');
					if (in_array(self::$languageKey, $locales->getLocales())) {
						foreach ($locales->getLocaleDependencies(self::$languageKey) as $language) {
							self::$alternativeLanguageKeys[] = $language;
						}
					}
				}
			}
		} elseif (strlen($GLOBALS['BE_USER']->uc['lang']) > 0) {
			self::$languageKey = $GLOBALS['BE_USER']->uc['lang'];
			// Get standard locale dependencies for the backend
			/** @var $locales \TYPO3\CMS\Core\Localization\Locales */
			$locales = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Localization\\Locales');
			if (in_array(self::$languageKey, $locales->getLocales())) {
				foreach ($locales->getLocaleDependencies(self::$languageKey) as $language) {
					self::$alternativeLanguageKeys[] = $language;
				}
			}
		}
	}

	/**
	 * Converts a string from the specified character set to the current.
	 * The current charset is defined by the TYPO3 mode.
	 *
	 * @param string $value string to be converted
	 * @param string $charset The source charset
	 * @return string converted string
	 */
	public static function convertCharset($value, $charset) {
		if (TYPO3_MODE === 'FE') {
			return $GLOBALS['TSFE']->csConv($value, $charset);
		} else {
			return $value;
		}
	}
}
?>