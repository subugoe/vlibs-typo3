<?php
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
 * Class for updating the db
 */
class ext_update {
	/**
	 * Main function, returning the HTML content
	 *
	 * @return string HTML
	 */
	function main()	{
		$content = '';

		$objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		$databaseUpdateUtility = $objectManager->get('SJBR\\StaticInfoTables\\Utility\\DatabaseUpdateUtility');
		
		// Clear the class cache
		$classCacheManager = $objectManager->get('SJBR\\StaticInfoTables\\Cache\\ClassCacheManager');
		$classCacheManager->reBuild();

		// Get the extensions which want to extend static_info_tables
		$loadedExtensions = array_unique(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getLoadedExtensionListArray());
		foreach ($loadedExtensions as $extensionKey) {
			$extensionInfoFile = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionKey) . 'Configuration/DomainModelExtension/StaticInfoTables.txt';
			if (file_exists($extensionInfoFile)) {
				$databaseUpdateUtility->doUpdate($extensionKey);
				$content.= '<p>' . nl2br(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('updateLanguageLabels', 'StaticInfoTables')) . ' ' . $extensionKey . '</p>';
			}
		}
		if (!$content) {
			// Nothing to do
			$content .= '<p>' . nl2br(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('nothingToDo', 'StaticInfoTables')) . '</p>';
		}
		// Notice for old language packs
		$content .= '<p>' . nl2br(\TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('update.oldLanguagePacks', 'StaticInfoTables')) . '</p>';
		return $content;
	}

	function access() {
		return TRUE;
	}
}
?>