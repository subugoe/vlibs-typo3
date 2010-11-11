<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 sensomedia.de <info@sensomedia.de>
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
 * creates xml sitemaps, more information see: 
 * http://www.sitemaps.org/protocol.php
 * https://www.google.com/webmasters/tools/docs/en/protocol.html
 * 
 * @author	Michael 'Iggy' Rudolph <info@sensomedia.de>
 * @package	TYPO3
 * @subpackage	tx_metaext
 */
require_once(PATH_t3lib.'class.t3lib_pagetree.php');

class tx_metaext_sitemap {


	var $cfg;
	var $conf;
	/**
	 * @param	array		parameters from hook
	 * @param	object		TSFE reference
	 * @return	void
	 */
	function createSitemap($params, $cfg) {

		### get EM config
		$this->conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['metaext']);
		
		### check if the sitemap is requested on rootlevel where it belongs, else redirect
		$this->redirectToRoot();
		
		### no cache during developement stage -----------------------------------------------------------------------------------
		$GLOBALS['TSFE']->no_cache=1;
		### no cache during developement stage -----------------------------------------------------------------------------------
		
		### if true, default false: includes pages even if they are hidden in menu	
		$showhiddeninmenu 	= $cfg['showhiddeninmenu'] ? $cfg['showhiddeninmenu'] : 0;
		### if true, default false: includes pages even if they are hidden at all	
		$showhiddenpages 	= $cfg['showhiddenpages'] ? $cfg['showhiddenpages'] : 0;
		### if true, default true: includes pages even if they are not in the list of pages to index	
		$showsearchexcluded	= $cfg['showsearchexcluded'] ? $cfg['showsearchexcluded'] : 0;
		### max depth of the tree	
		$levels				= $cfg['levels'];
		### if true, default true: excludes pages if the robots tag suggests 'noindex'	
		$excludeifrobots	= $cfg['excludeifrobots'] ? $cfg['excludeifrobots'] : 0;
		### if true, default true: exclude shortcut pages	
		$excludeshortcuts	= $cfg['excludeshortcuts'] ? $cfg['excludeshortcuts'] : 0;
		### the id where to start building a sitemap (if this is a shortcut, the next page down the rootline which isn't a shortcut will be taken as rootpage instead)
		$startuid			= $cfg['startuid'] ? $cfg['startuid'] : 0;
		### if true, default true: include priority tag according to the page settings	
		$includepriority	= $cfg['includepriority'] ? $cfg['includepriority'] : 0;
		
			
		### id of the start page... lets see if it's also the root page 
		$uid = $startuid ? $startuid : $GLOBALS['TSFE']->id;
		$startpage = $GLOBALS['TSFE']->sys_page->getRawRecord('pages', $uid);

		### starting with the parent (if any) of the suggested startpage
		$pid = $startpage['pid'];
		while ($pid > 0) {
			$parentpage = $GLOBALS['TSFE']->sys_page->getRawRecord('pages', $pid);
			### if it's a shortcut to another page or via shortcut_mode (first subpage, random subpage)... 
			if ( $parentpage['doktype'] == 4 && ( $parentpage['shortcut'] == $uid || $parentpage['shortcut_mode'] > 0 ) ) {
				### ...then recursing once more to see if it's the last one
				$startpage = $parentpage;
				$pid = $parentpage['pid'];
				$uid = $parentpage['uid'];
			} 
			else { break; }
		}
		$pagetree = t3lib_div::makeInstance('t3lib_pageTree');
		$pagetree->addField('SYS_LASTCHANGED', 1);
		$pagetree->addField('crdate', 1);
		$pagetree->addField('tx_metaext_robots', 1);
		$pagetree->addField('tx_metaext_importance', 1);
		$pagetree->init('
			'.(!$showhiddeninmenu? 'AND nav_hide = 0 AND doktype != 5' : '').'
			'.(!$showsearchexcluded? 'AND no_search = 0' : '').'
			'.(!$showhiddenpages? 'AND hidden = 0' : '').'
			AND deleted = 0
			AND (starttime = 0 || starttime > NOW())
			AND (endtime = 0 || endtime < NOW())
			AND doktype NOT IN (199, 254, 255)
		');
		$pagetree->getTree($uid, $levels);
		
		$pagesarray = array();
		$mytree = $pagetree->tree;
		foreach($mytree as $row => $page) {
			### skip this row if page is a shortcut and they shall be excluded
			if($excludeshortcuts && $page['row']['doktype'] == 4) { continue; }
			### skip this row if robots tag says noindex and this shall be excluded
			if($excludeifrobots && $page['row']['tx_metaext_robots'] > 3) { continue; }
			
			$pagesarray[] = array ( 
				'loc' => $GLOBALS['TSFE']->cObj->typoLink_URL( array( 'parameter' => $page['row']['uid'] ) ),
				'lastmod' => date("Y-m-d", ($page['row']['SYS_LASTCHANGED'] ? $page['row']['SYS_LASTCHANGED'] : $page['row']['crdate']) )

			);
			if ($includepriority) {
				$pagesarray[count($pagesarray)-1]['priority'] = $page['row']['tx_metaext_importance'];
			}

				### implementation pending
				###'changefreq' => '',			
		}
		
		### create som propper tags around the values
		foreach ($pagesarray as $idx => $pagerow) {
			array_walk( $pagerow, create_function ( '&$val,$tag', '$val="<".$tag.">".$val."</".$tag.">";' ) );
			$pagesarray[$idx]=$pagerow; 
		}
		### and put'em into <url> tags
		array_walk( $pagesarray, create_function ( '&$val,$tag', '$val="<url>".implode(\'\',$val)."</url>";' ) );
		
		### end finally wrap the whole stuff inside an urlset tag and return it back
		return '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'.chr(10).implode(chr(10),$pagesarray).chr(10).'</urlset>';

	}
	
	/**
	 * realurl allows to get the sitemap file on every sublevel... 
	 * if the file is not requested at the base level, redirect to this one 
	 *
	 * @return	void
	 */
	private function redirectToRoot() {
		
		### reference url. the intended path
		$refurl = $this->getDomainName().$this->getFilename();
		
		### request url. get only the part without any query params including the ? mark from the actual request
		$requrl = preg_replace("/(.*?)\?.*/i","$1",t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
		
		if( $refurl !== $requrl ) {
			$reqquery = preg_replace("/(.*?)(\?.*)/i","$2",t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
			header('Location: ' . $refurl.$reqquery, true, 301);
		}
		
	}


	/**
	 * tries return the sitemap filename for the current typeNum (if configured propperly ;)). 
	 *
	 * @return	string	sitemap name
	 */
	private function getFilename() {
		$sitemap = '';
		$map = split(',',$this->conf['sitemaplist']);
		if(count($map)) {
			foreach( $map as $idx => $mapvalue) {
				list($mapname, $typenum ) = split(':',trim($mapvalue));
				if ($typenum = $GLOBALS['TSFE']->type) {
					$sitemap = $mapname;
					break;
				}
			}	
		}
		return $sitemap;
	}

	/**
	 * tries to get a valid url prefix for the gererated urls. 
	 * checks if baseUrl/absRefPrefix provide one, if not maybe a domain record can be found
	 * and if even this is not possible, the HTTP_HOST will be taken.
	 *
	 * @return	string	donain name prefix
	 */
	private function getDomainName() {
		
		# getting baseUrl(absRefPrefix
		$domainname = $GLOBALS['TSFE']->baseUrl ? $GLOBALS['TSFE']->baseUrl : $GLOBALS['TSFE']->absRefPrefix;
		### debug ($domainname,'baseurl/absrefprefix');
		# checking domain record for requested HTTP_HOST if available.
		if(empty($domainname)) {
			$domainrecord = $GLOBALS['TSFE']->findDomainRecord();
			# reading domain name from sys_domain if available
			if($domainrecord) {
				$row = $GLOBALS['TSFE']->sys_page->getRawRecord('sys_domain', $domainrecord);
				$domainname = count($row) ? $row['domainName'] : '';
				### debug ($domainname,'domainrecord');
			} 
		}
		# if no according domain record was found, use the http_host instead
		if(empty($domainname)) {
			### as a last resort get at least the http_host
			$domainname = t3lib_div::getIndpEnv('TYPO3_REQUEST_HOST');
			### debug ($domainname,'TYPO3_REQUEST_HOST');
		}
		# make it a valid url prefix
		$domainname = preg_match('/\w+:\/\//i',$domainname) ? $domainname : 'http://'.$domainname;
		$domainname = preg_match('/\/$/i',$domainname) ? $domainname : $domainname.'/';
		
		return $domainname;
		
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/metaext/lib/class.tx_metaext_sitemap.php']) {
   include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/metaext/lib/class.tx_metaext_sitemap.php']);
}
?>
