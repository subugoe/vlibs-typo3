<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 senomedia <info@sensomedia.de>
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * various levels of (x)html content cleaning for post processing hook
 *
 * @author	Michael 'Iggy' Rudolph <info@sensomedia.de>
 * @package	TYPO3
 * @subpackage	tx_metaext
 */
 
class tx_metaext_postprocess {

	/**
	 * @param	array		parameters from hook
	 * @param	object		TSFE reference
	 * @return	void
	 */
	function formatOutput(&$params, &$pObj) {


		### get EM config
		$conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['metaext']);

		
		### only process the main page tyype, no processing for print, rss, etc...
		if ( $GLOBALS['TSFE']->type != 0 && !in_array( $GLOBALS['TSFE']->type, split(',',$conf['additionalpagetypes']) ) ) {	return;	}
		
		$u = $conf['processunicode'] ? 'u':'';

		### horizontal cleaning #############################################################
		#####################################################################################

		# remove html comments... but not inside script tags ;)
		if ($conf['removehtmlcomments']){
			
			preg_match_all( "/<!--TYPO3SEARCH_(begin|end)-->/is".$u, $pObj->content, $matches_1, PREG_OFFSET_CAPTURE );
			preg_match_all( "/<(!--\[|scri|styl).+?<(\]--|\/script|\/style)>/is".$u, $pObj->content, $matches_2, PREG_OFFSET_CAPTURE );
			$matchesunsorted = array_merge_recursive($matches_1[0],$matches_2[0]);
			$tmpmatches=$matches=array();
			# sort the array of matches for the starting position of each item which is $matchesunsorted[][1] 
			foreach($matchesunsorted as $idx => $founditems) { $tmpmatches[$idx]=$founditems[1]; }
			asort( $tmpmatches, SORT_NUMERIC );
			foreach($tmpmatches as $idx => $pos) { 
				$matches[] = array( '0' => $matchesunsorted[$idx][0], '1' => $pos );
			}
			# removing all the html comments in the save areas
			$startpos = 0;
			$resultstring = "";
			foreach($matches as $idx => $matcharray) {
				$prestring = substr($pObj->content, $startpos, (int)$matcharray[1]-$startpos);
				$prestring = preg_replace( "/<!--(.*?)-->/ism".$u, "", $prestring);
				# remove only the comment tags (inside cdata tags to prevent these very very old browsers 
				# from displaying inline styles and script content) not the content of the 'comment' inside script and style.
				# these comment tags aren't really necessary anymore and only make the gecko engine ranting about invalid selectors. 
				# 
				# look for whitespace around <!-- and -->, to make sure it isn't the TYPOSEARCH comment, which has to be left alone
				if ($conf['removetagsinsidescript']){
					$cleanedmatch = preg_replace( "/(<!--[ |\n]+|[\/]+[ ]?-->[ |\n]+)/ism".$u, "", $matcharray[0]);
				} else {
					$cleanedmatch = $matcharray[0];
				}
				$resultstring .= $prestring . $cleanedmatch . "\n";
				$startpos = (int)$matcharray[1] + strlen($matcharray[0]) ;
			}
			$prestring = substr($pObj->content, $startpos, strlen($pObj->content)- $startpos);
			$resultstring .= preg_replace( "/<!--(.*?)-->/ism".$u, "", $prestring);
			$pObj->content = $resultstring . "\n";
		}

		# always a linebreak before </head>, </body>, </html>
		$re_find[] = "/([^\n])(<\/head|<\/body|<\/html)/i".$u;
		$re_replace[] = "$1\n$2";
		# always a linebreak after <head>, <body>, <html>
		$re_find[] = "/(<head[^>]*?>|<body[^>]*?>|<html[^>]*?>)([^\n])/i".$u;
		$re_replace[] = "$1\n$2";
		# no linebreak before some ending tags
		$re_find[] = "/(\S)(\s*?)(<\/p>)/si".$u;
		$re_replace[] = "$1$3";


		# get everything between the head tag and the outer parts ofcourse
		if ($conf['sortheadertags']){
			$matches = '';
			preg_match( "/<head>\s*(.+?)\s*<\/head>/is".$u, $pObj->content, $matches, PREG_OFFSET_CAPTURE );
			$headstart = (int)$matches[1][1];
			$headcontent = $matches[1][0];
			$prehead = substr($pObj->content, 0, $headstart);
			$posthead = substr($pObj->content, $headstart+strlen($headcontent), strlen($pObj->content)-$headstart);
			
			# split the header tags apart
			$headarray = array(
				'base'	=>	'',
				'title'	=>	'',
				'meta'	=>	'',
				'link'	=>	'',
				'style'	=>	'',
				'script'=>	'',
				'noscript'=>	'',
				'!--['	=>	''
			);
			
			$matches = '';
			
			/**********************************************************************
			* first off, conditional comments are no good style at all.
			* you better use typo3s browser detecting capabilities to insert
			* specific stylesheets for IE browsers. 
			* but due to lots of developers still rely on them i finally
			* decided to include them properly in the sort header tags process
			* so they don't get blatantly removed when enabling this feature ;)
			**********************************************************************/
			#1st find conditional comments and remove them from headercontent
			preg_match_all( "/<(!--\[).*?(\]--)>/is".$u, $headcontent, $matches);
			$headerline_cc = $matches[0];
			$headertag_cc = $matches[1];
			$headcontent = str_replace($headerline_cc,'',$headcontent);
			
			#after that find various selfclosing head tags in the remaining headercontent: base, meta, link 
			preg_match_all( "/<(base|meta|link).*?\/>/is".$u, $headcontent, $matches);
			$headerline_st = $matches[0];
			$headertag_st = $matches[1];
			$headcontent = str_replace($headerline_st,'',$headcontent);

			#after that find various head tags in the remaining headercontent: title, style, script, noscript 
			# (indeed noscript isn't the sort of tag which should be placed inside the header at all, but some extensions do use it so i decided to also include it in this list...)
			preg_match_all( "/<(title|style|script|noscript).*?(\/title|\/style|\/script|\/noscript)>/is".$u, $headcontent, $matches);
	
			#merge already found conditional comments (if any) and various standard header tags
			$headerline = array_merge(array_merge($matches[0],$headerline_cc), $headerline_st);
			$headertag = array_merge(array_merge($matches[1],$headertag_cc), $headertag_st);

			#sort them following this priority -> base|title|meta|link|style|script|noscript|!--[ (conditional comments)
			foreach ($headerline as $idx => $line ) {
				$headarray[$headertag[$idx]][] = $line;
			}		
			$headcontent = "";
			foreach( $headarray as $tag => $tagcontent ) {
				if($tagcontent) $headcontent .= implode("\n",$tagcontent)."\n";
			}
			# and glue them together again		
			$pObj->content = $prehead . $headcontent . $posthead;
		}
		
		# reinject the copyright notice if it has been removed by sortheadertags/removehtmlcomments
		if ($conf['copyrightcomment'] && ($conf['sortheadertags'] || $conf['removehtmlcomments']) ){
			$copyrightnotice =	'
			<!--
				This website is brought to you by TYPO3 - get.content.right
				TYPO3 is a free open source Content Management Framework created by Kasper Skaarhoj and licensed under GNU/GPL.
				TYPO3 is copyright 1998-2005 of Kasper Skaarhoj. Extensions are copyright of their respective owners.
				Information and contribution at http://www.typo3.com
			-->';
			$matches = '';
			preg_match( "/<head>\s*(.+?)\s*<\/head>/is".$u, $pObj->content, $matches, PREG_OFFSET_CAPTURE );
			$headstart = (int)$matches[1][1];
			# dismember the header content and the surrounding parts
			$headcontent = $matches[1][0];
			$prehead = substr($pObj->content, 0, $headstart);
			$posthead = substr($pObj->content, $headstart+strlen($headcontent), strlen($pObj->content)-$headstart);
			# and inject the copyright notification into it		
			$pObj->content = $prehead . $headcontent . $copyrightnotice . $posthead;
		}

		# remove blank lines
		if ($conf['removeblanklines'] ){
			$re_find[] = "/\s*\n/".$u;
			$re_replace[] = "\n";
		}
		# do all the above configured replacements 
		$pObj->content = preg_replace($re_find,$re_replace,$pObj->content);

		# remove leading tabs/whitespace but spare them within textareas
		if ($conf['indentation'] || $conf['removewhitespace']){
			preg_match_all( "/<(textarea).+?<\/(textarea)>/is".$u, $pObj->content, $matches, PREG_OFFSET_CAPTURE );
			$startpos = 0;
			$resultstring = "";
			foreach($matches[0] as $idx => $matcharray) {
				$prestring = substr($pObj->content, $startpos, (int)$matcharray[1]-$startpos);
				$prestring = preg_replace( "/^[ |\t]+/m".$u, "", $prestring);
				$resultstring .= $prestring . $matcharray[0] . "\n";
				$startpos = (int)$matcharray[1] + strlen($matcharray[0]);
			}
			$prestring = substr($pObj->content, $startpos, strlen($pObj->content)- $startpos);
			$resultstring .= preg_replace( "/^[ |\t]+/m".$u, "", $prestring);
			$pObj->content = $resultstring . "\n";
		}



		### vertical cleaning ###############################################################
		#####################################################################################

		if (!$conf['indentation']){ return; }

		$tabstops = 0;
		$lines = explode("\n",$pObj->content);
		$indented = array();

		$indentationchar = array( "\t", "  ", "    ", "      " );
		$doindent = 1;
		foreach($lines as $idx => $row)	{
			$indented_line = $row;
			
			# line indentation algorythm ----------------------------------------------------
			
			# indentlevel -1 if: a closing div,ul,head,body tag is found at rowstart [or] a closing curlybrace is found at rowstart
			if ( preg_match( "/^(<\/div|<\/ul|<\/head|<\/body)/i".$u, $row) || ($conf['indentcurlybraces'] && preg_match( "/^( |\t)*\}{1}/i".$u, $row)) ) { $tabstops--; }

			# do indentation here
			if ($doindent ) {
				for($i = 0; $i < $tabstops; $i++)	{
					$indented_line = $indentationchar[$conf['indentationchar']].$indented_line;
				}
			}
			
			# no indentation on the following lines if: a <textarea> is found
			if ( preg_match( "/(<textarea)/i".$u, $row)) $doindent = 0;
			
			# indentation on the following lines if: a </textarea> is found
			if ( preg_match( "/(<\/textarea)/i".$u, $row)) $doindent = 1;

			# indentlevel +1 if: an opening div is found at rowstart and row doesn't end with a closing div [or] an opening ul,head,body tag is found at rowstart [or] an opening curlybrace is found at rowend
			if ( (preg_match( "/^<div[^>]*?>.*?/i".$u, $row) && !preg_match( "/<\/div>$/i".$u, $row)) || preg_match( "/^(<ul|<head|<body)/i".$u, $row) || ($conf['indentcurlybraces'] && preg_match( "/\{{1}( |\t)*$/i".$u, $row)) ) { $tabstops++; }

			$indented[] = $indented_line;
		}

		$pObj->content = implode("\n", $indented);

	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/metaext/lib/class.tx_metaext_postprocess.php']) {
   include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/metaext/lib/class.tx_metaext_postprocess.php']);
}
?>
