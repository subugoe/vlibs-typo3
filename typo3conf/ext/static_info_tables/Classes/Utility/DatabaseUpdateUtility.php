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
 * Utility used by the update script of the base extension and of the language packs
 */
class DatabaseUpdateUtility {

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
	 * Do the language pack update
	 *
	 * @param string $extensionKey: extension key of the language pack
	 * @return void
	 */
	public function doUpdate($extensionKey) {
		$extPath = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionKey);
		$fileContent = explode(LF, \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl($extPath . 'ext_tables_static+adt.sql'));
		$sqlParser = $this->objectManager->get('TYPO3\\CMS\\Core\\Database\\SqlParser');
		foreach ($fileContent as $line) {
			$line = trim($line);
			if ($line && preg_match('#^UPDATE#i', $line)) {
				$parsedResult = $sqlParser->parseSQL($line);
				// WHERE clause
				$whereClause = $sqlParser->compileWhereClause($parsedResult['WHERE']);
				// Fields
				$fields = array();
				foreach ($parsedResult['FIELDS'] as $fN => $fV) {
					$fields[$fN] = $fV[0];
				}
				$res = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($parsedResult['TABLE'], $whereClause, $fields, TRUE);
			}
		}
	}
}
?>