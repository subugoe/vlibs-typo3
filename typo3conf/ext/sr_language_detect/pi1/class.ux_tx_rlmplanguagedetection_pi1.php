<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2003-2004 robert lemke medienprojekte (rl@robertlemke.de)
*  All rights reserved
*  (c) 2005, 2006 Stanislas Rolland <stanislas.rolland(arobas)fructifor.ca>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is 
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
* 
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
* 
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/** 
 * Extends plugin 'Language Detection' for the 'rlmp_language_detection' extension.
 *
 * @author	robert lemke medienprojekte <rl@robertlemke.de>
 * @author	Stanislas Rolland <stanislas.rolland(arobas)fructifor.ca>
 *
 * Get iso language codes that include a country code, such as pt-br for Brazilian (encoded as br by TYPO3)
 * Get accepted languages including the country part when present
 * Generate typoLink that honors simulateStaticDocument settings
 *
 */

require_once(t3lib_extMgm::extPath('rlmp_language_detection').'pi1/class.tx_rlmplanguagedetection_pi1.php');

class ux_tx_rlmplanguagedetection_pi1 extends tx_rlmplanguagedetection_pi1 {
	
	/**
	 * The main function recognizes the browser's preferred languages and 
	 * reloads the page accordingly.
	 * 
	 * @param	string		$content: HTML content
	 * @param	array		$conf: The mandatory configuration array
	 * @return	void		
	 */
	function main($content,$conf)	{
		global $TSFE, $TYPO3_DB;
		$this->conf = $conf;
		
			// Break out, if language already selected
		if (t3lib_div::GPvar ('L') !== NULL) return;		
		
			// Break ouf if the last page visited was also on our site:
		$referer = t3lib_div::getIndpEnv('HTTP_REFERER');
		if (strlen($referer) && stristr($referer, t3lib_div::getIndpEnv('TYPO3_SITE_URL'))) return;
		
		$acceptedLanguagesArr = $this->getAcceptedLanguages();
		$availableLanguagesArr = $this->conf['useOneTreeMethod'] ? $this->getSysLanguages() : $this->getMultipleTreeLanguages();
		$preferredLanguageOrPageUid = FALSE;
		while (count($acceptedLanguagesArr) > 0) {
			$currentLanguage = array_shift($acceptedLanguagesArr);
			if (isset($availableLanguagesArr[$currentLanguage])) {
				$preferredLanguageOrPageUid = $availableLanguagesArr[$currentLanguage];
				break;
			} elseif (strlen($currentLanguage)>2) {
				$currentLanguage = substr($currentLanguage,0,2);
			    	if (isset($availableLanguagesArr[$currentLanguage])) {
					$preferredLanguageOrPageUid = $availableLanguagesArr[$currentLanguage];
					break;
				}
			}
		}
		
		if ($preferredLanguageOrPageUid !== FALSE) {
			if ($this->conf['useOneTreeMethod']) {
				$page = $TSFE->page;
			} else {
				$sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
				$sys_page->init(0);
				$page = $sys_page->getPage($preferredLanguageOrPageUid);
			}
			$linkData = $TSFE->tmpl->linkData($page,'',0,'',array(),'&L='.$preferredLanguageOrPageUid);
			$locationURL = $this->conf['dontAddSchemeToURL'] ? $linkData['totalURL'] : t3lib_div::locationHeaderUrl($linkData['totalURL']);
			header('Location: '.$locationURL);
		}
	
	}
	
	/**
	 * Returns the preferred languages ("accepted languages") from the visitor's
	 * browser settings.
	 * 
	 * The accepted languages are described in RFC 2616.
	 * It's a list of language codes (e.g. 'en' for english), separated by
	 * comma (,). Each language may have a quality-value (e.g. 'q=0.7') which
	 * defines a priority. If no q-value is given, '1' is assumed. The q-value
	 * is separated from the language code by a semicolon (;) (e.g. 'de;q=0.7')
	 * 
	 * @return	array	An array containing the accepted languages; key and value = iso code, sorted by quality
	 */
	function getAcceptedLanguages () {
		$languagesArr = array ();		
		$rawAcceptedLanguagesArr = t3lib_div::trimExplode (',',t3lib_div::getIndpEnv('HTTP_ACCEPT_LANGUAGE'),1);

		foreach ($rawAcceptedLanguagesArr as $languageAndQualityStr) {
			list ($languageCode, $quality) = t3lib_div::trimExplode (';',$languageAndQualityStr);
			$acceptedLanguagesArr[$languageCode] = $quality ? (float)substr ($quality,2) : (float)1;
		}

			// Now sort the accepted languages by their quality and create an array containing only the language codes in the correct order.
		if (is_array($acceptedLanguagesArr)) {
			arsort($acceptedLanguagesArr);
			$languageCodesArr = array_keys($acceptedLanguagesArr);
			if (is_array($languageCodesArr)) {
				foreach ($languageCodesArr as $languageCode) {
					$languagesArr[$languageCode] = $languageCode;
				}
			}
		}
		return $languagesArr;
	}
	
	/**
	 * Returns an array of sys_language records containing the ISO code as the key and the record's uid as the value
	 * 
	 * @return	array	sys_language records: ISO code => uid of sys_language record
	 * @access	private
	 */
	function getSysLanguages() {
		global $TYPO3_DB, $TSFE;
		$availableLanguages = array();
		
		if (strlen($this->conf['defaultLang'])) $availableLanguages[trim(strtolower($this->conf['defaultLang']))] = 0;
		
			// Select all pages_language_overlay records on the current page. Each represents a possibility for a language.
		$pageLangArr=array();
		$res = $TYPO3_DB->exec_SELECTquery(
			'sys_language_uid',
			'pages_language_overlay',
			'pid=' . intval($TSFE->id).
				$this->cObj->enableFields('pages_language_overlay')
			);
		while ($row = $TYPO3_DB->sql_fetch_assoc($res))	{
			$pageLangArr[] = $row['sys_language_uid'];
		}
		
			// Get the isocodes associated with the available sys_languade uid's
		if (!empty($pageLangArr)) {
			$res = $TYPO3_DB->exec_SELECTquery(
				'sys_language.uid, static_languages.lg_iso_2 as isocode, static_languages.lg_country_iso_2',
				'sys_language LEFT JOIN static_languages ON sys_language.static_lang_isocode=static_languages.uid',
				'sys_language.uid IN('.implode(',',$pageLangArr).')'.
					$this->cObj->enableFields('sys_language').
					$this->cObj->enableFields('static_languages')
				);
			while ($row = $TYPO3_DB->sql_fetch_assoc($res))	{
				$availableLanguages[trim(strtolower($row['isocode'].($row['lg_country_iso_2']?'-'.$row['lg_country_iso_2']:'')))] = $row['uid'];
			}
		}
		return $availableLanguages;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sr_language_detect/pi1/class.ux_tx_rlmplanguagedetection_pi1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/sr_language_detect/pi1/class.ux_tx_rlmplanguagedetection_pi1.php']);
}
?>