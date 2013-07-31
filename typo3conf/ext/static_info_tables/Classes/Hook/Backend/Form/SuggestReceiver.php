<?php
namespace SJBR\StaticInfoTables\Hook\Backend\Form;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2007-2011 Andreas Wolf <andreas.wolf@ikt-werk.de>
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
 * Default implementation of a handler class for an ajax record selector.
 *
 * Normally other implementations should be inherited from this one.
 * queryTable() should not be overwritten under normal circumstances.
 *
 * @author Andreas Wolf <andreas.wolf@ikt-werk.de>
 * @author Benjamin Mack <benni@typo3.org>
 * @author Stanislas Rolland <typo3(arobas)sjbr.ca>
 */
class SuggestReceiver extends \TYPO3\CMS\Backend\Form\Element\SuggestDefaultReceiver {

	/**
	 * Prepare the statement for selecting the records which will be returned to the selector. May also return some
	 * other records (e.g. from a mm-table) which will be used later on to select the real records
	 *
	 * @return void
	 */
	protected function prepareSelectStatement() {
		$searchWholePhrase = $this->config['searchWholePhrase'];
		$searchString = $this->params['value'];
		$searchUid = intval($searchString);
		if (strlen($searchString)) {
			$searchString = $GLOBALS['TYPO3_DB']->quoteStr($searchString, $this->table);
			$likeCondition = ' LIKE \'' . ($searchWholePhrase ? '%' : '') . $GLOBALS['TYPO3_DB']->escapeStrForLike($searchString, $this->table) . '%\'';
			// Search in all fields given by label or label_alt
			
			// Get the label field for the current language, if any is available
			$lang = \SJBR\StaticInfoTables\Utility\LocalizationUtility::getCurrentLanguage();
			$lang = \SJBR\StaticInfoTables\Utility\LocalizationUtility::getIsoLanguageKey($lang);
			$labelFields = \SJBR\StaticInfoTables\Utility\LocalizationUtility::getLabelFields($this->table, $lang);
			$selectFieldsList = $labelFields[0] . ',' . $this->config['additionalSearchFields'];
			
			//$selectFieldsList = $GLOBALS['TCA'][$this->table]['ctrl']['label'] . ',' . $GLOBALS['TCA'][$this->table]['ctrl']['label_alt'] . ',' . $this->config['additionalSearchFields'];
			$selectFields = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $selectFieldsList, TRUE);


			$selectFields = array_unique($selectFields);
			$selectParts = array();
			foreach ($selectFields as $field) {
				$selectParts[] = $field . $likeCondition;
			}
			$this->selectClause = '(' . implode(' OR ', $selectParts) . ')';
			if ($searchUid > 0 && $searchUid == $searchString) {
				$this->selectClause = '(' . $this->selectClause . ' OR uid = ' . $searchUid . ')';
			}
		}
		if (isset($GLOBALS['TCA'][$this->table]['ctrl']['delete'])) {
			$this->selectClause .= ' AND ' . $GLOBALS['TCA'][$this->table]['ctrl']['delete'] . ' = 0';
		}
		if (count($this->allowedPages)) {
			$pidList = $GLOBALS['TYPO3_DB']->cleanIntArray($this->allowedPages);
			if (count($pidList)) {
				$this->selectClause .= ' AND pid IN (' . implode(', ', $pidList) . ') ';
			}
		}
		// add an additional search condition comment
		if (isset($this->config['searchCondition']) && strlen($this->config['searchCondition']) > 0) {
			$this->selectClause .= ' AND ' . $this->config['searchCondition'];
		}
		// add the global clauses to the where-statement
		$this->selectClause .= $this->addWhere;
	}

	/**
	 * Prepares the clause by which the result elements are sorted. See description of ORDER BY in
	 * SQL standard for reference.
	 *
	 * @return void
	 */
	protected function prepareOrderByStatement() {
		if ($GLOBALS['TCA'][$this->table]['ctrl']['label']) {
			$this->orderByStatement = $GLOBALS['TCA'][$this->table]['ctrl']['label'];
		}
		// Get the label field for the current language, if any is available
		$lang = \SJBR\StaticInfoTables\Utility\LocalizationUtility::getCurrentLanguage();
		$lang = \SJBR\StaticInfoTables\Utility\LocalizationUtility::getIsoLanguageKey($lang);
		$labelFields = \SJBR\StaticInfoTables\Utility\LocalizationUtility::getLabelFields($this->table, $lang);
		$this->orderByStatement = implode(',' , $labelFields);
	}

	/**
	 * Manipulate a record before using it to render the selector; may be used to replace a MM-relation etc.
	 *
	 * @param array $row
	 */
	protected function manipulateRecord(&$row) {
		// Localize the record
		$row[$GLOBALS['TCA'][$this->table]['ctrl']['label']] = \SJBR\StaticInfoTables\Utility\LocalizationUtility::translate(array('uid' => $row['uid']), $this->table);
	}
}
?>