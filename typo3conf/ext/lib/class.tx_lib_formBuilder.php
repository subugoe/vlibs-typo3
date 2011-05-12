<?php

/**
 * A <form> builder class controlled by a TypoScript config array.
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2007 Elmar Hinz
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package    TYPO3
 * @subpackage lib
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @copyright  2006-2007 Elmar Hinz
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    SVN: $Id: class.tx_lib_formBuilder.php 7333 2007-11-30 12:21:48Z elmarhinz $
 * @since      0.1
 */

/**
 * A <form> builder class controlled by a TypoScript config array.
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 */
class tx_lib_formBuilder extends tx_lib_object {

	/**
	 * Builds the <form> from a TypoScript configuration.
	 *
	 * The configuration can be given either as string or array.
	 * If it is given as string, its parsed before building process into an array.
	 *
	 * @param	string|array	TypoScript configuration as string or array
	 * @return	void
	 */
	function build($setup) {
		if(is_string($setup))
			$setup = $this->_parseTs($setup);
		$this->form = tx_div::makeInstance('tx_lib_formBase'); //TODO
		$this->form->controller($this->controller);
		$this->form->requireListControl('<dl>', '</dl>');
		$this->form->setRowPattern('%1$s%4$s<dt>%2$s</dt>%4$s<dd>%3$s</dd>%4$s');
		$content = $this->form->begin($setup['key']);
		ksort($setup);
		foreach($setup as $key => $row) {
			if(is_numeric($key))
				$content .= $this->_buildRow($row);
		}
		$content .= $this->form->end();
		$this->content = $content;
	}

	/**
	 * Renders (returns) the HTML code of the build <form>.
	 *
	 * @return	string		HTML code of form
	 */
	function render() {
		return $this->content;
	}

	/**
	 * Renders a single <form> row depending on the given TypoScript config array.
	 *
	 * @param	array		TypoScript config array for a single row
	 * @return	string		HTML code of singe form row
	 * @access	protected
	 */
	function _buildRow($row) {
		switch($element = $row['element']) {
			case 'checkboxRow':
				$out = $this->form->checkboxRow($row['key'], $row['label'], $row['attributes.'], $row['legend']);
				break;
			case 'fieldsetBegin':
				$out = $this->form->fieldsetBegin($row['key'], $row['attributes.'], $row['legend']);
				break;
			case 'fieldsetEnd':
				$out = $this->form->fieldsetEnd();
				break;
			case 'multicheckboxesRow':
				$out = $this->form->multicheckboxesRow($row['key'], $row['label'], $row['attributes.'], $row['options.']);
				break;
			case 'multiselectRow':
				$out = $this->form->multiselectRow($row['key'], $row['label'], $row['attributes.'], $row['options.']);
				break;
			case 'selectRow':
				$out = $this->form->selectRow($row['key'], $row['label'], $row['attributes.'], $row['options.']);
				break;
			default:
				if(method_exists($this->form, $element)) {
					if(substr($element, -3) == 'Row')
						$out = $this->form->$element($row['key'], $row['label'], $row['attributes.']);
					else
						$out = $this->form->$element($row['key'], $row['attributes.']);
				}
		}
		return $out;
	}

	/**
	 * Helper function to parse a TypoScript string into an array.
	 *
	 * @param	string		the TypoScript config as string
	 * @return	array		the TypoScript config as array
	 * @access	protected
	 */
	function _parseTs($typoScript) {
		require_once(PATH_t3lib.'class.t3lib_tsparser.php');
		$TSparserObject = t3lib_div::makeInstance('t3lib_tsparser');
		$TSparserObject->parse($typoScript);
		return $TSparserObject->setup;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_formBuilder.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_formBuilder.php']);
}
?>
