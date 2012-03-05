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
 * Injector, CSS
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class Tx_Fed_ViewHelpers_StyleViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerArgument('href', 'mixed', 'String filename or array of filenames', FALSE, NULL);
		$this->registerArgument('cache', 'boolean', 'If true, file(s) is cached', FALSE, FALSE);
		$this->registerArgument('concat', 'boolean', 'If true, files are concatenated (makes sense if $file is array)', FALSE, FALSE);
		$this->registerArgument('compress', 'boolean', 'If true, files are compressed using JSPacker', FALSE, FALSE);
		$this->registerArgument('index', 'int', 'Which index to take in additionalHeaderData - pushes current resident DOWN', FALSE, -1);
		$this->registerArgument('media', 'string', 'Attributes of the stylesheet file', FALSE, NULL);
		$this->registerArgument('browser', 'mixed', 'Comma seperated list of allowed browsers for file inclusion', FALSE, NULL);
	}

	/**
	 * Inject CSS file in header or code. See examples in ScriptViewHelper
	 * the pragma is identical - only the output wrapper tags are different.
	 *
	 * @return string
	 */
	public function render() {
		$browser = t3lib_div::trimExplode(',', $this->arguments['browser'], TRUE);
		if(!empty($browser)) {
			if(!$this->documentHead->checkClientBrowser($browser)) return NULL;
		}
		$href = $this->arguments['href'];
		$cache = $this->arguments['cache'];
		$concat = $this->arguments['concat'];
		$compress = $this->arguments['compress'];
		$index = $this->arguments['index'];
		$attributes = $this->arguments['media'] ? array('media' => $this->arguments['media']) : NULL;
		if ($href) {
			$this->documentHead->includeFile($href, $cache, $concat, $compress, $index, $attributes);
		} else if ($href === NULL) {
			$css = $this->renderChildren();
			$this->documentHead->includeHeader($css, 'css', NULL, $index, $attributes);
		}
	}
}


?>