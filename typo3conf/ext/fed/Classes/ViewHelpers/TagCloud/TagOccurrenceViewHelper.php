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
 * @subpackage ViewHelpers\TagCloud
 */
class Tx_Fed_ViewHelpers_TagCloud_TagOccurrenceViewHelper extends Tx_Fed_ViewHelpers_TagCloudViewHelper {

	/**
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('tag', 'string', 'Name of the tag - if empty, tries to get from content. If that is empty too, it is Exception butter jelly time!');
		$this->registerArgument('occurrences', 'int', 'Number of occurrences to add.', FALSE, 1);
	}

	/**
	 * @return string
	 */
	public function render() {
		if ($this->arguments['tag']) {
			$tagName = $this->arguments['tag'];
		} else {
			$tagName = $this->renderChildren();
		}
		if (strlen(trim($tagName)) == 0) {
			throw new Exception('Cannot register an occurrence of a tag with no name');
		}
		$this->registerOccurrence($tagName, $this->arguments['occurrences']);
		return NULL;
	}

}

?>