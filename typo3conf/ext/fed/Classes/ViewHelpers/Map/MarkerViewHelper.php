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
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers\Map
 */
class Tx_Fed_ViewHelpers_Map_MarkerViewHelper extends Tx_Fed_ViewHelpers_MapViewHelper {

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $infoService;

	/**
	 * @param Tx_Fed_Utility_DomainObjectInfo $infoService
	 */
	public function injectInfoService(Tx_Fed_Utility_DomainObjectInfo $infoService) {
		$this->infoService = $infoService;
	}

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerArgument('lat', 'float', 'Latitude');
		$this->registerArgument('lng', 'float', 'Longitude');
		$this->registerArgument('icon', 'string', 'Icon filename', FALSE, t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Icons/MapMarker.png');
		$this->registerArgument('iconCenterX', 'int', 'Icon pivot coordinate X');
		$this->registerArgument('iconCenterY', 'int', 'Icon pivot coordinate Y');
		$this->registerArgument('clickable', 'boolean', 'If true, the marker receives mouse and touch events. Default value is true.');
		$this->registerArgument('cursor', 'string', 'Mouse cursor to show on hover');
		$this->registerArgument('draggable', 'boolean', 'If true, the marker can be dragged. Default value is false.');
		$this->registerArgument('flat', 'boolean', 'If true, the marker shadow will not be displayed.');
		$this->registerArgument('raiseOnDrag', 'boolean', 'If false, disables raising and lowering the marker on drag. This option is true by default.');
		$this->registerArgument('visible', 'boolean', 'If true, the marker is visible');
		$this->registerArgument('zIndex', 'float', 'All Markers are displayed on the map in order of their zIndex, with higher values displaying in front of Markers with lower values. By default, Markers are displayed according to their latitude, with Markers of lower latitudes appearing in front of Markers at higher latitudes.');
		$this->registerArgument('infobox', 'string', 'Optional infobox HTML');
	}

	/**
	 * Render a Map Marker
	 *
	 * @param array $data Optional data (for list display among other things) of Marker. Use keynames for labels.
	 * @param Tx_Extbase_DomainObject_AbstractDomainObject $object If specified and $data not specified, reads data from this DomainObject
	 * @param array $properties If specified, uses this array of property names for data reading. If not specified, if $object and !$data then the source annotation "@map list" is used to determine which values to read from $object (see manual about GoogleMap ViewHelper)
	 * @param string $markerId Id of this map marker - used for referencing afterwards
	 * @return string
	 */
	public function render(
			array $data=array(),
			Tx_Extbase_DomainObject_AbstractDomainObject $object=NULL,
			array $properties=array(),
			$markerId=NULL
			) {
		$marker = $this->inheritArguments();
		$infoBox = $this->arguments['infobox'];
		if ($infoBox === NULL) {
			$infoBox = $this->renderChildren();
			$infoBox = trim($infoBox);
		}
		$marker['infoWindow'] = $infoBox;
		$marker['id'] = $markerId ? 'marker' . $markerId : uniqid('wsgmkr');
		if (count($data) == 0 && $object) {
			if (count($properties) == 0) {
				$addUidToProperties = FALSE;
				$data = $this->infoService ->getPropertiesByAnnytation($object, 'map', 'list', $addUidToProperties);
			} else {
				$data = array();
				foreach ($properties as $property) {
					$getter = "get" . ucfirst($property);
					$data[$property] = $object->$getter();
				}
			}
		}
		$marker['data'] = $data;
		$this->addMarker($marker);
	}

	public function addMarker($marker) {
		$layers = $this->get('layers');
		$last = array_pop(array_keys($layers));
		array_push($layers[$last], $marker);
		$this->reassign('layers', $layers);
	}

	public function addInfoWindow($infoWindow) {
		$infoWindows = $this->get('infoWindows');
		array_push($infoWindows, $infoWindow);
		$this->reassign('infoWindows', $infoWindows);
	}

}


?>