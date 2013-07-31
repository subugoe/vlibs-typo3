<?php
namespace SJBR\StaticInfoTables\Domain\Repository;
/***************************************************************
*  Copyright notice
*
*  (c) 2011-2012 Armin Rüdiger Vieweg <info@professorweb.de>
*  (c) 2013 Stanislas Rolland <typo3(arobas)sjbr.ca>
*
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
 * Abstract Repository for static entities
 */
abstract class AbstractEntityRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

	/**
	 * @var string Name of the extension this class belongs to
	 */
	protected $extensionName = 'StaticInfoTables';

	/**
	 * @var \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper
	 */
	protected $dataMapper;

	/**
	 * Injects the DataMapper to map nodes to objects
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper
	 * @return void
	 */
	public function injectDataMapper(\TYPO3\CMS\Extbase\Persistence\Generic\Mapper\DataMapper $dataMapper) {
		$this->dataMapper = $dataMapper;
	}

	/**
	 * @var array ISO keys for this static table
	 */
	protected $isoKeys = array();
	
	/**
	 * Find all with deleted included
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array all entries
	 */
	public function findAllDeletedIncluded() {
		$querySettings = $this->objectManager->get('TYPO3\\CMS\\Extbase\\Persistence\\Generic\\QuerySettingsInterface');
		$querySettings->setStoragePageIds(array(0));
		$querySettings->setIncludeDeleted(TRUE);
		$this->setDefaultQuerySettings($querySettings);
		return parent::findAll();
	}

	/**
	 * Find all ordered by the localized name
	 *
	 * @param string $orderDirection may be "asc" or "desc". Default is "asc".
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array all entries ordered by localized name
	 */
	protected function findAllOrderedByLocalizedName($orderDirection = 'asc') {
		$entities = parent::findAll();
		return $this->localizedSort($entities, $orderDirection);
	}

	/**
	 * Sort entities by the localized name
	 *
	 * @param \TYPO3\CMS\Extbase\Persistence\QueryResultInterface $entities to be sorted
	 * @param string $orderDirection may be "asc" or "desc". Default is "asc".
	 * @return array entities ordered by localized name
	 */	
	public function localizedSort(\TYPO3\CMS\Extbase\Persistence\QueryResultInterface $entities, $orderDirection = 'asc') {
		$result = $entities->toArray();
		if ($orderDirection === 'asc') {
			usort($result, array($this, 'strcollOnLocalizedName'));
		} else {
			usort($result, array($this, 'strcollOnLocalizedNameDesc'));
		}
		return $result;
	}

	/**
	 * Using strcoll comparison on localized names
	 *
	 * @return integer see strcoll
	 */
	protected function strcollOnLocalizedName($entityA, $entityB) {
		return strcoll($entityA->getNameLocalized(), $entityB->getNameLocalized());
	}

	/**
	 * Using strcoll comparison on localized names - descending order
	 *
	 * @return integer see strcoll
	 */
	protected function strcollOnLocalizedNameDesc($entityA, $entityB) {
		return strcoll($entityB->getNameLocalized(), $entityA->getNameLocalized());
	}

	/**
	 * Find all ordered by given property name
	 *
	 * @param string $propertyName property name to order by
	 * @param string $orderDirection may be "asc" or "desc". Default is "asc".
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array all entries ordered by $propertyName
	 */
	public function findAllOrderedBy($propertyName, $orderDirection = 'asc') {
		$queryResult = array();

		if ($orderDirection !== 'asc' && $orderDirection !== 'desc') {
			throw new InvalidArgumentException('Order direction must be "asc" or "desc".', 1316607580);
		}

		if ($propertyName == 'nameLocalized') {
			$queryResult = $this->findAllOrderedByLocalizedName($orderDirection);
		} else {
			$query = $this->createQuery();

			$object = $this->objectManager->create($this->objectType);
			if (!array_key_exists($propertyName, $object->_getProperties())) {
				throw new InvalidArgumentException('The model "' . $this->objectType . '" has no property "' . $propertyName . '" to order by.', 1316607579);
			}

			if ($orderDirection === 'asc') {
				$orderDirection = \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_ASCENDING;
			} else {
				$orderDirection = \TYPO3\CMS\Extbase\Persistence\QueryInterface::ORDER_DESCENDING;
			}
			$query->setOrderings(array($propertyName => $orderDirection));

			return $query->execute();
		}
		return $queryResult;
	}

	/**
	 * Adds localization columns, if needed
	 *
	 * @param string $locale: the locale for which localization columns should be added
	 * @return AbstractEntityRepository $this
	 */
	public function addLocalizationColumns($locale) {
		$dataMap = $this->dataMapper->getDataMap($this->objectType);
		$tableName = $dataMap->getTableName();
		$fieldsInfo = $this->getFieldsInfo();
		foreach ($fieldsInfo as $field => $fieldInfo) {
			if ($field != 'cn_official_name_en') {
				$matches = array();
				if (preg_match('#_en$#', $field, $matches)) {
					// Make localization field name
					$localizationField = preg_replace('#_en$#', '_' . $locale, $field);
					// Add the field if it does not yet exist
					if (!$fieldsInfo[$localizationField]) {
						// Get field length
						$matches = array();
						if (preg_match('#\(([0-9]+)\)#', $fieldInfo['Type'], $matches)) {
							$localizationFieldLength = intval($matches[1]);
							// Add the localization field
							$query = 'ALTER TABLE ' . $tableName . ' ADD ' . $localizationField . ' varchar(' . $localizationFieldLength . ') DEFAULT \'\' NOT NULL;';
							$res = $GLOBALS['TYPO3_DB']->admin_query($query);
						}
					}
				}
			}
		}
		return $this;
	}

	/**
	 * Get the information on the table fields
	 *
	 * @return array table fields information array
	 */
	protected function getFieldsInfo() {
		$fieldsInfo = array();
		$dataMap = $this->dataMapper->getDataMap($this->objectType);
		$tableName = $dataMap->getTableName();
		$fieldsInfo = $GLOBALS['TYPO3_DB']->admin_get_fields($tableName);
		return $fieldsInfo;
	}

	/**
	 * Get update queries for the localization columns for a given locale
	 *
	 * @return array Update queries
	 */
	public function getUpdateQueries($locale) {
		// Get the information of the table and its fields
		$dataMap = $this->dataMapper->getDataMap($this->objectType);
		$tableName = $dataMap->getTableName();
		$tableFields = array_keys($this->getFieldsInfo());

		$updateQueries = array();

		// If the language pack is not yet created or not yet installed, the localization columns are not yet part of the domain model
		$exportFields = array();
		foreach ($tableFields as $field) {
			$matches = array();
			if (preg_match('#_' . strtolower($locale) . '$#', $field, $matches)) {
				$exportFields[] = $field;
			}
		}
		if (count($exportFields)) {
			$updateQueries[] = '## ' . $tableName;
			$exportFields = array_merge($exportFields, $this->isoKeys);
			$rows = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows(implode(',', $exportFields), $tableName, '');
			foreach ($rows as $row) {
				$set = array();
				foreach ($row as $field => $value) {
					if (!in_array($field, $this->isoKeys)) {
						$set[] = $field . '=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($value, $tableName);
					}
				}
				$whereClause = '';
				foreach ($this->isoKeys as $field) {
					$whereClause .= ($whereClause ? ' AND ' : ' WHERE ') . $field . '=' . $GLOBALS['TYPO3_DB']->fullQuoteStr($row[$field], $tableName);
				}
				$updateQueries[] = 'UPDATE ' . $tableName . ' SET ' . implode(',', $set) . $whereClause . ';';
			}
		}
		return $updateQueries;
	}

	/**
	 * Dump non-localized contents of the repository
	 *
	 * @return	void
	 */
	public function sqlDumpNonLocalizedData() {
		// Get the information of the table and its fields
		$dataMap = $this->dataMapper->getDataMap($this->objectType);
		$tableName = $dataMap->getTableName();

		$installToolSqlParser = $this->objectManager->get('TYPO3\\CMS\\Install\\Sql\\SchemaMigrator');
		$dbFieldDefinitions = $installToolSqlParser->getFieldDefinitions_database();
		$dbFields = array();
		$dbFields[$tableName] = $dbFieldDefinitions[$tableName];
		
		$extensionKey = \TYPO3\CMS\Core\Utility\GeneralUtility::camelCaseToLowerCaseUnderscored($this->extensionName);
		$extensionPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionKey);
		$ext_tables = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl($extensionPath . 'ext_tables.sql');

		$tableFields = array_keys($dbFields[$tableName]['fields']);
		foreach ($tableFields as $field) {
			// This is a very simple check if the field is from static_info_tables and not from a language pack
			$match = array();
			if (!preg_match('#' . preg_quote($field) . '#m', $ext_tables, $match)) {
				unset($dbFields[$tableName]['fields'][$field]);
			}
		}

		$databaseUtility = $this->objectManager->get('SJBR\\StaticInfoTables\\Utility\\DatabaseUtility');
		return $databaseUtility->dumpStaticTables($dbFields);
	}
}
?>