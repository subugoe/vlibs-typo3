<?php

/**
 * PHP4 implementation of the SPL class ArrayObject
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
 * @version    SVN: $Id: class.tx_lib_spl_arrayObject.php 5733 2007-06-21 15:27:25Z sir_gawain $
 * @since      0.1
 */

/**
 * PHP4 implementation of the SPL class ArrayObject
 *
 * This class would implement the interfaces: IteratorAggregate, ArrayAccess, Countable
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 */
class tx_lib_spl_arrayObject {

	var $array = array();
	var $iteratorClass;
	var $flags;

	/**
	 * Constructs a tx_lib_spl_arrayObject using the given arguments.
	 *
	 * @todo	flags is currently useless, specify with flags are used for
	 *			and which are available.
	 *
	 * @param	array		the array this object should handle
	 * @param	integer		some flags
	 * @param	string		classname of a iterator class
	 */
	function tx_lib_spl_arrayObject($array=array(), $flags=0, $iteratorClass = 'tx_lib_spl_arrayIterator') {
		$this->_setArray($array);
		$this->flags = $flags;
		$this->iteratorClass = $iteratorClass;
	}

	/**
	 * Appends the given value as element to this array.
	 *
	 * @param	mixed		value to append
	 */
	function append($value) {
		$this->array[] = $value;
	}

	/**
	 * Sorts this array using the asort() function of PHP.
	 */
	function asort() {
		asort($this->array);
	}

	/**
	 * Counts the elements in the array.
	 *
	 * @return	integer		number of elements
	 */
	function count() {
		return count($this->array);
	}

	/**
	 * Replaces the current array handled by this object with the new one
	 * given as argument.
	 *
	 * @param	array		the new array to be set
	 */
	function exchangeArray($array){
		$this->_setArray($array);
	}

	/**
	 * Returns a copy of the array handled by this object.
	 *
	 * @return	array		a copy of the array
	 */
	function getArrayCopy() {
		return $this->array;
	}

	/**
	 * Returns the flags associated with this object.
	 *
	 * @return	integer		the flags
	 */
	function getFlags() {
		return $this->flags;
	}

	/**
	 * Returns a new iterator object for this array.
	 *
	 * @return	object		the new iterator
	 */
	function getIterator() {
		$iteratorClass = $this->iteratorClass;
		tx_div::loadClass('class.' . $iteratorClass . '.php');
		return new $iteraratorClass($this->array);
	}

	/**
	 * Returns the class name of the iterator associated with this object.
	 *
	 * @return	string		iterator class name
	 */
	function getIteratorClass() {
		return $this->iteratorClass;
	}

	/**
	 * Sorts this array using the ksort() function of PHP.
	 */
	function ksort() {
		ksort($this->array);
	}

	/**
	 * Sorts this array using the natcasesort() function of PHP.
	 */
	function natcasesort() {
		natcasesort($this->array);
	}

	/**
	 * Sorts this array using the natsort() function of PHP.
	 */
	function natsort() {
		natsort($this->array);
	}

	/**
	 * Tests if an element exists at the given offset.
	 *
	 * @param	integer		array offset to test
	 * @return	boolean		true if element exists, false otherwise
	 */
	function offsetExists($index){
		return isset($this->array[$index]);
	}

	/**
	 * Returns the element at the given offset.
	 *
	 * @param	integer		the index of the element to be returned
	 * @return	mixed		the element at given index
	 */
	function offsetGet($index){
		return $this->array[$index];
	}

	/**
	 * Writes a value to a given offset in the array.
	 *
	 * @param	integer		the offset to write to
	 * @param	mixed		the new value
	 */
	function offsetSet($index,$newval){
		$this->array[$index] = $newval;
	}

	/**
	 * Unsets the element at the given offset.
	 *
	 * @param	integer		position of array to unset
	 */
	function offsetUnset($index){
		unset($this->array[$index]);
	}

	/**
	 * Sets the flags.
	 *
	 * @param	integer		the flags
	 */
	function setFlags($flags){
		$this->flags = $flags;
	}

	/**
	 * Set the name of the iterator class to the one given as argument.
	 *
	 * @param	string		name of iterator class
	 */
	function setIteratorClass($iteratorClass){
		$this->iteratorClass = $iteratorClass;
	}

	/**
	 * Sorts this array using the uasort() function of PHP.
	 *
	 * @param	function		a function used as callback for sorting
	 */
	function uasort($userFunction){
		uasort ($this->array, $userFunction);
	}

	/**
	 * Sorts this array using the uksort() function of PHP.
	 *
	 * @param	function		a function used as callback for sorting
	 */
	function uksort($userFunction){
		uksort ($this->array, $userFunction);
	}

	/**
	 * Sets the data of argument array as new array handled by this object.
	 *
	 * @param	mixed			the new array
	 * @access	private
	 */
	function _setArray($array){
		if(is_object($array)){
			$this->array = $array->getArrayCopy();
		} elseif(is_array($array)){
			$this->array = $array;
		} else {
			$this->array = array();
		}
		reset($this->array);
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/spl/class.tx_lib_spl_arrayObject.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/spl/class.tx_lib_spl_arrayObject.php']);
}
?>
