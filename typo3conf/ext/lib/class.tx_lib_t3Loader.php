<?php

/**
 * Collection of functions to load t3 classes and instanciate them.
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
 * @version    SVN: $Id: class.tx_lib_t3Loader.php 5733 2007-06-21 15:27:25Z sir_gawain $
 * @since      0.1
 */

require_once(t3lib_extMgm::extPath('div') . 'class.tx_div.php');

/**
 * Collection of functions to load t3 classes and instanciate them.
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 */
class tx_lib_t3Loader {

	/**
	 * Load a t3 class
	 *
	 * Loads from extension directories ext, sysext, etc.
	 *
	 * Loading: '.../ext/key/subs/prefix.class.suffix
	 *
	 * The files are searched on two levels:
	 *
	 * <pre>
	 * tx_key           '.../ext/key/class.tx_key.php'
	 * tx_key_file      '.../ext/key/class.tx_key_file.php'
	 * tx_key_file      '.../ext/key/file/class.tx_key_file.php'
	 * tx_key_subs_file '.../ext/key/subs/class.tx_key_subs_file.php'
	 * tx_key_subs_file '.../ext/key/subs/file/class.tx_key_subs_file.php'
	 * </pre>
	 *
	 * @param	string		classname or speaking part of path
	 * @param	string		extension key that varies from classname
	 * @param	string		prefix of classname
	 * @param	string		ending of classname
	 * @return	boolean		TRUE if class was loaded
	 */
	function load($minimalInformation, $alternativeKey='', $prefix = 'class.', $suffix = '.php') {
		$path = tx_lib_t3Loader::_find($minimalInformation, $alternativeKey, $prefix, $suffix);
		if($path) {
			require_once($path);
			return TRUE;
		} else {
			return FALSE;
		}
	}

	/**
	 * Load a t3 class and make an instance
	 *
	 * Returns ux_ extension class if any by make use of t3lib_div::makeInstance
	 *
	 * @param	string		classname
	 * @param	string		extension key that varies from classnames
	 * @param	string		prefix of classname
	 * @param	string		ending of classname
	 * @return	object		instance of the class or false if it fails
	 * @see		t3lib_div::makeInstance
	 * @see		load()
	 */
	function makeInstance($class, $alternativeKey='', $prefix = 'class.', $suffix = '.php') {
		if(tx_lib_t3Loader::load($class, $alternativeKey, $prefix, $suffix)) {
			return t3lib_div::makeInstance($class); // includes ux_ classes
		} else {
			return false;
		}
	}

	/**
	 * Load a t3 class and make an instance
	 *
	 * Returns ux_ extension classname if any by, making use of t3lib_div::makeInstanceClassName
	 *
	 * @param	string		classname
	 * @param	string		extension key that varies from classnames
	 * @param	string		prefix of classname
	 * @param	string		ending of classname
	 * @return	string		classname or ux_ classname
	 * @see		t3lib_div::makeInstanceClassName
	 * @see		load()
	 */
	function makeInstanceClassName($class, $alternativeKey='', $prefix = 'class.', $suffix = '.php') {
		if(tx_lib_t3Loader::load($class, $alternativeKey, $prefix, $suffix)) {
			return t3lib_div::makeInstanceClassName($class); // returns ux_ classes
		} else {
			return false;
		}
	}

	//--------------------------------------------------------------
	// Private functions
	//--------------------------------------------------------------

	/**
	 * Find path to load
	 *
	 * see load
	 *
	 * @param	string		classname
	 * @param	string		extension key that varies from classnames
	 * @param	string		prefix of classname
	 * @param	string		ending of classname
	 * @return	string		the path, FALSE if invalid
	 * @see		load()
	 */
	function _find($minimalInformation, $alternativeKey='', $prefix = 'class.', $suffix = '.php') {
		$info=trim($minimalInformation);
		$path = '';
		if(!$info) {
			$error = 'emptyParameter';
		}
		if(!$error) {
			$qSuffix = preg_quote ($suffix, '/');
			// If it is a path extract the key first.
			// Either the relevant part starts with a slash: xyz/[tx_].....php
			if(preg_match('/^.*\/([0-9A-Za-z_]+)' . $qSuffix . '$/', $info, $matches)) {
				$class = $matches[1];
			}elseif(preg_match('/^.*\.([0-9A-Za-z_]+)' . $qSuffix . '$/', $info, $matches)) {
				// Or it starts with a Dot: class.[tx_]....php

				$class = $matches[1];
			}elseif(preg_match('/^([0-9A-Za-z_]+)' . $qSuffix . '$/', $info, $matches)) {
				// Or it starts directly with the relevant part
				$class = $matches[1];
			}elseif(preg_match('/^[0-9a-zA-Z_]+$/', trim($info), $matches)) {
				// It may be the key itself
				$class = $info;
			}else{
				$error = 'classError';
			}
		}
		// With this a possible alternative Key is also validated
		if(!$error && !$key = tx_div::guessKey($alternativeKey ? $alternativeKey : $class)) {
			$error = 'classError';
		}
		if(!$error) {
			if(preg_match('/^tx_[0-9A-Za-z_]*$/', $class)) {  // with tx_ prefix
				$parts=split('_', trim($class));
				array_shift($parts); // strip tx
			}elseif(preg_match('/^[0-9A-Za-z_]*$/', $class)) { // without tx_ prefix
				$parts=split('_', trim($class));
			}else{
				$error = 'classError';
			}
		}
		if(!$error) {

			// Set extPath for key (first element)
			$first = array_shift($parts);

			// Save last element of path
			if(count($parts) > 0) {
				$last = array_pop($parts) . '/';
			}

			$dir = '';
			// Build the relative path if any
			foreach((array)$parts as $part) {
				$dir .= $part . '/';
			}

			// if an alternative Key is given use that
			$ext = t3lib_extMgm::extPath($key);

			// First we try ABOVE last directory (dir and last may be empty)
			// ext(/dir)/last
			// ext(/dir)/prefix.tx_key_parts_last.php.
			if(!$path && !is_file($path =  $ext . $dir . $prefix . $class . $suffix)) {
				$path = FALSE;
			}

			// Now we try INSIDE the last directory (dir and last may be empty)
			// ext(/dir)/last
			// ext(/dir)/last/prefix.tx_key_parts_last.php.
			if(!$path && !is_file($path =  $ext . $dir . $last . $prefix . $class . $suffix)) {
				$path = FALSE;
			}
		}
		return $path;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_t3Loader.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_t3Loader.php']);
}
?>
