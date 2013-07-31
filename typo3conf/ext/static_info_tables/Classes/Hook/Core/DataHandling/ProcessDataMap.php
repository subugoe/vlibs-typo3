<?php
namespace SJBR\StaticInfoTables\Hook\Core\DataHandling;
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
 * Hook on Core/DataHandling/DataHandler to manage redundancy of ISO codes in static info tables
 */
class ProcessDataMap {

	/**
	 * Post-process redundant ISO codes fields
	 *
	 * @param	object		$fobj TCEmain object reference
	 * @return	void
	 */
	public function processDatamap_postProcessFieldArray ($status, $table, $id, &$incomingFieldArray, &$fObj) {
		switch ($table) {
			case 'static_territories':
				//Post-process containing territory ISO numeric code
				if ($incomingFieldArray['tr_parent_territory_uid']) {
					$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
						'uid,tr_iso_nr',
						'static_territories',
						'uid = ' . intval($incomingFieldArray['tr_parent_territory_uid']) . \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('static_territories')
					);
					$incomingFieldArray['tr_parent_iso_nr'] = $rows[0]['tr_iso_nr'];
				} else if (isset($incomingFieldArray['tr_parent_territory_uid'])) {
					$incomingFieldArray['tr_parent_iso_nr'] = 0;
				}
				break;
			case 'static_countries':
				//Post-process containing territory ISO numeric code
				if ($incomingFieldArray['cn_parent_territory_uid']) {
					$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
						'uid,tr_iso_nr',
						'static_territories',
						'uid = ' . intval($incomingFieldArray['cn_parent_territory_uid']) . \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('static_territories')
					);
					$incomingFieldArray['cn_parent_tr_iso_nr'] = $rows[0]['tr_iso_nr'];
				} else if (isset($incomingFieldArray['cn_parent_territory_uid'])) {
					$incomingFieldArray['cn_parent_tr_iso_nr'] = 0;
				}
				//Post-process currency ISO numeric and A3 codes
				if ($incomingFieldArray['cn_currency_uid']) {
					$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
						'uid,cu_iso_nr,cu_iso_3',
						'static_currencies',
						'uid = ' . intval($incomingFieldArray['cn_currency_uid']) . \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('static_currencies')
					);
					$incomingFieldArray['cn_currency_iso_nr'] = $rows[0]['cu_iso_nr'];
					$incomingFieldArray['cn_currency_iso_3'] = $rows[0]['cu_iso_3'];
				} else if (isset($incomingFieldArray['cn_currency_uid'])) {
					$incomingFieldArray['cn_currency_iso_nr'] = 0;
					$incomingFieldArray['cn_currency_iso_3'] = '';
				}
				break;			
		}
	}
	/**
	 * Post-process redundant ISO codes fields of IRRE child
	 *
	 * @param	object		$fobj TCEmain object reference
	 * @return	void
	 */
	public function processDatamap_afterDatabaseOperations ($status, $table, $id, &$fieldArray, &$fObj) {
		switch ($table) {
			case 'static_countries':
				//Post-process country ISO numeric, A2 and A3 codes on country zones
				// Get the country record uid
				if ($status == 'new') {
					$id = $fObj->substNEWwithIDs[$id];
				}
				// Get the country zones
				$countryZones = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
					'*',
					'static_country_zones',
					'zn_country_uid = ' . intval($id) . \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause('static_country_zones')
				);
				if (is_array($countryZones) && count($countryZones)) {
					$countries = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(
						'uid,cn_iso_nr,cn_iso_2,cn_iso_3',
						$table,
						'uid = ' . intval($id) . \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause($table)
					);
					foreach ($countryZones as $countryZone) {
						$GLOBALS['TYPO3_DB']->exec_UPDATEquery(
							'static_country_zones',
							'uid = ' . intval($countryZone['uid']),
							array (
								'zn_country_iso_nr' => intval($countries[0]['cn_iso_nr']),
								'zn_country_iso_2' => $countries[0]['cn_iso_2'],
								'zn_country_iso_3' => $countries[0]['cn_iso_3']
							)
						);
					}
				}
				break;				
		}
	}
}
?>
