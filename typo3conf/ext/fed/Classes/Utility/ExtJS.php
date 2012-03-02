<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * ExtJS integration service. Generates automatic models of DomainObjects usable
 * directly in ExtJS(4) Stores and Components.
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage Utility
 */
class Tx_Fed_Utility_ExtJS implements t3lib_Singleton {

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo;
	 */
	protected $infoService;

	/**
	 * @var Tx_Fed_ExtJS_ModelGenerator
	 */
	protected $modelGenerator;

	/**
	 * @var Tx_Extbase_Property_Mapper
	 */
	protected $propertyMapper;

	/**
	 * @param Tx_Fed_Utility_DomainObjectInfo $infoService
	 */
	public function injectInfoService(Tx_Fed_Utility_DomainObjectInfo $infoService) {
		$this->infoService = $infoService;
	}

	/**
	 * @param Tx_Fed_ExtJS_ModelGeneratpr
	 */
	public function injectModelGenerator(Tx_Fed_ExtJS_ModelGenerator $modelGenerator) {
		$this->modelGenerator = $modelGenerator;
	}

	/**
	 * @param Tx_Extbase_Property_Mapper $propertyMapper
	 */
	public function injectPropertyMapper(Tx_Extbase_Property_Mapper $propertyMapper) {
		$this->propertyMapper = $propertyMapper;
	}

	/**
	 * Exposes one or mode ModelObjects as ExtJS Model classes in Javascript
	 *
	 * @param mixed $object ModelObject to expose to ExtJS
	 * @param int $typeNum The typeNum you have registered for your plugin+controller+action combo
	 * @param array $properties Optional array of properties (of only 1st level object) to export; can override @ExtJS annotations
	 * @param string $prefix Optional prefix to add to the generated Model class. For instance "MyApplication." to get "MyApplication.MyModelClass"
	 * @param string $template Optional filename of Fluid template containing rendering instructions for a Model definition
	 * @return string
	 * @api
	 */
	public function expose($object, $typeNum, $properties=NULL, $prefix=NULL, $template=NULL) {
		$this->modelGenerator->setTypeNum($typeNum);
		$this->modelGenerator->setPrefix($prefix);
		return $this->modelGenerator->generateModelClass($object, $properties, $template);
	}

	/**
	 * Maps data from ExtJS unto a DomainObject - recursively. Sets proper types
	 * of data based on the source code annotations and uses proper setters to
	 * set values. The result can be updated or added using the corresponding
	 * Repository
	 * @param Tx_Extbase_DomainObject_AbstractDomainEntity $object
	 * @param object $data
	 * @return Tx_Extbase_DomainObject_AbstractDomainEntity
	 */
	public function mapDataFromExtJS($object, $data) {
		unset($data['uid']);
		$properties = array_keys($data);
		$mappingResult = $this->propertyMapper->map($properties, $data, $object);
		return $object;
	}

	/**
	 * Maps data onto an stdClass object - recursively - based on the ExtJS source
	 * annotations.
	 *
	 * @param Tx_Extbase_DomainObject_AbstractDomainEntity $object
	 * @return stdClass
	 */
	public function exportDataToExtJS($object) {
		$data = $this->infoService->getValuesByAnnotation($object, 'ExtJS');
		return $data;
	}

}

?>
