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
 * @subpackage ViewHelpers\Data
 */
class Tx_Fed_ViewHelpers_Data_VarViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	public function initializeArguments() {
		$this->registerArgument('name', 'string', 'Name of the variable to get or set', TRUE, NULL, TRUE);
		$this->registerArgument('value', 'mixed', 'If specified, takes value from content of this argument', FALSE, NULL, TRUE);
		$this->registerArgument('type', 'string', 'Data-type for this variable. Empty means string', FALSE, NULL, TRUE);
	}

	/**
	 * Get or set a variable
	 * @return mixed
	 */
	public function render() {
		$name = $this->arguments['name'];
		$value = $this->arguments['value'];
		$type = $this->arguments['type'];
		if ($value === NULL) {
			$value = $this->renderChildren();
		}
		if ($value) {
				// we are setting a variable
			if ($type === NULL) {
				if (is_object($value)) {
					$type = 'object';
				} else if (is_string($value)) {
					$type = 'string';
				} else if (is_int($value)) {
					$type = 'integer';
				} else if (is_float($value)) {
					$type = 'float';
				} else if (is_array($value)) {
					$type = 'array';
				}
			}
			$value = $this->typeCast($value, $type);
			if ($this->templateVariableContainer->exists($name)) {
				$this->templateVariableContainer->remove($name);
			}
			$this->templateVariableContainer->add($name, $value);
			return NULL;
		} else {
				// we are echoing a variable
			if (strpos($name, '.')) {
				$parts = explode('.', $name);
				$name = array_shift($parts);
			}
			if ($this->templateVariableContainer->exists($name)) {
				$value = $this->templateVariableContainer->get($name);
				if (is_array($parts) && count($parts) > 0) {
					$value = $this->recursiveValueRead($value, $parts);
				}
				return $value;
			} else {
				return NULL;
			}
		}
		return NULL;
	}

	/**
	 * Type-cast a value with type $type
	 * @param mixed $value
	 * @param string $type
	 */
	private function typeCast($value, $type) {
		switch ($type) {
			case 'integer':
				$value = intval($value);
				break;
			case 'float':
				$value = floatval($value);
				break;
			case 'object':
				$value = (object) $value;
				break;
			case 'array':
				// cheat a bit; assume CSV
				if (is_array($value) === FALSE) {
					$value = explode(',', $value);
				}
				break;
			case 'string':
			default:
				$value = (string) $value;
		}
		return $value;
	}

	private function recursiveValueRead($value, &$parts) {
		if ((!is_array($value) && !is_object($value)) || count($parts) === 0) {
			return $value;
		}
		$field = array_shift($parts);
		if ($field) {
			$newValue = Tx_Extbase_Reflection_ObjectAccess::getProperty($value, $field);
			return $this->recursiveValueRead($newValue, $parts);
		}
	}
}

?>