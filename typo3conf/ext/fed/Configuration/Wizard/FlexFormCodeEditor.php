<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * Class that renders a t3editor Code Editor in flexforms.
 *
 * @package	TYPO3
 * @subpackage	fed
 */
class Tx_Fed_Configuration_Wizard_FlexFormCodeEditor {

	public function renderField(&$parameters, &$pObj) {
		$fallback = '<textarea cols="85" rows="40" name="' . $parameters['itemFormElName'] . '">' . $parameters['itemFormElValue'] . '</textarea>' . LF;
		if (t3lib_extMgm::isLoaded('t3editor') === FALSE) {
			return $fallback;
		}
		$t3editorWizard = t3lib_extMgm::extPath('t3editor' , 'classes/class.tx_t3editor_tceforms_wizard.php');
		if (is_file($t3editorWizard) === FALSE) {
			// version of t3editor not supported; missing wizard class
			return $fallback;
		}
		require_once $t3editorWizard;
		$t3editor = t3lib_div::makeInstance('tx_t3editor');
		if (!$t3editor->isEnabled()) {
				return;
		}
		if ($parameters['params']['format'] !== '') {
				$t3editor->setModeByType($parameters['params']['format']);
		} else {
				$t3editor->setMode(tx_t3editor::MODE_MIXED);
		}

		$doc = $GLOBALS['SOBE']->doc;
		$attributes = 'rows="40" ' .
				'cols="" ' .
				'wrap="off" ' .
				'style="width:98%; height: 500px;" ' .
				'onchange="' . $parameters['fieldChangeFunc']['TBE_EDITOR_fieldChanged'] . '" ';

		$editor = $t3editor->getCodeEditor(
				$parameters['itemFormElName'], //name of field
				'fixed-font enable-tab',
				$parameters['itemFormElValue'], //value of field
				$attributes,
				'HTML > Fluid' , // text in footer of editor
				array(
						'target' => intval($pObj->target)
				)
		);
		$editor .= $t3editor->getJavascriptCode($doc);
		return $editor;
	}
}
?>