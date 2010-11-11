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
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 * Hint: use extdeveval to insert/update function index above.
 */


require_once(PATH_t3lib.'class.t3lib_extobjbase.php');
require_once(PATH_t3lib.'class.t3lib_pagetree.php');
require_once(PATH_t3lib.'class.t3lib_recordlist.php');
require_once(PATH_typo3.'class.db_list.inc');
require_once(PATH_t3lib.'class.t3lib_extobjbase.php');

/**
 * Module extension (addition to function menu) 'Metatags Manager' for the 'metaext' extension.
 *
 * @author	sensomedia.de <info@sensomedia.de>
 * @package	TYPO3
 * @subpackage	tx_metaext
 */
class tx_metaext_modfunc1 extends t3lib_extobjbase  {

	/**
	 * Returns the module menu
	 *
	 * @return	Array with menuitems
	 */
	function modMenu () {
        global $LANG;
        $menuArray = array(
 			'pages' => array (
				0 => $LANG->getLL('pages_0'),
				1 => $LANG->getLL('pages_1')
			),
	       	'depth' => array( 
        		0 => $LANG->getLL('depth_0'),
        		1 => $LANG->getLL('depth_1'),
        		2 => $LANG->getLL('depth_2'),
        		3 => $LANG->getLL('depth_3'),
        		99 => $LANG->getLL('depth_I')
        	)
        );
		return $menuArray;
  	}


	/**
	 * MAIN function for page information display 
	 *
	 * @return	string		Output HTML for the module.
	 */
	function main()	{
		global $BACK_PATH,$LANG,$SOBE;


		$this->pObj->MOD_SETTINGS['pages_levels']=$this->pObj->MOD_SETTINGS['depth'];		// ONLY for the sake of dblist module which uses this value.

		$h_func = t3lib_BEfunc::getFuncMenu($this->pObj->id,'SET[depth]',$this->pObj->MOD_SETTINGS['depth'],$this->pObj->MOD_MENU['depth'],'index.php');
		$h_func.= t3lib_BEfunc::getFuncMenu($this->pObj->id,'SET[pages]',$this->pObj->MOD_SETTINGS['pages'],$this->pObj->MOD_MENU['pages'],'index.php');


		$theOutput.=$this->pObj->doc->section($LANG->getLL('page_title'),
			t3lib_BEfunc::cshItem($dblist->descrTable,'pagetree_overview',$GLOBALS['BACK_PATH'],'|<br/>').	// CSH
				$h_func.
				"There's no functional content right this moment... this is work in progress",
			0,
			1
		);

		// PAGE INFORMATION
		if ($this->pObj->pageinfo['uid'])	{
			$theOutput.=$this->pObj->doc->spacer(10);
			$theOutput.=$this->pObj->doc->section($LANG->getLL('pageInformation'),$dblist->getPageInfoBox($this->pObj->pageinfo,$this->pObj->CALC_PERMS&2),0,1);
		}
		
		return $theOutput;
	}



}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/metaext/modfunc1/class.tx_metaext_modfunc1.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/metaext/modfunc1/class.tx_metaext_modfunc1.php']);
}

?>