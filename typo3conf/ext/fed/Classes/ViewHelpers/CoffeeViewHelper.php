<?php

/* * *************************************************************
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
 * ************************************************************* */

/**
 * I f'ing love Coffee.
 *
 * This ViewHelper lets you render a Partial Fluid template containing
 * Javascript - in other words, "Java Script Fluid", popularly known as "Coffee"
 *
 * Does exactly what fed:script does, except uses Fluid with a special pattern
 * when parsing the file or tag content.
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers\Extbase\Widget
 */
class Tx_Fed_ViewHelpers_CoffeeViewHelper extends Tx_Fed_ViewHelpers_ScriptViewHelper {

	/**
	 * Make coffee.
	 *
	 * @return string
	 */
	public function render() {
		if ($this->arguments['src']) {
			$filename = PATH_site . $this->arguments['src'];
			$template = $this->getTemplate($filename);
			$variables = $this->templateVariableContainer->getAll();
			$template->assignMultiple($variables);
			$rendered = $template->render();
			$uniqid = md5($rendered);
			$extension = 'js';
			$tempfile = $this->documentHead->saveContentToTempFile($rendered, $uniqid, $extension);
			$this->includeFile($tempfile);
		}
		// child content passes through as usual, we're only interested in "src"
		return $this->renderChildren();
	}

}

?>
