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
 * ************************************************************* */

/**
 * Group repeat count selector.
 *
 * Allows the number of fed:form.group groups to be modified through DHTML.
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers/Form/Group
 */
class Tx_Fed_ViewHelpers_Form_Group_CountSelectorViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractTagBasedViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'select';

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerUniversalTagAttributes();
		$this->registerArgument('labelZero', 'string', 'If zero is among your options you can use this label text instead of displaying a 0 in the select box');
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$amount = $this->viewHelperVariableContainer->get('Tx_Fed_ViewHelpers_Form_GroupViewHelper', 'amount');
		$maximum = $this->viewHelperVariableContainer->get('Tx_Fed_ViewHelpers_Form_GroupViewHelper', 'maximum');
		$options = array();
		$num = 0;
		while ($num < $maximum) {
			$label = $num == 0 ? $this->arguments['labelZero'] : $num;
			$selected = $num == $amount ? ' selected="selected"' : '';
			$options[] = '<option value="' . $num . '"' . $selected . '>' . $label . '</option>' . LF;
			$num++;
		}

		$this->tag->addAttribute('class', $this->arguments['class'] . ' form-field-group-count');
		$this->tag->setContent(implode(LF, $options));
		return $this->tag->render();
	}

}

?>