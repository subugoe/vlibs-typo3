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
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers/PageRenderer
 */
class Tx_Fed_ViewHelpers_PageRenderer_AddJsFileViewHelper extends Tx_Fed_ViewHelpers_PageRenderer_AbstractPageRendererViewHelper {

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerArgument('type', 'string', 'Type argument - see PageRenderer documentation', FALSE, 'text/javascript');
		$this->registerArgument('compress', 'boolean', 'Compress argument - see PageRenderer documentation', FALSE, TRUE);
		$this->registerArgument('forceOnTop', 'boolean', 'ForceOnTop argument - see PageRenderer documentation', FALSE, FALSE);
		$this->registerArgument('allWrap', 'string', 'AllWrap argument - see PageRenderer documentation', FALSE, '');
		$this->registerArgument('excludeFromConcatenation', 'string', 'ExcludeFromConcatenation argument - see PageRenderer documentation', FALSE, FALSE);
	}

	/**
	 * Render
	 *
	 * @param string $file
	 */
	public function render($file=NULL) {
		if ($this->isCached()) {
			$this->pageRenderer->addJsFile(
				$file,
				$this->arguments['type'],
				$this->arguments['compress'],
				$this->arguments['forceOnTop'],
				$this->arguments['allWrap'],
				$this->arguments['excludeFromConcatenation']
			);
		} else {
			$GLOBALS['TSFE']->additionalHeaderData[md5($file)] = '<script type="text/javascript" src="' . $file . '"></script>';
		}
	}
}

?>