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
class Tx_Fed_ViewHelpers_Map_LayerViewHelper extends Tx_Fed_ViewHelpers_MapViewHelper {

	public function initializeArguments() {
		$this->registerArgument('lat', 'float', 'Latitude');
		$this->registerArgument('lng', 'float', 'Longitude');
		$this->registerArgument('icon', 'string', 'Icon filename', FALSE, t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Icons/MapMarker.png');
		$this->registerArgument('iconCenterX', 'int', 'Icon pivot coordinate X');
		$this->registerArgument('iconCenterY', 'int', 'Icon pivot coordinate Y');
	}

	/**
	 * Add a layer of map markers
	 *
	 * @return string
	 */
	public function render() {
		$this->addLayer();
		$this->inheritArguments();
		$this->renderChildren();
	}

	public function addLayer() {
		$layers = $this->get('layers');
		$layers[] = array();
		$this->reassign('layers', $layers);
	}

}


?>