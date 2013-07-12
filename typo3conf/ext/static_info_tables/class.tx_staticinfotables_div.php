<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2010 René Fritz (r.fritz@colorcube.de)
*  (c) 2013 Stanislas Rolland <typo3(arobas)sjbr.ca>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
 * Misc functions to access the static info tables
 * This class is deprecated since 6.0 and will be removed in version 6.2
 */
class tx_staticinfotables_div extends \SJBR\StaticInfoTables\Utility\LocalizationUtility {

	/**
	 * @deprecated since 6.0, will be removed two versions later - Use \SJBR\StaticInfoTables\Utility\LocalizationUtility::getLabelFields instead
	 */
	public static function getTCAlabelField ($tableName, $loadTCA = TRUE, $lang = '', $local = FALSE) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();
		return parent::getLabelFields($tableName, $lang, $local);
	}

	/**
	 * Get a list of countries by specific parameters or parts of names of countries
	 * in different languages. Parameters might be left empty.
	 *
	 * @deprecated since 6.0, will be removed two versions later - Use methods of \SJBR\StaticInfoTables\Domain\Repository\CountryRepository directly
	 *
	 *
	 * @param	string		a name of the country or a part of it in any language
	 * @param	string		ISO alpha-2 code of the country
	 * @param	string		ISO alpha-3 code of the country
	 * @param	array		Database row.
	 * @return	array		Array of rows of country records
	 */
	public static function fetchCountries ($country, $iso2='', $iso3='', $isonr='') {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();
		$rcArray = array();
		$where = '';

		$table = 'static_countries';
		if ($country != '') {
			$value = $GLOBALS['TYPO3_DB']->fullQuoteStr(trim('%'.$country.'%'),$table);
			$where = 'cn_official_name_local LIKE '.$value.' OR cn_official_name_en LIKE '.$value.' OR cn_short_local LIKE '.$value;
		}

		if ($isonr != '') {
			$where = 'cn_iso_nr='.$GLOBALS['TYPO3_DB']->fullQuoteStr(trim($isonr),$table);
		}

		if ($iso2 != '') {
			$where = 'cn_iso_2='.$GLOBALS['TYPO3_DB']->fullQuoteStr(trim($iso2),$table);
		}

		if ($iso3 !='') {
			$where = 'cn_iso_3='.$GLOBALS['TYPO3_DB']->fullQuoteStr(trim($iso3),$table);
		}

		if ($where != '') {
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', $table, $where);

			if ($res) {
				while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
					$rcArray[] = $row;
				}
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
		}
		return $rcArray;
	}
}
?>