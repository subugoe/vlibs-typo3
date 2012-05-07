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
 * Hides output from browser but renders tag content
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers
 *
 */
class Tx_Fed_ViewHelpers_HideViewHelper extends Tx_Fluid_ViewHelpers_ImageViewHelper {

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerArgument('disabled', 'boolean', 'If TRUE, renders content - use to quickly enable/disable Fluid code', FALSE, FALSE);
	}

	/**
	 * @return string
	 */
	public function render() {
		if ($this->arguments['disabled'] === TRUE) {
			return $this->renderChildren();
		} else {
			$this->renderChildren();
		}
	}

}

?>
