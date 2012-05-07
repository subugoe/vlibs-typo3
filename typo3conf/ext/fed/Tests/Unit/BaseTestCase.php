<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Claus Due <claus@wildside.dk>
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
 * Base Testcase
 *
 * @package TYPO3
 * @subpackage Fed/Tests
 */
abstract class Tx_Fed_Tests_Unit_BaseTestCase extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @param string $name
	 * @param array $data
	 * @param string $dataName
	 */
	public function __construct($name = NULL, array $data = array(), $dataName = '') {
		parent::__construct($name, $data, $dataName);
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$domainObjectInfo = $objectManager->get('Tx_Fed_Utility_DomainObjectInfo');
		$injectableProperties = $domainObjectInfo->getPropertiesByAnnotation($this, 'inject', TRUE, FALSE);
		#var_dump($injectableProperties);
		foreach ($injectableProperties as $injectableProperty) {
			$propertyType = $domainObjectInfo->getPropertyType($this, $injectableProperty);
			$instance = $objectManager->get($propertyType);
			$this->$injectableProperty = $instance;
		}
	}
}
?>