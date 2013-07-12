<?php
namespace SJBR\StaticInfoTables\Domain\Model;
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
 * Language Pack object
 *
 * @author Stanislas Rolland <typo3(arobas)sjbr.ca>
 */
class LanguagePack extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity {

	/**
	 * @var string Name of the extension this class belongs to
	 */
	protected $extensionName = 'StaticInfoTables';

	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManagerInterface
	 */
	protected $objectManager;

	/**
	 * @var string
	 * *@validate StringLength(minimum=1, maximum=255)*
	 */
	protected $author;

	/**
	 * @var string
	 * *@validate StringLength(minimum=1, maximum=255)*
	 */
	protected $authorCompany;

	/**
	 * @var string
	 * *@validate EmailAddress*
	 */
	protected $authorEmail;

	/**
	 * @var string
	 */
	protected $locale;

	/**
	 * @var string
	 */
	protected $language;

	/**
	 * @var string
	 */
	protected $version;

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
	 * @var \SJBR\StaticInfoTables\Domain\Repository\CountryRepository
	 */
	protected $countryRepository;

 	/**
	 * Dependency injection of the Country Repository
 	 *
	 * @param \SJBR\StaticInfoTables\Domain\Repository\CountryRepository $countryRepository
 	 * @return void
	 */
	public function injectCountryRepository(\SJBR\StaticInfoTables\Domain\Repository\CountryRepository $countryRepository) {
		$this->countryRepository = $countryRepository;
	}

	/**
	 * @var \SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository
	 */
	protected $countryZoneRepository;

 	/**
	 * Dependency injection of the Country Zone Repository
 	 *
	 * @param \SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository $countryZoneRepository
 	 * @return void
	 */
	public function injectCountryZoneRepository(\SJBR\StaticInfoTables\Domain\Repository\CountryZoneRepository $countryZoneRepository) {
		$this->countryZoneRepository = $countryZoneRepository;
	}

	/**
	 * @var \SJBR\StaticInfoTables\Domain\Repository\CurrencyRepository
	 */
	protected $currencyRepository;

 	/**
	 * Dependency injection of the Currency Repository
 	 *
	 * @param \SJBR\StaticInfoTables\Domain\Repository\CurrencyRepository $currencyRepository
 	 * @return void
	 */
	public function injectCurrencyRepository(\SJBR\StaticInfoTables\Domain\Repository\CurrencyRepository $currencyRepository) {
		$this->currencyRepository = $currencyRepository;
	}

	/**
	 * @var \SJBR\StaticInfoTables\Domain\Repository\LanguageRepository
	 */
	protected $languageRepository;

 	/**
	 * Dependency injection of the Language Repository
 	 *
	 * @param \SJBR\StaticInfoTables\Domain\Repository\LanguageRepository $languageRepository
 	 * @return void
	 */
	public function injectLanguageRepository(\SJBR\StaticInfoTables\Domain\Repository\LanguageRepository $languageRepository) {
		$this->languageRepository = $languageRepository;
	}

	/**
	 * @var \SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository
	 */
	protected $territoryRepository;

 	/**
	 * Dependency injection of the Territory Repository
 	 *
	 * @param \SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository $territoryRepository
 	 * @return void
	 */
	public function injectTerritoryRepository(\SJBR\StaticInfoTables\Domain\Repository\TerritoryRepository $territoryRepository) {
		$this->territoryRepository = $territoryRepository;
	}

	public function __construct(
			$author = '',
			$authorCompany = '',
			$authorEmail = '',
			$locale = '',
			$language = ''
		) {
		$this->setAuthor($author);
		$this->setAuthorCompany($authorCompany);
		$this->setAuthorEmail($authorEmail);
		$this->setLocale($locale);
		$this->setLanguage($language);
	}

	public function setAuthor($author) {
		$this->author = $author;
	}

	public function getAuthor() {
		return $this->author;
	}

	public function setAuthorCompany($authorCompany) {
		$this->authorCompany = $authorCompany;
	}

	public function getAuthorCompany() {
		return $this->authorCompany;
	}

	public function setAuthorEmail($authorEmail) {
		$this->authorEmail = $authorEmail;
	}

	public function getAuthorEmail() {
		return $this->authorEmail;
	}
                                                                                                    
	public function setLocale($locale) {
		$this->locale = $locale;
	}

	public function getLocale() {
		return $this->locale;
	}

	public function setLanguage($language) {
		$this->language = $language;
	}

	public function getLanguage() {
		return $this->language;
	}

	public function setVersion($version) {
		$this->version = $version;
	}

	public function getVersion() {
		return $this->version;
	}

	/**
	 * Gets the localization labels for this language pack
	 *
	 * @return string localization labels in xliff format
	 */
	public function getLocalizationLabels() {
		// Build the localization labels of the language pack
		$XliffParser = $this->objectManager->get('TYPO3\\CMS\\Core\\Localization\\Parser\\XliffParser');
		$extensionKey = \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName);
	 	$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionKey);
		$sourceXliffFilePath = $extensionPath . 'Resources/Private/Language/locallang_db.xlf';
		$parsedData = $XliffParser->getParsedData($sourceXliffFilePath, 'default');
		$localizationLabels = array();
		$localeLowerCase = strtolower($this->getLocale());
		$localeUpperCase = strtoupper($this->getLocale());
		foreach ($parsedData['default'] as $translationElementId => $translationElement) {
			if (substr($translationElementId, -3) == '_en') {
				$localizationLabels[] = TAB . TAB . TAB . '<trans-unit id="' . substr($translationElementId, 0, -2) . $localeLowerCase . '" xml:space="preserve">';
				$localizationLabels[] = TAB . TAB . TAB . TAB . '<source>' . str_replace('(EN)', '(' . $localeUpperCase . ')', $translationElement[0]['source']) . '</source>';
				if ($translationElement[0]['target']) {
					$localizationLabels[] = TAB . TAB . TAB . TAB . '<target>' . str_replace('(EN)', '(' . $localeUpperCase . ')', $translationElement[0]['target']) . '</target>';	
				}
				$localizationLabels[] = TAB . TAB . TAB . '</trans-unit>';
			}
		}
		return implode(LF, $localizationLabels);
	}

	/**
	 * Gets the update queries for this language pack
	 *
	 * @return string update queries in sql format
	 */
	public function getUpdateQueries() {
		$updateQueries = array();
		$locale = $this->getLocale();
		$updateQueries = array_merge($updateQueries, $this->countryRepository->getUpdateQueries($locale));
		$updateQueries = array_merge($updateQueries, $this->countryZoneRepository->getUpdateQueries($locale));
		$updateQueries = array_merge($updateQueries, $this->currencyRepository->getUpdateQueries($locale));
		$updateQueries = array_merge($updateQueries, $this->languageRepository->getUpdateQueries($locale));
		$updateQueries = array_merge($updateQueries, $this->territoryRepository->getUpdateQueries($locale));
		return implode(LF, $updateQueries);
	}
}
?>