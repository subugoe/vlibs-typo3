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
class Tx_Fed_ViewHelpers_TagCloud_TagViewHelper extends Tx_Fed_ViewHelpers_TagCloudViewHelper {
	
	public function initializeArguments() {
		$this->registerArgument('tag', 'string'); // universal
		$this->registerArgument('href', 'string');
		$this->registerArgument('title', 'string'); // universal
		$this->registerArgument('style', 'string'); // universal
		$this->registerArgument('occurrences', 'int', 'Number of times this tag occurs - default is zero', FALSE, 0);
	}
	
	public function render() {
		if ($this->arguments['tag']) {
			$tag = $this->arguments['tag'];
		} else {
			$tag = $this->renderChildren();
		}
		if (strlen(trim($tag)) == 0) {
			throw new Exception('Your tag must at the very least have a name, which was neither found in argument "tag" nor tag content');
		}
		$config = array(
			'tag' => $tag,
			'occurrences' => $this->arguments['occurrences'],
			'href' => $this->arguments['href'],
			'title' => $this->arguments['title'],
			'style' => $this->arguments['style'],
		);
		$this->addTag($config);
	}
	
	
}

?>