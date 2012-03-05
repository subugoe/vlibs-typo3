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
 * SOLR Search Widget
 *
 * Creates a SOLR search form wrapper and inserts configuration necessary to
 * operate the SOLR Service.
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers/Widget
 */
class Tx_Fed_ViewHelpers_Widget_SolrViewHelper extends Tx_Fluid_Core_Widget_AbstractWidgetViewHelper implements Tx_Fluid_Core_ViewHelper_Facets_ChildNodeAccessInterface {

	/**
	 * @var Tx_Fed_ViewHelpers_Widget_Controller_SolrController
	 */
	protected $controller;

	/**
	 * @var boolean
	 */
	protected $ajaxWidget = TRUE;

	/**
	 * @param Tx_Fed_ViewHelpers_Widget_Controller_SolrController $controller
	 */
	public function injectController(Tx_Fed_ViewHelpers_Widget_Controller_SolrController $controller) {
		$this->controller = $controller;
	}

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerArgument('id', 'string', 'If specified, is used as ID for widget container');
		$this->registerArgument('options', 'array', 'Options to override options configured in TypoScript', FALSE, array());
		$this->registerArgument('templatePathAndFilename', 'string', 'Optional path to a template file containing rendering sections');
		$this->registerArgument('layoutRootPath', 'string', 'Optional Layout root path');
		$this->registerArgument('partialRootPath', 'string', 'Optional Partialroot path');
		$this->registerArgument('addDefaultStylesheet', 'boolean', 'Add default CSS file for the widget', FALSE, TRUE);
	}

	/**
	 * Render
	 */
	public function render() {
		return $this->initiateSubRequest();
	}

}

?>