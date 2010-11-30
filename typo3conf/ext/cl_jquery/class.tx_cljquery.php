<?php
/***************************************************************
 * Copyright notice
 *
 * (c) 2010 Sven Wappler (typo3(at)wapplersystems(dot)de)
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license
 * from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/


require_once(dirname(__FILE__).'/cljquerylib.class.php');



/**
 * jQuery Javascript Loader functions
 *
 *
 * @author Sven Wappler
 * @package TYPO3
 * @subpackage cl_jquery
 */
class tx_cljquery
{
  /**
   * @var object
   */
  var $cObj;

  var $cljquerylib = null;

  public static $extKey = 'cl_jquery';

  public static $custom_files = array();
  public static $custom_jsdata = array();


  /**
   * Hook function for adding script
   *
   * @param	array	Params for hook
   * @return	void
   */
  function getJavaScriptAssets($params) {
    

    $absoluteRootLine = $GLOBALS['TSFE']->tmpl->absoluteRootLine;


    $include_extjs = array();
    $jquery_version;
    $jquery_ui_version;
    $jquery_ui_modules;
    $jquery_noconflict;
    $js_debug = 0;


    reset($absoluteRootLine);
    $c = count($absoluteRootLine);
    for ($a=0;$a<$c;$a++) {

      $res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'sys_template', 'pid='.intval($absoluteRootLine[$a]['uid']).$addC.' '.$GLOBALS['TSFE']->tmpl->whereClause,'','sorting',1);
      if ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
        if (is_array($row)) {
          //print_r($row['jquery_ui_modules']);
          //print_r($row['jquery_version']);
          if (strlen($row['include_extjs']) > 0) $include_extjs = explode(",",$row['include_extjs']);
          if (strlen($row['jquery_version']) > 0) $jquery_version = $row['jquery_version'];
          if (strlen($row['jquery_ui_version']) > 0) $jquery_ui_version = $row['jquery_ui_version'];
          if (strlen($row['jquery_noconflict']) > 0) $jquery_noconflict = $row['jquery_noconflict'];
          if (strlen($row['jquery_ui_modules']) > 0) $jquery_ui_modules = explode(",",$row['jquery_ui_modules']);

        }
      }
      $GLOBALS['TYPO3_DB']->sql_free_result($res);
    }
    
    $pagerender = $GLOBALS['TSFE']->getPageRenderer();

    if ($jquery_version) {
      $pagerender->addJsLibrary('jquery',t3lib_extMgm::siteRelPath(self::$extKey)."js/jquery-".$jquery_version.".js",'text/javascript', true, true);
    }
    // ui
    if ($jquery_ui_version) {
      foreach ($jquery_ui_modules as $module) {
        $pagerender->addJsLibrary('jqueryui-'.$module,t3lib_extMgm::siteRelPath(self::$extKey)."js/ui/".$jquery_ui_version."/jquery.".$module.".js",'text/javascript', true);
      }
    }
    // extensions
    foreach ($include_extjs as $extjs) {
      $pagerender->addJsFile(substr(t3lib_extMgm::siteRelPath(self::$extKey),0,-strlen(self::$extKey)-1)."".$extjs,'text/javascript', true);
    }
    // other scripts
    foreach (self::$custom_files as $script) {
      $pagerender->addJsFile($script,'text/javascript', true);
    }
    
    
    /**
     * jquery.includeJS {
     *   file1 = /fileadmin/file1.js
     * }
     */
    if (isset($GLOBALS['TSFE']->tmpl->setup['jquery.']['includeJS.'])) {
      foreach ($GLOBALS['TSFE']->tmpl->setup['jquery.']['includeJS.'] as $name => $file) {
        $pagerender->addJsFile($file,'text/javascript', true);
      }
    }
    
    
    if ($jquery_noconflict) {
      $pagerender->addJsInlineCode('jquerynoconflict','jQuery.noConflict();',true,true);
    }


  }


}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cl_jquery/class.tx_cljquery.php'])	{
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cl_jquery/class.tx_cljquery.php']);
}

