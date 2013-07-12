<?php
namespace SJBR\StaticInfoTables\Configuration\TypoScript;
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
 * Class providing TypoScript configuration help for Static Info Tables
 *
 */
class ConfigurationHelper {


	/**
	 * @var \TYPO3\CMS\Extbase\Object\ObjectManager
	 */
	protected $objectManager;

	/**
	 * Renders a select element to select an entity
	 *
	 * @param array $params: Field information to be rendered
	 * @param \TYPO3\CMS\Core\TypoScript\ExtendedTemplateService $pObj: The calling parent object.
	 * @return string The HTML input field
	 */
	public function buildEntitySelector(array $params, \TYPO3\CMS\Core\TypoScript\ExtendedTemplateService $pObj, $arg = '') {
		$field = '';
		$this->objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Extbase\\Object\\ObjectManager');
		switch ($params['fieldName']) {
			case 'data[plugin.tx_staticinfotables_pi1.countryCode]':
			case 'data[plugin.tx_staticinfotables_pi1.countriesAllowed]':
				$repository = $this->objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\CountryRepository');
				$entities = $repository->findAllOrderedBy('nameLocalized');
				break;
			case 'data[plugin.tx_staticinfotables_pi1.countryZoneCode]':
				$repository = $this->objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\CountryZoneRepository');
				$countryCode = $this->getConfiguredCountryCode();
				if ($countryCode) {
					$countryRepository = $this->objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\CountryRepository');
					$country = $countryRepository->findOneByIsoCodeA3($countryCode);
					if (is_object($country)) {
						$entities = $repository->findByCountryOrderedByLocalizedName($country);
					}
				}
				if (!$countryCode || (empty($entities) && $params['fieldValue'])) {
					$entities = $repository->findAllOrderedBy('nameLocalized');
				}
				break;
			case 'data[plugin.tx_staticinfotables_pi1.currencyCode]':
				$repository = $this->objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\CurrencyRepository');
				$entities = $repository->findAllOrderedBy('nameLocalized');
				break;
			case 'data[plugin.tx_staticinfotables_pi1.languageCode]':
				$repository = $this->objectManager->get('SJBR\\StaticInfoTables\\Domain\\Repository\\LanguageRepository');
				$entities = $repository->findAllNonConstructedNonSacred();
				$entities = $repository->localizedSort($entities);
				break;
		}
		if (is_array($entities) && count($entities)) {
			$options = array();
			foreach ($entities as $entity) {
				switch ($params['fieldName']) {
					case 'data[plugin.tx_staticinfotables_pi1.countryZoneCode]':
						$value = $entity->getIsoCode();
						$options[] = array('name' => $entity->getNameLocalized() . ' (' . $value . ')', 'value' => $value);
						break;
					case 'data[plugin.tx_staticinfotables_pi1.countryCode]':
					case 'data[plugin.tx_staticinfotables_pi1.countriesAllowed]':
					case 'data[plugin.tx_staticinfotables_pi1.currencyCode]':
						$value = $entity->getIsoCodeA3();
						$options[] = array('name' => $entity->getNameLocalized() . ' (' . $value . ')', 'value' => $value);
						break;
					case 'data[plugin.tx_staticinfotables_pi1.languageCode]':
						$countryCode = $entity->getCountryIsoCodeA2();
						$value = $entity->getIsoCodeA2() . ($countryCode ? '_' . $countryCode : '');
						$options[] = array('name' => $entity->getNameLocalized() . ' (' . $value . ')', 'value' => $value);
						break;
				}
			}
			$outSelected = array();
			$size = $params['fieldName'] == 'data[plugin.tx_staticinfotables_pi1.countriesAllowed]' ? 5 : 1;
			$field = \SJBR\StaticInfoTables\Utility\HtmlElementUtility::selectConstructor($options, array($params['fieldValue']), $outSelected, $params['fieldName'], '', '', '', '', $size);
		}
		return $field;
	}

	/**
	 * Gets the configured default country code
	 *
	 * @return string The configured default country code
	 */
	protected function getConfiguredCountryCode () {
		$configurationManager = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Configuration\\ConfigurationManager');
		$settings = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
		return $settings['plugin.']['tx_staticinfotables_pi1.']['countryCode'];
	}
}
?>