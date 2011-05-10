<?php

/**
 * Collection of functions to load and instanciate classes of PEAR framework.
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
 * @version    SVN: $Id: class.tx_lib_pearLoader.php 5733 2007-06-21 15:27:25Z sir_gawain $
 * @since      0.1
 */

/**
 * Collection of functions to load and instanciate classes of PEAR framework.
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 */
class tx_lib_pearLoader{

	/**
	 * Load a pear class
	 *
	 * Loads from extension directories ext, sysext, etc.
	 *
	 * <pre>
	 * with class only:
	 * tx_key           '.../ext/key.php' (yet not supported by manager)
	 * tx_key_file      '.../ext/key/file.php'
	 * tx_key_subs_file '.../ext/key/subs/file.php'
	 *
	 * without alternative key 'alt':
	 * tx_key           '.../ext/key.php' (yet not supported by manager)
	 * tx_key_file      '.../ext/alt/file.php'
	 * tx_key_subs_file '.../ext/alt/subs/file.php'
	 *
	 * with prefix 'class.' and suffix '.inc.php'
	 * tx_key           '.../ext/class.key.inc.php'  (yet not supported by manager)
	 * tx_key_file      '.../ext/key/class.file.inc.php'
	 * tx_key_subs_file '.../ext/key/subs/class.file.inc.php'
	 * </pre>
	 *
	 * @param	string		classname
	 * @param	string		extension key that varies from classnames
	 * @param	string		prefix of classname
	 * @param	string		ending of classname
	 * @return	boolean		TRUE if class was loaded
	 */
	function load($class, $alternativeKey='', $prefix = '', $suffix = '.php') {
		$path = tx_lib_pearLoader::_find($class, $alternativeKey, $prefix, $suffix);
		if($path) {
			require_once($path);
			if(t3lib_extMgm::isLoaded($class)) {
				return TRUE;
			} else {
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}

	/**
	 * Load a pear class and make an instance
	 *
	 * See load. Returns ux_ extension class if any.
	 *
	 * @param	string		classname
	 * @param	string		extension key that varies from classnames
	 * @param	string		prefix of classname
	 * @param	string		ending of classname
	 * @return	object		instance of class
	 */
	function makeInstance($class, $alternativeKey='', $prefix = '', $suffix = '.php') {
		tx_lib_pearLoader::load($class, $alternativeKey, $prefix, $suffix);
		return t3lib_div::makeInstance($class); // includes ux_ classes
	}

	/**
	 * Load a pear class and make an instance
	 *
	 * See load. Returns ux_ extension classname if any.
	 *
	 * @param	string		classname
	 * @param	string		extension key that varies from classnames
	 * @param	string		prefix of classname
	 * @param	string		ending of classname
	 * @return	string		classname or ux_ classname
	 */
	function makeInstanceClassName($class, $alternativeKey='', $prefix = '', $suffix = '.php') {
		tx_lib_pearLoader::load($class, $alternativeKey, $prefix, $suffix);
		return t3lib_div::makeInstanceClassName($class); // returns ux_ classes
	}

	/****************************************************************
	 * Private functions
	 ****************************************************************/

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
	 */
	function _find($class, $alternativeKey='', $prefix = '', $suffix = '.php') {
		if(preg_match('/^tx_[A-Za-z]+.*$/', $class)){  // with tx_ prefix
			$parts = split('_', trim($class));
			array_shift($parts); // strip tx
		}elseif(preg_match('/^[A-Za-z]+.*$/', $class)){ // without tx_ prefix
			$parts = split('_', trim($class));
		}else{
			$error = 'classError';
		}

		// The last part is the file anyway. Pop off.
		if(!$error && $last = array_pop($parts)){
			$file = $prefix . $last . $suffix;
		}

		// Now we find out the root
		if(!$error && $alternativeKey){
			if(count($parts)> 0) {
				// If the key is given as argument it can either replace the first part
				// of the classname or precede it. We shift it into
				// $firstWhileKeyIsGiven to remember for the case of cases.
				$firstWhileKeyIsGiven = array_shift($parts) . '/';
				$root = t3lib_extMgm::extPath($alternativeKey);
			}else{
				// If there is no part left now, the file is in the common extension root.
				// We need to pop off the extension directory again.
				if(preg_match('|(.*/)[^/]*/|',
							t3lib_extMgm::extPath($alternativeKey), $matches)){
					$root = $matches[1];
				} else {
					die('Programming error');
				}
			}
		}elseif(!$error){
			if(count($parts) > 0) {
				// If there is still a first part, it gives us the extension key.
				$first = array_shift($parts);
				if($key = tx_div::getValidKey($first)){
					$root = t3lib_extMgm::extPath($key);
				} else {
					$error = 'invalidKey 2';
				}
			}else {
				// If there is not a first part left, the file is in the common extension root.
				// We have to find the extension path by filename "last" in this case.
				// We need to pop off the extension directory again.
				if($key = tx_div::getValidKey($last)){
					if(preg_match('|(.*/)[^/]*/|', t3lib_extMgm::extPath($key),
								$matches)){ // pop directory
						$root = $matches[1];
					} else {
						die('Programming error');
					}
				} else {
					$error = 'invalidKey 1';
				}
			}
		}

		// In case of subdirectories loop them.
		if(!$error && count($parts) > 0) {
			$loop = '';
			foreach($parts as $part) {
				$loop .= $part . '/';
			}
		}

		// Combine the elements and test the path.
		if(!$error && !$path && !is_file($path = $root . $loop . $file)) {
			$path = FALSE;
		}
		if(!$error && !$path
				&& !is_file($path = $root . $firstWhileKeyIsGiven . $loop . $file)) {
			$error = 'fileNotFoundError';
		}
		if($error){
			$path =  FALSE;
		}
		return $path;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_pearLoader.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_pearLoader.php']);
}
?>
