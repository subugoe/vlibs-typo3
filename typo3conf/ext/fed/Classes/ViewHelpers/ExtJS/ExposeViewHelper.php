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
 * Exposes a model to ExtJS - generates and includes a Model definition class file
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class Tx_Fed_ViewHelpers_ExtJS_ExposeViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Fed_Utility_ExtJS
	 */
	protected $extJS;

	/**
	 * @param Tx_Fed_ExtJS_ModelGenerator $modelGenerator
	 */
	public function injectExJS(Tx_Fed_Utility_ExtJS $extJS) {
		$this->extJS = $extJS;
	}

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerArgument('object', 'mixed', 'Model object instance or string name of Model class', TRUE);
		$this->registerArgument('typeNum', 'int', 'Typenum registered for AJAX communications - see manual', TRUE);
		$this->registerArgument('prefix', 'string', 'Optional prefix for generated class name; use to avoid collisions');
		$this->registerArgument('properties', 'array', 'optional array of property names to expose - disregards source annotation');
		$this->registerArgument('template', 'string', 'Optional filename (absolute path) of a Fluid template containing rendering instructions', FALSE, NULL);
	}

	/**
	 * Render
	 *
	 * @return string
	 */
	public function render() {
		$content = $this->extJS->expose(
				$this->arguments['object'],
				$this->arguments['typeNum'],
				$this->arguments['properties'],
				$this->arguments['prefix'],
				$this->arguments['template']
			);
		$this->includeHeader($content, 'js');
		return '';
	}
}



?>
