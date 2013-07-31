<?php
namespace SJBR\StaticInfoTables\Utility;
/***************************************************************
*  Copyright notice
*
*  (c) 2013 StanislasRolland <typo3@sjbr.ca>
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
 * TCA-related functions
 */
class TcaUtility {

	/**
	 * Load the configuration of a table and additional configuration by language packs
	 *
	 * @param string $tableName: the name of the table
	 * @return	void
	 */
	static public function loadTca ($tableName) {
		\TYPO3\CMS\Core\Utility\GeneralUtility::loadTCA($tableName);
		// Get all extending TCA's
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['extendingTCA'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['static_info_tables']['extendingTCA'] as $extensionKey) {
				if (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded($extensionKey)) {
					include(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionKey) . 'ext_tables.php');
				}
			}
		}
	}

	/**
	 * Get the enable fields clause based on the table configuration
	 *
	 * @param string $tableName: the name of the table
	 * @return string enable fileds clause
	 */
	static public function getEnableFields ($tableName) {
		if (TYPO3_MODE === 'FE') {
			$enableFields = $GLOBALS['TSFE']->sys_page->enableFields($tableName);
		} else {
			$enableFields = \TYPO3\CMS\Backend\Utility\BackendUtility::deleteClause($tableName);
		}
	}
}
?>