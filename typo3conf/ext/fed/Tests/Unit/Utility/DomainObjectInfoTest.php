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
 * Testcase for Tx_Fed_Utility_DomainObjectInfo
 *
 * @package TYPO3
 * @subpackage Fed/Utility
 */
class Tx_Fed_Tests_Unit_Utility_DomainObjectInfoTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $domainObjectInfo;

	public function setUp() {
		$this->domainObjectInfo = $this->objectManager->get('Tx_Fed_Utility_DomainObjectInfo');
	}

	/**
	 * @test
	 */
	public function canReadInfoFromClassname() {

	}

	/**
	 * @test
	 */
	public function canReadInfoFromInstance() {

	}

	/**
	 * @test
	 */
	public function canGetValuesByAnnotationPresence() {

	}

	/**
	 * @test
	 */
	public function canGetValuesByAnnotationValue() {

	}

	/**
	 * @test
	 */
	public function canGetRepositoryByClass() {

	}

	/**
	 * @test
	 */
	public function canGetControllerNameByClass() {

	}

	/**
	 * @test
	 */
	public function canGetExtensionNameByClass() {

	}

	/**
	 * @test
	 */
	public function canGetPluginNameByClass() {

	}

	/**
	 * @test
	 */
	public function canGetPluginNamespaceByClass() {

	}

	/**
	 * @test
	 */
	public function canGetViewTyposcriptByClass() {

	}

	/**
	 * @test
	 */
	public function canGetPartialTemplatePathByClass() {

	}

	/**
	 * @test
	 */
	public function canGetResourcePathByClass() {

	}

	/**
	 * @test
	 */
	public function canGetResourceRelPathByClass() {

	}

	/**
	 * @test
	 */
	public function canCheckForAnnotationByClassAndProperty() {

	}

	/**
	 * @test
	 */
	public function canParseObjectStorageAnnotation() {
		$input = "Tx_Extbase_Persistence_ObjectStorage<Tx_Fed_Domain_Model_DataSource>";
		$output = $this->domainObjectInfo->parseObjectStorageAnnotation($input);
		$this->assertEquals('Tx_Fed_Domain_Model_DataSource', $output);
	}

	/**
	 * @test
	 */
	public function canGetAllPropertyTypes() {

	}

	/**
	 * @test
	 */
	public function canGetSpecificPropertyTypes() {

	}

	/**
	 * @test
	 */
	public function canGetAllTagsByAnnotation() {

	}

	/**
	 * @test
	 */
	public function canGetExtensionTyposcriptConfiguration() {

	}

	/**
	 * @test
	 */
	public function canGetPropertiesByAnnotationPresence() {

	}

	/**
	 * @test
	 */
	public function canGetPropertiesByAnnotationValue() {

	}

}
?>