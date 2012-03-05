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
 * Injector, JS
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class Tx_Fed_ViewHelpers_ScriptViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerArgument('src', 'mixed', 'String filename or array of filenames', FALSE, NULL);
		$this->registerArgument('cache', 'boolean', 'If true, file(s) is cached', FALSE, FALSE);
		$this->registerArgument('concat', 'boolean', 'If true, files are concatenated (makes sense if $file is array)', FALSE, FALSE);
		$this->registerArgument('compress', 'boolean', 'If true, files are compressed using JSPacker', FALSE, FALSE);
		$this->registerArgument('index', 'int', 'Which index to take in additionalHeaderData - pushes current resident DOWN', FALSE, -1);
		$this->registerArgument('browser', 'mixed', 'Comma seperated list of allowed browsers for file inclusion', FALSE, NULL);
	}

	/**
	 * Inject JS file in the header code.
	 *
	 * @return string
	 */
	public function render() {
		$browser = t3lib_div::trimExplode(',', $this->arguments['browser'], TRUE);
		if(!empty($browser)) {
			if(!$this->documentHead->checkClientBrowser($browser)) return NULL;
		}
		$src = $this->arguments['src'];
		$cache = $this->arguments['cache'];
		$concat = $this->arguments['concat'];
		$compress = $this->arguments['compress'];
		$index = $this->arguments['index'];
		if ($src === NULL) {
			$js = $this->renderChildren();
			$this->documentHead->includeHeader($js, 'js', NULL, $index);
		} else if (is_array($src)) {
			$this->documentHead->includeFiles($src, $cache, $concat, $compress, $index);
		} else {
			$this->documentHead->includeFile($src, $cache, $concat, $compress, $index);
		}
		return NULL;
	}
}


?>