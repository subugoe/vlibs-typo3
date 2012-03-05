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
 * SOLR Widget: Result template
 *
 * The content of this ViewHelper gets used as template for results. DHTML fills
 * in values in elements according to their class. When loaded, the template is
 * removed from the DOM and stored in JS until needed. Results will be added to
 * the parent element of this template element when a search result is returned.
 *
 * The class name "fed-solr-result-templtae" is replaced with "fed-solr-result"
 * when results are rendered from the template and placed in the DOM.
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers/Widget/Solr
 */
class Tx_Fed_ViewHelpers_Widget_Solr_ResultViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'div';

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
	}

	/**
	 * Render
	 *
	 * @return string
	 */
	public function render() {
		$this->tag->forceClosingTag(TRUE);
		$this->tag->addAttribute('class', 'fed-solr-result-template');
		$this->tag->setContent($this->renderChildren());
		return $this->tag->render();
	}

}

?>