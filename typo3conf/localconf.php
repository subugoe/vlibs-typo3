<?php
$TYPO3_CONF_VARS['SYS']['sitename'] = 'New TYPO3 site';

	// Default password is "joh316" :
$TYPO3_CONF_VARS['BE']['installToolPassword'] = 'bacb98acf97e0b6112b1d1b650b84971';

$TYPO3_CONF_VARS['EXT']['extList'] = 'version,tsconfig_help,context_help,extra_page_cm_options,impexp,sys_note,tstemplate,tstemplate_ceditor,tstemplate_info,tstemplate_objbrowser,tstemplate_analyzer,func_wizards,wizard_crpages,wizard_sortpages,lowlevel,install,belog,beuser,aboutmodules,setup,taskcenter,info_pagetsconfig,viewpage,rtehtmlarea,css_styled_content,t3skin,t3editor,reports';

$typo_db_extTableDef_script = 'extTables.php';

## INSTALL SCRIPT EDIT POINT TOKEN - all lines after this points may be changed by the install script!

$TYPO3_CONF_VARS['EXT']['extList'] = 'extbase,css_styled_content,version,tsconfig_help,context_help,extra_page_cm_options,impexp,sys_note,tstemplate,tstemplate_ceditor,tstemplate_info,tstemplate_objbrowser,tstemplate_analyzer,func_wizards,wizard_crpages,wizard_sortpages,lowlevel,install,belog,beuser,aboutmodules,setup,taskcenter,info_pagetsconfig,viewpage,rtehtmlarea,t3skin,t3editor,reports,realurl,cag_linkchecker,static_info_tables,cshmanual,recycler,rlmp_language_detection,fluid,blog_example,kickstarter,sqlbuddyadmin,extbase_kickstarter,pazpar2test,pazpar2';	// Modified or inserted by TYPO3 Extension Manager. Modified or inserted by TYPO3 Core Update Manager. 
// Updated by TYPO3 Core Update Manager 08-09-10 12:29:22
$TYPO3_CONF_VARS['SYS']['encryptionKey'] = 'eff43017a2a82314f23139d9daf0e129a20af589316ec80bd0084f268b67125e5a14a2b2a9e0527657b4059ebd41f44e';	// Modified or inserted by TYPO3 Install Tool. 
$TYPO3_CONF_VARS['SYS']['compat_version'] = '4.5';	// Modified or inserted by TYPO3 Install Tool. 
$typo_db_username = 'root';	//  Modified or inserted by TYPO3 Install Tool.
$typo_db_password = 'DB4Typo3';	//  Modified or inserted by TYPO3 Install Tool.
$typo_db_host = 'localhost';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['EXTCONF']['dbal']['handlerCfg'] = array('_DEFAULT' => array('type' => 'adodb','config' => array('driver' => '',)));;	// Modified or inserted by TYPO3 Install Tool. 
// Updated by TYPO3 Install Tool 08-09-10 12:44:18
// Updated by TYPO3 Core Update Manager 08-09-10 12:54:50
$typo_db = 'typo3';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['BE']['installToolPassword'] = '7c68019aff42ab5929c42aea9a441bcd';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['BE']['disable_exec_function'] = '0';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['gdlib_png'] = '1';	// Modified or inserted by TYPO3 Install Tool. 
$TYPO3_CONF_VARS['GFX']['im'] = '0';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['im_combine_filename'] = '';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['im_path'] = '';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['im_path_lzw'] = '';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['SYS']['sitename'] = 'VLib-Test';	//  Modified or inserted by TYPO3 Install Tool.
// Updated by TYPO3 Install Tool 08-09-10 13:03:28
$TYPO3_CONF_VARS['EXT']['extList_FE'] = 'extbase,css_styled_content,version,install,rtehtmlarea,t3skin,realurl,cag_linkchecker,static_info_tables,rlmp_language_detection,fluid,blog_example,kickstarter,sqlbuddyadmin,extbase_kickstarter,pazpar2test,pazpar2';	// Modified or inserted by TYPO3 Extension Manager. 
// Updated by TYPO3 Extension Manager 22-09-10 13:52:06

$TYPO3_CONF_VARS['FE']['addRootLineFields'].= ',tx_realurl_pathsegment';

$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT'] = array(
	'init' => array (
		'appendMissingSlash' => 'ifNotFile',
		'postVarSet_failureMode' => 'redirect_goodUpperDir',
		'emptyUrlReturnValue' => TRUE,
		'respectSimulateStaticURLs' => 1,
		'enableCHashCache' => 1,
		'enableUrlDecodeCache' => 1,
		'enableUrlEncodeCache' => 1,
	),
	'redirects' => array (),
	'preVars' => array (
		array (
			'GETvar' => 'L',
			'valueMap' => array (
				'en' => 2,
			),
			'noMatch' => 'bypass'
		),
		array (
			'GETvar' => 'no_cache',
			'valueMap' => array (
				'no_cache' => 1,
			),
			'noMatch' => 'bypass'
		),
	),
	'pagePath' => array(
		'type' => 'user',
		'userFunc' => 'EXT:realurl/class.tx_realurl_advanced.php:&tx_realurl_advanced->main',
		'spaceCharacter' => '-',
		'segTitleFieldList' => 'tx_realurl_pathsegment,nav_title,title',
		'languageGetVar' => 'L',
		'expireDays' => 3,
		'rootpage_id' => '12',
	),
/*	'fileName' => array (
		'index' => array (
			'index.html' => array (
				'keyValues' => array (
					'type' => 1,
				),
			),
			'atom.xml' => array (
				'keyValues' => array (
					'type' => 35,
				),
			),
		),
		'_DEFAULT' => array(
			'keyValues' => array( )
		),
	),
*/	
	'postVarSets' => array(
		'_DEFAULT' => array (
			'artikel' => array(
				array(
					'GETvar' => 'tx_ttnews[tt_news]',
				),
				array(
					'GETvar' => 'tx_ttnews[backPid]',
				),
			),
			'kategorie' => array(
				array(
					'GETvar' => 'tx_ttnews[cat]',
				),
			),
			'eintrag' => array(
				array(
					'GETvar' => 'tx_ttnews[pointer]',
				),
			),
		),
	),

);


$TYPO3_CONF_VARS['SYS']['setDBinit'] = 'SET NAMES UTF-8;\' . LF . \'SET CHARACTER SET UTF-8;';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['SYS']['UTF8filesystem'] = '1';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['BE']['forceCharset'] = 'utf-8';	//  Modified or inserted by TYPO3 Install Tool.
// Updated by TYPO3 Install Tool 22-09-10 17:19:39
$TYPO3_CONF_VARS['EXT']['extConf']['cag_linkchecker'] = 'a:1:{s:15:"setPageTSconfig";s:1:"1";}';	//  Modified or inserted by TYPO3 Extension Manager.
// Updated by TYPO3 Extension Manager 27-09-10 12:20:42
$TYPO3_CONF_VARS['EXT']['extConf']['metaext'] = 'a:28:{s:14:"extconsteditor";s:1:"1";s:9:"patch6637";s:1:"0";s:23:"disableNoCacheParameter";s:1:"0";s:21:"hideUntranslatedPages";s:1:"1";s:11:"sitemaplist";s:16:"sitemap.xml:2755";s:10:"hideauthor";s:1:"1";s:9:"hideemail";s:1:"0";s:12:"hideabstract";s:1:"1";s:12:"hidekeywords";s:1:"0";s:15:"hidedescription";s:1:"0";s:12:"hidealttitle";s:1:"0";s:16:"hidegeotagfields";s:1:"1";s:13:"hidecopyright";s:1:"1";s:13:"hidepublisher";s:1:"1";s:10:"hiderobots";s:1:"1";s:14:"hideimportance";s:1:"1";s:14:"postprocessing";s:1:"1";s:14:"processunicode";s:1:"1";s:18:"removehtmlcomments";s:1:"0";s:16:"copyrightcomment";s:1:"0";s:22:"removetagsinsidescript";s:1:"1";s:16:"removeblanklines";s:1:"1";s:16:"removewhitespace";s:1:"1";s:14:"sortheadertags";s:1:"1";s:11:"indentation";s:1:"1";s:17:"indentcurlybraces";s:1:"1";s:15:"indentationchar";s:1:"0";s:19:"additionalpagetypes";s:0:"";}';	//  Modified or inserted by TYPO3 Extension Manager.
// Updated by TYPO3 Extension Manager 23-11-10 14:23:11
// Updated by TYPO3 Install Tool 23-11-10 14:57:42
$TYPO3_CONF_VARS['EXT']['extConf']['realurl'] = 'a:5:{s:10:"configFile";s:26:"typo3conf/realurl_conf.php";s:14:"enableAutoConf";s:1:"1";s:14:"autoConfFormat";s:1:"0";s:12:"enableDevLog";s:1:"0";s:19:"enableChashUrlDebug";s:1:"0";}';	//  Modified or inserted by TYPO3 Extension Manager.
// Updated by TYPO3 Extension Manager 23-11-10 17:35:27
?>