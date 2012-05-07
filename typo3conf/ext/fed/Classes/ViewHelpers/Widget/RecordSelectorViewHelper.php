<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * Record Selector Widget
 *
 * @package Fed
 * @subpackage ViewHelpers/Widget
 */
class Tx_Fed_ViewHelpers_Widget_RecordSelectorViewHelper extends Tx_Fluid_Core_Widget_AbstractWidgetViewHelper implements Tx_Fluid_Core_ViewHelper_Facets_ChildNodeAccessInterface {

	/**
	 * @var Tx_Fed_ViewHelpers_Widget_Controller_RecordSelectorController
	 */
	protected $controller;

	/**
	 * @var boolean
	 */
	protected $ajaxWidget = TRUE;

	/**
	 * @param Tx_Fed_ViewHelpers_Widget_Controller_RecordSelectorController $controller
	 */
	public function injectController(Tx_Fed_ViewHelpers_Widget_Controller_RecordSelectorController $controller) {
		$this->controller = $controller;
	}

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerArgument('id', 'string', 'If specified, is used as ID for the form field');
		$this->registerArgument('object', 'Tx_Extbase_DomainObject_AbstractDomainObject', 'Object which has property to fill');
		$this->registerArgument('property', 'string', 'Property of form object for which to select record relationships');
		$this->registerArgument('name', 'string', 'Name of input field which stores selected values', TRUE);
		$this->registerArgument('searchProperty', 'string', 'Property on the related object in which to search for the entered string', TRUE);
		$this->registerArgument('objectType', 'string', 'Optional class name of property. If not specified, detects from "object" and "property" arguments');
		$this->registerArgument('listLabel', 'string', 'Label (title) to insert before list of selected items', FALSE, 'Selected items');
		$this->registerArgument('listHelp', 'string', 'Help text to display below list of selected items');
		$this->registerArgument('buttonLabel', 'string', 'Label for the button which adds new elements', FALSE, 'Add item');
		$this->registerArgument('listButtonLabel', 'string', 'Label for the button which displays the selection list', FALSE, 'Select from list');
		$this->registerArgument('allowAdd', 'boolean', 'Allow adding of elements if a match could not be found', FALSE, FALSE);
		$this->registerArgument('allUrl', 'string', 'URL on which a JSON array of objects used in the selection list can be found', FALSE);
		$this->registerArgument('sections', 'array', 'Array of section names to render, in sequence', FALSE);
		$this->registerArgument('multiple', 'boolean', 'If TRUE, allows multiple selections', FALSE, FALSE);
	}

	/**
	 * Render
	 */
	public function render() {
		return $this->initiateSubRequest();
	}

}
?>