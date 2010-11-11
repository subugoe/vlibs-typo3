<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Bas van Beek <bvbmedia@gmail.com>
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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */


$LANG->includeLLFile('EXT:metatags_manager/mod1/locallang.xml');
require_once(PATH_t3lib . 'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]



/**
 * Module 'Meta Tags Manager' for the 'metatags_manager' extension.
 *
 * @author	Bas van Beek <bvbmedia@gmail.com>
 * @package	TYPO3
 * @subpackage	tx_metatagsmanager
 */
class  tx_metatagsmanager_module1 extends t3lib_SCbase {
				var $pageinfo;

				/**
				 * Initializes the Module
				 * @return	void
				 */
				function init()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;
					parent::init();
				}

				/**
				 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
				 *
				 * @return	void
				 */
				 
				function menuConfig()	{
					global $LANG;
					$this->MOD_MENU = Array (
						'function' => ''
					);
					parent::menuConfig();
				}

				/**
				 * Main function of the module. Write the content to $this->content
				 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
				 *
				 * @return	[type]		...
				 */
				function main()	{
					global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

					// Access check!
					// The page will show only if there is a valid page and if this page may be viewed by the user
					$this->pageinfo = t3lib_BEfunc::readPageAccess($this->id,$this->perms_clause);
					$access = is_array($this->pageinfo) ? 1 : 0;
				
					if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

							// Draw the header.
						$this->doc = t3lib_div::makeInstance('noDoc');
						$this->doc->backPath = $BACK_PATH;
						$this->doc->form='<form action="" method="post" enctype="multipart/form-data">';

							// JavaScript
						$this->doc->JScode = '
							<style type="text/css"> 
							div.typo3-noDoc
							{
								width:100%;
							}
							input, textarea
							{
								width:100%;
							}
							body#typo3-mod-php { 
									  padding: 0; 
									  margin: 0; 
									  overflow: auto; 
									  height: 100%; 
							} 	
							.header td
							{
								color:white;
							}
							.shadow_bottom {
							overflow:hidden;
							width:100%;
							background:url('.TYPO3_MOD_PATH.'images/shadow_bottom.png) left bottom no-repeat;
							padding:0 0 35px;
							}
							fieldset {
							display:block;
							border:1px solid #999;
							background:#fff;
							margin:10px 0 0;
							padding:10px 10px 10px;
							}
							fieldset legend {
							background:#585858;
							color:#fff;
							font-family:Verdana,Arial,Helvetica,sans-serif;
							font-size:11px;
							padding:2px 4px;
							}
							fieldset legend a
							{
								color:#fff;
							}
							fieldset legend a:hover
							{
								color:#fff;
								text-decoration:underline;
							}							 
							ul {
							list-style:none;
							margin:0;
							padding:0;
							}							
							.header
							{
								font-size:12px;
								font-weight:bold;
								background-color:#000;
								color:white;
							}
							.even
							{
								background-color:#E1E1E1;
							}
							.odd
							{
								background-color:#F2F2F2;
							}
							.even_overlay
							{
								background-color:#EAE6FB;
							}
							.odd_overlay
							{
								background-color:#D2CFF5;
							}
							
							.c-table
							{
								color:black;
								font-weight:bold;
							}
							.table_border
							{
									border: 0px solid #CECECE;	
							}	
							.hidden
							{
								background-color:#FFD7D7;
							}							
							</style> 													
						';
						$this->doc->postCode='
							<script language="javascript" type="text/javascript">
								script_ended = 1;
								if (top.fsMod) top.fsMod.recentIds["web"] = 0;
							</script>
						';

						$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.t3lib_div::fixed_lgd_pre($this->pageinfo['_thePath'],50);

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
					$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));



						// Render content:
						$this->moduleContent();


						// ShortCut
						if ($BE_USER->mayMakeShortcut())	{
							$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
						}

					} else {
							// If no access or if ID == zero

						$this->doc = t3lib_div::makeInstance('noDoc');
						$this->doc->backPath = $BACK_PATH;

						$this->content.=$this->doc->startPage($LANG->getLL('title'));
						$this->content.=$this->doc->header($LANG->getLL('title'));
						$this->content.=$this->doc->spacer(5);
						$this->content.=$this->doc->spacer(10);
					}
				
				}

				/**
				 * Prints out the module HTML
				 *
				 * @return	void
				 */
				function printContent()	{

					$this->content.=$this->doc->endPage();
					echo $this->content;
				}

				/**
				 * Generates the module content
				 *
				 * @return	void
				 */
				function moduleContent()	{
					$this->codeGet = t3lib_div::_GET();
					$this->codePost = t3lib_div::_POST();	
					$this->languages = t3lib_beFunc::getSystemLanguages();
					switch((string)$this->MOD_SETTINGS['function'])	{
						case 1:				
						default:
						if ($this->codePost)
						{
							$total_adjusted_records=0;
							foreach ($this->codePost['tx_metatags_manager']['title'] as $key => $value)
							{
								$array=array();
								$array['title']			=	$value;
								$array['description']	=	$this->codePost['tx_metatags_manager']['description'][$key];
								$array['keywords']		=	$this->codePost['tx_metatags_manager']['keywords'][$key];
								$query = $GLOBALS['TYPO3_DB']->UPDATEquery('pages',  'uid="'.$key.'"', $array);
								$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
								$adjusted_records=mysql_affected_rows();
								if ($adjusted_records) $total_adjusted_records=$total_adjusted_records+$adjusted_records;																
							}
							// now saving the language overlay uids
							$total_adjusted_overlay_records=0;
							foreach ($this->codePost['tx_metatags_manager']['language_overlay']['title'] as $key => $value)
							{
								$array=array();
								$array['title']			=	$value;
								$array['description']	=	$this->codePost['tx_metatags_manager']['language_overlay']['description'][$key];
								$array['keywords']		=	$this->codePost['tx_metatags_manager']['language_overlay']['keywords'][$key];
								$query = $GLOBALS['TYPO3_DB']->UPDATEquery('pages_language_overlay',  'uid="'.$key.'"', $array);
								$res = $GLOBALS['TYPO3_DB']->sql(TYPO3_db, $query);
								$adjusted_records=mysql_affected_rows();
								if ($adjusted_records) $total_adjusted_overlay_records=$total_adjusted_overlay_records+$adjusted_records;																
							}							
							// now saving the language overlay uids eof							
							$content.=$total_adjusted_records.' '.(($total_adjusted_records==1)?'page':'pages').' has been adjusted.<BR>';
							$content.=$total_adjusted_overlay_records.' language overlay '.(($total_adjusted_overlay_records==1)?'page':'pages').' has been adjusted.<BR>';
							$content.='<h2>Saving completed.</h2>';
						}						
						if (is_numeric($this->codeGet['id'])) 	$rootpid=$this->codeGet['id'];
						else									$rootpid=0;
						$pids = $this->getPageTree($rootpid,'','',1);
						$save_row='<input name="Submit" type="submit" style="font-weight:bold;width:100px;text-align:center;" value="Save" />';		
						$type='';
						if (count($pids) > 1)
						{
							$content.='<form action="" id="metatags_manager" name="metatags_manager" method="post" enctype="multipart/form-data">
							<table border="0" cellpadding="2" cellspacing="2" class="table_border" width="100%">
							<tr class="header"><td>Page</td><td>Meta Title</td><td>Meta Keywords</td><td><div style="float:right">'.$save_row.'</div>Meta Description</td></tr>';							
							foreach ($pids as $key => $value)
							{
								if ($type=='odd') 	$type='even';
								else						$type='odd';
								$content.='<tr class="c-table '.$type.' '.($value[1]['hidden']==1?'hidden':'').'">';
								$content.='<td nowrap><a href="#" onclick="'.htmlspecialchars(t3lib_BEfunc::viewOnClick($value[1]['uid'],$this->backPath,t3lib_BEfunc::BEgetRootLine($value[1]['uid']),'','')).'">'.$value[0].'</a>'.($value[1]['hidden']==1?' <i>(hidden)</i>':'').'</td>';
								$content.='<td nowrap><input name="tx_metatags_manager[title]['.$key.']" type="text" size="25" value="'.htmlspecialchars($value[1]['title']).'"></td>';
								$content.='<td nowrap><input name="tx_metatags_manager[keywords]['.$key.']" type="text" size="25" value="'.htmlspecialchars($value[1]['keywords']).'"></td>';
								$content.='<td><textarea cols="45" rows="2" name="tx_metatags_manager[description]['.$key.']">'.htmlspecialchars($value[1]['description']).'</textarea></td>';
								$content.='</tr>'."\n";
								// now fetch the pages_language_overlay
								$pids_overlay = $this->getPageTreeOverlay($key);
								if (count($pids_overlay) >0)
								{
									foreach ($pids_overlay as $overlay_key => $overlay_value)
									{
										if ($type_overlay=='odd_overlay') 	$type_overlay='odd_even';
										else											$type_overlay='odd_overlay';
										$content.='<tr class="c-table '.$type.' '.($value[1]['hidden']==1?'hidden':'').'">';
										if ($this->languages[$overlay_value['sys_language_uid']][2]) 	$lang_icon='<img src="'.$this->doc->backPath.'gfx/'.$this->languages[$overlay_value['sys_language_uid']][2].'" title="'.htmlspecialchars($this->languages[$overlay_value['sys_language_uid']][0]).'" alt="'.htmlspecialchars($this->languages[$overlay_value['sys_language_uid']][0]).'">';
										else																				$lang_icon=$this->languages[$overlay_value['sys_language_uid']][0];
										$content.='<td nowrap>language overlay '.$lang_icon.'</td>';
										$content.='<td nowrap><input name="tx_metatags_manager[language_overlay][title]['.$overlay_key.']" type="text" size="25" value="'.htmlspecialchars($overlay_value['title']).'"></td>';
										$content.='<td nowrap><input name="tx_metatags_manager[language_overlay][keywords]['.$overlay_key.']" type="text" size="25" value="'.htmlspecialchars($overlay_value['keywords']).'"></td>';
										$content.='<td nowrap><textarea cols="45" rows="2" name="tx_metatags_manager[language_overlay][description]['.$overlay_key.']">'.htmlspecialchars($overlay_value['description']).'</textarea></td>';
										$content.='</tr>'."\n";									
									}
								}
								// now fetch the pages_language_overlay eof
							}	
							$content.='<tr><td colspan="4" align="right">'.$save_row.'</td></tr>
							</table>
							</form>';
						}
						else
						{
							$content.='<h2>No pages here.</h2>';
						}
						$content.='
<fieldset>						
<center><strong>
						<a title="created by BVB Media Ltd" href="http://www.bvbmedia.com/?utm_source=metatags_manager_backend&utm_medium=cpc&utm_term=metatags_manager&utm_content=Listing&utm_campaign=metatags_manager" target="_blank">created by BVB Media Ltd</a><br>
						webdevelopment by <a href="http://www.basvanbeek.nl/?utm_source=metatags_manager_backend&utm_medium=cpc&utm_term=metatags_manager&utm_content=Listing&utm_campaign=metatags_manager" target="_blank">Bas van Beek</a> - <a href="mailto:bvbmedia@gmail.com">bvbmedia@gmail.com</a><br>
</strong></center>						
</fieldset>					
						';
						$this->content.=$content;
						break;
					}
				}
				protected function getPageTree($pid = '0',$cates=array(),$times=0,$include_itself=0){
					if ($include_itself)	
					{
						$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title,keywords,description,hidden', 'pages', 'doktype <> 254 and deleted = 0 and (uid='.$pid.')', '');
						while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{						
								if ($row['uid'])
								{
									$cates[$row['uid']] = array('|'.str_repeat("--",$times-1)."-[ ".$row['title'],$row);
									$cates=$this->getPageTree($row['uid'],$cates,$times);
								}
						}						
						$times++;						
					}
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title,keywords,description,hidden', 'pages', 'doktype <> 254 and deleted = 0 and pid='.$pid.'', '');
					$times++;
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))	{	
						if ($row['uid'])
						{
							$cates[$row['uid']] = array('|'.str_repeat("--",$times-1)."-[ ".$row['title'],$row);
							$cates=$this->getPageTree($row['uid'],$cates,$times);
						}
					}
					$times--;
					return $cates;
				}	
				protected function getPageTreeOverlay($uid){
					$cates=array();
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title,keywords,description,sys_language_uid,hidden', 'pages_language_overlay', 'deleted = 0 and pid='.$uid.'', '');
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res))
					{					
						if ($row['uid'])	$cates[$row['uid']] = $row;
					}
					return $cates;
				}					
		}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/metatags_manager/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/metatags_manager/mod1/index.php']);
}
// Make instance:
$SOBE = t3lib_div::makeInstance('tx_metatagsmanager_module1');
$SOBE->init();
// Include files?
foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);
$SOBE->main();
$SOBE->printContent();
?>