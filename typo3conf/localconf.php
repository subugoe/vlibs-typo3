<?php
$typo_db_extTableDef_script = 'extTables.php';

## INSTALL SCRIPT EDIT POINT TOKEN - all lines after this points may be changed by the install script!
$TYPO3_CONF_VARS['SYS']['displayErrors'] = 2;
$TYPO3_CONF_VARS['SYS']['devIPmask'] = '127.0.0.1,::1,134.76.162.165';

## INSTALL SCRIPT EDIT POINT TOKEN - all lines after this points may be changed by the install script!

// Updated by TYPO3 Core Update Manager 08-09-10 12:29:22
$TYPO3_CONF_VARS['SYS']['encryptionKey'] = 'eff43017a2a82314f23139d9daf0e129a20af589316ec80bd0084f268b67125e5a14a2b2a9e0527657b4059ebd41f44e';	// Modified or inserted by TYPO3 Install Tool. 
$typo_db_username = 'root';	//  Modified or inserted by TYPO3 Install Tool.
$typo_db_password = '';	//  Modified or inserted by TYPO3 Install Tool.
$typo_db_host = 'localhost';	//  Modified or inserted by TYPO3 Install Tool.
$typo_db = 'typo3';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['BE']['installToolPassword'] = '7c68019aff42ab5929c42aea9a441bcd';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['BE']['disable_exec_function'] = '0';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['gdlib_png'] = '1';	// Modified or inserted by TYPO3 Install Tool. 
$TYPO3_CONF_VARS['GFX']['im'] = '0';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['im_combine_filename'] = '';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['im_path'] = '';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['im_path_lzw'] = '';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['SYS']['sitename'] = 'VLib-Test';	//  Modified or inserted by TYPO3 Install Tool.

$TYPO3_CONF_VARS['FE']['addRootLineFields'].= ',tx_realurl_pathsegment';
$TYPO3_CONF_VARS['FE']['pageNotFound_handling'] = '404/';	// Modified or inserted by TYPO3 Install Tool.

$TYPO3_CONF_VARS['EXT']['extList'] = 'extbase,css_styled_content,version,tsconfig_help,context_help,extra_page_cm_options,impexp,sys_note,tstemplate,tstemplate_ceditor,tstemplate_info,tstemplate_objbrowser,tstemplate_analyzer,func_wizards,wizard_crpages,wizard_sortpages,lowlevel,install,belog,beuser,aboutmodules,setup,taskcenter,info_pagetsconfig,viewpage,rtehtmlarea,t3skin,reports,realurl,static_info_tables,cshmanual,recycler,fluid,beko_debugster,scheduler,t3editor,devlog,ezbrequest,filelist,info,perm,rlmp_language_detection,piwik,func,about,feedit,opendocs,workspaces,rsaauth,saltedpasswords,linkvalidator,xmlinclude,fed,flux,extdeveval,t3jquery,pazpar2,nkwgok';	// Modified or inserted by TYPO3 Extension Manager. Modified or inserted by TYPO3 Core Update Manager. 
$TYPO3_CONF_VARS['EXT']['extList_FE'] = 'extbase,css_styled_content,version,install,rtehtmlarea,t3skin,realurl,static_info_tables,fluid,beko_debugster,devlog,ezbrequest,rlmp_language_detection,piwik,feedit,workspaces,rsaauth,saltedpasswords,xmlinclude,fed,flux,extdeveval,t3jquery,pazpar2,nkwgok';	// Modified or inserted by TYPO3 Extension Manager.

$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT'] = array(
	'init' => array (
		'appendMissingSlash' => 'ifNotFile',
		'postVarSet_failureMode' => 'redirect_goodUpperDir',
		'emptyUrlReturnValue' => TRUE,
		'respectSimulateStaticURLs' => 1,
		'enableCHashCache' => 1,
		'enableUrlDecodeCache' => 0,
		'enableUrlEncodeCache' => 0,
	),
	//'redirects' => array (),
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
	'pagePath' => array (
		'type' => 'user',
		'userFunc' => 'EXT:realurl/class.tx_realurl_advanced.php:&tx_realurl_advanced->main',
		'spaceCharacter' => '-',
		'segTitleFieldList' => 'tx_realurl_pathsegment,nav_title,title',
		'languageGetVar' => 'L',
		'expireDays' => 3,
		'rootpage_id' => '12',
	),
	'fixedPostVars' => array (
		'xmlinclude' => array (
			array(
				'GETvar' => 'tx_xmlinclude_xmlinclude[URL]',
				'userFunc' => 'EXT:xmlinclude/Classes/RealURL/tx_xmlinclude_realurl.php:&tx_xmlinclude_realurl->main'
			)
		),
		'57' => 'xmlinclude',
		'58' => 'xmlinclude',
	),
);


$TYPO3_CONF_VARS['SYS']['setDBinit'] = 'SET NAMES utf8;';	// Modified or inserted by TYPO3 Install Tool. 
$TYPO3_CONF_VARS['SYS']['UTF8filesystem'] = '1';	//  Modified or inserted by TYPO3 Install Tool.
// Updated by TYPO3 Install Tool 22-09-10 17:19:39
$TYPO3_CONF_VARS['EXT']['extConf']['cag_linkchecker'] = 'a:1:{s:15:"setPageTSconfig";s:1:"1";}';	// Modified or inserted by TYPO3 Extension Manager. 
// Updated by TYPO3 Extension Manager 27-09-10 12:20:42
$TYPO3_CONF_VARS['EXT']['extConf']['metaext'] = 'a:28:{s:14:"extconsteditor";s:1:"1";s:9:"patch6637";s:1:"0";s:23:"disableNoCacheParameter";s:1:"0";s:21:"hideUntranslatedPages";s:1:"1";s:11:"sitemaplist";s:16:"sitemap.xml:2755";s:10:"hideauthor";s:1:"1";s:9:"hideemail";s:1:"0";s:12:"hideabstract";s:1:"1";s:12:"hidekeywords";s:1:"0";s:15:"hidedescription";s:1:"0";s:12:"hidealttitle";s:1:"0";s:16:"hidegeotagfields";s:1:"1";s:13:"hidecopyright";s:1:"1";s:13:"hidepublisher";s:1:"1";s:10:"hiderobots";s:1:"1";s:14:"hideimportance";s:1:"1";s:14:"postprocessing";s:1:"1";s:14:"processunicode";s:1:"1";s:18:"removehtmlcomments";s:1:"0";s:16:"copyrightcomment";s:1:"0";s:22:"removetagsinsidescript";s:1:"1";s:16:"removeblanklines";s:1:"1";s:16:"removewhitespace";s:1:"1";s:14:"sortheadertags";s:1:"1";s:11:"indentation";s:1:"1";s:17:"indentcurlybraces";s:1:"1";s:15:"indentationchar";s:1:"0";s:19:"additionalpagetypes";s:0:"";}';	//  Modified or inserted by TYPO3 Extension Manager.
// Updated by TYPO3 Extension Manager 23-11-10 14:23:11
// Updated by TYPO3 Install Tool 23-11-10 14:57:42
$TYPO3_CONF_VARS['EXT']['extConf']['realurl'] = 'a:5:{s:10:"configFile";s:26:"typo3conf/realurl_conf.php";s:14:"enableAutoConf";s:1:"1";s:14:"autoConfFormat";s:1:"0";s:12:"enableDevLog";s:1:"0";s:19:"enableChashUrlDebug";s:1:"0";}';	//  Modified or inserted by TYPO3 Extension Manager.
$TYPO3_CONF_VARS['EXT']['extConf']['t3jquery'] = 'a:13:{s:15:"alwaysIntegrate";s:1:"1";s:18:"dontIntegrateOnUID";s:0:"";s:9:"configDir";s:19:"uploads/tx_t3jquery";s:13:"jQueryVersion";s:5:"1.7.x";s:15:"jQueryUiVersion";s:0:"";s:18:"jQueryTOOLSVersion";s:0:"";s:17:"integrateToFooter";s:1:"0";s:23:"dontIntegrateInRootline";s:0:"";s:13:"jqLibFilename";s:23:"jquery-###VERSION###.js";s:16:"integrateFromCDN";s:1:"0";s:11:"locationCDN";s:6:"google";s:17:"enableStyleStatic";s:1:"0";s:22:"jQueryBootstrapVersion";s:0:"";}';	// Modified or inserted by TYPO3 Extension Manager. 
$TYPO3_CONF_VARS['EXT']['extConf']['beko_debugster'] = 'a:2:{s:7:"ip_mask";s:38:"134.76.*, 127.0.0.1, 10.0.*, 192.168.*";s:10:"steps_back";s:1:"3";}';	// Modified or inserted by TYPO3 Extension Manager. 
$TYPO3_CONF_VARS['EXT']['extConf']['nkwgok'] = 'a:2:{s:11:"opacBaseURL";s:39:"http://opac.sub.uni-goettingen.de/DB=1/";s:7:"CSSPath";s:0:"";}';	// Modified or inserted by TYPO3 Extension Manager.opac.sub.uni-goettingen.de/DB=1/";s:7:"CSSPath";s:0:"";}';	//
$TYPO3_CONF_VARS['EXT']['extConf']['devlog'] = 'a:11:{s:10:"maxLogRuns";s:2:"15";s:14:"entriesPerPage";s:2:"25";s:7:"maxRows";s:4:"1000";s:8:"optimize";s:1:"0";s:8:"dumpSize";s:7:"1000000";s:11:"minLogLevel";s:1:"0";s:11:"excludeKeys";s:0:"";s:14:"highlightStyle";s:60:"padding: 2px; background-color: #fc3; border: 1px solid #666";s:16:"refreshFrequency";s:1:"2";s:13:"prototypePath";s:0:"";s:11:"autoCleanup";s:1:"0";}';	// Modified or inserted by TYPO3 Extension Manager.

$TYPO3_CONF_VARS['GFX']['gdlib_png'] = '1';	// Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['im'] = '0';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['im_combine_filename'] = '';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['im_path'] = '';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['GFX']['im_path_lzw'] = '';	//  Modified or inserted by TYPO3 Install Tool.

$TYPO3_CONF_VARS['EXT']['extConf']['piwik'] = 'a:1:{s:20:"showFaultyConfigHelp";s:1:"1";}';	// Modified or inserted by TYPO3 Extension Manager. 
// Updated by TYPO3 Extension Manager 11-01-12 09:23:00
$TYPO3_CONF_VARS['INSTALL']['wizardDone']['tx_coreupdates_installsysexts'] = '1';	//  Modified or inserted by TYPO3 Upgrade Wizard.
// Updated by TYPO3 Upgrade Wizard 11-01-12 09:23:00
$TYPO3_CONF_VARS['EXT']['extConf']['em'] = 'a:1:{s:17:"selectedLanguages";s:2:"de";}';	//  Modified or inserted by TYPO3 Extension Manager.
$TYPO3_CONF_VARS['EXT']['extConf']['lang'] = 'a:0:{}';	//  Modified or inserted by TYPO3 Extension Manager.
$TYPO3_CONF_VARS['EXT']['extConf']['fed'] = 'a:8:{s:29:"enableBackendRecordController";s:1:"0";s:26:"enableFluidContentElements";s:1:"0";s:24:"enableFluidPageTemplates";s:1:"0";s:18:"enableSolrFeatures";s:1:"0";s:21:"enableFrontendPlugins";s:1:"0";s:30:"enableIntegratedBackendLayouts";s:1:"0";s:28:"increaseExtbaseCacheLifetime";s:1:"1";s:31:"enableFallbackFluidPageTemplate";s:1:"0";}';	// Modified or inserted by TYPO3 Extension Manager. 
$TYPO3_CONF_VARS['SYS']['compat_version'] = '4.7';	//  Modified or inserted by TYPO3 Install Tool.
// Updated by TYPO3 Install Tool 31-05-12 15:13:00
// Updated by TYPO3 Extension Manager 01-06-12 09:56:10
?>
