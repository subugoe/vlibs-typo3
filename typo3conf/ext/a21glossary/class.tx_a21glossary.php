<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2003 Andreas Schwarzkopf (schwarzkopf at artplan21 de)
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
 * Accessibility glossary processing:
 * all found words in content wich correspond with the glossary entries 
 * will be enriched with special accessibility markup and evtl. with links to the glossary
 *
 * @author	Andreas Schwarzkopf <schwarzkopf at artplan21 de>
 */
 
class tx_a21glossary {
 	
	/**
	 * lookup all glossary words in the page content
	 * replace the words with accessible markup or a link to the glossary
	 * 
	 */
	function convertGlossaryWords(&$content,$pObj) {
		$searchArray = array();
		$replaceArray = array();
			// load TS configuration
		$conf = $pObj->config['config']['tx_a21glossary.'];
			// exit if this page is an exclude page OR (config.tx_a21glossary.includePagetypes is set AND the current Pagetype is not an includePage) OR page type is not 0 (based on code of jens hirschfeld 20060209)
		if (t3lib_div::inList($conf['excludePages'],$pObj->id) || isset($conf['includePagetypes'])?!t3lib_div::inList($conf['includePagetypes'],$pObj->type):($pObj->type=='0'?false:true)) {
			if ($conf['excludePages.'][$pObj->id]) {
				$conf['excludeTypes'] .= ','.$conf['excludePages.'][$pObj->id];
			} else {
				return false;
			}
		}
			// prepare the query and retrieve the glossary entries from the DB
		$andWhere = '1=1';
			// use only the glossary entries in the current pagetree (jens hirschfeld 20060209)
		if (strtolower($conf['pidList']) == 'pagetree') {
			$result = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tx_a21glossary_main','1=1');
			$inpagetree = '';
			$notinpagetree = '';
			for ($i = count($result)-1;$i >= 0;$i--) {
				if (!t3lib_div::inList($inpagetree,$result["$i"]['pid']) && !t3lib_div::inList($notinpagetree,$result["$i"]['pid'])) {
					$glossaryrootline = $pObj->sys_page->getRootLine($result["$i"]['pid']);
					if ($glossaryrootline['0']['uid'] == $pObj->rootLine['0']['uid']) {
						$inpagetree .= $result["$i"]['pid'] . ',';
					} else {
						$notinpagetree .= $result["$i"]['pid'] . ',';
					}
				}
			}
			$inpagetree != ''?$andWhere .= ' AND pid IN('.t3lib_div::rm_endcomma($inpagetree).')':$andWhere .= ' AND 1=2';
		} else {
			$andWhere .= $conf['pidList']?(' AND pid IN('.implode(',',t3lib_div::intExplode(',',$conf['pidList'])).')'):'';
		}
							// search only in the local language, A.S. 20051217
		$andWhere .= ' AND tx_a21glossary_main. sys_language_uid = '.intval($GLOBALS['TSFE']->sys_language_uid);
		if ($conf['debug']) $GLOBALS['TYPO3_DB']->debugOutput = TRUE;
		$result = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*','tx_a21glossary_main',$andWhere.tslib_cObj::enableFields('tx_a21glossary_main'));
			// the TS configuration can set the replace rule to case sensitivity
		$caseSensitive = $conf['caseSensitiv']?'':'i';
			// TS configuration of PCRE pattern modifiers, e.g. /su
		$PCREmodifiers = $conf['patternModifiers'];
			// special configuration of PCRE pattern modifiers for strings wich will be embedded in a link
		$PCREmodifiersLink = $conf['patternModifiersLink'];
			// compile the tags to replace
		if (count($result) > 0) {
				// instantiate a new cObj
			$cObj = t3lib_div::makeInstance("tslib_cObj");
			foreach($result as $row) {
				if (!t3lib_div::inList($conf['excludeTypes'],$row['shorttype'])) {
						// load the row in the cObj current record
					$cObj->data = $row;
					$generateLink = false; // thanks to Frank-Xaver Koch, 2006-02-28
						// check if a link to the glossary list should be generated
					if (	t3lib_div::inList($conf['linkToGlossary'],$row['shorttype']) 
							&& count($conf['typolink.'])) {
							// now check first, if the fields configured to be not empty. if all of them are empty, the link will not be created
						if ($conf['linkOnlyIfNotEmpty']) {
							$notEmptyFieldsArray = t3lib_div::trimExplode(',',$conf['linkOnlyIfNotEmpty']);
							$generateLink = 0;
							foreach($notEmptyFieldsArray as $checkField) {
								if ($row[$checkField]) {
									$generateLink++;
									break;
								}
							}
						} else {
							$generateLink = 1;
						}
					}
						// replace all words except the content within tags
						/*	old version, does not work with characters >128 properly (e.g. german umlauts):				$searchArray[] = '/'.'(?!<.*?)\b'.preg_quote($row['shortcut']?$row['shortcut']:$row['short'],'/').'\b(?![^<>]*?>)'.'/us'.$caseSensitive; */
					if ($generateLink) {							// if a link should be generated, then replace at this point only the words wich are already wrapped in a link (we make no nested links!)
						$searchArray[] = '/'.'(?!<a.*?)(?!<.*?)(?<=\s|[[:punct:]])'.$this->a21quote($row['shortcut']?$row['shortcut']:$row['short'],'/').'(?=\s|[[:punct:]])(?![^<>]*?>)(?=.*<\/a>)'.'/'.$caseSensitive.$PCREmodifiersLink;
					} else {											// normal procedure: all words not within of tags, wich are divided by whitespace or punctuation signs will be replaced
						$searchArray[] = '/'.'(?!<.*?)(?<=\s|[[:punct:]])'.$this->a21quote($row['shortcut']?$row['shortcut']:$row['short'],'/').'(?=\s|[[:punct:]])(?![^<>]*?>)'.'/'.$caseSensitive.$PCREmodifiers;
					}
						// if the page language is identical with the language of the glossary word or is not defined at all, then the lang attribute will not be shown
						// if the language of the word is defined and different to the current page language, then the lang attribute is added
					$pageLanguage = $pObj->config['config']['language']?$pObj->config['config']['language']:$pObj->config['config']['htmlTag_langKey'];
					if ($row['language'] && $pageLanguage!=$row['language']) {
						$lang = $conf['noLang']?'':(' lang="'.$row['language'].'"');
						$lang .= $conf['xmlLang']?(' xml:lang="'.$row['language'].'"'):'';
					} else {
						$lang='';
					}
						// the (x)html element is taken from the field shorttype; other types (=elements) can be added with TSConfig->TCEFORM->addItem
					$element = trim(htmlentities(strip_tags($row['shorttype']),ENT_QUOTES,$pObj->renderCharset));
					$titleText = trim(htmlentities(strip_tags($row['longversion']),ENT_QUOTES,$pObj->renderCharset));
					$title = $row['longversion']?(' title="'.$titleText.'"'):'';
						// load lang and title to the register, so they can be retrieved later with stdWrap
					$GLOBALS['TSFE']->register['lang'] = $lang;
					$GLOBALS['TSFE']->register['title'] = $title;
						// compile the tags
					$before = '<'.$element.$lang.$title.'>';
					$after = '</'.$element.'>';
						// process the short text via stdWrap and embed it in the new HTML element
					$result = $before.$cObj->stdWrap($row['short'],$conf[$element.'.']).$after;
						// add the result string to the replace array
					$replaceArray[] = $result;
						// if the record is marked to be linkable or the HTML element is configured to be linkable, then wrap the result in a link to
					if ($generateLink) {
							// the same condition as above, additionally the word must not be wrapped in a link (?!.*<\/a>) - otherwise we would produce nested links
						$searchArray[] = '/'.'(?!<a.*?)(?!<.*?)(?<=\s|[[:punct:]])'.$this->a21quote($row['shortcut']?$row['shortcut']:$row['short'],'/').'(?=\s|[[:punct:]])(?![^<>]*?>)(?!.*<\/a>)/'.$caseSensitive.$PCREmodifiersLink;
						$replaceArray[] = $cObj->typolink($result,$conf['typolink.']);
					}
				}
			}
		}
			
			// process only the html body part
		$dividePos = strpos($content['pObj']->content,'<body');
		$head = substr($content['pObj']->content,0,$dividePos);
		$body = substr($content['pObj']->content,strpos($content['pObj']->content,'<body'));
			// apply changes on the parent object
		$content['pObj']->content = $head.$this->a21replace($searchArray,$replaceArray,$body);
	}
	
	/**
	 * wrapper function for preg_replace / ereg_replace depending of their availibility
	 */
	function a21replace($search,$replace,$source) {
		if (!function_exists('preg_replace')) {
			//  !! this is buggy, because the ereg_replace function can not handle arrays
			// so this function will only process the first glossary entry!
			return ereg_replace($search,$replace,$source);
		}
			// this has priority because of much better performance
		return preg_replace($search,$replace,$source);
	}
	
	/**
	 * wrapper function for preg_quote, fall back to manual qouting if PCRE not availible
	 */
	function a21quote($content,$params='') {
		if (!function_exists('preg_quote')) {
				// build a subsitute quoting function
			$search = explode(' ','. \ + * ? [ ^ ] $ ( ) { } = ! < > | :');
			$search[] = $params;
			foreach($search as $char) {
				$replace[]=chr(92).$char;
			}
			return str_replace($search,$replace,$content);
		}
		return preg_quote($content,$params);
	}
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/a21glossary/class.tx_a21glossary.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/a21glossary/class.tx_a21glossary.php"]);
}
?>