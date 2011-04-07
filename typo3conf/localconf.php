<?php
$TYPO3_CONF_VARS['SYS']['sitename'] = 'New TYPO3 site';

	// Default password is "joh316" :
$TYPO3_CONF_VARS['BE']['installToolPassword'] = 'bacb98acf97e0b6112b1d1b650b84971';

$TYPO3_CONF_VARS['EXT']['extList'] = 'version,tsconfig_help,context_help,extra_page_cm_options,impexp,sys_note,tstemplate,tstemplate_ceditor,tstemplate_info,tstemplate_objbrowser,tstemplate_analyzer,func_wizards,wizard_crpages,wizard_sortpages,lowlevel,install,belog,beuser,aboutmodules,setup,taskcenter,info_pagetsconfig,viewpage,rtehtmlarea,css_styled_content,t3skin,t3editor,reports';

$typo_db_extTableDef_script = 'extTables.php';

$TYPO3_CONF_VARS['SYS']['displayErrors'] = 2;
$TYPO3_CONF_VARS['SYS']['devIPmask'] = '127.0.0.1,::1,134.76.162.165';

## INSTALL SCRIPT EDIT POINT TOKEN - all lines after this points may be changed by the install script!

$TYPO3_CONF_VARS['EXT']['extList'] = 'extbase,css_styled_content,version,tsconfig_help,context_help,extra_page_cm_options,impexp,sys_note,tstemplate,tstemplate_ceditor,tstemplate_info,tstemplate_objbrowser,tstemplate_analyzer,func_wizards,wizard_crpages,wizard_sortpages,lowlevel,install,belog,beuser,aboutmodules,setup,taskcenter,info_pagetsconfig,viewpage,rtehtmlarea,t3skin,reports,realurl,cag_linkchecker,static_info_tables,cshmanual,recycler,fluid,extbase_kickstarter,beko_debugster,kickstarter,scheduler,t3jquery,pazpar2,pazpar2neuerwerbungen,efempty,t3editor,devlog,nkwgok';	// Modified or inserted by TYPO3 Extension Manager. Modified or inserted by TYPO3 Core Update Manager. 
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
$TYPO3_CONF_VARS['EXT']['extList_FE'] = 'extbase,css_styled_content,version,install,rtehtmlarea,t3skin,realurl,cag_linkchecker,static_info_tables,fluid,extbase_kickstarter,beko_debugster,kickstarter,t3jquery,pazpar2,pazpar2neuerwerbungen,efempty,devlog,nkwgok';	// Modified or inserted by TYPO3 Extension Manager. 
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


$TYPO3_CONF_VARS['SYS']['setDBinit'] = 'SET NAMES utf-8;\' . LF . \'SET CHARACTER SET utf-8;';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['SYS']['UTF8filesystem'] = '1';	//  Modified or inserted by TYPO3 Install Tool.
// Updated by TYPO3 Install Tool 22-09-10 17:19:39
$TYPO3_CONF_VARS['EXT']['extConf']['cag_linkchecker'] = 'a:1:{s:15:"setPageTSconfig";s:1:"1";}';	// Modified or inserted by TYPO3 Extension Manager. 
// Updated by TYPO3 Extension Manager 27-09-10 12:20:42
$TYPO3_CONF_VARS['EXT']['extConf']['metaext'] = 'a:28:{s:14:"extconsteditor";s:1:"1";s:9:"patch6637";s:1:"0";s:23:"disableNoCacheParameter";s:1:"0";s:21:"hideUntranslatedPages";s:1:"1";s:11:"sitemaplist";s:16:"sitemap.xml:2755";s:10:"hideauthor";s:1:"1";s:9:"hideemail";s:1:"0";s:12:"hideabstract";s:1:"1";s:12:"hidekeywords";s:1:"0";s:15:"hidedescription";s:1:"0";s:12:"hidealttitle";s:1:"0";s:16:"hidegeotagfields";s:1:"1";s:13:"hidecopyright";s:1:"1";s:13:"hidepublisher";s:1:"1";s:10:"hiderobots";s:1:"1";s:14:"hideimportance";s:1:"1";s:14:"postprocessing";s:1:"1";s:14:"processunicode";s:1:"1";s:18:"removehtmlcomments";s:1:"0";s:16:"copyrightcomment";s:1:"0";s:22:"removetagsinsidescript";s:1:"1";s:16:"removeblanklines";s:1:"1";s:16:"removewhitespace";s:1:"1";s:14:"sortheadertags";s:1:"1";s:11:"indentation";s:1:"1";s:17:"indentcurlybraces";s:1:"1";s:15:"indentationchar";s:1:"0";s:19:"additionalpagetypes";s:0:"";}';	//  Modified or inserted by TYPO3 Extension Manager.
// Updated by TYPO3 Extension Manager 23-11-10 14:23:11
// Updated by TYPO3 Install Tool 23-11-10 14:57:42
$TYPO3_CONF_VARS['EXT']['extConf']['realurl'] = 'a:5:{s:10:"configFile";s:26:"typo3conf/realurl_conf.php";s:14:"enableAutoConf";s:1:"1";s:14:"autoConfFormat";s:1:"0";s:12:"enableDevLog";s:1:"0";s:19:"enableChashUrlDebug";s:1:"0";}';	//  Modified or inserted by TYPO3 Extension Manager.
$TYPO3_CONF_VARS['EXT']['extConf']['t3jquery'] = 'a:6:{s:15:"alwaysIntegrate";s:1:"1";s:18:"dontIntegrateOnUID";s:0:"";s:9:"configDir";s:19:"uploads/tx_t3jquery";s:13:"jQueryVersion";s:5:"1.4.4";s:15:"jQueryUiVersion";s:5:"1.8.7";s:18:"jQueryTOOLSVersion";s:0:"";}';	// Modified or inserted by TYPO3 Extension Manager. 
$TYPO3_CONF_VARS['EXT']['extConf']['beko_debugster'] = 'a:2:{s:7:"ip_mask";s:38:"134.76.*, 127.0.0.1, 10.0.*, 192.168.*";s:10:"steps_back";s:1:"3";}';	// Modified or inserted by TYPO3 Extension Manager. 
// Updated by TYPO3 Extension Manager 01-12-10 10:21:06
$TYPO3_CONF_VARS['SYS']['enable_DLOG'] = '1';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['BE']['versionNumberInFilename'] = '0';	//  Modified or inserted by TYPO3 Install Tool.
$TYPO3_CONF_VARS['FE']['logfile_dir'] = '/var/log/typo3/';	//  Modified or inserted by TYPO3 Install Tool.
// Updated by TYPO3 Install Tool 02-12-10 10:47:45
$TYPO3_CONF_VARS['EXT']['extConf']['devlog'] = 'a:11:{s:10:"maxLogRuns";s:2:"15";s:14:"entriesPerPage";s:2:"25";s:7:"maxRows";s:4:"1000";s:8:"optimize";s:1:"0";s:8:"dumpSize";s:7:"1000000";s:11:"minLogLevel";s:1:"0";s:11:"excludeKeys";s:0:"";s:14:"highlightStyle";s:60:"padding: 2px; background-color: #fc3; border: 1px solid #666";s:16:"refreshFrequency";s:1:"2";s:13:"prototypePath";s:0:"";s:11:"autoCleanup";s:1:"0";}';	// Modified or inserted by TYPO3 Extension Manager. 
// Updated by TYPO3 Extension Manager 28-02-11 19:28:42
$TYPO3_CONF_VARS['EXT']['extConf']['nkwgok'] = 'a:6:{s:14:"defaultOpacUrl";s:193:"http://opac.sub.uni-goettingen.de/DB=1/LNG=DU/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER,http://opac.sub.uni-goettingen.de/DB=1/LNG=EN/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER";s:18:"alternativeOpacUrl";s:0:"";s:25:"alternativeOpacUrlTrigger";s:0:"";s:16:"jQueryNoConflict";s:1:"0";s:11:"opacBaseURL";s:39:"http://opac.sub.uni-goettingen.de/DB=1/";s:7:"CSSPath";s:0:"";}';	// Modified or inserted by TYPO3 Extension Manager.opac.sub.uni-goettingen.de/DB=1/LNG=DU/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER,http://opac.sub.uni-goettingen.de/DB=1/LNG=EN/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER";s:18:"alternativeOpacUrl";s:0:"";s:25:"alternativeOpacUrlTrigger";s:0:"";s:16:"jQueryNoConflict";s:1:"0";s:11:"opacBaseURL";s:39:"http://opac.sub.uni-goettingen.de/DB=1/";s:7:"CSSPath";s:0:"";}';	//opac.sub.uni-goettingen.de/DB=1/LNG=DU/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER,http://opac.sub.uni-goettingen.de/DB=1/LNG=EN/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER";s:18:"alternativeOpacUrl";s:0:"";s:25:"alternativeOpacUrlTrigger";s:0:"";s:16:"jQueryNoConflict";s:1:"0";s:11:"opacBaseURL";s:39:"http://opac.sub.uni-goettingen.de/DB=1/";s:7:"CSSPath";s:41:"http://earthlingsoft.net/ssp/blog/qlc.css";}';	//opac.sub.uni-goettingen.de/DB=1/LNG=DU/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER,http://opac.sub.uni-goettingen.de/DB=1/LNG=EN/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER";s:18:"alternativeOpacUrl";s:0:"";s:25:"alternativeOpacUrlTrigger";s:0:"";s:16:"jQueryNoConflict";s:1:"0";s:11:"opacBaseURL";s:39:"http://opac.sub.uni-goettingen.de/DB=1/";s:7:"CSSPath";s:40:"typo3conf/ext/nkwgok/res/nkwgok-tree.css";}';	//opac.sub.uni-goettingen.de/DB=1/LNG=DU/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER,http://opac.sub.uni-goettingen.de/DB=1/LNG=EN/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER";s:18:"alternativeOpacUrl";s:0:"";s:25:"alternativeOpacUrlTrigger";s:0:"";s:16:"jQueryNoConflict";s:1:"0";s:11:"opacBaseURL";s:39:"http://opac.sub.uni-goettingen.de/DB=1/";s:7:"CSSPath";s:0:"";}';	//opac.sub.uni-goettingen.de/DB=1/LNG=DU/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER,http://opac.sub.uni-goettingen.de/DB=1/LNG=EN/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER";s:18:"alternativeOpacUrl";s:0:"";s:25:"alternativeOpacUrlTrigger";s:0:"";s:16:"jQueryNoConflict";s:1:"0";s:11:"opacBaseURL";s:39:"http://opac.sub.uni-goettingen.de/DB=1/";s:7:"CSSPath";s:4:"test";}';	//opac.sub.uni-goettingen.de/DB=1/LNG=DU/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER,http://opac.sub.uni-goettingen.de/DB=1/LNG=EN/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER";s:18:"alternativeOpacUrl";s:0:"";s:25:"alternativeOpacUrlTrigger";s:0:"";s:16:"jQueryNoConflict";s:1:"0";s:11:"opacBaseURL";s:39:"http://opac.sub.uni-goettingen.de/DB=1/";}';	//opac.sub.uni-goettingen.de/DB=1/LNG=DU/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER,http://opac.sub.uni-goettingen.de/DB=1/LNG=EN/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER";s:18:"alternativeOpacUrl";s:0:"";s:25:"alternativeOpacUrlTrigger";s:0:"";s:16:"jQueryNoConflict";s:1:"0";s:11:"opacBaseURL";s:39:"http://opac.sub.uni-goettingen.de/DB=1/";}';	//opac.sub.uni-goettingen.de/DB=1/LNG=DU/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER,http://opac.sub.uni-goettingen.de/DB=1/LNG=EN/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER";s:18:"alternativeOpacUrl";s:0:"";s:25:"alternativeOpacUrlTrigger";s:0:"";s:16:"jQueryNoConflict";s:1:"0";}';	//opac.sub.uni-goettingen.de/DB=1/LNG=DU/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER,http://opac.sub.uni-goettingen.de/DB=1/LNG=EN/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER";s:18:"alternativeOpacUrl";s:0:"";s:25:"alternativeOpacUrlTrigger";s:0:"";s:16:"jQueryNoConflict";s:1:"0";}';	//opac.sub.uni-goettingen.de/DB=1/LNG=DU/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER,http://opac.sub.uni-goettingen.de/DB=1/LNG=EN/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER";s:18:"alternativeOpacUrl";s:0:"";s:25:"alternativeOpacUrlTrigger";s:0:"";s:16:"jQueryNoConflict";s:1:"0";}';	//opac.sub.uni-goettingen.de/DB=1/LNG=DU/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER,http://opac.sub.uni-goettingen.de/DB=1/LNG=EN/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=LKL+PLACEHOLDER";s:18:"alternativeOpacUrl";s:0:"";s:25:"alternativeOpacUrlTrigger";s:0:"";s:16:"jQueryNoConflict";s:1:"0";}';	//opac.sub.uni-goettingen.de/DB=1/LNG=DU/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=PLACEHOLDER,http://opac.sub.uni-goettingen.de/DB=1/LNG=EN/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=PLACEHOLDER";s:18:"alternativeOpacUrl";s:0:"";s:25:"alternativeOpacUrlTrigger";s:0:"";s:16:"jQueryNoConflict";s:1:"0";}';	//opac.sub.uni-goettingen.de/DB=1/LNG=DU/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=,http://opac.sub.uni-goettingen.de/DB=1/LNG=EN/CMD?ACT=SRCHA&IKT=1016&SRT=YOP&TRM=";s:18:"alternativeOpacUrl";s:0:"";s:25:"alternativeOpacUrlTrigger";s:0:"";s:16:"jQueryNoConflict";s:1:"0";}';	// 
// Updated by TYPO3 Extension Manager 14-03-11 18:57:18
?>
