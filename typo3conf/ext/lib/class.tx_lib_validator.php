<?php

/**
 * A class to validate key => value pairs against a set of rules.
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
 * @version    SVN: $Id: class.tx_lib_validator.php 7333 2007-11-30 12:21:48Z elmarhinz $
 * @since      0.1
 */

/**
 * A class to validate key => value pairs against a set of rules.
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 */
class tx_lib_validator extends tx_lib_object {

	var $pathToRules;
	var $errors;

	/**
	 * Set the namespace of rules to be used for validation.
	 *
	 * @param	string		validation rules namespace
	 * @return	void
	 */
	function useRules($path = 'validationRules.') {
		$this->pathToRules = $path;
	}

	/**
	 * Validates the given object against the specified rules.
	 *
	 * @param	object		object with key=>value pairs
	 * @return	void
	 */
	function validate($object = null) {
		if(is_object($object)) {
			$this->setArray($object);
		}
		if($this->pathToRules) {
			$this->_validateByRules();
		}
		$this->set('_errorList', $this->errors);
		$this->set('_errorCount', count($this->errors));
	}

	/**
	 * Check whether there were failures during validation or not.
	 *
	 * @return	boolean		true if there are errors, false otherwise
	 */
	function ok() {
		return count($this->errors) == 0;
	}

	/**
	 * Do the actual validation.
	 *
	 * @return	void
	 * @access	private
	 */
	function _validateByRules() {
		foreach($this->controller->configurations->get($this->pathToRules) as $rule) {
			if(!preg_match($rule['pattern'], $this->get($rule['field']))) {
				$this->errors[] = array_merge(array('.type' => 'rule'), $rule);
			}
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_validator.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_validator.php']);
}

?>
