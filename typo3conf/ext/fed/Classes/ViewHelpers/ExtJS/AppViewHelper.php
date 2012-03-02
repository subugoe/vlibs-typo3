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
 * ExtJS4 Application initiation ViewHelper
 *
 * Does two things:
 *
 * 1) Renders template file in argument "template" with arguments from
 * argument "arguments" - then puts the rendered result in a semi-cachable file
 * where filename is based on an md5() of the rendered content. This allows you
 * to render the same application in different contexts using different arguments
 * without having to create seperate ExtJS4 class files.
 *
 * 2) Takes tag contents and places it in header as:
 *
 * Ext.ready(function() {
 *		###TAGCONTENT###
 * });
 *
 * The ID of the DOM element to render to is available as {id} in the App Template
 * file. It is not required - but if left out we will assume that you want to
 * render to the body tag AND AVOID OUTPUTTING UNNECESSARY TAGS. If you want
 * custom targeting of your element simply take care of this in your ExtJS4 application
 * source file and, if needed, insert the necessary DOM element somewhere else in
 * the template rendering chain.
 *
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers
 */
class Tx_Fed_ViewHelpers_ExtJS_AppViewHelper extends Tx_Fed_ViewHelpers_RenderViewHelper {

	/**
	 * @var Tx_Fed_Utility_PartialRender
	 */
	protected $partialRender;

	/**
	 * @param Tx_Fed_Utility_PartialRender $partialRender
	 */
	public function injectPartialRender(Tx_Fed_Utility_PartialRender $partialRender) {
		$this->partialRender = $partialRender;
	}

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('id', 'string', 'ID of DOM element to insert', TRUE);
		$this->registerArgument('tagName', 'string', 'Optional override for the tag name to render, defaults to "div"', FALSE, 'div');
		$this->registerArgument('fluid', 'boolean', 'If TRUE, parses application source file as if it were Fluid', FALSE, FALSE);
		// inherits "template" and "arguments" params from RenderViewHelper
	}

	/**
	 * Render
	 *
	 * @return string
	 */
	public function render() {
		$arguments = $this->arguments['arguments'];
		$tag = $this->arguments['tagName'];
		$arguments['id'] = $this->arguments['id'];

		if ($this->arguments['fluid'] === TRUE) {
			$applicationScriptContent = $this->partialRender->render($this->arguments['template'], $arguments);
			$initScript = $this->renderChildren();
			$uniqid = md5($applicationScriptContent);
			$tempFile = $this->documentHead->saveContentToTempFile($applicationScriptContent, $uniqid, 'js');
			$this->includeHeader($initScript, 'js');
			$this->includeFile($tempFile);
		} else {
			$this->includeFile($this->arguments['template']);
		}
		$element = '<' . $tag . ' id="' . $this->arguments['id'] . '"><span>&nbsp;</span></' . $tag . '>' . LF;
		return $element;
	}
}



?>