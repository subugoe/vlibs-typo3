<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2009 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
/**
 * Generating navigation / menus from TypoScript
 *
 * This file contains five classes, four of which are extensions to the main class, tslib_menu.
 * The main class, tslib_menu, is also extended by other external PHP scripts such as the GMENU_LAYERS and GMENU_FOLDOUT scripts which creates pop-up menus.
 * Notice that extension classes (like "tslib_tmenu") must have their suffix (here "tmenu") listed in $this->tmpl->menuclasses - otherwise they cannot be instantiated.
 *
 * $Id: class.tslib_menu.php 5174 2009-03-10 20:23:43Z ohader $
 * Revised for TYPO3 3.6 June/2003 by Kasper Skaarhoj
 * XHTML compliant
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 */


/**
 * Applying Patch 6637 to the function makeMenu 
 * which offers the .reverseOrder option for 
 * special = rootline
 * see: line 388 ff.
 * see: http://bugs.typo3.org/view.php?id=6637 for more detail
 * NOTE: this patch becomes obsolete with Typo3v4.3 and should be 
 * diabled then.
 *
 * @author	Michael Rudolph - sensomedia.de <info@sensomedia.de>
 * @package TYPO3
 * @subpackage metaext
 */
class ux_tslib_menu extends tslib_menu {


	/**
	 * Creates the menu in the internal variables, ready for output.
	 * Basically this will read the page records needed and fill in the internal $this->menuArr
	 * Based on a hash of this array and some other variables the $this->result variable will be loaded either from cache OR by calling the generate() method of the class to create the menu for real.
	 *
	 * @return	void
	 */
	function makeMenu()	{
		if ($this->id)	{

				// Initializing showAccessRestrictedPages
			if ($this->mconf['showAccessRestrictedPages'])	{
					// SAVING where_groupAccess
				$SAVED_where_groupAccess = $this->sys_page->where_groupAccess;
				$this->sys_page->where_groupAccess = '';	// Temporarily removing fe_group checking!
			}

				// Begin production of menu:
			$temp = array();
			$altSortFieldValue = trim($this->mconf['alternativeSortingField']);
			$altSortField = $altSortFieldValue ? $altSortFieldValue : 'sorting';
			if ($this->menuNumber==1 && $this->conf['special'])	{		// ... only for the FIRST level of a HMENU
				$value = $this->parent_cObj->stdWrap($this->conf['special.']['value'], $this->conf['special.']['value.']);

				switch($this->conf['special'])	{
					case 'userdefined':
						$temp = $this->includeMakeMenu($this->conf['special.'],$altSortField);
					break;
					case 'userfunction':
						$temp = $this->parent_cObj->callUserFunction(
							$this->conf['special.']['userFunc'],
							array_merge($this->conf['special.'],array('_altSortField'=>$altSortField)),
							''
						);
						if (!is_array($temp))	$temp=array();
					break;
					case 'language':
						$temp = array();

							// Getting current page record NOT overlaid by any translation:
						$currentPageWithNoOverlay = $this->sys_page->getRawRecord('pages',$GLOBALS['TSFE']->page['uid']);

							// Traverse languages set up:
						$languageItems = t3lib_div::intExplode(',',$value);
						foreach($languageItems as $sUid)	{
								// Find overlay record:
							if ($sUid)	{
								$lRecs = $this->sys_page->getPageOverlay($GLOBALS['TSFE']->page['uid'],$sUid);
							} else $lRecs=array();
								// Checking if the "disabled" state should be set.
							if (
										(t3lib_div::hideIfNotTranslated($GLOBALS['TSFE']->page['l18n_cfg']) && $sUid && !count($lRecs)) // Blocking for all translations?
									|| ($GLOBALS['TSFE']->page['l18n_cfg']&1 && (!$sUid || !count($lRecs))) // Blocking default translation?
									|| (!$this->conf['special.']['normalWhenNoLanguage'] && $sUid && !count($lRecs))
								)	{
								$iState = $GLOBALS['TSFE']->sys_language_uid==$sUid ? 'USERDEF2' : 'USERDEF1';
							} else {
								$iState = $GLOBALS['TSFE']->sys_language_uid==$sUid ? 'ACT' : 'NO';
							}

							if ($this->conf['addQueryString'])	{
								$getVars = $this->parent_cObj->getQueryArguments($this->conf['addQueryString.'],array('L'=>$sUid),true);
							} else {
								$getVars = '&L='.$sUid;
							}

								// Adding menu item:
							$temp[] = array_merge(
								array_merge($currentPageWithNoOverlay, $lRecs),
								array(
									'ITEM_STATE' => $iState,
									'_ADD_GETVARS' => $getVars,
									'_SAFE' => TRUE
								)
							);
						}
					break;
					case 'directory':
						if ($value=='') {
							$value=$GLOBALS['TSFE']->page['uid'];
						}
						$items=t3lib_div::intExplode(',',$value);

						foreach($items as $id)	{
							$MP = $this->tmpl->getFromMPmap($id);

								// Checking if a page is a mount page and if so, change the ID and set the MP var properly.
							$mount_info = $this->sys_page->getMountPointInfo($id);
							if (is_array($mount_info))	{
								if ($mount_info['overlay'])	{	// Overlays should already have their full MPvars calculated:
									$MP = $this->tmpl->getFromMPmap($mount_info['mount_pid']);
									$MP = $MP ? $MP : $mount_info['MPvar'];
								} else {
									$MP = ($MP ? $MP.',' : '').$mount_info['MPvar'];
								}
								$id = $mount_info['mount_pid'];
							}

								// Get sub-pages:
							$res = $GLOBALS['TSFE']->cObj->exec_getQuery('pages',Array('pidInList'=>$id,'orderBy'=>$altSortField));
							while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
								$GLOBALS['TSFE']->sys_page->versionOL('pages',$row);

								if (is_array($row))	{
										// Keep mount point?
									$mount_info = $this->sys_page->getMountPointInfo($row['uid'], $row);
									if (is_array($mount_info) && $mount_info['overlay'])	{	// There is a valid mount point.
										$mp_row = $this->sys_page->getPage($mount_info['mount_pid']);		// Using "getPage" is OK since we need the check for enableFields AND for type 2 of mount pids we DO require a doktype < 200!
										if (count($mp_row))	{
											$row = $mp_row;
											$row['_MP_PARAM'] = $mount_info['MPvar'];
										} else unset($row);	// If the mount point could not be fetched with respect to enableFields, unset the row so it does not become a part of the menu!
									}

										// Add external MP params, then the row:
									if (is_array($row))	{
										if ($MP)	$row['_MP_PARAM'] = $MP.($row['_MP_PARAM'] ? ','.$row['_MP_PARAM'] : '');
										$temp[$row['uid']] = $this->sys_page->getPageOverlay($row);
									}
								}
							}
						}
					break;
					case 'list':
						if ($value=='') {
							$value=$this->id;
						}
						$loadDB = t3lib_div::makeInstance('FE_loadDBGroup');
						$loadDB->start($value, 'pages');
						$loadDB->additionalWhere['pages']=tslib_cObj::enableFields('pages');
						$loadDB->getFromDB();

						foreach($loadDB->itemArray as $val)	{
							$MP = $this->tmpl->getFromMPmap($val['id']);

								// Keep mount point?
							$mount_info = $this->sys_page->getMountPointInfo($val['id']);
							if (is_array($mount_info) && $mount_info['overlay'])	{	// There is a valid mount point.
								$mp_row = $this->sys_page->getPage($mount_info['mount_pid']);		// Using "getPage" is OK since we need the check for enableFields AND for type 2 of mount pids we DO require a doktype < 200!
								if (count($mp_row))	{
									$row = $mp_row;
									$row['_MP_PARAM'] = $mount_info['MPvar'];

									if ($mount_info['overlay'])	{	// Overlays should already have their full MPvars calculated:
										$MP = $this->tmpl->getFromMPmap($mount_info['mount_pid']);
										if ($MP) unset($row['_MP_PARAM']);
									}
								} else unset($row);	// If the mount point could not be fetched with respect to enableFields, unset the row so it does not become a part of the menu!
							} else {
								$row = $loadDB->results['pages'][$val['id']];
							}

								// Add external MP params, then the row:
							if (is_array($row))	{
								if ($MP)	$row['_MP_PARAM'] = $MP.($row['_MP_PARAM'] ? ','.$row['_MP_PARAM'] : '');
								$temp[] = $this->sys_page->getPageOverlay($row);
							}
						}
					break;
					case 'updated':
						if ($value=='') {
							$value=$GLOBALS['TSFE']->page['uid'];
						}
						$items=t3lib_div::intExplode(',',$value);
						if (t3lib_div::testInt($this->conf['special.']['depth']))	{
							$depth = t3lib_div::intInRange($this->conf['special.']['depth'],1,20);		// Tree depth
						} else {
							$depth=20;
						}
						$limit = t3lib_div::intInRange($this->conf['special.']['limit'],0,100);	// max number of items
						$maxAge = intval(tslib_cObj::calc($this->conf['special.']['maxAge']));
						if (!$limit)	$limit=10;
						$mode = $this->conf['special.']['mode'];	// *'auto', 'manual', 'tstamp'
							// Get id's
						$id_list_arr = Array();

						foreach($items as $id)	{
							$bA = t3lib_div::intInRange($this->conf['special.']['beginAtLevel'],0,100);
							$id_list_arr[] = tslib_cObj::getTreeList(-1*$id,$depth-1+$bA,$bA-1);
						}
						$id_list = implode(',',$id_list_arr);
							// Get sortField (mode)
						switch($mode)	{
							case 'starttime':
								$sortField = 'starttime';
							break;
							case 'lastUpdated':
							case 'manual':
								$sortField = 'lastUpdated';
							break;
							case 'tstamp':
								$sortField = 'tstamp';
							break;
							case 'crdate':
								$sortField = 'crdate';
							break;
							default:
								$sortField = 'SYS_LASTCHANGED';
							break;
						}
							// Get
						$extraWhere = ($this->conf['includeNotInMenu'] ? '' : ' AND pages.nav_hide=0').$this->getDoktypeExcludeWhere();

						if ($this->conf['special.']['excludeNoSearchPages']) {
							$extraWhere.= ' AND pages.no_search=0';
						}
						if ($maxAge>0)	{
							$extraWhere.=' AND '.$sortField.'>'.($GLOBALS['SIM_ACCESS_TIME']-$maxAge);
						}

						$res = $GLOBALS['TSFE']->cObj->exec_getQuery('pages',Array('pidInList'=>'0', 'uidInList'=>$id_list, 'where'=>$sortField.'>=0'.$extraWhere, 'orderBy'=>($altSortFieldValue ? $altSortFieldValue : $sortField.' desc'),'max'=>$limit));
						while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
							$GLOBALS['TSFE']->sys_page->versionOL('pages',$row);
							if (is_array($row))	{
								$temp[$row['uid']]=$this->sys_page->getPageOverlay($row);
							}
						}
					break;
					case 'keywords':
						list($value)=t3lib_div::intExplode(',',$value);
						if (!$value) {
							$value=$GLOBALS['TSFE']->page['uid'];
						}
						if ($this->conf['special.']['setKeywords'] || $this->conf['special.']['setKeywords.']) {
							$kw = $this->parent_cObj->stdWrap($this->conf['special.']['setKeywords'], $this->conf['special.']['setKeywords.']);
	 					} else {
		 					$value_rec=$this->sys_page->getPage($value);	// The page record of the 'value'.

							$kfieldSrc = $this->conf['special.']['keywordsField.']['sourceField'] ? $this->conf['special.']['keywordsField.']['sourceField'] : 'keywords';
							$kw = trim(tslib_cObj::keywords($value_rec[$kfieldSrc]));		// keywords.
	 					}

						$mode = $this->conf['special.']['mode'];	// *'auto', 'manual', 'tstamp'
						switch($mode)	{
							case 'starttime':
								$sortField = 'starttime';
							break;
							case 'lastUpdated':
							case 'manual':
								$sortField = 'lastUpdated';
							break;
							case 'tstamp':
								$sortField = 'tstamp';
							break;
							case 'crdate':
								$sortField = 'crdate';
							break;
							default:
								$sortField = 'SYS_LASTCHANGED';
							break;
						}

							// depth, limit, extra where
						if (t3lib_div::testInt($this->conf['special.']['depth']))	{
							$depth = t3lib_div::intInRange($this->conf['special.']['depth'],0,20);		// Tree depth
						} else {
							$depth=20;
						}
						$limit = t3lib_div::intInRange($this->conf['special.']['limit'],0,100);	// max number of items
						$extraWhere = ' AND pages.uid!='.$value.($this->conf['includeNotInMenu'] ? '' : ' AND pages.nav_hide=0').$this->getDoktypeExcludeWhere();
						if ($this->conf['special.']['excludeNoSearchPages']) {
							$extraWhere.= ' AND pages.no_search=0';
						}
							// start point
						$eLevel = tslib_cObj::getKey(
							$this->parent_cObj->stdWrap($this->conf['special.']['entryLevel'], $this->conf['special.']['entryLevel.']),
							$this->tmpl->rootLine
						);
						$startUid = intval($this->tmpl->rootLine[$eLevel]['uid']);

							// which field is for keywords
						$kfield = 'keywords';
						if ( $this->conf['special.']['keywordsField'] ) {
							list($kfield) = explode(' ',trim ($this->conf['special.']['keywordsField']));
						}

							// If there are keywords and the startuid is present.
						if ($kw && $startUid)	{
							$bA = t3lib_div::intInRange($this->conf['special.']['beginAtLevel'],0,100);
							$id_list=tslib_cObj::getTreeList(-1*$startUid,$depth-1+$bA,$bA-1);

							$kwArr = explode(',',$kw);
							foreach($kwArr as $word)	{
								$word = trim($word);
								if ($word)	{
									$keyWordsWhereArr[] = $kfield.' LIKE \'%'.$GLOBALS['TYPO3_DB']->quoteStr($word, 'pages').'%\'';
								}
							}
							$res = $GLOBALS['TSFE']->cObj->exec_getQuery('pages',Array('pidInList'=>'0', 'uidInList'=>$id_list, 'where'=>'('.implode(' OR ',$keyWordsWhereArr).')'.$extraWhere, 'orderBy'=>($altSortFieldValue ? $altSortFieldValue : $sortField.' desc'),'max'=>$limit));
							while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
								$GLOBALS['TSFE']->sys_page->versionOL('pages',$row);
								if (is_array($row))	{
									$temp[$row['uid']]=$this->sys_page->getPageOverlay($row);
								}
							}
						}
					break;
					case 'rootline':
						$begin_end = explode('|', $this->parent_cObj->stdWrap($this->conf['special.']['range'], $this->conf['special.']['range.']));
						$begin_end[0] = intval($begin_end[0]);
						if (!t3lib_div::testInt($begin_end[1])) {
							$begin_end[1] = -1;
						}

						$beginKey = tslib_cObj::getKey ($begin_end[0],$this->tmpl->rootLine);
						$endKey = tslib_cObj::getKey ($begin_end[1],$this->tmpl->rootLine);
						if ($endKey<$beginKey)	{$endKey=$beginKey;}

						$rl_MParray = array();
						foreach($this->tmpl->rootLine as $k_rl => $v_rl)	{
								// For overlaid mount points, set the variable right now:
							if ($v_rl['_MP_PARAM'] && $v_rl['_MOUNT_OL'])	{
								$rl_MParray[] = $v_rl['_MP_PARAM'];
							}
								// Traverse rootline:
							if ($k_rl>=$beginKey && $k_rl<=$endKey)	{
								$temp_key=$k_rl;
								$temp[$temp_key]=$this->sys_page->getPage($v_rl['uid']);
								if (count($temp[$temp_key]))	{
									if (!$temp[$temp_key]['target'])	{	// If there are no specific target for the page, put the level specific target on.
										$temp[$temp_key]['target'] = $this->conf['special.']['targets.'][$k_rl];
										$temp[$temp_key]['_MP_PARAM'] = implode(',',$rl_MParray);
									}
								} else unset($temp[$temp_key]);
							}
								// For normal mount points, set the variable for next level.
							if ($v_rl['_MP_PARAM'] && !$v_rl['_MOUNT_OL'])	{
								$rl_MParray[] = $v_rl['_MP_PARAM'];
							}
						}
							// Reverse order of elements (e.g. "1,2,3,4" gets "4,3,2,1"):
						if (isset($this->conf['special.']['reverseOrder']) && $this->conf['special.']['reverseOrder']) {
							$temp = array_reverse($temp);
							$rl_MParray = array_reverse($rl_MParray);
						}
					break;
					case 'browse':
						list($value)=t3lib_div::intExplode(',',$value);
						if (!$value) {
							$value=$GLOBALS['TSFE']->page['uid'];
						}
						if ($value!=$this->tmpl->rootLine[0]['uid'])	{	// Will not work out of rootline
							$recArr=array();
							$value_rec=$this->sys_page->getPage($value);	// The page record of the 'value'.
							if ($value_rec['pid'])	{	// 'up' page cannot be outside rootline
								$recArr['up']=$this->sys_page->getPage($value_rec['pid']);	// The page record of 'up'.
							}
							if ($recArr['up']['pid'] && $value_rec['pid']!=$this->tmpl->rootLine[0]['uid'])	{	// If the 'up' item was NOT level 0 in rootline...
								$recArr['index']=$this->sys_page->getPage($recArr['up']['pid']);	// The page record of "index".
							}

								// prev / next is found
							$prevnext_menu = $this->sys_page->getMenu($value_rec['pid'],'*',$altSortField);
							$lastKey=0;
							$nextActive=0;
							reset($prevnext_menu);
							while(list($k_b,$v_b)=each($prevnext_menu))	{
								if ($nextActive)	{
									$recArr['next']=$v_b;
									$nextActive=0;
								}
								if ($v_b['uid']==$value)	{
									if ($lastKey)	{
										$recArr['prev']=$prevnext_menu[$lastKey];
									}
									$nextActive=1;
								}
								$lastKey=$k_b;
							}
							reset($prevnext_menu);
							$recArr['first']=pos($prevnext_menu);
							end($prevnext_menu);
							$recArr['last']=pos($prevnext_menu);

								// prevsection / nextsection is found
							if (is_array($recArr['index']))	{	// You can only do this, if there is a valid page two levels up!
								$prevnextsection_menu = $this->sys_page->getMenu($recArr['index']['uid'],'*',$altSortField);
								$lastKey=0;
								$nextActive=0;
								reset($prevnextsection_menu);
								while(list($k_b,$v_b)=each($prevnextsection_menu))	{
									if ($nextActive)	{
										$sectionRec_temp = $this->sys_page->getMenu($v_b['uid'],'*',$altSortField);
										if (count($sectionRec_temp))	{
											reset($sectionRec_temp);
											$recArr['nextsection']=pos($sectionRec_temp);
											end ($sectionRec_temp);
											$recArr['nextsection_last']=pos($sectionRec_temp);
											$nextActive=0;
										}
									}
									if ($v_b['uid']==$value_rec['pid'])	{
										if ($lastKey)	{
											$sectionRec_temp = $this->sys_page->getMenu($prevnextsection_menu[$lastKey]['uid'],'*',$altSortField);
											if (count($sectionRec_temp))	{
												reset($sectionRec_temp);
												$recArr['prevsection']=pos($sectionRec_temp);
												end ($sectionRec_temp);
												$recArr['prevsection_last']=pos($sectionRec_temp);
											}
										}
										$nextActive=1;
									}
									$lastKey=$k_b;
								}
							}
							if ($this->conf['special.']['items.']['prevnextToSection'])	{
								if (!is_array($recArr['prev']) && is_array($recArr['prevsection_last']))	{
									$recArr['prev']=$recArr['prevsection_last'];
								}
								if (!is_array($recArr['next']) && is_array($recArr['nextsection']))	{
									$recArr['next']=$recArr['nextsection'];
								}
							}

							$items = explode('|',$this->conf['special.']['items']);
							$c=0;
							while(list($k_b,$v_b)=each($items))	{
								$v_b=strtolower(trim($v_b));
								if (intval($this->conf['special.'][$v_b.'.']['uid']))	{
									$recArr[$v_b] = $this->sys_page->getPage(intval($this->conf['special.'][$v_b.'.']['uid']));	// fetches the page in case of a hardcoded pid in template
								}
								if (is_array($recArr[$v_b]))	{
									$temp[$c]=$recArr[$v_b];
									if ($this->conf['special.'][$v_b.'.']['target'])	{
										$temp[$c]['target']=$this->conf['special.'][$v_b.'.']['target'];
									}
									if (is_array($this->conf['special.'][$v_b.'.']['fields.']))	{
										reset($this->conf['special.'][$v_b.'.']['fields.']);
										while(list($fk,$val)=each($this->conf['special.'][$v_b.'.']['fields.']))	{
											$temp[$c][$fk]=$val;
										}
									}
									$c++;
								}
							}
						}
					break;
				}
			} elseif (is_array($this->alternativeMenuTempArray))	{	// Setting $temp array if not level 1.
				$temp = $this->alternativeMenuTempArray;
			} elseif ($this->mconf['sectionIndex']) {
				if ($GLOBALS['TSFE']->sys_language_uid && count($this->sys_page->getPageOverlay($this->id)))	{
					$sys_language_uid = intval($GLOBALS['TSFE']->sys_language_uid);
				} else $sys_language_uid=0;

				$selectSetup = Array(
					'pidInList'=>$this->id,
					'orderBy'=>$altSortField,
					'where' => 'colPos=0 AND sys_language_uid='.$sys_language_uid,
					'andWhere' => 'sectionIndex!=0'
					);
				switch($this->mconf['sectionIndex.']['type'])	{
					case 'all':
						unset($selectSetup['andWhere']);
					break;
					case 'header':
						$selectSetup['andWhere']='header_layout!=100 AND header!=""';
					break;
				}
				$basePageRow=$this->sys_page->getPage($this->id);
				if (is_array($basePageRow))	{
					$res = $GLOBALS['TSFE']->cObj->exec_getQuery('tt_content',	$selectSetup);
					while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{
						$GLOBALS['TSFE']->sys_page->versionOL('tt_content',$row);

						if (is_array($row))	{
							$temp[$row['uid']] = $basePageRow;
							$temp[$row['uid']]['title'] = $row['header'];
							$temp[$row['uid']]['nav_title'] = $row['header'];
							$temp[$row['uid']]['subtitle'] = $row['subheader'];
							$temp[$row['uid']]['starttime'] = $row['starttime'];
							$temp[$row['uid']]['endtime'] = $row['endtime'];
							$temp[$row['uid']]['fe_group'] = $row['fe_group'];
							$temp[$row['uid']]['media'] = $row['media'];

							$temp[$row['uid']]['header_layout'] = $row['header_layout'];
							$temp[$row['uid']]['bodytext'] = $row['bodytext'];
							$temp[$row['uid']]['image'] = $row['image'];

							$temp[$row['uid']]['sectionIndex_uid'] = $row['uid'];
						}
					}
				}
			} else {	// Default:
				$temp = $this->sys_page->getMenu($this->id,'*',$altSortField);		// gets the menu
			}

			$c=0;
			$c_b=0;
			$minItems = intval($this->mconf['minItems'] ? $this->mconf['minItems'] : $this->conf['minItems']);
			$maxItems = intval($this->mconf['maxItems'] ? $this->mconf['maxItems'] : $this->conf['maxItems']);
			$begin = tslib_cObj::calc($this->mconf['begin'] ? $this->mconf['begin'] : $this->conf['begin']);

			$banUidArray = $this->getBannedUids();

				// Fill in the menuArr with elements that should go into the menu:
			$this->menuArr = Array();
			foreach($temp as $data)	{
				$spacer = (t3lib_div::inList($this->spacerIDList,$data['doktype']) || !strcmp($data['ITEM_STATE'],'SPC')) ? 1 : 0;		// if item is a spacer, $spacer is set
				if ($this->filterMenuPages($data, $banUidArray, $spacer))	{
					$c_b++;
					if ($begin<=$c_b)	{		// If the beginning item has been reached.
						$this->menuArr[$c] = $data;
						$this->menuArr[$c]['isSpacer'] = $spacer;
						$c++;
						if ($maxItems && $c>=$maxItems)	{
							break;
						}
					}
				}
			}

				// Fill in fake items, if min-items is set.
			if ($minItems)	{
				while($c<$minItems)	{
					$this->menuArr[$c] = Array(
						'title' => '...',
						'uid' => $GLOBALS['TSFE']->id
					);
					$c++;
				}
			}
				// Setting number of menu items
			$GLOBALS['TSFE']->register['count_menuItems'] = count($this->menuArr);
				//	Passing the menuArr through a user defined function:
			if ($this->mconf['itemArrayProcFunc'])	{
				if (!is_array($this->parentMenuArr)) {$this->parentMenuArr=array();}
				$this->menuArr = $this->userProcess('itemArrayProcFunc',$this->menuArr);
			}
			$this->hash = md5(serialize($this->menuArr).serialize($this->mconf).serialize($this->tmpl->rootLine).serialize($this->MP_array));

				// Get the cache timeout:
			if ($this->conf['cache_period']) {
				$cacheTimeout = $this->conf['cache_period'];
			} else {
				$cacheTimeout = $GLOBALS['TSFE']->get_cache_timeout();
			}

			$serData = $this->sys_page->getHash($this->hash);
			if (!$serData)	{
				$this->generate();
				$this->sys_page->storeHash($this->hash, serialize($this->result), 'MENUDATA', $cacheTimeout);
			} else {
				$this->result = unserialize($serData);
			}

				// End showAccessRestrictedPages
			if ($this->mconf['showAccessRestrictedPages'])	{
					// RESTORING where_groupAccess
				$this->sys_page->where_groupAccess = $SAVED_where_groupAccess;
			}
		}
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/metaext/lib/class.ux_tslib_menu.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/metaext/lib/class.ux_tslib_menu.php']);
}

?>
