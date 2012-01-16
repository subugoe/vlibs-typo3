<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Nicole Cordes <cordes@cps-it.de>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class tx_cpsdevlib_itemsprocfuncs {

	/**
	 * Charset conversion object
	 *
	 * @var t3lib_cs $csConvObj
	 */
	var $csConvObj;

	/**
	 * Set charset of current language
	 *
	 * @var string $charset
	 */
	var $charset;

	/**
	 * Array with static locales
	 *
	 * @var array $localArray
	 */
	var $localeArray = array(
		'ar' => array('ar_SA', 'ar', 'arabic'),
		'ba' => array('bs_BA', 'bs', 'bosnian'),
		'bg' => array('bg_BG', 'bg', 'bulgarian'),
		'br' => array('pt_BR', 'pt', 'portuguese-brazil', 'ptb'),
		'ca' => array('ca_ES', 'ca', 'catalan'),
		'ch' => array('zh_CN', 'zh', 'chinese-simplified', 'chs', 'chinese'),
		'cz' => array('cs_CZ', 'cs', 'czech', 'csy'),
		'de' => array('de_DE', 'de', 'german', 'deu'),
		'dk' => array('da_DK', 'da', 'danish', 'dan'),
		'en' => array('en_US', 'en', 'english'),
		'eo' => array('eo_EO', 'eo', 'esperanto'),
		'es' => array('es_ES', 'es', 'spanish', 'esp'),
		'et' => array('et_EE', 'et', 'estonian'),
		'eu' => array('en_US', 'en', 'english'),
		'fa' => array('fa_IR', 'fa', 'persian'),
		'fi' => array('fi_FI', 'fi', 'finnish', 'fin'),
		'fo' => array('fo_FO', 'fo', 'faeroese'),
		'fr' => array('fr_FR', 'fr', 'french', 'fra'),
		'ga' => array('ga_GA', 'ga', 'galician'),
		'ge' => array('ka_GE', 'ka', 'georgian'),
		'gl' => array('gl_GL', 'gl', 'greenlandic'),
		'gr' => array('el_GR', 'el', 'greek', 'ell'),
		'he' => array('he_IL', 'he', 'hebrew'),
		'hi' => array('hi_IN', 'hi', 'hindi'),
		'hk' => array('zh_TW', 'zh', 'chinese-traditional', 'cht', 'chinese'),
		'hr' => array('hr_HR', 'hr', 'croatian'),
		'hu' => array('hu_HU', 'hu', 'hungarian', 'hun'),
		'is' => array('is_IS', 'is', 'icelandic', 'isl'),
		'it' => array('it_IT', 'it', 'italian', 'ita'),
		'jp' => array('ja_JP', 'ja', 'japanese', 'jpn'),
		'kr' => array('ko_KR', 'ko', 'korean', 'kor'),
		'lt' => array('lt_LT', 'lt', 'lithuanian'),
		'lv' => array('lv_LV', 'lv', 'latvian'),
		'my' => array('ms_MY', 'ms', 'burmese'),
		'nl' => array('nl_NL', 'nl', 'dutch', 'nld'),
		'no' => array('no_NO', 'no', 'norwegian'),
		'pl' => array('pl_PL', 'pl', 'polish', 'plk'),
		'pt' => array('pt_PT', 'pt', 'portuguese', 'ptg'),
		'ro' => array('ro_RO', 'ro', 'romanian'),
		'ru' => array('ru_RU', 'ru', 'russian', 'rus'),
		'se' => array('sv_SE', 'sv', 'swedish', 'sve'),
		'si' => array('sl_SI', 'sl', 'slovenian'),
		'sk' => array('sk_SK', 'sk', 'slovak', 'sky'),
		'sq' => array('sq_AL', 'sq', 'albanian'),
		'sr' => array('sr_CS', 'sr', 'serbian'),
		'th' => array('th_TH', 'th', 'thai'),
		'tr' => array('tr_TR', 'tr', 'turkish', 'trk'),
		'ua' => array('uk_UA', 'uk', 'ukrainian'),
		'vn' => array('vi_VN', 'vi' ,'vietnamese'),
	);

	public function __construct() {
		$this->csConvObj = t3lib_div::makeInstance('t3lib_cs');
	}

	/**
	 * Function to sort select items by label
	 *
	 * @param array $config: parameter array by t3lib_tceforms::procItems
	 * @param t3lib_tceforms $pObj: parent object of class t3lib_tceforms
	 * @return void
	 *
	 */
	public function sortItemsByLabel($config, $pObj) {

		// Get current language and find proper charset
		$currentLanguage = tx_cpsdevlib_extmgm::getLanguage('en');
		$this->charset = $this->csConvObj->get_locale_charset($currentLanguage);

		// Try to save current locale
		$oldLocale = setlocale(LC_ALL, 0);
		if ($oldLocale) {
			setlocale(LC_ALL, $this->localeArray[$currentLanguage]);
		}

		usort($config['items'], array('tx_cpsdevlib_itemsprocfuncs', 'sortArray'));

		// Reset locale
		if ($oldLocale) {
			setlocale(LC_ALL, $oldLocale);
		}
	}

	/**
	 * Sort arrays by label (index 0)
	 *
	 * @param array $arrayA
	 * @param array $arrayB
	 * @return int
	 */
	private function sortArray($arrayA, $arrayB) {

		// Convert labels from current charset system charset
		$strA = $this->csConvObj->conv($arrayA[0], $GLOBALS['LANG']->charSet, $this->charset);
		$strB = $this->csConvObj->conv($arrayB[0], $GLOBALS['LANG']->charSet, $this->charset);

		return strcoll($strA, $strB);
	}
}

?>