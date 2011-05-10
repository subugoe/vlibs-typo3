<?php

/**
 * The pluripotent stem cell of lib/div
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
 * @version    SVN: $Id: class.tx_lib_object.php 7333 2007-11-30 12:21:48Z elmarhinz $
 * @since      0.1
 */

/**
 * Parent class for tx_lib_object 
 * 
 * <b>Don't use this class directly. Always use tx_lib_object.</b>
 * <b>Please also see tx_lib_object!!!</b>
 * 
 * Depends on: tx_div, tx_lib_selfAwareness, tx_lib_spl_arrayIterator, tx_lib_spl_arrayObject
 * Used by: tx_lib_object
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 * @see        tx_lib_object
 */
class tx_lib_objectBase extends tx_lib_selfAwareness  {  
	var $controller;
	var $_iterator;

	/**
	 * Constructor of the data object
	 *
	 * You can set the controller by one of the 2 parameters.
	 * You can set the data by one of the 2 prameters. Order doesn't matter.
	 *
	 * If you don't set the controller in the constructor you MUST set it by one of the functions:
	 * $this->controller($controller), $this->setController($controller);
	 *
	 * @param	mixed		controller or data array or data object
	 * @param	mixed		controller or data array or data object
	 * @return	void
	 */
	function tx_lib_objectBase($parameter1 = null, $parameter2 = null) {
		$this->_iterator = new tx_lib_spl_arrayIterator();
		if(method_exists($this, 'preset')) {
			$this->preset();
		}
		if(is_object($parameter1) && is_subclass_of($parameter1, 'tx_lib_controller')) {
			$this->controller = &$parameter1;
		} elseif(isset($parameter1)) {
			$this->overwriteArray($parameter1);
		}
		if(is_object($parameter2) && is_subclass_of($parameter2, 'tx_lib_controller')) {
			$this->controller = &$parameter2;
		} elseif(isset($parameter2)) {
			$this->overwriteArray($parameter2);
		}
		if(method_exists($this, 'construct')) {
			$this->construct();
		}
	}

	// -------------------------------------------------------------------------------------
	// Interface to tx_lib_spl_arrayObject
	// -------------------------------------------------------------------------------------

	/**
	 * Appends the given value as element to this array.
	 *
	 * @param	mixed		value to append
	 */
	function append($value){ $this->_iterator->append($value); }

	/**
	 * Sorts this array using the asort() function of PHP.
	 */
	function asort(){ $this->_iterator->asort(); }

	/**
	 * Counts the elements in the array.
	 *
	 * @return	integer		number of elements
	 */
	function count(){ return $this->_iterator->count(); }

	/**
	 * Replaces the current array handled by this object with the new one
	 * given as argument.
	 *
	 * @param	array		the new array to be set
	 */
	function exchangeArray($array){ $this->_iterator->exchangeArray($array); }

	/**
	 * Returns a copy of the array handled by this object.
	 *
	 * @return	array		a copy of the array
	 */
	function getArrayCopy(){ return $this->_iterator->getArrayCopy(); }

	/**
	 * Returns the flags associated with this object.
	 *
	 * @return	integer		the flags
	 */
	function getFlags(){ return $this->_iterator->getFlags(); }

	/**
	 * Returns a new iterator object for this array.
	 *
	 * @return	object		the new iterator
	 */
	function getIterator(){ return $this->_iterator->getIterator(); }

	/**
	 * Returns the class name of the iterator associated with this object.
	 *
	 * @return	string		iterator class name
	 */
	function getIteratorClass(){ return $this->_iterator->getIteratorClass(); }

	/**
	 * Sorts this array using the ksort() function of PHP.
	 */
	function ksort(){ $this->_iterator->ksort(); }

	/**
	 * Sorts this array using the natcasesort() function of PHP.
	 */
	function natcasesort(){ $this->_iterator->natcasesort(); }

	/**
	 * Sorts this array using the natsort() function of PHP.
	 */
	function natsort(){ $this->_iterator->natsort(); }

	/**
	 * Tests if an element exists at the given offset.
	 *
	 * @param	integer		array offset to test
	 * @return	boolean		true if element exists, false otherwise
	 */
	function offsetExists($index){ return $this->_iterator->offsetExists($index); }

	/**
	 * Returns the element at the given offset.
	 *
	 * @param	integer		the index of the element to be returned
	 * @return	mixed		the element at given index
	 */
	function offsetGet($index){ return $this->_iterator->offsetGet($index); }

	/**
	 * Writes a value to a given offset in the array.
	 *
	 * @param	integer		the offset to write to
	 * @param	mixed		the new value
	 */
	function offsetSet($index,$newval){ $this->_iterator->offsetSet($index, $newval); }

	/**
	 * Unsets the element at the given offset.
	 *
	 * @param	integer		position of array to unset
	 */
	function offsetUnset($index){ $this->_iterator->offsetUnset($index); }

	/**
	 * Sets the flags.
	 *
	 * @param	integer		the flags
	 */
	function setFlags($flags){ $this->_iterator->setFlags($flags); }

	/**
	 * Set the name of the iterator class to the one given as argument.
	 *
	 * @param	string		name of iterator class
	 */
	function setIteratorClass($iteratorClass){ $this->_iterator->setIteratorClass($iteratorClass); }

	/**
	 * Sorts this array using the uasort() function of PHP.
	 *
	 * @param	function		a function used as callback for sorting
	 */
	function uasort($userFunction){ $this->_iterator->uasort($userFunction); }

	/**
	 * Sorts this array using the uksort() function of PHP.
	 *
	 * @param	function		a function used as callback for sorting
	 */
	function uksort($userFunction){ $this->_iterator->uksort($userFunction); }

	// -------------------------------------------------------------------------------------
	// Interface to tx_lib_spl_arrayIterator
	// -------------------------------------------------------------------------------------

	/**
	 * Returns the current element in the iterated array.
	 *
	 * @return	mixed		the current element
	 */
	function current(){ return $this->_iterator->current(); }

	/**
	 * Returns the key of the current element in array.
	 *
	 * @return	mixed		the key of the current element
	 */
	function key(){ return $this->_iterator->key(); }

	/**
	 * Moves the iterator to next element in array.
	 *
	 * @return	boolean		true if there is a next element, false otherwise
	 */
	function next(){ $this->_iterator->next(); }

	/**
	 * Resets the iterator to the first element of array.
	 *
	 * @return	boolean		true if the array is not empty, false otherwise
	 */
	function rewind(){ $this->_iterator->rewind(); }

	/**
	 * Returns the element of array at index $index.
	 *
	 * @param	integer		the position of the requested element in array
	 * @return	mixed		an array element
	 */
	function seek($index){ return $this->_iterator->seek($index); }

	/**
	 * Returns the actual state of this iterator.
	 *
	 * @return	boolean		true if iterator is valid, false otherwise
	 */
	function valid(){ return $this->_iterator->valid(); }

	// -------------------------------------------------------------------------------------
	// Setters
	// -------------------------------------------------------------------------------------

	/**
	 * Import the data as an object containing a list of hash objects
	 *
	 * Takes a list array or list object of hash data as first argument 
	 * and a class (SPL type) as second argument. For each of the hash
	 * data an object of that class is created and appended to the 
	 * internal array. 
	 *
	 * @param	object		the data object list (i.e. from the model)
	 * @param	string		classname of output entries, defaults to tx_lib_object
	 * @return	void
	 */
	function asObjectOfObjects($objectList, $entryClassName = 'tx_lib_object') {
		$this->checkController(__FILE__, __LINE__);
		$entryClassName = tx_div::makeInstanceClassName($entryClassName);
		$this->clear();
		for($objectList->rewind(); $objectList->valid(); $objectList->next()) {
			$this->append(new $entryClassName($this->controller, tx_div::toHashArray($objectList->current())));
		}
	}

	/**
	 * Convert the internal elmements to objects of the given class name
	 *
	 * All (hash) elements of the internal array are transformed to objects of 
	 * the class given as parameter.
	 *
	 * By default the function tx_div::makeInstanceClassName() is applied. That means:
	 * 
	 * - The file is loaded automatically. 
	 * - XCLASS is used if available.
	 *
	 * @param  string   Class name for the internal elements.
	 * @param  boolean  Yes, apply tx_div:makeInstanceClassName().
	 * @return void
	 * @see    tx_div::makeInstanceClassName()
	 */ 
	function castElements($entryClassName = 'tx_lib_object', $callMakeInstanceClassName = TRUE) {
		if($callMakeInstanceClasName) $entryClassName = tx_div::makeInstanceClassName($entryClassName);
		for($this->rewind(); $this->valid(); $this->next()) 
			$this->set($this->key(), new $entryClassName($this->controller, tx_div::toHashArray($this->current()))); 
	}

	/**
	 * Empty the object
	 *
	 * Clear the objects array.
	 *
	 * @return	void
	 */
	function clear(){
		$this->exchangeArray(array());
	}

	/**
	 * Overwrite some of the internal array values
	 *
	 * Overwrite a selection of the internal values by providing new ones
	 * in form of a data structure of the tx_div hash family.
	 *
	 * @param	mixed		hash array, SPL object or hash string ( i.e. "key1 : value1, key2 : valu2, ... ")
	 * @param	string		possible split charaters in case the first parameter is a hash string
	 * @return	void
	 * @see		tx_div::toHashArray()
	 */
	function overwriteArray($hashData, $splitCharacters = ',;:\s') {
		$array = tx_div::toHashArray($hashData, $splitCharacters);
		foreach((array) $array as $key => $value) {
			$this->set($key, $value);
		}
	}

	/**
	 * Assign a value to a key
	 *
	 * It's just a convenient way to use the offsetSet() function from tx_lib_spl_arrayObject.
	 *
	 * @param	mixed		key
	 * @param	mixed		value
	 * @return	void
	 * @see		tx_lib_spl_arrayObject::offsetSet()
	 */
	function set($key, $value){
		$this->offsetSet($key, $value);
	}

	/**
	 * Set or exchange all array values
	 *
	 * On the one hand it works as an alias to $this->exchangeArray().
	 * On the other it is a little more flexible, as it takes all data members
	 * of the tx_div hash family as parameters.
	 *
	 * @param	mixed		hash array, SPL object or hash string ( i.e. "key1 : value1, key2 : valu2, ... ")
	 * @param	string		possible split charaters in case the first parameter is a hash string
	 * @return	void
	 * @see		tx_div::toHashArray()
	 */
	function setArray($hashData, $splitCharacters = ',;:\s'){
		$this->exchangeArray(tx_div::toHashArray($hashData, $splitCharacters));
	}

	// -------------------------------------------------------------------------------------
	// Getters
	// -------------------------------------------------------------------------------------

	/**
	 * Dump the internal data array
	 *
	 * If a key is given, only the value is selected.
	 *
	 * @param   optional key
	 * @return	void
	 */
	function dump($key = NULL){
		if($key) 
			$value = $this->get($key);
		else 
			$value = $this->getHashArray();
		print '<pre>';
		print htmlspecialchars(print_r($value, 1));
		print '</pre>';
	}

	/**
	 * Get a value for a key
	 *
	 * It's just a convenient way to use the offsetGet() function from tx_lib_spl_arrayObject.
	 *
	 * @param	mixed		key
	 * @return	mixed		value
	 * @see		tx_lib_spl_arrayObject::offsetGet()
	 */
	function get($key){
		return $this->offsetGet($key);
	}

	/**
	 * Alias for getArrayCopy
	 *
	 * @return	array		Copy of the internal array
	 */
	function getHashArray() {
		return $this->getArrayCopy();
	}

	/**
	 * Export the data as an object containing a list of objects  
	 *
	 * This object has to contain a list of hash data. 
	 * The hash data is created into the exported object as hash objects.
	 * The classes of the exported object and the entries are take as arguments.
	 *
	 * @param	string		Classname of exported object, defaults to tx_lib_object.
	 * @param	string		Classname of exported entries, defaults to tx_lib_object.
	 * @return	object  The exported object.
	 */
	function toObjectOfObjects($outputListClass = 'tx_lib_object', $outputEntryClass = 'tx_lib_object'){
		$this->checkController(__FILE__, __LINE__);
		$outputList = tx_div::makeInstance($outputListClass);
		$outputList->controller = $this->controller;
		$outputEntryClassName = tx_div::makeInstanceClassName($outputEntryClass);
		for($this->rewind(); $this->valid(); $this->next()) 
			$outputList->append(new $outputEntryClassName($this->controller, tx_div::toHashArray($this->current())));
		return $outputList;
	}

	/**
	 * Find out if there is a content for this key
	 *
	 * Returns true if something has been set for the variable,
	 * even if it is 0 or the empty string.
	 *
	 * @param	mixed		key of internal data array
	 * @return	boolean		is something set?
	 */
	function has($key) {
		return ($this->get($key) != null);
	}

	/**
	 * Find out if this object has something in his data array
	 *
	 * @return	boolean		is it empty?
	 */
	function isEmpty() {
		return ($this->count() == 0);
	}

	/**
	 * Find out if this object has something in his data array
	 *
	 * @return	boolean		is something in it?
	 */
	function isNotEmpty() {
		return ($this->count() > 0);
	}

	/**
	 * Return a selection of the object values as hash array.
	 *
	 * The parameter is of the list family defined in tx_div. (object, array, string)
	 * The return value is an of the hash type defind in tx_div.
	 *
	 * @param	mixed		string, array or object of the tx_div list family
	 * @param	string		a string of characters to split the keys string
	 * @return	array		selected values associative array
	 * @see		tx_div:toListArray();
	 */
	function selectHashArray($keys, $splitCharacters = ',;:\s') {
		foreach(tx_div::toListArray($keys, $splitCharacters) as $key) {
			$return[$key] = $this->get($key);
		}
		return (array) $return;
	}

	// -------------------------------------------------------------------------------------
	// Session
	// -------------------------------------------------------------------------------------

	/**
	 * Stores this object data under the key "key" into the current session.
	 *
	 * @param	mixed		the key
	 * @return	void
	 */
	function storeToSession($key) {
		session_start();
		$_SESSION[$key] = new tx_lib_object($this); // use a copy resp. a new object (for PHP4)
		$_SESSION[$key . '.']['className'] = $this->getClassName();
	}

	/**
	 * Retrieves data from the current session. The data is accessed by "key".
	 *
	 * @param	mixed		the key
	 * @return	void
	 */
	function loadFromSession($key) {
		session_start();
		if($className = $_SESSION[$key . '.']['className']){
			tx_div::load($className);
			session_write_close();
			session_start();
			$this->overwriteArray($_SESSION[$key]);
		}
	}

	// -------------------------------------------------------------------------------------
	// GetSetters for the controller 
	// -------------------------------------------------------------------------------------

	/**
	 * Check presence of the controller
	 *
	 * @param	string		set the __FILE__ constant
	 * @param	string		set the __LINE__ constant
	 * @return	object		tx_lib_controller
	 */
	function checkController($file, $line) {
		if(!is_object($this->controller))
			$this->_die('Missing the controller.', $file, $line);
		else
			return $this->controller;
	}

	/**
	 * Set and get the controller object
	 *
	 * @param	object		tx_lib_controller type
	 * @return	object		tx_lib_controller type
	 */
	function controller($object = NULL) {
		$object = $this->controller = $object ? $object : $this->controller;
		if(!$object) die('Missing controller in ' . __CLASS__ . ' line ' . __LINE__);
		return $object;
	}

}

/**
 * This is the "pluripotent stem cell" of lib/div.
 *
 * <b>MOST CENTRAL OBJECT</b>
 *
 * This object is the common parent of almoust all objects used in lib/div development. It provides 
 * functionality and an API that all lib/div objects have in common. By knowing this object you know
 * 90% of all objects. 
 *
 * This class implements the powerfull PHP5 interfaces <b>ArrayAccess</b> and <b>Iterator</b> and 
 * also backports them for PHP4. This is done by implementing the central SPL classes <b>ArrayObject</b> 
 * and <b>ArrayIterator</b> in form of plain PHP code. 
 *
 * <a href="http://de2.php.net/manual/en/ref.spl.php">See Standard PHP Library</a>
 *
 * <b>ArrayAccess</b>
 *
 * Access the values of an object by keys like an array. 
 *
 * PHP5: 
 *   $value = $this->parameters['exampleKey']
 * PHP4 and PHP5: 
 *   $value = $this->parameters->get('exampleKey');
 *
 * <b>Iterator</b>
 *
 * Iterate over the values of an object just like an array.
 *
 * PHP5: 
 *   foreach($this->parameters as $key => $value) { ... }
 * PHP4 and PHP5: 
 *   for($this->parameters->rewind(), $this->parameters->valid(), $this->parameters->next()) {
 *      $key = $this->parameters->key(); 
 *      $value = $this->parameters->current();
 *   }
 *
 * <b>The request cycle as a chain of SPL objects</b> 
 *
 * A central feature of SPL objects is the possiblity to feed one SPL object into the constructor of the next.
 * By this list of values can be processed by a chain of SPL objects alwasys using the same simple API. 
 * It is suggested to implement the different stations of the request cycle from request to response in form
 * of SPL objects.
 *
 * The class provides a lot of addiotional functions to make setting and getting still more comfortables.
 * Functions to store the data into the session are also provided.
 * 
 *
 * Depends on: tx_lib_objectBase
 * Used by: All object within this framework by direct or indirect inheritance.
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 * @see        tx_lib_objectBase
 */
if(t3lib_div::int_from_ver(phpversion())>=5000000) {
	eval('class tx_lib_object extends tx_lib_objectBase implements ArrayAccess, SeekableIterator{ }');
} else {
	class tx_lib_object extends tx_lib_objectBase{ }	
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_object.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_object.php']);
}
?>
