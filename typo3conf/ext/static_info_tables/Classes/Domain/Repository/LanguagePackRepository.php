<?php
namespace SJBR\StaticInfoTables\Domain\Repository;
use \TYPO3\CMS\Core\Utility\GeneralUtility;
use \TYPO3\CMS\Extbase\Utility\LocalizationUtility;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
/**
 * Language Pack repository
 *
 * @author Stanislas Rolland <typo3(arobas)sjbr.ca>
 */
class LanguagePackRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * @var string Name of the extension this class belongs to
	 */
	protected $extensionName = 'StaticInfoTables';

	/**
	 * Writes the language pack files
	 *
	 * @param \SJBR\StaticInfoTables\Domain\Model\LanguagePack the object to be stored
	 * @return array localized messages
	 */
	public function writeLanguagePack(\SJBR\StaticInfoTables\Domain\Model\LanguagePack $languagePack) {
		
		$content = array();

	 	$extensionKey = GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName);
	 	$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionKey);

		$content = array();
		$locale = $languagePack->getLocale();
		$localeLowerCase = strtolower($locale);
		$localeUpperCase = strtoupper($locale);
		$localeCamel = GeneralUtility::underscoredToUpperCamelCase(strtolower($locale));

		$languagePackExtensionKey = $extensionKey . '_' . $localeLowerCase;
		$languagePackExtensionPath = PATH_site . 'typo3conf/ext/' . $languagePackExtensionKey . '/';

		// Cleanup any pre-existing language pack
		if (is_dir($languagePackExtensionPath)) {
			GeneralUtility::rmdir($languagePackExtensionPath, TRUE);
		}
		// Create language pack directory structure		
		if (!is_dir($languagePackExtensionPath)) {
			GeneralUtility::mkdir_deep(PATH_site, 'typo3conf/ext/' . $languagePackExtensionKey . '/');
		}
		if (!is_dir($languagePackExtensionPath . 'Classes/Domain/Model/')) {
			GeneralUtility::mkdir_deep($languagePackExtensionPath, 'Classes/Domain/Model/');
		}
		if (!is_dir($languagePackExtensionPath . 'Configuration/DomainModelExtension/')) {
			GeneralUtility::mkdir_deep($languagePackExtensionPath, 'Configuration/DomainModelExtension/');
		}
		if (!is_dir($languagePackExtensionPath . 'Configuration/TypoScript/Extbase/')) {
			GeneralUtility::mkdir_deep($languagePackExtensionPath, 'Configuration/TypoScript/Extbase/');
		}
		if (!is_dir($languagePackExtensionPath . 'Resources/Private/Language/')) {
			GeneralUtility::mkdir_deep($languagePackExtensionPath, 'Resources/Private/Language/');
		}

		// Get the source files of the language pack template
		$sourcePath = $extensionPath . 'Resources/Private/LanguagePackTemplate/';
		$sourceFiles = array();
		$sourceFiles = GeneralUtility::getAllFilesAndFoldersInPath($sourceFiles, $sourcePath);
		$sourceFiles = GeneralUtility::removePrefixPathFromList($sourceFiles, $sourcePath);
		// Set markers replacement values
		$replace = array (
			'###LANG_ISO_LOWER###' => $localeLowerCase,
			'###LANG_ISO_UPPER###' => $localeUpperCase,
			'###LANG_ISO_CAMEL###' => $localeCamel,
			'###VERSION###' => $languagePack->getVersion(),
			'###LANG_NAME###' => $languagePack->getLanguage(),
			'###AUTHOR###' => $languagePack->getAuthor(),
			'###AUTHOR_EMAIL###' => $languagePack->getAuthorEmail(),
			'###AUTHOR_COMPANY###' => $languagePack->getAuthorCompany(),
			'###VERSION_BASE###' => $GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$extensionKey]['version'],
			'###LANG_TCA_LABELS###' => $languagePack->getLocalizationLabels(),
			'###LANG_SQL_UPDATE###' => $languagePack->getUpdateQueries()
		);
		// Create the language pack files
		$success = TRUE;
		foreach ($sourceFiles as $hash => $file) {
			$fileContent = GeneralUtility::getUrl($sourcePath . $file);
			foreach ($replace as $marker => $replacement) {
				$fileContent = str_replace($marker, $replacement, $fileContent);
			}
			$success = GeneralUtility::writeFile($languagePackExtensionPath . str_replace('.code', '.php', $file), $fileContent);
			if (!$success) {
				$content[] = LocalizationUtility::translate('couldNotWriteFile', $this->extensionName) . ' ' . $languagePackExtensionPath . $file;
				break;
			}
		}
		if ($success) {
			$classCacheManager = $this->objectManager->get('SJBR\\StaticInfoTables\\Cache\\ClassCacheManager');
			$installUtility = $this->objectManager->get('TYPO3\\CMS\\Extensionmanager\\Utility\\InstallUtility');
			$installed = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded($languagePackExtensionKey);
			if ($installed) {
				$content[] =  LocalizationUtility::translate('languagePack', $this->extensionName)
					. ' ' . $languagePackExtensionKey
					. ' ' . LocalizationUtility::translate('languagePackUpdated', $this->extensionName);
			} else {
				$content[] = LocalizationUtility::translate('languagePackCreated', $this->extensionName) . ' ' . $languagePack->getLanguage() . ' (' . $locale . ')';
				$installUtility->install($languagePackExtensionKey);
				$content[] = LocalizationUtility::translate('languagePack', $this->extensionName)
					. ' ' . $languagePackExtensionKey
					. ' ' . LocalizationUtility::translate('wasInstalled', $this->extensionName);
			}
			$classCacheManager->reBuild();
		}
		return $content;
	}
}
?>