<?php
namespace SJBR\StaticInfoTables\Hook\Backend\Form;
/***************************************************************
*  Copyright notice
*
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
 * Custom rendering of some backend forms elements
 *
 */
class ElementRenderingHelper {

	/*
	 * Add ISO codes to the label of entities
	 */
	public function addIsoCodeToLabel (&$PA, &$fObj) {
		$PA['title'] = $PA['row'][$GLOBALS['TCA'][$PA['table']]['ctrl']['label']];
		if (TYPO3_MODE == 'BE') {
			switch ($PA['table']) {
				case 'static_territories':
					$isoCode = $PA['row']['tr_iso_nr'];
					if (!$isoCode) {
						$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
							'uid,tr_iso_nr',
							$PA['table'],
							'uid = ' . intval($PA['row']['uid']) . \SJBR\StaticInfoTables\Utility\TcaUtility::getEnableFields($PA['table'])
						);
						$isoCode = $rows[0]['tr_iso_nr'];
					}
					if ($isoCode) {
						$PA['title'] = $PA['title'] . ' (' . $isoCode . ')';
					}
					break;
				case 'static_countries':
					$isoCode = $PA['row']['cn_iso_2'];
					if (!$isoCode) {
						$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
							'uid,cn_iso_2',
							$PA['table'],
							'uid = ' . intval($PA['row']['uid']) . \SJBR\StaticInfoTables\Utility\TcaUtility::getEnableFields($PA['table'])
						);
						$isoCode = $rows[0]['cn_iso_2'];
					}
					if ($isoCode) {
						$PA['title'] = $PA['title'] . ' (' . $isoCode . ')';
					}
					break;
				case 'static_languages':
					$isoCodes = array($PA['row']['lg_iso_2']);
					if ($PA['row']['lg_country_iso_2']) {
						$isoCodes[] = $PA['row']['lg_country_iso_2'];
					}
					$isoCode = implode('_', $isoCodes);
					if (!$isoCode || !$PA['row']['lg_country_iso_2']) {
						$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
							'uid,lg_iso_2,lg_country_iso_2',
							$PA['table'],
							'uid = ' . intval($PA['row']['uid']) . \SJBR\StaticInfoTables\Utility\TcaUtility::getEnableFields($PA['table'])
						);
						$isoCodes = array($rows[0]['lg_iso_2']);
						if ($rows[0]['lg_country_iso_2']) {
							$isoCodes[] = $rows[0]['lg_country_iso_2'];
						}
						$isoCode = implode('_', $isoCodes);	
					}
					if ($isoCode) {
						$PA['title'] = $PA['title'] . ' (' . $isoCode . ')';
					}
					break;
				case 'static_currencies':
					$isoCode = $PA['row']['cu_iso_3'];
					if (!$isoCode) {
						$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
							'uid,cu_iso_3',
							$PA['table'],
							'uid = ' . intval($PA['row']['uid']) . \SJBR\StaticInfoTables\Utility\TcaUtility::getEnableFields($PA['table'])
						);
						$isoCode = $rows[0]['cu_iso_3'];
					}
					if ($isoCode) {
						$PA['title'] = $PA['title'] . ' (' . $isoCode . ')';
					}
					break;
				default:
					break;
			}
		}
	}

	/*
	 * Translate and sort the territories selector using the current locale
	 */
	public function translateTerritoriesSelector ($PA, $fObj) {
		switch ($PA['table']) {
			case 'static_territories':
				// Avoid circular relation
				$row = $PA['row'];
				foreach ($PA['items'] as $index => $item) {
					if ($item[1] == $row['uid']) {
						unset($PA['items'][$index]);
					}
				}
				break;
		}
		foreach ($PA['items'] as $index => $item) {
			if ($PA['items'][$index][1]) {
				$PA['items'][$index][0] = \SJBR\StaticInfoTables\Utility\LocalizationUtility::translate(array('uid' => $item[1]), 'static_territories');
			}
		}
		asort($PA['items']);
	}

	/*
	 * Translate and sort the countries selector using the current locale
	 */
	public function translateCountriesSelector ($PA, $fObj) {
		foreach ($PA['items'] as $index => $item) {
			if ($PA['items'][$index][1]) {
				$PA['items'][$index][0] = \SJBR\StaticInfoTables\Utility\LocalizationUtility::translate(array('uid' => $item[1]), 'static_countries');
			}
		}
		asort($PA['items']);
	}

	/*
	 * Translate and sort the currencies selector using the current locale
	 */
	public function translateCurrenciesSelector ($PA, $fObj) {
		foreach ($PA['items'] as $index => $item) {
			if ($PA['items'][$index][1]) {
				$PA['items'][$index][0] = \SJBR\StaticInfoTables\Utility\LocalizationUtility::translate(array('uid' => $item[1]), 'static_currencies');
			}
		}
		asort($PA['items']);
	}

	/*
	 * Translate and sort the languages selector using the current locale
	 */
	public function translateLanguagesSelector ($PA, $fObj) {
		foreach ($PA['items'] as $index => $item) {
			if ($PA['items'][$index][1]) {
				//Get isocode if present
				$code = strstr($item[0], '(');
				$code2 = strstr(substr($code, 1), '(');
				$code = $code2 ? $code2 : $code;
				// Translate
				$PA['items'][$index][0] = \SJBR\StaticInfoTables\Utility\LocalizationUtility::translate(array('uid' => $item[1]), 'static_languages');
				// Re-append isocode, if present
				$PA['items'][$index][0] = $PA['items'][$index][0] . ($code ? ' ' . $code : '');
			}
		}
		asort($PA['items']);
	}
}
?>
