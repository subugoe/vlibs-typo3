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
 * ExtJS4 Component initiation ViewHelper
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class Tx_Fed_ViewHelpers_ExtJS_ComponentViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'div';

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$uniqid = uniqid('fedsandbox');
		$this->registerArgument('src', 'string', 'Site-relative URL to Javascript file. If empty, assumes your Component definition is the tag content', FALSE, NULL);
		$this->registerArgument('id', 'string', 'Optional ID - will be auto-generated if not added. Specify this if you use an external Component class definition file', FALSE, $uniqid);
	}

	/**
	 * Render
	 *
	 * @return string
	 */
	public function render() {
		$this->templateVariableContainer->add('id', $this->arguments['id']);
		$js = $this->renderChildren();
		$this->templateVariableContainer->remove('id');
		if ($this->arguments['src']) {
			$this->documentHead->includeFile($this->arguments['src']);
		}
		if (strlen(trim($js)) > 0) {
			$this->documentHead->includeHeader($js, 'js');
		}
		$this->tag->addAttribute('id', $this->arguments['id']);
		$this->tag->setContent('');
		$this->tag->forceClosingTag(TRUE);
		return $this->tag->render();
	}
}



?>
