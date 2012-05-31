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
class Tx_Fed_ViewHelpers_Data_VarViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper implements Tx_Fluid_Core_ViewHelper_Facets_ChildNodeAccessInterface {

	/**
	 * @var array<Tx_Fluid_Core_Parser_SyntaxTree_AbstractNode>
	 */
	protected $childNodes;

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerArgument('name', 'string', 'Name of the variable to get or set', TRUE, NULL, TRUE);
		$this->registerArgument('value', 'mixed', 'If specified, takes value from content of this argument', FALSE, NULL, TRUE);
		$this->registerArgument('type', 'string', 'Data-type for this variable. Casts the value if set.', FALSE, NULL, TRUE);
		$this->registerArgument('scope', 'string', 'Scope in which to get the variable - switch this to "php" to read PHP variables by path', FALSE, 'fluid');
	}

	/**
	 * Get or set a variable
	 * @return mixed
	 */
	public function render() {
		$value = NULL;
		$name = $this->arguments['name'];
		$value = $this->arguments['value'];
		$type = $this->arguments['type'];
		$parts = array();
		if (count($this->childNodes) > 0 && isset($this->arguments['value']) === FALSE) {
			$value = $this->renderChildren();
		}
		if ($value !== NULL || isset($this->arguments['value']) === TRUE) {
				// we are setting a variable
			if ($type !== NULL) {
				$value = $this->typeCast($value, $type);
			}
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
			if ($this->arguments['scope'] === 'php') {
				global $$name;
				$allVariables = get_defined_vars();
				if (isset($allVariables[$name])) {
					$rootVariable = $allVariables[$name];
					if (count($parts) > 0) {
						return Tx_Extbase_Reflection_ObjectAccess::getPropertyPath($rootVariable, implode('.', $parts));
					} else {
						return $rootVariable;
					}
				}
			}
			if ($this->templateVariableContainer->exists($name)) {
				$value = $this->templateVariableContainer->get($name);
				if (is_array($parts) && count($parts) > 0) {
					$value = Tx_Extbase_Reflection_ObjectAccess::getPropertyPath($value, implode('.', $parts));
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
			case 'DateTime':
				// TODO: remove first if-part if TYPO3 4.5 is not supported anymore:
				if (!class_exists('t3lib_utility_Math')) {
					if (t3lib_div::testInt($value)) {
						$value = date(DateTime::W3C, $value);
					}
					$value = DateTime::createFromFormat(DateTime::W3C, $value);
					if ($value === FALSE) {
						throw new Exception('fed.data.var ViewHelper: The given value could not be converted to DateTime. Use this format: "' . DateTime::W3C . '"', 1307719788);
					}
				} else {
					// pretty easy assumption: integer = Unix timestamp
					if (t3lib_utility_Math::canBeInterpretedAsInteger($value)) {
						// Convert to interpretable string to respect the local timezone
						$value = date(DateTime::W3C, $value);
					}
					$converter = new Tx_Extbase_Property_TypeConverter_DateTimeConverter();
					$value = $converter->convertFrom($value, 'DateTime');
				}
				break;
			case 'string':
				$value = (string) $value;
		}
		return $value;
	}

	/**
	 * Sets the direct child nodes of the current syntax tree node.
	 *
	 * @param array<Tx_Fluid_Core_Parser_SyntaxTree_AbstractNode> $childNodes
	 * @return void
	 */
	public function setChildNodes(array $childNodes) {
		$this->childNodes = $childNodes;
	}
}

?>