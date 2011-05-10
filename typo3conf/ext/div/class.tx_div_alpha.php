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
 * @copyright  2006-2007 Elmar Hinz
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    SVN: $Id: class.tx_div_alpha.php 6790 2007-10-05 18:52:14Z franzholz $
 * @since      0.1
 */

/**
 * Collection of static functions contributed by different people 
 *
 * This class contains diverse staticfunctions in "alphpa" status.
 * It is a kind of quarantine for newly suggested functions.
 *
 * The class offers the possibilty to quickly add new functions to div,
 * without much planning before. In a second step the functions will be reviewed,
 * adapted and fully implemented into the system of lib/div classes.
 *
 * @package    TYPO3
 * @subpackage div
 * @author     different Members of Extension Coordination Team
 */

class tx_div_alpha {

	/**
	 * Returns the help page with a mini guide how to setup the extension
	 * 
	 * example:
	 * 	$content .= tx_fhlibrary_view::displayHelpPage($this->cObj->fileResource('EXT:'.TT_PRODUCTS_EXTkey.'/template/products_help.tmpl'));
	 * 	unset($this->errorMessage);
	 *
	 * @param	object		tslib_pibase object
	 * @param	string		path and filename of the template file
	 * 				
	 * @return	string		HTML to display the help page
	 * @access	public
	 * 
	 * @see fhlibrary_pibase::pi_displayHelpPage
	 */
	function displayHelpPage_fh001(&$pibase, $helpTemplate, $extKey, $errorMessage='', $theCode='') {
			// Get language version
		$helpTemplate_lang='';
		if ($pibase->LLkey)	{
			$helpTemplate_lang = $this->cObj->getSubpart($helpTemplate,'###TEMPLATE_'.$this->LLkey.'###');
		}
		
		$helpTemplate = $helpTemplate_lang ? $helpTemplate_lang : $this->cObj->getSubpart($helpTemplate,'###TEMPLATE_DEFAULT###');
			// Markers and substitution:
		$markerArray['###PATH###'] = t3lib_extMgm::siteRelPath($extKey);
		$markerArray['###ERROR_MESSAGE###'] = ($this->errorMessage ? '<b>'.$errorMessage.'</b><br/>' : '');
		$markerArray['###CODE###'] = $theCode;
		$rc = $pibase->cObj->substituteMarkerArray($helpTemplate,$markerArray);
		return $rc;
	}


	/* loadTcaAdditions($ext_keys)
	*
	* Your extension may depend on fields that are added by other
	* extensios. For reasons of performance parts of the TCA are only
	* loaded on demand. To ensure that the extended TCA is loaded for
	* the extensions you depend on or which extend your extension by
	* hooks, you shall apply this function.
	*
	* @param array     extension keys which have TCA additions to load
	*/
	function loadTcaAdditions_fh001($ext_keys){
		global $_EXTKEY, $TCA;

		//Merge all ext_keys
		if (is_array($ext_keys)) {
			for($i = 0; $i < sizeof($ext_keys); $i++)	{
				if (t3lib_extMgm::isLoaded($ext_keys[$i]))	{
					//Include the ext_table
					$_EXTKEY = $ext_keys[$i];
					include(t3lib_extMgm::extPath($ext_keys[$i]).'ext_tables.php');
				}
			}
		}
	}


	/**
	 * Gets information for an extension, eg. version and most-recently-edited-script
	 *
	 * @param	string		Extension key
	 * @return	array		Information array (unless an error occured)
	 */
	function getExtensionInfo_fh001($extKey)	{
		$rc = '';

		if (t3lib_extMgm::isLoaded($extKey))	{
			$path = t3lib_extMgm::extPath($extKey);
			$file = $path.'/ext_emconf.php';
			if (@is_file($file))	{
				$_EXTKEY = $extKey;
				$EM_CONF = array();
				include($file);

				$eInfo = array();
					// Info from emconf:
				$eInfo['title'] = $EM_CONF[$extKey]['title'];
				$eInfo['author'] = $EM_CONF[$extKey]['author'];
				$eInfo['author_email'] = $EM_CONF[$extKey]['author_email'];
				$eInfo['author_company'] = $EM_CONF[$extKey]['author_company'];
				$eInfo['version'] = $EM_CONF[$extKey]['version'];
				$eInfo['CGLcompliance'] = $EM_CONF[$extKey]['CGLcompliance'];
				$eInfo['CGLcompliance_note'] = $EM_CONF[$extKey]['CGLcompliance_note'];
				if (is_array($EM_CONF[$extKey]['constraints']) && is_array($EM_CONF[$extKey]['constraints']['depends']))	{
					$eInfo['TYPO3_version'] = $EM_CONF[$extKey]['constraints']['depends']['typo3'];
				} else {
					$eInfo['TYPO3_version'] = $EM_CONF[$extKey]['TYPO3_version'];
				}
				$filesHash = unserialize($EM_CONF[$extKey]['_md5_values_when_last_written']);
				$eInfo['manual'] = @is_file($path.'/doc/manual.sxw');
				$rc = $eInfo;
			} else {
				$rc = 'ERROR: No emconf.php file: '.$file;
			}
		} else {
			$rc = 'Error: Extension '.$extKey.' has not been installed. (tx_fhlibrary_system::getExtensionInfo)';
		}

		return $rc;
	}


	/**
	 * This is the original pi_getLL from tslib_pibase
	 * Returns the localized label of the LOCAL_LANG key, $key
	 * Notice that for debugging purposes prefixes for the output values can be set with the internal vars ->LLtestPrefixAlt and ->LLtestPrefix
	 *
	 * @param	object		tslib_pibase object
	 * @param	string		The key from the LOCAL_LANG array for which to return the value.
	 * @param	string		Alternative string to return IF no value is found set for the key, neither for the local language nor the default.
	 * @param	boolean		If true, the output label is passed through htmlspecialchars()
	 * @return	string		The value from LOCAL_LANG.
	 */
	function getLL(&$pibase,$key,$alt='',$hsc=FALSE)	{
		if (isset($pibase->LOCAL_LANG[$pibase->LLkey][$key]))	{
			$word = $GLOBALS['TSFE']->csConv($pibase->LOCAL_LANG[$pibase->LLkey][$key], $pibase->LOCAL_LANG_charset[$pibase->LLkey][$key]);	// The "from" charset is normally empty and thus it will convert from the charset of the system language, but if it is set (see ->pi_loadLL()) it will be used.
		} elseif ($pibase->altLLkey && isset($pibase->LOCAL_LANG[$pibase->altLLkey][$key]))	{
			$word = $GLOBALS['TSFE']->csConv($pibase->LOCAL_LANG[$pibase->altLLkey][$key], $pibase->LOCAL_LANG_charset[$pibase->altLLkey][$key]);	// The "from" charset is normally empty and thus it will convert from the charset of the system language, but if it is set (see ->pi_loadLL()) it will be used.
		} elseif (isset($pibase->LOCAL_LANG['default'][$key]))	{
			$word = $pibase->LOCAL_LANG['default'][$key];	// No charset conversion because default is english and thereby ASCII
		} else {
			$word = $pibase->LLtestPrefixAlt.$alt;
		}
		$output = $pibase->LLtestPrefix.$word;
		if ($hsc)	$output = htmlspecialchars($output);

		return $output;
	}


	/**
	 * Loads local-language values by looking for a "locallang.php" file in the plugin class directory ($this->scriptRelPath) and if found includes it.
	 * Also locallang values set in the TypoScript property "_LOCAL_LANG" are merged onto the values found in the "locallang.php" file.
	 * Allows to add a language file name like this: 'EXT:tt_products/locallang_db.xml'
	 *
	 * @param	object		tslib_pibase object
	 * @param	string		relative path and filename of the language file
	 * @param	boolean		overwrite ... if current settings should be overwritten
	 * 
	 * @return	void
	 */
	function loadLL_fh001(&$pibase,$langFileParam,$overwrite=TRUE)	{
		$langFile = ($langFileParam ? $langFile = $langFileParam : 'locallang.php');

		if ($pibase->scriptRelPath)	{
			if (substr($langFile,0,4)==='EXT:')	{
				$basePath = $langFile;
			} else {
				$basePath = t3lib_extMgm::extPath($pibase->extKey).dirname($pibase->scriptRelPath).'/'.$langFile;
			}

				// php or xml as source: In any case the charset will be that of the system language.
				// However, this function guarantees only return output for default language plus the specified language (which is different from how 3.7.0 dealt with it)
			$tempLOCAL_LANG = t3lib_div::readLLfile($basePath,$pibase->LLkey);
			if (count($pibase->LOCAL_LANG) && is_array($tempLOCAL_LANG))	{
				foreach ($pibase->LOCAL_LANG as $langKey => $tempArray)	{
					if (is_array($tempLOCAL_LANG[$langKey]))	{
						if ($overwrite)	{
							$pibase->LOCAL_LANG[$langKey] = array_merge($pibase->LOCAL_LANG[$langKey],$tempLOCAL_LANG[$langKey]);
						} else {
							$pibase->LOCAL_LANG[$langKey] = array_merge($tempLOCAL_LANG[$langKey], $pibase->LOCAL_LANG[$langKey]);
						}
					}
				}
			} else {
				$pibase->LOCAL_LANG = $tempLOCAL_LANG; 
			}
			if ($pibase->altLLkey)	{
				$tempLOCAL_LANG = t3lib_div::readLLfile($basePath,$pibase->altLLkey);

				if (count($pibase->LOCAL_LANG) && is_array($tempLOCAL_LANG))	{
					foreach ($pibase->LOCAL_LANG as $langKey => $tempArray)	{
						if (is_array($tempLOCAL_LANG[$langKey]))	{
							if ($overwrite)	{
								$pibase->LOCAL_LANG[$langKey] = array_merge($pibase->LOCAL_LANG[$langKey],$tempLOCAL_LANG[$langKey]);
							} else {
								$pibase->LOCAL_LANG[$langKey] = array_merge($tempLOCAL_LANG[$langKey],$pibase->LOCAL_LANG[$langKey]);
							}
						} 
					}
				} else {
					$pibase->LOCAL_LANG = $tempLOCAL_LANG; 
				}
			}

				// Overlaying labels from TypoScript (including fictious language keys for non-system languages!):
			if (is_array($pibase->conf['_LOCAL_LANG.']))	{
				reset($pibase->conf['_LOCAL_LANG.']);
				while(list($k,$lA)=each($pibase->conf['_LOCAL_LANG.']))	{
					if (is_array($lA))	{
						$k = substr($k,0,-1);
						foreach($lA as $llK => $llV)	{
							if (is_array($llV))	{
								foreach ($llV as $llk2 => $llV2) {
									if (is_array($llV2))	{
										foreach ($llV2 as $llk3 => $llV3) {
											if (is_array($llV3))	{
												foreach ($llV3 as $llk4 => $llV4) {
													 if (is_array($llV4))	{
													 } else {
														$pibase->LOCAL_LANG[$k][$llK.$llk2.$llk3.$llk4] = $llV4;
														if ($k != 'default')	{
															$pibase->LOCAL_LANG_charset[$k][$llK.$llk2.$llk3.$llk4] = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'];	// For labels coming from the TypoScript (database) the charset is assumed to be "forceCharset" and if that is not set, assumed to be that of the individual system languages (thus no conversion)
														}													 	
													 }
												}
											} else {
												$pibase->LOCAL_LANG[$k][$llK.$llk2.$llk3] = $llV3;
												if ($k != 'default')	{
													$pibase->LOCAL_LANG_charset[$k][$llK.$llk2.$llk3] = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'];	// For labels coming from the TypoScript (database) the charset is assumed to be "forceCharset" and if that is not set, assumed to be that of the individual system languages (thus no conversion)
												}
											}
										}
									} else {
										$pibase->LOCAL_LANG[$k][$llK.$llk2] = $llV2;
										if ($k != 'default')	{
											$pibase->LOCAL_LANG_charset[$k][$llK.$llk2] = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'];	// For labels coming from the TypoScript (database) the charset is assumed to be "forceCharset" and if that is not set, assumed to be that of the individual system languages (thus no conversion)
										}
									}
								}
							} else	{
								$pibase->LOCAL_LANG[$k][$llK] = $llV;
								if ($k != 'default')	{
									$pibase->LOCAL_LANG_charset[$k][$llK] = $GLOBALS['TYPO3_CONF_VARS']['BE']['forceCharset'];	// For labels coming from the TypoScript (database) the charset is assumed to be "forceCharset" and if that is not set, assumed to be that of the individual system languages (thus no conversion)
								}
							}
						}
					}
				}
			}
		}

	}


	/**
	 * Split Label function for front-end applications.
	 *
	 * @param	string		Key string. Accepts the "LLL:" prefix.
	 * @return	string		Label value, if any.
	 */
	function sL_fh001($input)	{
		$restStr = trim(substr($input,4));
		$extPrfx='';
		if (!strcmp(substr($restStr,0,4),'EXT:'))	{
			$restStr = trim(substr($restStr,4));
			$extPrfx='EXT:';
		}
		$parts = explode(':',$restStr);
		return ($parts[1]);
	}


	/**
	 * Returns the values from the setup field or the field of the flexform converted into the value
	 * The default value will be used if no return value would be available.
	 * This can be used fine to get the CODE values or the display mode dependant if flexforms are used or not.
	 * And all others fields of the flexforms can be read.
	 * 
	 * example:
	 * 	$config['code'] = tx_fhlibrary_flexform::getSetupOrFFvalue(
	 * 					$this->conf['code'], 
	 * 					$this->conf['code.'], 
	 * 					$this->conf['defaultCode'], 
	 * 					$this->cObj->data['pi_flexform'],
	 * 					'display_mode',
	 * 					$GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][TT_PRODUCTS_EXTkey]['useFlexforms']);
	 * 
	 * You have to call $this->pi_initPIflexForm(); before you call this method!
	 * @param	object		tslib_pibase object
	 * @param	string		TypoScript configuration
	 * @param	string		extended TypoScript configuration
	 * @param	string		default value to use if the result would be empty
	 * @param	boolean		if flexforms are used or not
	 * @param	string		name of the flexform which has been used in ext_tables.php
	 * 						$TCA['tt_content']['types']['list']['subtypes_addlist']['5']='pi_flexform';
	 * @return	string		name of the field to look for in the flexform
	 * @access	public
	 *
	 * @see fhlibrary_pibase::pi_getSetupOrFFvalue
	 */

	function getSetupOrFFvalue_fh001(&$pibase, $code, $codeExt, $defaultCode, $T3FlexForm_array, $fieldName='display_mode', $useFlexforms=1, $sheet='sDEF',$lang='lDEF',$value='vDEF') {
		$rc = '';
		if (empty($code)) {
			if ($useFlexforms) {
				// Converting flexform data into array:
				$rc = $pibase->pi_getFFvalue($T3FlexForm_array, $fieldName, $sheet, $lang, $value);
			} else {
				$rc = strtoupper(trim($pibase->cObj->stdWrap($code, $codeExt)));
			}
			if (empty($rc)) {
				$rc = strtoupper($defaultCode);
			}
		} else {
			$rc = $code;
		}
		return $rc;
	}


	/**
	 * Returns a linked string made from typoLink parameters.
	 *
	 * This function takes $label as a string, wraps it in a link-tag based on the $params string, which should contain data like that you would normally pass to the popular <LINK>-tag in the TSFE.
	 * Optionally you can supply $urlParameters which is an array with key/value pairs that are rawurlencoded and appended to the resulting url.
	 *
	 * @param	object		tslib_pibase object
	 * @param	string		Text string being wrapped by the link.
	 * @param	string		Link parameter; eg. "123" for page id, "kasperYYYY@typo3.com" for email address, "http://...." for URL, "fileadmin/blabla.txt" for file.
	 * @param	array		An array with key/value pairs representing URL parameters to set. Values NOT URL-encoded yet.
	 * @param	string		Specific target set, if any. (Default is using the current)
	 * @param	array		Configuration
 	 * @return	string		The wrapped $label-text string
	 * @see getTypoLink_URL()
	 */
	function getTypoLink_fh001(&$pibase, $label,$params,$urlParameters=array(),$target='',$conf=array())	{
		$conf['parameter'] = $params;
		if ($target)	{
			$conf['target']=$target;
			$conf['extTarget']=$target;
		}
		if (is_array($urlParameters))	{
			if (count($urlParameters))	{
				$conf['additionalParams'].= t3lib_div::implodeArrayForUrl('',$urlParameters);
			}
		} else {
			$conf['additionalParams'].=$urlParameters;
		}
		$out = $pibase->cObj->typolink($label,$conf);
		return $out;
	}


	/**
	 * Returns the URL of a "typolink" create from the input parameter string, url-parameters and target
	 *
	 * @param	object		tslib_pibase object
	 * @param	string		Link parameter; eg. "123" for page id, "kasperYYYY@typo3.com" for email address, "http://...." for URL, "fileadmin/blabla.txt" for file.
	 * @param	array		An array with key/value pairs representing URL parameters to set. Values NOT URL-encoded yet.
	 * @param	string		Specific target set, if any. (Default is using the current)
	 * @param	array		Configuration
	 * @return	string		The URL
	 * @see getTypoLink()
	 */
	function getTypoLink_URL_fh001(&$pibase, $params,$urlParameters=array(),$target='',$conf=array())	{
		self::getTypoLink_fh001($pibase,'',$params,$urlParameters,$target,$conf);
		$rc = $pibase->cObj->lastTypoLinkUrl;
		return $rc;
	}

	/***************************
	 *
	 * Link functions
	 *
	 **************************/

	/**
	 * Get URL to some page.
	 * Returns the URL to page $id with $target and an array of additional url-parameters, $urlParameters
	 * Simple example: $this->pi_getPageLink(123) to get the URL for page-id 123.
	 *
	 * The function basically calls $this->cObj->getTypoLink_URL()
	 *
	 * @param	object		tslib_pibase object
	 * @param	integer		Page id
	 * @param	string		Target value to use. Affects the &type-value of the URL, defaults to current.
	 * @param	array		Additional URL parameters to set (key/value pairs)
	 * @param	array		Configuration
	 * @return	string		The resulting URL
	 * @see pi_linkToPage()
	 */
	function getPageLink_fh001(&$pibase, $id,$target='',$urlParameters=array(),$conf=array())	{
		$rc = self::getTypoLink_URL_fh001($pibase,$id,$urlParameters,$target, $conf);
		return $rc; // ?$target:$GLOBALS['TSFE']->sPre
	}


	/**
	 * Get External CObjects
	 * @param	object		tslib_pibase object
	 */
	function getExternalCObject_fh001(&$pibase, $mConfKey)	{
		if ($pibase->conf[$mConfKey] && $pibase->conf[$mConfKey.'.'])	{
			$pibase->cObj->regObj = &$pibase;
			return $pibase->cObj->cObjGetSingle($pibase->conf[$mConfKey],$pibase->conf[$mConfKey.'.'],'/'.$mConfKey.'/').'';
		}
	}


	/**
	 * run function from external cObject
	 * @param	object		tslib_pibase object
	 */
	function load_noLinkExtCobj_fh001(&$pibase)	{
		if ($pibase->conf['externalProcessing_final'] || is_array($pibase->conf['externalProcessing_final.']))	{	// If there is given another cObject for the final order confirmation template!
			$pibase->externalCObject = self::getExternalCObject_fh001($pibase, 'externalProcessing_final');
		}
	} // load_noLinkExtCobj



	/**
	 * Calls user function
	 */
	function userProcess_fh001(&$pObject, &$conf, $mConfKey, $passVar)	{
		global $TSFE;

		if (isset($conf) && is_array($conf) && $conf[$mConfKey])	{
			$funcConf = $conf[$mConfKey.'.'];
			$funcConf['parentObj']=&$pObject;
			$passVar = $TSFE->cObj->callUserFunction($conf[$mConfKey], $funcConf, $passVar);
		}
		return $passVar;
	} // userProcess



	/**
	 * This is the original pi_RTEcssText from tslib_pibase
	 * Will process the input string with the parseFunc function from tslib_cObj based on configuration set in "lib.parseFunc_RTE" in the current TypoScript template.
	 * This is useful for rendering of content in RTE fields where the transformation mode is set to "ts_css" or so.
	 * Notice that this requires the use of "css_styled_content" to work right.
	 *
	 * @param	object		cOject of class tslib_cObj
	 * @param	string		The input text string to process
	 * @return	string		The processed string
	 * @see tslib_cObj::parseFunc()
	 */
	function RTEcssText(&$cObj, $str)	{
		global $TSFE;

		$parseFunc = $TSFE->tmpl->setup['lib.']['parseFunc_RTE.'];
		if (is_array($parseFunc))	{
			$str = $cObj->parseFunc($str, $parseFunc);
		}
		return $str;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/div/class.tx_div_alpha.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/div/class.tx_div_alpha.php']);
}
?>
