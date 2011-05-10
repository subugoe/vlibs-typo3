<?php

/**
 * Provide some typical information for all objects of this library
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
 * @version    SVN: $Id: class.tx_lib_selfAwareness.php 7333 2007-11-30 12:21:48Z elmarhinz $
 * @since      0.1
 */

/**
 * Provide some typical information for all objects of this library
 *
 * This class is the common root of all inheritence.
 *
 * Provides functions to find extension key, designator, different pathes
 * and some related informations. It contains tools for debugging.
 *
 * Depends on: nothing	<br>
 * Used by: tx_lib_object
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 */
class tx_lib_selfAwareness {
	var $cObjectSingleton;
	var $defaultDesignator;
	var $defaultDestinaton;


	// -------------------------------------------------------------------------------------
	// Setters
	// -------------------------------------------------------------------------------------

	/**
	 * Set the default designator.
	 *
	 * Usefull for classes of the library tx_lib that are not inherited but directly instantiated.
	 *
	 * A default designator is rarely set explicitly. Typically you will use the fallback of the designator,
	 * the extension key by calling getDefaultDesignator() or getDesignator().
	 *
	 * @param	string		designator
	 * @return	void
	 * @see		getDesignator()
	 * @see		getDefaultDesignator()
	 */
	function setDefaultDesignator($string) {
		$this->defaultDesignator = $string;
	}

	/**
	 * Set the destination.
	 *
	 * The default destionation of
	 *
	 * @param	string		destination
	 * @return	void
	 * @see		getDestination()
	 * @see		getDefaultDestination()
	 */
	function setDefaultDestination($string) {
		$this->defaultDestination = $string;
	}

	/**
	 * Includes a file of the extension.
	 *
	 * Takes a filepath relative to the extensions main directory and includes it.
	 *
	 * @param	string		filepath relative to the extensions main directory
	 * @return	void
	 */
	function includeExtensionFile($relativePath) {
		include($this->extensionPath() . $relativePath);
	}

	// -------------------------------------------------------------------------------------
	// Getters
	// -------------------------------------------------------------------------------------

	/**
	 * Development helper function.
	 *
	 * Displays class methods, variables and all declared classes.
	 * Then exits the script.
	 *
	 * @return	void
	 */
	function api() {
		print '<h1>Class: ' . get_class($this) . '</h1>';
		print '<h2>Methodes:</h2>';
		$methods = get_class_methods($this);
		if($methods) {
			sort($methods);
			print '<ul>' . chr(10);
			foreach ($methods as $name) {
				print '<li>' . $name . '</li>' . chr(10);
			}
			print '</ul>' . chr(10);
		}
		print '<h2>Variables:</h2>';
		$variables = get_class_vars(get_class($this));
		if($variables) {
			ksort($variables);
			print '<dl>' . chr(10);
			foreach ($variables as $name => $value) {
				print '<dt><b>' . $name . ':</b></dt>' . chr(10);
				print '<dd>' . $value . '</dd>' . chr(10);
			}
			print '</dl>' . chr(10);
		}
		print '<h2>Declared classes:</h2>';
		$declaredClasses = get_declared_classes();
		if($declaredClasses) {
			sort($declaredClasses);
			print '<ul>' . chr(10);
			foreach ($declaredClasses as $name) {
				print '<li>' . $name . '</li>' . chr(10);
			}
			print '</ul>' . chr(10);
		}
		exit();
	}

	/**
	 * Singleton to access the features of cObject.
	 *
	 * This cObject should only be used to access cObject functionality, not to store
	 * any data into it, because it is a singleton and may by used from several places.
	 * Also the scope of the singleton may be altered in future.
	 *
	 * @return	object		cObject
	 */
	function findCObject() {
		if(!$this->cObjectSingleton) {
			$this->cObjectSingleton = t3lib_div::makeInstance('tslib_cObj');
		}
		return	$this->cObjectSingleton;
	}

	/**
	 * Return the classname.
	 *
	 * In PHP4 the classname is returned in lowercase.
	 * So this is only usefull for caseinsensitive tasks.
	 *
	 * @return	string		classname
	 */
	function getClassName() {
		return get_class($this);
	}

	/**
	 * Return the default designator.
	 *
	 * A designator is the name of the parameter array of forms and url and may by used for
	 * other identification purpuses. In tslib_pibase it is called prefixId.
	 *
	 * A default designator can be set as class variable.
	 * It defaults to the extension key of the controller.
	 *
	 * @return	string		default designator
	 */
	function getDefaultDesignator() {
			if($this->defaultDesignator) {
				return $this->defaultDesignator;                    // explicit given designator
			} elseif(is_object($this->controller)) {
				return $this->controller->getDefaultDesignator();   // the controllers designator
			} else {
				return $this->getExtensionKey();                    // the own extension key (it's the controller)
			}
	}

	/**
	 * Return the default destination of links.
	 *
	 * A default designation can be set to be used by links.
	 * It defaults to the page id of the controller.
	 *
	 * @return	string		default designator
	 */
	function getDefaultDestination() {
			if($this->defaultDestination) {
				return $this->defaultDestination;                    // explicitly given destination
			} elseif(is_object($this->controller)) {
				return $this->controller->getDefaultDestination();   // the controllers destination
			} else {
				return $GLOBALS['TSFE']->id;                        // the page id (it's the controller)
			}
	}

	/**
	 * Get the extension key.
	 *
	 * Automatically detects the extension key from the classname.
	 *
	 * @return	string		extension key
	 */
	function getExtensionKey() {
		if(preg_match('/^tx_([^_]+)/', get_class($this), $matches) ||
		   preg_match('/^user_([^_]+)/', get_class($this), $matches)) {
			$candidate = $matches[1];
			if($candidate != 'lib') {
				$keys = t3lib_div::trimExplode(',', $GLOBALS['TYPO3_CONF_VARS']['EXT']['extList']);
				foreach($keys as $key) {
					if($candidate == str_replace('_', '', $key)) {
						return $key;
					}
				}
			}
		}
		if('error') {
			$message .= 'No extension key could be found.' . chr(10);
			$this->_die($message, __FILE__, __LINE__);
		}
	}

	/**
	 * Find the absolute path of this extension.
	 *
	 * @return	string		path to the extension
	 * @see		t3lib_extMgm::extPath()
	 */
	function getExtensionPath() {
		return t3lib_extMgm::extPath($this->getExtensionKey());
	}

	/**
	 * Get the extension prefix.
	 *
	 * Returns the extension prefix based on $this->extensionKey.
	 * That is the extension key with underscores stripped and prefixed with tx_.
	 *
	 * The extension prefix is used by table names and for lot of other matters.
	 * See the TYPO3 coding guidelines for all information.
	 *
	 * @return	string		extension key
	 * @see		getExtensionKey()
	 */
	function getExtensionPrefix() {
		return 'tx_' . str_replace ('_','', $this->getExtensionKey());
	}

	/**
	 * Returns the id of the current frontside page.
	 *
	 * @return	integer		frontside page UID
	 */
	function getPageId() {
		return $GLOBALS['TSFE']->id;
	}

	/**
	 * Returns the result status of the object
	 *
	 * @return string 	
	 * @status alphpa
	 */
	function getResultStatus() {
		return 1;
	}

	// -------------------------------------------------------------------------------------
	// Aliases
	// -------------------------------------------------------------------------------------

	/**
	 * Return the default designator.
	 *
	 * Alias for getDefaultDesignator();
	 *
	 * @return	string		default designator
	 * @see		getDefaultDesignator()
	 */
	function getDesignator() {
		return $this->getDefaultDesignator();
	}

	/**
	 * Return the default destination
	 *
	 * Alias for getDefaultDestination();
	 *
	 * @return	string		default destination
	 * @see		getDefaultDestination()
	 */
	function getDestination() {
		return $this->getDefaultDestination();
	}

	// -------------------------------------------------------------------------------------
	// Helper
	// -------------------------------------------------------------------------------------
	
	/**
	 * Exits the current script with die() and prints a message and a file/line pair.
	 *
	 * @param	string		message to display
	 * @param	string		filename the script died
	 * @param	integer		linenumber the script died
	 * @return	void
	 * @access	protected
	 */
	function _die($text, $file, $line) {
		print '<h1>You died:</h1>';
		print '<pre><strong>' . chr(10) . $text . chr(10) . '</strong></pre>';
		print '<p>File: ' . $file . '</p>';
		print '<p>Line: ' . $line . '</p>';
		die();
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_selfAwareness.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_selfAwareness.php']);
}
?>
