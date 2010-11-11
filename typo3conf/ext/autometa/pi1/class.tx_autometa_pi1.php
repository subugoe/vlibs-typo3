<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2009 Sigfried Arnold (s.arnold@rebell.at)
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

require_once (PATH_tslib . 'class.tslib_pibase.php');
require_once (PATH_tslib . 'class.tslib_content.php');

class tx_autometa_pi1 extends tslib_pibase {

	var $prefixId      = "tx_autometa_pi1";               // Same as class name
	var $scriptRelPath = "pi1/class.tx_autometa_pi1.php"; // Path to this script relative to the extension dir.
	var $extKey        = "autometa";                      // The extension key.
	
	var $content;   // HTML code of the page
	var $config;    // TypoScript Setup
	var $extConf;   // constants from extension config
	var $charset;   // charset used for generation contents

	var $rawhtml;   // raw html, reduced to TYPO3SEARCH sections
	
	function main(&$content, $config, $extConf) {
		$this->content = &$content;
		$this->config  = $config;
		$this->extConf = $extConf;
		
		if ($this->extConf['enableDevLog'] == 1) {
			$timer = t3lib_div::convertMicrotime(microtime());
			t3lib_div::devLog('debugging is on', $this->extKey, 1);
			t3lib_div::devLog('config passed', $this->extKey, 0, array('config' => $this->config, 'extConf' => $this->extConf));
			if($this->config['charset'] != 'UTF-8') {
				t3lib_div::devLog('using non UTF-8 charset', $this->extKey, 1);
			}
		} else {
			t3lib_div::devLog('debugging is off', $this->extKey, 0);
		}
		
		$this->get_rawtext     ($this->content);
		
		$this->add_keywords    ($this->content);
		$this->add_description ($this->content);
		$this->add_date        ($this->content);
		$this->add_author      ($this->content);
		
		if ($this->extConf['enableDevLog'] == 1) {
			t3lib_div::devLog('total parsetime', $this->extKey, 0, array('mircoseconds' => ((int)t3lib_div::convertMicrotime(microtime())-(int)$timer)));
		}
	}
	
	/**
	 * Replaces the pseudo marker with the meta element for keywords
	 *
	 * @param   string  plain html of the document, passed by reference
	 * @return  void
	 */
	function add_keywords(&$html) {
		$html = str_replace('###AUTOMETA_KEYWORDS###', $this->html_codec($this->get_keywords($this->get_plaintext(), $this->config['keywords.']['amount'])), $html);
	}
	
	/**
	 * Replaces the pseudo marker with the meta element for description
	 *
	 * @param   string  plain html of the document, passed by reference
	 * @return  void
	 */
	function add_description(&$html) {
		$html = str_replace('###AUTOMETA_DESCRIPTION###', $this->html_codec($this->get_plaintext(false, true, $this->config['description.']['length'])), $html);
	}
	
	/**
	 * Does nothing. A placeholder at the moment for possible other formats that can't be done via pure TypoCcript.
	 *
	 * @param   string  plain html of the document, passed by reference
	 * @return  void
	 */
	function add_date(&$html) {
	}
	
	/**
	 * Does nothing. A placeholder at the moment for possible other formats that can't be done via pure TypoCcript.
	 *
	 * @param   string  plain html of the document, passed by reference
	 * @return  void
	 */
	function add_author(&$html) {
	}
	
	/**
	 * Gets the raw HTML content of the page.
	 * first parse the content like indexsearch does. after that remove any HTML we wont user anyway
	 *
	 * @param   string  plain html of the document, passed by reference
	 * @return  void
	 */
	function get_rawtext($str) {
		// pretty similar to indexed_search: tx_indexedsearch_indexer->typoSearchTags();
		$expBody = preg_split('/\<\!\-\-[\s]?TYPO3SEARCH_/',$str);
		unset($str);
		if(count($expBody)>1) {
			$this->rawhtml = '';
			foreach($expBody as $val) {
				$part = explode('-->',$val,2);
				if (trim($part[0]) == 'begin') {
					$this->rawhtml.= $part[1];
					$prev = '';
				} elseif (trim($part[0]) == 'end') {
					$this->rawhtml .= $prev;
				} else {
					$prev  = $val;
				}
			}
		} else {
			if ($this->extConf['enableDevLog'] == 1) { t3lib_div::devLog('get_rawhtml(): no TYPO3SEARCH comment found', $this->extKey, 1); }
			$this->rawhtml = $expBody[0];
		}
		
		// remove head, script and style elements entirely
		$this->rawhtml = preg_replace(
			array('/<head[^>]*?>.*?<\/head>/si', '/<script[^>]*?>.*?<\/script>/si', '/<style[^>]*?>.*?<\/style>/si',),
			array(' ', ' ', ' ',),
			$this->rawhtml
		);
		
		if ($this->extConf['enableDevLog'] == 1) { t3lib_div::devLog('get_rawhtml()', $this->extKey, 0, array('rawhtml' => $this->rawhtml)); }
	}
	
	/*
	 * $weighted    bool    - defines if the plaintext string will contain weighted words
	 * $strip_title bool    - defines if the title will get stripped out by the defined pattern
	 * $truncate    integer - appox amount of characters of plaintext (usefull for generating description)
	 */
	function get_plaintext($weighted=false, $strip_title=false, $truncate=false) {
		$str = $this->rawhtml;
		
		if ($strip_title === true) {
			if (empty($this->config['description.']['title.']['regex'])) {
				// since str_replace has no limit parameter, we use preg_replace
				$str = preg_replace('/' . preg_quote($GLOBALS['TSFE']->page['title'], '/') . '/i', '', $str, 1);
				if ($this->extConf['enableDevLog'] == 1) { t3lib_div::devLog('determined title', $this->extKey, 0, array('title' => $GLOBALS['TSFE']->page['title'], 'title_pattern' => preg_quote($GLOBALS['TSFE']->page['title'], '/'))); }
			} else {
				if(@preg_match($this->config['description.']['title.']['regex'], $this->content, $title) !== false) {
					// str_replace does not work here (again)
					$str = preg_replace('/' . preg_quote($title[1], '/i') . '/', '', $str, 1);
					if ($this->extConf['enableDevLog'] == 1) { t3lib_div::devLog('determined title', $this->extKey, 0, array('title' => $title[0], 'title_pattern' => $this->config['description.']['title.']['regex'])); }
				} else {
					if ($this->extConf['enableDevLog'] == 1) { t3lib_div::devLog('preg_match failed, title not stripped', $this->extKey, 3, array('title_pattern' => $this->config['description.']['title.']['regex'], 'title_pattern_insert' => $title_pattern_insert)); }
				}
			}
		}
		
		if ($this->config['keywords.']['weight.']['enable'] = '1' && $weighted === true) {
			$str = preg_replace(
				array(
					'/<h1[^>]*?>.*?<\/h1>/si',
					'/<h2[^>]*?>.*?<\/h2>/si',
					'/<h3[^>]*?>.*?<\/h3>/si',
					'/<h4[^>]*?>.*?<\/h4>/si',
					'/<h5[^>]*?>.*?<\/h5>/si',
					'/<h6[^>]*?>.*?<\/h6>/si',
					'/<strong[^>]*?>.*?<\/strong>/si',
					'/<em[^>]*?>.*?<\/em>/si',
				),
				array(
					'\0\0\0\0\0', // h1
					'\0\0\0\0\0', // h2
					'\0\0\0\0',   // h3
					'\0\0\0\0',   // h4
					'\0\0\0',     // h5
					'\0\0\0',     // h6
					'\0\0',       // strong
					'\0\0',       // em
				),
				$str
			);
		}
		
		// Replace other tags and comments with space (strip_tags is not an option) and collapse multiple whitespace charaters with one single space
		$str = preg_replace(
			array('/<([^>]+)>/si', '/\s+/',),
			array(' ', ' ',),
			$str
		);
		
		$str = $this->html_codec($str, false); // remove entities
		
		// insert errorhandling if not numeric
		if ($truncate !== false) {
			$str = substr($str, 0, (intval($truncate <= 0) ? 175 : $truncate)); 
			$str = trim(substr($str, 0, strrpos($str, ' ')) . ' ' . (($this->config['charset'] == 'UTF-8') ? '…' : '...'));
		}
		
		if ($this->extConf['enableDevLog'] == 1) {
			t3lib_div::devLog('get_plaintext()', $this->extKey, 0, array(
				'str'         => '[...]',
				'weighted'    => ($weighted)    ? 'true' : 'false',
				'strip_title' => ($strip_title) ? 'true' : 'false',
				'truncate'    => ($truncate) ? $truncate : 'false',
				'plaintext'   => $str)
			);
		}
		return $str;
	}
	
	function get_keywords($str, $amount) {
		// \P, \p and \X patterns are not compiled by default - therfore we use \w, but this limits us to character set specific limitations by the Perl "word" definition
		// PCRE need to be compiled with --enable-unicode-properties
		if(@preg_match_all('/\p{L}+/u', $str, $arr) === false) {
			if ($this->extConf['enableDevLog'] == 1) { t3lib_div::devLog('--enable-unicode-properties is probably not compiled', $this->extKey, 2); }
			// add u modifier in case we're using UTF-8, else multibyte characters won't work
			preg_match_all('/\w+/' . (($this->config['charset'] == 'UTF-8') ? 'u' : ''), $str, $arr);
		}
		$arr = $arr[0];
		unset($str);
		
		// read the stopwordsfile - must be called "stopwords.**" where ** represents a two letter ISO 639-1 language code
		// stopwords can be separated by whitespace, commma or semicolon
		// should be extended to a configurable path (via typoscript) where other stopwords.** files could reside - atm fileadmin is hardcoded
		
		$stopwords = preg_split(
			'/\s+|,|;/',
			strtolower(
				$this->read_stopwords_file('/' . t3lib_extMgm::siteRelPath($this->extKey) . 'res/stopwords', true) . ' ' . 
				$this->read_stopwords_file('/' . t3lib_extMgm::siteRelPath($this->extKey) . 'res/stopwords.' . $GLOBALS['TSFE']->tmpl->setup['config.']['language'], true) . ' ' . 
				$this->read_stopwords_file($this->config['keywords.']['stopwordsdir'] . 'stopwords') . ' ' . 
				$this->read_stopwords_file($this->config['keywords.']['stopwordsdir'] . 'stopwords.' . $GLOBALS['TSFE']->tmpl->setup['config.']['language']) 
			)
		);
		
		foreach($arr as $key => $value) {
			if (
				strlen($value) <= 1 ||                   // keys shorter/equal 1 character
				is_numeric($value) ||                    // numeric keys
				in_array(strtolower($value), $stopwords) // stopwords
			) {
				unset($arr[$key]);
			} 
		};
		
		// group valus by frequency, count them and sort them by frequency
		$arr = array_count_values($arr);
		arsort($arr);
		array_splice($arr, (intval($amount) <= 0 ? 10 : $amount)); // splice out n elements of the array, if given amount lower or equal zero, use 10 as default
		
		$i = 0;
		foreach($arr as $key => $value) {
			if ($i != 0) {
				$str .= ', ';
			}
			$str .= $key;
			$i++;
		}
		if ($this->extConf['enableDevLog'] == 1) { t3lib_div::devLog('get_keywords()', $this->extKey, 0, array('arr' => $arr, 'str' => $str)); }
		return $str;
	}
	
	function read_stopwords_file($path, $core=false) {
		$path = t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT') . $path;
		if(!$stopwords = @file_get_contents($path)) {
			if ($this->extConf['enableDevLog'] == 1) {
				t3lib_div::devLog(
					'could not read ' . (($core) ? 'core' : 'custom') . ' stopwords file',
					$this->extKey,
					1,
					array('path' => $path)
				);
			}
		} else {
			if ($this->extConf['enableDevLog'] == 1) { t3lib_div::devLog('read stopwords file', $this->extKey, -1, array('path' => $path)); }
			if ($this->config['charset'] == 'UTF-8') {
				return $stopwords;
			} else {
				return iconv('UTF-8', $this->config['charset'], $stopwords);
			}
		}
	}
	
	function html_codec($str, $encode=true) {
		if($encode) {
			if($this->config['charset'] == 'UTF-8') {
				return htmlspecialchars($str, ENT_QUOTES, $this->config['charset']);
			} else {
				return htmlentities($str, ENT_QUOTES, $this->config['charset']);
			}
		} else {
			return html_entity_decode($str, ENT_QUOTES, $this->config['charset']); 
		}
	}
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/autometa/pi1/class.tx_autometa_pi1.php"]){
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/autometa/pi1/class.tx_autometa_pi1.php"]);
}
?>
