<?php

/**
 * PHP4 implementation of the SPL class ArrayIterator
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
 * @version    SVN: $Id: class.tx_lib_spl_arrayIterator.php 5733 2007-06-21 15:27:25Z sir_gawain $
 * @since      0.1
 */

/**
 * PHP4 implementation of the SPL class ArrayIterator
 *
 * This class would implement the interfaces: SeekableIterator, ArrayAccess, Countable
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 */
class tx_lib_spl_arrayIterator extends tx_lib_spl_arrayObject {

	var $valid = FALSE;

	/**
	 * Returns the current element in the iterated array.
	 *
	 * @return	mixed		the current element
	 */
	function current() {
		return current($this->array);
	}

	/**
	 * Returns the key of the current element in array.
	 *
	 * @return	mixed		the key of the current element
	 */
	function key() {
		return key($this->array);
	}

	/**
	 * Moves the iterator to next element in array.
	 *
	 * @return	boolean		true if there is a next element, false otherwise
	 */
	function next() {
		$this->valid = (FALSE !== next($this->array));
	}

	/**
	 * Resets the iterator to the first element of array.
	 *
	 * @return	boolean		true if the array is not empty, false otherwise
	 */
	function rewind() {
		$this->valid = (FALSE !== reset($this->array));
	}

	/**
	 * Returns the element of array at index $index.
	 *
	 * @param	integer		the position of the requested element in array
	 * @return	mixed		an array element
	 */
	function seek($index) {
		return $this->array[$index];
	}

	/**
	 * Returns the actual state of this iterator.
	 *
	 * @return	boolean		true if iterator is valid, false otherwise
	 */
	function valid() {
		return $this->valid;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/spl/class.tx_lib_spl_arrayIterator.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/spl/class.tx_lib_spl_arrayIterator.php']);
}
?>
