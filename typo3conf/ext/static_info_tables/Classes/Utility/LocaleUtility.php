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
 * Locale-related functions
 */
class LocaleUtility {

	/**
	 * @var string Name of the extension this class belongs to
	 */
	protected $extensionName = 'StaticInfoTables';

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * Injects the object manager
	 *
	 * @param \TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager
	 * @return void
	 */
	public function injectObjectManager(\TYPO3\CMS\Extbase\Object\ObjectManagerInterface $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * Get the typo3-supported locale options
	 *
	 * @return array An array of language objects
	 */
	public function getLocales() {
		$localeArray = array();
		$locales = $this->objectManager->get('TYPO3\\CMS\\Core\\Localization\\Locales');
		$languages = $locales->getLanguages();
		foreach ($languages as $locale => $language) {
			// No language pack for English
			if ($locale != 'default') {
				$languageObject = $this->objectManager->create('SJBR\\StaticInfoTables\\Domain\\Model\\Language');
				$languageObject->setCollatingLocale($locale);
				$localizedLanguage = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('lang_' . $locale, $this->extensionName);
				$label = ($localizedLanguage ? $localizedLanguage : $language) . ' (' . $locale . ')';
				$languageObject->setNameEn($label);
				$localeArray[$label] = $languageObject;
			}
		}
		ksort($localeArray);
		return $localeArray;
	}

	/**
	 * Get language name from locale
	 *
	 * @param string $locale
	 * @return string Language name
	 */
	public function getLanguageFromLocale($locale) {
		$locales = $this->objectManager->get('TYPO3\\CMS\\Core\\Localization\\Locales');
		$languages = $locales->getLanguages();
		return $languages[$locale];
	}
}
?>