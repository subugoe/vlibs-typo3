<?php
/** 
 * Collection of static functions to work in cooperation with the extension lib (lib/div)
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
 * @subpackage div
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @copyright  2006-2007 Elmar Hinz
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    SVN: $Id: class.tx_div.php 6415 2007-09-04 08:03:27Z elmarhinz $
 * @since      0.1
 */

/**
 * Collection of static functions to work in cooperation with the extension lib (lib/div)
 *
 *
 * This is a library that results of the work of the Extension Coordination Team (ECT).
 *
 * In this class we collect diverse static functions that are usefull for extension development,
 * but that didn't made their way into t3lib_div. A part of the functions are peer functions
 * to classes of the extension lib.
 *
 * <b>Contribute your own functions</b>
 *
 * You are invited to share your own most usefull functions with the world of TYPO3 developers.
 *
 * Advantages for you:
 * - You don't need to spoil the TER with new extensions only to provide a few functions.
 * - You don't need to bother with the maintainance of an individual extension.
 * - You have a unique short extension kex (tx_div) to access the different contributed functions.
 * - It is more likely that others will really use your contribution, because this library is
 *   documented in books and magazines.
 * - Your function will become a common standard.
 *
 * You can contribute via the SVN repository, by becomming a member of the project typo3xdev
 * on sourceforge.net. Please ask the project admin (Andreas Otto) for a membership to 
 * {@link http://sourceforge.net/projects/typo3xdev/ typo3xdev project on Sourceforge}.
 *
 * This collection of functions is "moderated". We want to keep the style coherent and the quality high.
 * So we will not automatically accept any contribution as it is.  We will check if your contribution
 * is usefull and adheres to the coding guidelines. We will maybe keep the leading idea of your contribution
 * but we may adapt it to fit into the style of this exension. Anyway we thank you for all your contributions.
 *
 * Style guidelines in short
 *
 * - Use camalCase.
 * - Avoid underscores and abbreviations.
 * - Use speaking function names in the imperative form.
 * - Provide documentation in Java format.
 *
 *
 * <b>Definition of data structures</b>
 *
 * <b>The list family</b>
 *
 * - listString:
 *    This is a CSV like string of values that are separated by whitespace and/or other characters.
 *    <pre>
 *     Example: 'one, two, three'
 *     Example: 'alpha beta gamma'
 *    </pre>
 *    The default splitting characters are '\s,;:': whitespace, comma, semicolon, colon.
 *    Other splitting characters can be given to the splitting functions as parameters.
 *    Elments that contain whitespace or the splitting characters are currently not supported. That may
 *    by optimized in future. Until then only use it as human written and controlled input format.
 *
 * - listArray:
 *    This is an array with integers as keys:
 *    <pre>
 *     Example: array( 'red', 'yellow', 'green')
 *     Example: split(' ', 'alpha beta gamma')
 *    </pre>
 *
 * - listObject:
 *    This is an object of the SPL type i.e. tx_lib_object with integers as keys to the internal value:
 *    <pre>
 *     Example: new tx_lib_object(array( 'red', 'yellow', 'green'))
 *     Example: new tx_lib_object(split(' ', 'alpha beta gamma'))
 *    </pre>
 *
 * <b> The hash family</b>
 *
 * - hashString:
 *    This is a string of values that are separated by whitespace and/or other characters which are ordered as pairs.
 *    The even items are the keys, the odd items are the values.
 *    <pre>
 *     Example: 'firstname Peter surname Potter email peter@example.org'
 *     Example: 'firstname: Peter,  surname: Potter,  email: peter@example.org'
 *    </pre>
 *    The default splitting characters are '\s,;:': whitespace, comma, semicolon, colon.
 *    Other splitting characters can be given to the splitting functions as parameters.
 *    Elments that contain whitespace or the splitting characters are currently not supported. That may
 *    by optimized in future. Until then only use it as human written and controlled input format.
 *
 * - hashArray:
 *    This is an array of key value pairs:
 *    <pre>
 *     Example: array( 'firstname' => 'Peter', 'surname' => 'Potter')
 *    </pre>
 *
 * - hashObject:
 *    This is an object of the SPL type i.e. tx_lib_object with keys and values:
 *    <pre>
 *     Example: new tx_lib_object(array( 'firstname' => 'Peter', 'surname' => 'Potter'))
 *    </pre>
 *
 * @package    TYPO3
 * @subpackage div
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 */

class tx_div {

	/**
	 * Do a recursive require_once for all classes of an extension or subdirectory
	 *
	 * This function is a workaround for the __autoload() function from PHP5.
	 * - Limitation: Doesn't work for "intra extension inheritance". (see below)
	 * - Disadvantage: All classfiles are required, even if not used.
	 * - Advantage: It also works in PHP4.
	 * - Advantage: It doesn't require the same __autolod pattern for all extensions.
	 * - Alternative: tx_div::load() to require on demand.
	 *
	 * Usage example:
	 *
	 * In ext_localconf.php
	 * <code>
	 *    require_once(t3lib_extMgm::extPath('div') . 'class.tx_div.php');
	 *    if(TYPO3_MODE == 'FE') tx_div::autoLoadAll($_EXTKEY);
	 * </code>
	 *
	 * Intra extension inheritance limitation:
	 *
	 * This function doesn't work trustworthy, for classes that inherit from other
	 * classes within the same extension, because it doesn't respect the required
	 * order. Workaround: Use tx_div::load() to require an extension internal
	 * class as parent.
	 *
	 * @todo    Improve Intra extension inheritance limitation
	 * @see     tx_div::load()
	 *
	 * @param	string		preg pattern matching the filenames to require
	 * @param	string		extension Key
	 * @param	string		subdirectory
	 * @return	void
	 */
	function autoLoadAll($extensionKey, $subdirectory = '', $pregFileNamePattern='/^class[.]tx_(.*)[.]php$/') {
		// Format subdirectory first to '.../' or ''
		preg_match('/^\/?(.*)\/?$/', $subdirectory, $matches);
		$subdirectory = strlen($matches[1]) ? $matches[1] . '/' : '';
		$path = t3lib_extMgm::extPath($extensionKey) . $subdirectory;
		if(is_dir($path)) {
			$handle = opendir($path);
			while($entry = readdir($handle)) {
				if(preg_match($pregFileNamePattern, $entry)) {
					require_once($path . $entry);
				} elseif(is_dir($path . $entry) && !preg_match('/\./', $entry)) {
					tx_div::autoLoadAll($extensionKey, $subdirectory . $entry, $pregFileNamePattern);
				}
			}
		} else {
			die('No such directory: ' . $path . ' in ' . __FILE__ . ' line ' . __LINE__);
		}
	}
	
	/**
	 * Using the browser session
	 *
	 * The browser session is bound to the browser not to the frontend user.
	 *
	 * The value for the given key is returned.
	 * If a value is given it is stored into the session before. 
	 *
	 * @param  session 	key
	 * @param  mixed 	sesion value
	 * @return mixed	session value
	 * @see    userSeesion()
	 * @see    session()
	 */
	function browserSession($key, $value = NULL) {
		if($value != NULL) 
			$GLOBALS['TSFE']->fe_user->setKey('ses', $key, $value);
		return $GLOBALS['TSFE']->fe_user->getKey('ses', $key);
	}

	/**
	 * Clear all caches
	 *
	 * WARNING: Only use during development!!!!
	 * It's not a runtime function. If you use it during development keep in mind, 
	 * that functionality may depend on the cached content. So the use can lead to 
	 * irritating results.
	 *
	 * @return	void
	 */
	function clearAllCaches() {
		require_once(PATH_t3lib.'class.t3lib_tcemain.php');
		$tce = new t3lib_tcemain();
		$tce->admin = TRUE;
		$tce->clear_cacheCmd('all');
	}

	/**  
	 * Get the database object TYPO3_DB
	 * 
	 * Alias for the function getDataBase().
	 *
	 * @return object TYPO3_DB
	 * @see tx_div::getDataBase()
	 */ 
	function db(){
		return tx_div::getDataBase();
	}

	/**
	 * Explode a list into an array
	 *
	 * Explodes a string by any number of the given charactrs.
	 * By default it uses comma, semicolon, colon and whitespace.
	 *
	 * The returned values are trimmed.
	 *
	 * @param	string		string to split
	 * @param	string		regular expression that defines the splitter
	 * @return	array		with the results
	 */
	function explode($value, $splitCharacters = ',;:\s') {
		$pattern = '/[' . $splitCharacters . ']+/';
		$results = preg_split($pattern, $value, -1, PREG_SPLIT_NO_EMPTY);
		$return = array();
		foreach($results as $result)
		 $return[] = trim($result);
		return (array) $return;
	}

	/**
	 * Get an instance of the T3 core engine (TCE)
	 *
	 * This function requires that a BE user is logged in. 
	 * You can log in a BE user into the FE i.e. by using 
	 * the extension "simulatebe". Alternatively you can 
	 * work with a "faked" $BE_USER object.
	 * 
	 * @return	object		TCE
	 */
	function findTce() {
		global $BE_USER, $TCA, $PAGES_TYPES, $ICON_TYPES, $LANG_GENERAL_LABELS, $TBE_STYLES, $TBE_MODULES, $FILEICONS;
		ob_start();
		require(PATH_t3lib.'stddb/tables.php');
		require(PATH_t3lib.'stddb/load_ext_tables.php');
		require_once(PATH_t3lib.'class.t3lib_tcemain.php');
		ob_end_clean();
		if(!isset($tce)) {
			static $tce; // Singleton.
			$tce = t3lib_div::makeInstance('t3lib_tcemain');
			$tce->stripslashes_value = 0;
		}
		return $tce;
	}

	/**  
	 * Get the database object TYPO3_DB
	 *
	 * @return object TYPO3_DB
	 * @see tx_div::db();
	 */ 
	function getDataBase() {
		return tx_div::getGlobal('TYPO3_DB');
	}

	/** 
	 * Get the frontend user
	 *
	 * @return object The current frontend user.
	 * @see tx_div::user();
	 */ 
	function getFrontEndUser() {
	 return $GLOBALS['TSFE']->fe_user;
	}

	/**
	 * Load the site relative extension path for the given extension key.
	 * 
	 * @param string Extension key to resolve.
	 * @return string Site relative path. FALSE if not found.
	 */
	function getSiteRelativeExtensionPath($key) {
		global $TYPO3_LOADED_EXT;
		if(isset($TYPO3_LOADED_EXT[$key]['siteRelPath']) ) {
			return $TYPO3_LOADED_EXT[$key]['siteRelPath'];
		} else {
			return FALSE;
		}
	}

	/**
	 * Check if the given extension key is within the loaded extensions
	 *
	 * The key can be given in the regular format or with underscores stripped.
	 *
	 * @param	string		extension key to check
	 * @return	boolean		is the key valid?
	 */
	function getValidKey($rawKey) {
		$uKeys = array_keys(tx_div::getGlobal('TYPO3_LOADED_EXT'));
		foreach((array)$uKeys as $uKey) {
			if( str_replace('_', '', $uKey) == str_replace('_', '', $rawKey) ){
				$result =  $uKey;
			}
		}
		return $result ? $result : FALSE;
	}


	/**
	 * Guess the key from the given information
	 *
	 * Guessing has the following order:
	 *
	 * 1. A KEY itself is tried.
	 *    <pre>
	 *     Example: my_extension
	 *    </pre>
	 * 2. A classnmae of the pattern tx_KEY_something_else is tried.
	 *    <pre>
	 *     Example: tx_myextension_view
	 *    </pre>
	 * 3. A full classname of the pattern ' * tx_KEY_something_else.php' is tried.
	 *    <pre>
	 *     Example: class.tx_myextension_view.php
	 *     Example: brokenPath/class.tx_myextension_view.php
	 *    </pre>
	 * 4. A path that starts with the KEY is tried.
	 *    <pre>
	 *     Example: my_extension/class.view.php
	 *    </pre>
	 *
	 * @param	string		the minimal necessary information (see 1-4)
	 * @return	string		the guessed key, FALSE if no result
	 */
	function guessKey($minimalInformation) {
		$info=trim($minimalInformation);
		$key = FALSE;
		if($info){
			// Can it be the key itself?
			if(!$key && preg_match('/^([A-Za-z_]*)$/', $info, $matches ) ) {
				$key = $matches[1];
				$key = tx_div::getValidKey($key);
			}
			// Is it a classname that contains the key?
			if(!$key && (preg_match('/^tx_([^_]*)(.*)$/', $info, $matches ) || preg_match('/^user_([^_]*)(.*)$/', $info, $matches )) ) {
				$key = $matches[1];
				$key = tx_div::getValidKey($key);
			}
			// Is there a full filename that contains the key in it?
			if(!$key && (preg_match('/^.*?tx_([^_]*)(.*)\.php$/', $info, $matches ) || preg_match('/^.*?user_([^_]*)(.*)\.php$/', $info, $matches )) ) {
				$key = $matches[1];
				$key = tx_div::getValidKey($key);
			}
			// Is it a path that starts with the key?
			if(!$key && $last = strstr('/',$info)) {
				$key = substr($info, 0, $last);
				$key = tx_div::getValidKey($key);
			}
		}
		return $key ? $key : FALSE;
	}

	/**
	 * Get a global variable
	 *
	 * @param string   The key of the global variable
	 * @return mixed   The global variable.
	 */
	function getGlobal($key) {
		return $GLOBALS[$key];
	}

	/**
	 * This function is an alias for tx_div::loadClass() for your convinience
	 *
	 * @param	string		classname or path matching for the type of loader
	 * @return	boolean		true if successfull else false
	 * @see     tx_div::loadClass()
	 */
	function load($classNameOrPathInformation) {
		return tx_div::loadClass($classNameOrPathInformation);
	}


	/**
	 * Load the class file
	 *
	 * Load the file for a given classname 'tx_key_path_file'
	 * or a given part of the filepath that contains enough information to find the class.
	 *
	 * @param	string		classname or path matching for the type of loader
	 * @return	boolean		true if successfull, false otherwise
	 * @see     tx_lib_t3Loader
	 * @see     tx_lib_pearLoader
	 */
	function loadClass($classNameOrPathInformation) {
		require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_t3Loader.php');
		if(tx_lib_t3Loader::load($classNameOrPathInformation)) {
			return true;
		}
		print '<p>Trying Pear Loader: ' . $classNameOrPathInformation;
		require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_pearLoader.php');
		if(tx_lib_pearLoader::load($classNameOrPathInformation)) {
			return true;
		}
		return false;
	}


	/**
	 * Loads TCA additions of other extensions
	 *
	 * Your extension may depend on fields that are added by other
	 * extensions. For reasons of performance parts of the TCA are only
	 * loaded on demand. To ensure that the extended TCA is loaded for
	 * the extensions yours depends you can apply this function.
	 *
	 * @param       array  extension keys which have TCA additions to load
	 * @return      void
	 * @author      Franz Holzinger
	 */
	function loadTcaAdditions($ext_keys) {
		global $_EXTKEY, $TCA;
		//Merge all ext_keys
		if (is_array($ext_keys)) {
			for($i = 0; $i < sizeof($ext_keys); $i++){
				//Include the ext_table
				$_EXTKEY = $ext_keys[$i];
				include(t3lib_extMgm::extPath($ext_keys[$i]).'ext_tables.php');
			}
		}
	}


	/**
	 * Load the class file and make an instance of the class
	 *
	 * This is an extension to t3lib_div::makeInstance(). The advantage
	 * is that it tries to autoload the file wich in combination
	 * with the shorter notation simplyfies the generation of objects.
	 *
	 * @param	string		classname
	 * @return	object		the instance else FALSE
	 * @see     tx_div::makeInstanceClassName
	 * @see     tx_lib_t3Loader
	 * @see     tx_lib_pearLoader
	 */
	function makeInstance($className) {
		$instance = false;
		if(!is_object($instance)) {
			require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_t3Loader.php');
			$instance = tx_lib_t3Loader::makeInstance($className);
		}
		if(!is_object($instance)) {
			require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_pearLoader.php');
			$instance = tx_lib_pearLoader::makeInstance($className);
		}
		if(!is_object($instance)) {
			return false;
		} else {
			return $instance;
		}
	}


	/**
	 * Load the class file, return the classname or the ux_classname
	 *
	 * This is an extension to t3lib_div::makeInstanceClassName. The advantage
	 * is that it tries to autoload the file. In combination with the shorter
	 * notation it simplyfies the finding of the classname.
	 *
	 * @param	string		classname
	 * @return	string		classname or ux_classsname (maybe  service classname)
	 * @see     tx_div::makeInstance
	 * @see     tx_lib_t3Loader
	 * @see     tx_lib_pearLoader
	 */
	function makeInstanceClassName($inputName) {
		$outputName = false;
		if(!$outputName) {
			require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_t3Loader.php');
			$outputName = tx_lib_t3Loader::makeInstanceClassName($inputName);
		}
		if(!$outputName) {
			require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_pearLoader.php');
			$outputName = tx_lib_pearLoader::makeInstanceClassName($inputName);
		}
		return $outputName;
	}

	/**
	 * Resolves the "EXT:" prefix relative to PATH_site. If not given the path is untouched.
	 * 
	 * @param string Path to resolve.
	 * @return string Resolved path.
	 */
	function resolvePathWithExtPrefix($path) {
		if(substr($path, 0, 4) == 'EXT:') {
			list($extKey, $local) = explode('/', substr($path,4),2);
			if(t3lib_extMgm::isLoaded($extKey)) {
				$path = tx_div::getSiteRelativeExtensionPath($extKey) . $local;
			}
		}
		return $path;
	}

	/**
	 * Using the browser session
	 *
	 * This is an alias for the function tx_div::browserSession()
	 *
	 * @param  session	key
	 * @param  mixed	sesion value
	 * @return mixed	session value
	 * @see    userSeesion()
	 * @see    browserSession()
	 */
	function session($key, $value = NULL) {
		return tx_div::browserSession($key, $value);
	}

	/**  
	 * Get an instance of the T3 core engine (TCE)
	 * Alias for the function findTce() 
	 *
	 * @return object TYPO3_DB
	 * @see tx_div::findTce()
	 */ 
	function tce(){
		return tx_div::findTce();
	}

	/**
	 * Converts the given mixed data into an hashArray
	 *
	 * @param   mixed       data to be converted
	 * @param   string      string of characters used to split first argument
	 * @return  array       an hashArray
	 */
	function toHashArray($mixed, $splitCharacters = ',;:\s' ) {
		if(is_string($mixed)) {
			$array = tx_div::explode($mixed, $splitCharacters); // TODO: Enable empty values by defining a better explode functions.
			for($i = 0; $i < count($array); $i = $i + 2) {
				$hashArray[$array[$i]] = $array[$i+1];
			}
		} elseif(is_array($mixed)) {
			$hashArray = $mixed;
		} elseif(is_object($mixed) && method_exists($mixed, 'getArrayCopy')) {
			$hashArray = $mixed->getArrayCopy();
		} else {
			$hashArray = array();
		}
		return $hashArray;
	}


	/**
	 * Converts the given mixed data into an hashObject
	 *
	 * @param   mixed       data to be converted
	 * @param   string      string of characters used to split first argument
	 * @return  object      an hashObject
	 */
	function toHashObject($mixed, $splitCharacters = ',;:' ) {
		return new tx_lib_object(tx_div::toHashArray($mixed, $splitCharacters));
	}


	/**
	 * Converts the given mixed data into an hashString
	 *
	 * @param   mixed       data to be converted
	 * @param   string      string of characters used to split first argument
	 * @return  string      an hashString
	 */
	function toHashString($mixed, $splitCharacters = ',;:' ) {
		$array = tx_div::toHashArray($mixed, $splitCharacters);
		$string = '';
		for($i = 0; $i < count($array); $i = $i + 2) {
			$string .= $array[$i] . ' : ' . $array[$i + 1] . ', ';
		}
		return $string ? substr($sting, 0, -1) : false;
	}


	/**
	 * Converts the given mixed data into a listArray
	 *
	 * @param   mixed       data to be converted
	 * @param   string      string of characters used to split first argument
	 * @return  array       a listArray
	 */
	function toListArray($mixed, $splitCharacters = ',;:\s') {
		if(is_string($mixed)) {
			$listArray = tx_div::explode($mixed, $splitCharacters);
		} elseif(is_array($mixed)) {
			$listArray = array_values($mixed);
		} elseif(is_object($mixed) && method_exists($mixed, 'getArrayCopy')) {
			$listArray = array_values($mixed->getArrayCopy());
		} else {
			$listArray = array();
		}
		return $listArray;
	}


	/**
	 * Converts the given mixed data into a listObject
	 *
	 * @param   mixed       data to be converted
	 * @param   string      string of characters used to split first argument
	 * @return  object      a listObject
	 */
	function toListObject($mixed, $splitCharacters = ',;:' ) {
		return new tx_lib_object(tx_div::toListArray($mixed, $splitCharacters));
	}


	/**
	 * Converts the given mixed data into a listString
	 *
	 * @param   mixed       data to be converted
	 * @param   string      string of characters used to split first argument
	 * @return  string      a listString
	 */
	function toListString($mixed, $splitCharacters = ',;:' ) {
		return implode(', ', tx_div::toListArray($mixed, $splitCharacters));
	}

	/**
	 * Get the frontend user 
	 *
	 * Alias to getFrontEndUser();
	 * 
	 * @return	object	The current frontend user.	
	 */
	function user() {
		return tx_div::getFrontEndUser();
	}

	/**
	 * Using the user session
	 *
	 * The user session is bound to the frontend user.
	 *
	 * The value for the given key is returned.
	 * If a value is given it is stored into the session before. 
	 *
	 * @param  session key
	 * @param  mixed sesion value
	 * @return	mixed	session value
	 * @see    browserSeesion()
	 */
	function userSession($key, $value = NULL) {
		if($value != NULL) 
			$GLOBALS['TSFE']->fe_user->setKey('user', $key, $value);
		return $GLOBALS['TSFE']->fe_user->getKey('user', $key);
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/div/class.tx_div.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/div/class.tx_div.php']);
}
?>
