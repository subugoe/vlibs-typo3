<?php
/**
 * Copyright notice
 * 
 * Copyright (c) 2007 Joerg Schoppet
 * All rights reserved
 * 
 * This script is part of the TYPO3 project. The TYPO3 project is 
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license 
 * from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */
/**
 * Central processing class and global registry
 * 
 * This class acts as a global registry for all js-lib extensions
 * and is responsible for the right processing.
 * 
 * @package		TYPO3
 * @subpackage	jsmanager
 * @author		Joerg Schoppet <joerg@schoppet.de>
 * @version		SVN: $Id: tx_jsmanager_Manager.php 7932 2008-01-17 07:45:34Z derjoerg $
 */
class tx_jsmanager_Manager {

	/**
	 * holds all registered js-lib extensions
	 * 
	 * @var	array	$registryArr
	 */
	private static $registryArr = array();

	/**
	 * holds the state, if headerData was already set
	 * 
	 * @var	bool	$included
	 */
	public static $included = FALSE;

	/**
	 * autoload-function for all classes, exceptions and interfaces within this extension
	 * 
	 * @param	string	$className
	 * @return	void
	 */
	public static function autoload($className) {

		if (strcasecmp(substr($className, 0, strlen('tx_jsmanager')), 'tx_jsmanager') == 0) {

			if (is_file(t3lib_extMgm::extPath('jsmanager', $className . '.php'))) {
				require_once (t3lib_extMgm::extPath('jsmanager', $className . '.php'));
			} // if (is_file(t3lib_extMgm::extPath('jsmanager', $className . '.php')))

		} // if (strcasecmp(substr($className, 0, strlen('tx_jsmanager')), 'tx_jsmanager') == 0)

	} // public static function autoload($className)

	/**
	 * registers the different js-lib classes in the global registry array
	 * 
	 * @param	tx_jsmanager_ManagerInterface	$object
	 * @return	void
	 */
	public static function register(tx_jsmanager_ManagerInterface $object) {
		$className = substr(get_class($object), 3);

		if (array_key_exists($className, self::$registryArr)) {
			throw new tx_jsmanager_Exception($className . ' already registered in tx_jsmanager_Manager::$registryArr');
		} // if (array_key_exists($className, self::$registryArr))

		foreach (self::$registryArr as $keyName => $registeredObject) {

			if ($object === $registeredObject) {
				throw new tx_jsmanager_Exception('Duplicate object handle already exists in tx_jsmanager_Manager::$registryArr as ' . $keyName);
			} // if ($object === $registeredObject)

		} // foreach (self::$registryArr as $keyName => $registeredObject)

		self::$registryArr[$className] = $object;
	} // public static function register(tx_jsmanager_ManagerInterface $object)

	/**
	 * unregisters the different js-lib classes in the global registry array
	 * 
	 * @param	string	$name
	 * @return	void
	 */
	public static function unregister($name) {

		if (array_key_exists($name, self::$registryArr)) {
			unset(self::$registryArr[$name]);
		} // if (array_key_exists($name, self::$registryArr))

	} // public static function unregister($name)

	/**
	 * checks if a js-lib is registered by name
	 * 
	 * @param	string	$name
	 * @return	bool
	 */
	public static function isRegistered($name) {

		if (array_key_exists($name, self::$registryArr)) {
			return TRUE;
		} // if (array_key_exists($name, self::$registryArr))

		return FALSE;
	} // public static function isRegistered($name)

	/**
	 * returns the js-object by name
	 * 
	 * @param	string	$name
	 * @return	tx_jsmanager_ManagerInterface|tx_jsmanager_Exception
	 */
	public static function retrieve($name) {

		if (self::isRegistered($name)) {
			return self::$registryArr[$name];
		} // if (self::isRegistered($name))

		throw new tx_jsmanager_Exception('The js-lib ' . $name . ' cannot be retrieved.');
	} // public static function retrieve($name)

} // class tx_jsmanager_Manager

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jsmanager/tx_jsmanager_Manager.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/jsmanager/tx_jsmanager_Manager.php']);
}

?>