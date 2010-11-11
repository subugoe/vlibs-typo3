<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2005 Michael Brauchl (mcyra@chello.at)
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
 * Class/Function for autogenerating meta keywords when typo
 * documents are saved or changed.
 *
 * @author    Michael Brauchl (mcyra@chello.at)
 *
 * for generating keywords automatically, you must set
 *   "plugin.mc_autokeywords.autogenerate = 1" 
 * in the TSConfig of the rootpage of your website.
 *
 * you can set stopwords as list in the pluginvariable
 *   "plugin.mc_autokeywords.stopWords = ist,oder,nicht" 
 * in the TSConfig of the rootpage of your website.
 *
 * you can limit the amount of generated keywords
 * with the pluginparm
 *   "plugin.mc_autokeywords.count = 150" 
 * default is 150 keywords
 */

class tx_mcautokeywords {

	/**
    *  Change page or content was requested
    */    
	function processDatamap_afterDatabaseOperations( $status,$table,$id,&$fieldArray,&$reference ) {

		$zeit = date('H:i:s', time() );
		
		// map new id
		if ( $status == 'new' ) {
			$id = $reference->substNEWwithIDs[$id];
		}		
		
		//  get page uid
		$uid = $this->getPageUid( $id, $table, $reference );
		
		// get keyword settings
		$PageTSconfig = t3lib_BEfunc::getPagesTSconfig( $uid );
		$this->conf = $PageTSconfig['plugin.']['mc_autokeywords.'];
		
		// if autogenerate is off, exit 
		if ( !isset ( $this->conf ) OR 
		     !isset ( $this->conf['autogenerate'] ) OR 
		     $this->conf['autogenerate'] != 1 ) {
			return;
		}
		
		// get page content including Title, header etc.
		$pageContent = $this->getPageContent( $uid );
		if ( $pageContent === FALSE ) {
			// user have checked to dont change keywords
			return;
		}
		
		// make Keywords from wordlist
		$keywords = $this->makeKeywords( $this->conf, $pageContent );
		
		// set keywords in page
		$where = 'uid='.intval( $uid );
		$fieldArray = array( 'keywords' => $keywords );
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery( $table, $where, $fieldArray);
	}
	
	/**
	  *  Delete of page or content was requested
	  */	  
	function processCmdmap_postProcess( $command, $table, $id, $value, &$reference ) {

		// back if no delete-action is requested
		if ( $command != 'delete' ) {
			return;
		}

		$zeit = date('H:i:s', time() );

		// map new id
		if ( $status == 'new' ) {
			$id = $reference->substNEWwithIDs[$id];
		}

		//  get page uid
		$uid = $this->getPageUid( $id, $table, $reference );

		// get keyword settings
		$PageTSconfig = t3lib_BEfunc::getPagesTSconfig($uid);
		$this->conf = $PageTSconfig['plugin.']['user_keywords.'];

		// if autogenerate is off, exit 
		if ( !isset ( $this->conf ) OR 
		     !isset ( $this->conf['autogenerate'] ) OR 
		     $this->conf['autogenerate'] != 1 ) {
			return;
		}

		// get page content including Title, header etc.
		$pageContent = $this->getPageContent( $uid );
		if ( $pageContent === FALSE ) {
			// user have checked to dont change keywords
			return;
		}		
		
		
		// make keywords from wordlist
		$keywords = $this->makeKeywords( $this->conf, $pageContent );		
		
		// set keywords in page
		$where = 'uid='.intval( $uid );
		$fieldArray = array( 'keywords' => $keywords );
		$GLOBALS['TYPO3_DB']->exec_UPDATEquery( $table, $where, $fieldArray);
	}

	/**
	  *  make keywords from wordlist
	  */	
	function makeKeywords( $conf, $pageContent ) {
		
		$retVal = '';
		
		// stop word array configuration
		if ( !isset ( $conf['stopWords'] ) ) {
			$stopWords = Array();
		} else {
			$tempAr = explode( ',', $conf['stopWords'] );
			reset ( $tempAr );
			foreach ( $tempAr as $word ) {
				$word = strtolower( trim( $word ) );
				$stopWords[$word] = $word;
			}
		}
		
		// how much keywords should we render?
		if ( !isset ( $conf['count'] ) OR $conf['count'] == 0 OR $conf['count'] == '' ) {
			$count = 150;
		} else {
			$count = $conf['count'];
		}
		
		// sort the extracted wordlist
		$wordList = Array();
		$tempAr = explode( ',', $pageContent );
		reset ( $tempAr );
		foreach ( $tempAr as $word ) {
			$word = trim( $word );
			if ( isset ( $stopWords[strtolower($word)] ) ) {
				continue;
			}
			if ( strlen ($word) < 4 ) {
				continue;
			}
			if ( !isset ( $wordList[$word] ) ) {
				$wordList[$word] = 1;
				continue;
			}
			$wordList[$word]++;
		}

		reset ( $wordList );
		arsort ( $wordList );

		$i = 0;
		
		// fill the keywordlist
		while ( list ( $word, $ammount ) = each ( $wordList ) AND $i < $count ) {
			$i++;
			$retVal.= $word.',';
		}
		
		if ( strlen ( $retVal ) > 1 ) {
			$retVal = substr( $retVal, 0, -1 );
		}
		return $retVal;
	}
	
	/**
	  *  internal - gets uid from id
	  */	
	function getPageUid( $id, $table, &$reference ){
		
		if ( $table == 'pages' ) {
			// id = uid
			return $id;
		} else {
			// get uid from tt_content table
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery( 'pid', $table, 'uid='.$id );
			if ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res ) )	{
				return trim( $row['pid'] );
			}
		}		
	}
	
	/**
	  *  gets the content of the page in plain text
	  */		
	function getPageContent( $uid ){
		
		$content = '';
		
		// get page record
		$fields = 'title,subtitle,description,abstract,tx_mcautokeywords_keyword_change';
		$table 	= 'pages';
		$where	= 'uid='.$uid;
		$res 		= $GLOBALS['TYPO3_DB']->exec_SELECTquery( $fields, $table, $where );
		$row 		= $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res );
		reset ( $row );
		while ( list( $field, $value ) = each ( $row ) ) {
			if ( $field == 'tx_mcautokeywords_keyword_change' ){
				if ( $value == 1 ) {
					return FALSE;
				}
				continue;
			}
			$content.= $this->addWords( $value );
		}
		
		// get content from tt_content
		$fields = 'header,bodytext,subheader';
		$table 	= 'tt_content';
		$where	= 'pid='.$uid.' AND deleted = 0';		
		$res 		= $GLOBALS['TYPO3_DB']->exec_SELECTquery( $fields, $table, $where );
		while ( $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc( $res ) ) {
			reset ( $row );
			while ( list( $field, $value ) = each ( $row ) ) {
				$content.= $this->addWords( $value );
			}			
		}
		
		if ( strlen( $content ) > 1 ) {
			$content = substr( $content, 0, -1 );
		} else {
			$content = '';
		}
		
		return $content;
	}

	/**
	  *  adds all words from $value to the wordlist
	  */			
	function addWords( $value ) {

		$value = strip_tags( $value );
		$value = html_entity_decode ( $value );

		$retVal = '';
		$pattern = '/([a-z0-9öäüÖÄÜß]+)/is';
		if ( preg_match_all( $pattern, $value, $matches ) ) {
			foreach ( $matches[1] as $thisValue ) {
				$retVal.= $thisValue.',';
			}
		}
		return $retVal;
	}
}




if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/mc_autokeywords/class.tx_mcautokeywords.php"])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/mc_autokeywords/class.tx_mcautokeywords.php"]);
}

?>