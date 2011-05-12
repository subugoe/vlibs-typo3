<?php

/**
 * A class to load, transport and deliver the setup parameters.
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
 * @version    SVN: $Id: class.tx_lib_configurations.php 6082 2007-07-31 18:13:49Z elmarhinz $
 * @since      0.1
 */

/**
 * A class to load, transport and deliver the setup parameters.
 *
 * Member of the central quad: $controller, $parameters, $configurations, $context.<br>
 * Address it from every controlled object as: <samp>$this->controller->configurations</samp>
 *
 * All configuration parameters should be accessed from all other classes by a 
 * $configurations object. Either use this class directly or as parent for your 
 * own inherited class.
 *
 * Configuration is loaded from TS. Flexform configuration overwrites it.
 *
 * 1. setTypoScriptConfiguration():  loads the TS array given to controller->main() by the outer framework.
 * 2. setFlexFormConfiguration():       loads flexform data (xml or array) into the object
 *
 * Details:
 *
 * 1. The $configurationArray of rendered TypoScript that is handeled to the main function
 *    of plugins by the outer framework:
 *    <samp>tx_myextension_controller->main($input, $configurationArray)</samp>.
 * 2. The flexform configuration to gives the enduser the option to overwrite some of the previous 
 *    settings.
 *
 * Depends on: tx_div, tx_lib_object	<br>
 * Instantiated in: $this->controller	<br>
 * Used by: almoust every class
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 */

class tx_lib_configurations extends tx_lib_object {

	// -------------------------------------------------------------------------------------
	// Setters
	// -------------------------------------------------------------------------------------

	/**
	 * Load a (local) configuration array into the object
	 *
	 * An configuration array of rendered TypoScript like it is handeled to the main function
	 * of plugins by the outer framework: tx_myextension_controller->main($out, $configurationArray).
	 *
	 * @param	array		direct setup input in form of a renderd TS array
	 * @return	void
	 */
	function setTypoScriptConfiguration($configuration) {
		if(is_array($configuration['configurations.']))  $configuration = $configuration['configurations.'];
		foreach((array)$configuration as $key => $value) $this->set($key, $value);
	}

	/**
	 * Load flexformdata into the object
	 *
	 * Takes a xml string or an already rendered array.
	 * Typically it would come from the field tt_content.pi_flexform
	 *
	 * This configuration assumes unique key names for the fields.
	 * The names of the sheets are of no relevance.
	 * (If you need a more sophisticated solution simply write a your
	 * own loader function in an inherited class.)
	 *
	 * @param	mixed		xml or rendered flexform array
	 * @return	void
	 */
	function setFlexFormConfiguration($xmlOrArray) {
		$languagePointer = 'lDEF'; // we don't support languages here for now
		$valuePointer = 'vDEF'; // also hardcoded here
		if (!$xmlOrArray) {
			return false;
		}
		// Converting flexform data into array if neccessary
		if (is_array($xmlOrArray)) {
			$array = $xmlOrArray;
		} else {
			$array = t3lib_div::xml2array($xmlOrArray);
		}
		$data = $array['data'];
		//
		foreach((array) $data as $sheet => $languages) {
			foreach((array) $languages[$languagePointer] as $key => $def) {
				$this->set($key, $def[$valuePointer]);
			}
		}
	}

	// -------------------------------------------------------------------------------------
	// Getters
	// -------------------------------------------------------------------------------------

	/**
	 * Get a value or an array by providing a relative pathKey
	 *
	 * The provided pathKey is relative to the part of the TS-Setup you have loaded. Examples:
	 *
	 * <pre>
	 * Absolute Setup:          'plugin.tx_myextension.configuration.parts.10.id = 33'
	 * Loaded Path:             'plugin.tx_myextension.configuration.'
	 * Resulting relative pathKey of a value:                       'parts.10.id'
	 * Resulting relative pathKey of an array:                      'parts.'
	 * Resulting relative pathKey of an array:                      'parts.10.'
	 * </pre>
	 *
	 * Mind: To query an array end with a DOT. To query a single value end without DOT.
	 *
	 * @param	string		relative setupPath
	 * @return	array|string
	 */
	function get($pathKey) {
		return $this->_queryArrayByPath($this->getArrayCopy(), $pathKey);
	}

	/**
	 * Get an array by providing a relative pathKey
	 *
	 * The provided pathKey is relative to the part of the TS-Setup you have loaded. Examples:
	 *
	 * @see		tx_lib_configurations::get()
	 * @param	string		relative setupPath
	 * @param	string		pattern used to split the result into an array
	 * @return	array
	 */
	function getExploded($pathKey, $pattern = '/[\s,]+/') {
		return (array) preg_split($pattern, $this->get($pathKey));
	}

	/**
	 * Query a uniform hash from a dataset like setup
	 *
	 * <code>
	 * persons.10.id = 103
	 * persons.10.firstName = Peter
	 * persons.10.surName = Posters
	 * persons.10.yearOfBirth = 1973
	 * persons.20.id = 104
	 * persons.20.firstName = Susan
	 * persons.20.surName = Sunny
	 * persons.20.yearOfBirth = 1965
	 * persons.30.id = 105
	 * persons.30.firstName = Mary
	 * persons.30.surName = Martins
	 * persons.30.yearOfBirth = 1989
	 *
	 * usage: $configurations->queryHash('persons.', 'firstName', 'yearOfBirth');
	 * result: array('Peter' => '1973', 'Susan' => '1965', 'Mary' => '1989');
	 * </code>
	 *
	 * @param	string		relative pathKey
	 * @param	string		key of of the wanted key
	 * @param	string		key of of the wanted value
	 * @return	array		wanted Hash (key-value-pairs)
	 */
	function queryHash($pathKey, $keyName, $valueName) {
		$selection = $this->get($pathKey);
		$array = array();
		foreach($selection as $set) {
			$array[$set[$keyName]] = $set[$valueName];
		}
		return $array;
	}

	/**
	 * Query a single dataset from a list of datasets by a key entry
	 *
	 * <code>
	 * persons.10.id = 103
	 * persons.10.firstName = Peter
	 * persons.10.surName = Posters
	 * persons.10.yearOfBirth = 1973
	 * persons.20.id = 104
	 * persons.20.firstName = Susan
	 * persons.20.surName = Sunny
	 * persons.20.yearOfBirth = 1965
	 * persons.30.id = 105
	 * persons.30.firstName = Mary
	 * persons.30.surName = Martins
	 * persons.30.yearOfBirth = 1989
	 *
	 * usage: $configurations->queryDataSet('persons.', 'id', 104);
	 * result: array('id' => '104', 'firstName' => 'Susan', 'surName' => 'Sunny', 'yearOfBirth' => '1965')
	 * </code>
	 *
	 * @param	string		relative pathKey
	 * @param	string		key of key
	 * @param	string		value of key
	 * @return	array		wanted dataset
	 */
	function queryDataSet($path, $key, $value) {
		$selection = $this->get($path);
		foreach($selection as $set) {
			if ($set[$key] == $value) {
				return $set;
			}
		}
	}

	/**
	 * Query a single data from a list of datasets by a combination of key entries
	 *
	 * <code>
	 * persons.10.id = 103
	 * persons.10.firstName = Peter
	 * persons.10.surName = Posters
	 * persons.10.yearOfBirth = 1973
	 * persons.20.id = 104
	 * persons.20.firstName = Susan
	 * persons.20.surName = Sunny
	 * persons.20.yearOfBirth = 1965
	 * persons.30.id = 105
	 * persons.30.firstName = Mary
	 * persons.30.surName = Martins
	 * persons.30.yearOfBirth = 1989
	 *
	 * usage: $configurations->queryData('persons.', 'id', 104, 'yearOfBirth');
	 * result: '1965'
	 * </code>
	 *
	 * @param	string		relative pathKey
	 * @param	string		key of key
	 * @param	string		value of key
	 * @param	string		key of the wanted result value
	 * @return	string		wanted value
	 */
	function queryData($path, $key, $value, $wanted) {
		$selection = $this->get($path);
		foreach($selection as $set) {
			if ($set[$key] == $value) {
				return $set[$wanted];
			}
		}
	}

	// -------------------------------------------------------------------------------------
	// Private functions
	// -------------------------------------------------------------------------------------

	/**
	 * ...
	 *
	 * @param	string		...
	 * @param	string		...
	 * @return	array		...
	 * @access	private
	 */
	function _queryArrayByPath($array, $path) {
		$pathArray = explode('.', trim($path));
		for($i = 0; $i < count($pathArray); $i++) {
			if ($i < (count($pathArray) -1 )) {
				$array = $array[$pathArray[$i] . '.'];
			} elseif(empty($pathArray[$i])) {
				// It ends with a dot. We return the rest of the array
				return $array;
			} else {
				// It endes without a dot. We return the value.
				return $array[$pathArray[$i]];
			}
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_configurations.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_configurations.php']);
}
?>
