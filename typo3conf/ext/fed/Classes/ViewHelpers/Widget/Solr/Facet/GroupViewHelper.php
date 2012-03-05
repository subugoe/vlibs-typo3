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
 * SOLR Widget: Facet group container
 *
 * Used as template for facets returned in the search result. When DOM is loaded,
 * the content of this ViewHelper is removed from DOM and stored in JS until
 * needed.
 *
 * When added to the DOM, copies of this template are added to the same parent
 * element the template originally had. Child elements are filled with data
 * according to their class names.
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers/Widget/Solr/Facet
 */
class Tx_Fed_ViewHelpers_Widget_Solr_Facet_GroupViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {

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
		$this->tag->addAttribute('class', 'fed-solr-facet-group-template');
		$this->tag->setContent($this->renderChildren());
		return $this->tag->render();
	}

}

?>