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
 * SOLR Widget: Items-per-page Selector
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers/Widget/Solr
 */
class Tx_Fed_ViewHelpers_Widget_Solr_ItemsPerPageViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'select';

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerArgument('default', 'integer', 'Default number of items per page if not overridden by $value', FALSE, 10);
		$this->registerArgument('value', 'integer', 'Initially selected items per page', FALSE);
		$this->registerArgument('options', 'mixed', 'Array of key=>value options or CSV of possible integer values', FALSE, '10,20,50,100,200');
	}

	/**
	 * Render
	 *
	 * @return string
	 */
	public function render() {
		$this->tag->addAttribute('class', 'fed-solr-items-per-page');
		$options = $this->arguments['options'];
		$useKey = TRUE;
		if (is_string($options)) {
			$options = explode(',', $options);
			$useKey = FALSE;
		}
		$content = '';
		foreach ($options as $label=>$value) {
			$option = new Tx_Fluid_Core_ViewHelper_TagBuilder();
			$option->setTagName('option');
			if (!$useKey) {
				$label = $value;
			}
			if ($this->arguments['value'] == $value || ($this->arguments['value'] < 1 && $this->arguments['default'] == $value)) {
				$option->addAttribute('selected', 'selected');
			}
			$content .= $option->render() . LF;
		}
		$this->tag->setContent($content);
		return $this->tag->render();
	}

}

?>