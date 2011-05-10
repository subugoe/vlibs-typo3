<?php

/**
 * This Class does translations for frontend plugins.
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
 * @version    SVN: $Id: class.tx_lib_translator.php 7333 2007-11-30 12:21:48Z elmarhinz $
 * @since      0.1
 */

/**
 * This Class does translations for frontend plugins.
 *
 * Usage:
 *
 * <code>
 *  $translator = tx_div::makeInstance('tx_lib_translator');
 *  $translator->setExtensionKey('myExtensionKey');
 *  return $translator->translate($out);
 * </code>
 *
 * The markers in the text to translate have the format '%%%keyToTranslation%%%'.
 * They are extracted by preg_replace() with the default pattern '/%%%([^%])%%%/'.
 * You may use other markers by setting another pattern:
 *
 * <code>
 *  $translator->setTranslationPattern('/§§§([^§])§§§/');
 * </code>
 *
 * The code is extracted mainly from tslib_pibase with few adaptions.
 * That is the reason why it is not done in the typical lib/div style.
 * That doesn't matter so much in this case as the target of this class is direct use
 * not inheritance. The API itself is done in typical lib/div style.
 *
 * Depends on: tx_div, tx_lib_object	<br>
 * Used by: tx_lib_controller
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 */
class tx_lib_translator extends tx_lib_object {
	var $LLkey = 'default';
	var $LOCAL_LANG = array();
	var $LOCAL_LANG_loaded = 0;
	var $altLLkey = '';
	var $pathToLanguageFile = '';
	var $translationPattern = '/%%%([^%]*)%%%/';

	// -------------------------------------------------------------------------------------
	// Setters
	// -------------------------------------------------------------------------------------

	/**
	 * Set the path to locallang.
	 *
	 * The absolute Path of the language file.
	 * The path can make use the syntax  EXT:myextension/somepath.
	 *
	 * @param	string		absolute path
	 * @return	void
	 */
	function setPathToLanguageFile($absolutePath) {
		$this->pathToLanguageFile = t3lib_div::getFileAbsFileName($absolutePath);
	}

	/**
	 * Set the regular expression pattern used to find the markers.
	 *
	 * The syntax is that of preg_match().	<br>
	 * The whole expression is the match.	<br>
	 * The match of the first () is the key to the language file.
	 *
	 * If not set it defaults to '/%%%([^%]*)%%%/'.
	 *
	 * @param	string		pattern
	 * @return	void
	 */
	function setTranslationPattern($patternString) {
		$this->translationPattern = $patternString;
	}

	// -------------------------------------------------------------------------------------
	// Getters
	// -------------------------------------------------------------------------------------

	/**
	 * Call this function to find the the path to the language file
	 *
	 * $this->pathToLanguageFile is checked first, if it has been set actively. (ALTERNATIVE)
	 * If it is missing, call $this->configurations->get('pathToLanguageFile'). (RECOMMENDED)
	 * The path can make use the syntax  EXT:myextension/somepath.
	 *
	 * @return	string  Absolute path to the language file.
	 */
	function getPathToLanguageFile() {
		$path = $this->pathToLanguageFile ?  $this->pathToLanguageFile 
			: $this->controller->configurations->get('pathToLanguageFile');
		return t3lib_div::getFileAbsFileName($path);
	}

	/**
	 * Translate text.
	 *
	 * Direct translation of input text.
	 *
	 * @param	string		the text to translate
	 * @return	string		the translated text
	 */
	function translate($text) {
		return $this->_translate($text);
	}

	/**
	 * Translate a single data item.
	 *
	 * Translate, store internally and return directly.
	 *
	 * @param	mixed		key of internal storage
	 * @return	string		translated text
	 */
	function translateContent($item = '_content') {
		$this->set($item, $this->_translate($this->get($item)));
		return $this->get($item);
	}

	/**
	 * Translate multiple data items.
	 *
	 * The list of keys can be given as string, array or object.
	 * Translate and store internally.
	 * Returns the translated fields concatenated.
	 *
	 * @param	mixed		keys of internal storage
	 * @return	string		translated text
	 */
	function translateContents($items) {
			foreach(tx_div::explode($items) as $item) {
				$chain .= $this->translateContent($item);
			}
			return $chain;
	}

	// -------------------------------------------------------------------------------------
	// Private functions
	// -------------------------------------------------------------------------------------

	/**
	 * Returns the localized label of the LOCAL_LANG key, $key.
	 *
	 * @param	string		The key to the LOCAL_LANG array for which to return the value.
	 * @param	string		Alternative key to the LOCAL_LANG array
	 * @param	boolean		if true, the result is piped through htmlspecialchars()
	 * @return	string		The value from LOCAL_LANG.
	 * @author	Kasper Skårhøj
	 * @author	Elmar Hinz <elmar.hinz@team-red.net>
	 * @access	private
	 */
	function _getLocalLang($key, $alt='', $hsc=false)    {
		if (isset($this->LOCAL_LANG[$this->LLkey][$key]))       {
			$word = $GLOBALS['TSFE']->csConv($this->LOCAL_LANG[$this->LLkey][$key], $this->LOCAL_LANG_charset[$this->LLkey][$key]); // The "from" charset is normally empty and thus it will convert from the charset of the system language, but if it is set (see ->pi_loadLL()) it will be used.
		} elseif ($this->altLLkey && isset($this->LOCAL_LANG[$this->altLLkey][$key]))   {
			$word = $GLOBALS['TSFE']->csConv($this->LOCAL_LANG[$this->altLLkey][$key], $this->LOCAL_LANG_charset[$this->altLLkey][$key]);   // The "from" charset is normally empty and thus it will convert from the charset of the system language, but if it is set (see ->pi_loadLL()) it will be used.
		} elseif (isset($this->LOCAL_LANG['default'][$key]))    {
			$word = $this->LOCAL_LANG['default'][$key];     // No charset conversion because default is english and thereby ASCII
		} else {
			$word = $this->LLtestPrefixAlt.$alt;
		}

		$output = $this->LLtestPrefix.$word;
		if ($hsc) {
			$output = htmlspecialchars($output);
		}
		$output = $output ? $output : '%%%' . $key . '%%%';
		$output = $output == '_VOID_' ? '' : $output;
		return $output;
	}

	/**
	 * Loads the language Files.
	 *
	 * @return	void
	 * @author	Kasper Skårhøj
	 * @author	Elmar Hinz <elmar.hinz@team-red.net>
	 * @access	private
	 */
	function _loadLocalLang() {
		if ($GLOBALS['TSFE']->config['config']['language']){
			$this->LLkey = $GLOBALS['TSFE']->config['config']['language'];
			if ($GLOBALS['TSFE']->config['config']['language_alt']) {
				$this->altLLkey = $GLOBALS['TSFE']->config['config']['language_alt'];
			}
		}
		$basePath = $this->getPathToLanguageFile();
		if (!is_readable($basePath))  {
			$this->_die('Please set a correct path for tx_lib_translator to the locallang file.' . chr(10) .
					'Example: $translator->setPathToLanguageFile(\'EXT:myextension/locallang.xml\');' . chr(10), __FILE__, __LINE__);
		}
		if(!$this->LOCAL_LANG_loaded){
			// php or xml as source: In any case the charset will be that of the system language.
			// However, this function guarantees only return output for default language plus the specified language (which is different from how 3.7.0 dealt with it)
			$this->LOCAL_LANG = t3lib_div::readLLfile($basePath,$this->LLkey);
			if ($this->altLLkey)    {
				$tempLOCAL_LANG = t3lib_div::readLLfile($basePath,$this->altLLkey);
				$this->LOCAL_LANG = array_merge(is_array($this->LOCAL_LANG) ? $this->LOCAL_LANG : array(),$tempLOCAL_LANG);
			}

			// Overlaying labels from TypoScript (including fictitious language keys for non-system languages!):
			if (is_array($this->conf['_LOCAL_LANG.']))      {
				reset($this->conf['_LOCAL_LANG.']);
				while(list($k,$lA)=each($this->conf['_LOCAL_LANG.']))   {
					if (is_array($lA))      {
						$k = substr($k,0,-1);
						foreach($lA as $llK => $llV)    {
							if (!is_array($llV))    {
								$this->LOCAL_LANG[$k][$llK] = $llV;
								if ($k != 'default')    {
									$this->LOCAL_LANG_charset[$k][$llK] = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'];        // For labels coming from the TypoScript (database) the charset is assumed to be "forceCharset" and if that is not set, assumed to be that of the individual system languages (thus no conversion)
								}
							}
						}
					}
				}
			}		
			$this->LOCAL_LANG_loaded = 1;
		}
	} 

	/**
	 * Translate the given text using the internally stored patterns.
	 *
	 * @param	string		the text to translate
	 * @return	string		the translated text
	 * @access	private
	 */
	function _translate($text) {
		$this->_loadLocalLang();
		$pattern = $this->translationPattern . 'e';
		return preg_replace($pattern, '$this->_getLocalLang(\'$1\')', $text);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_translator.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_translator.php']);
}
?>
