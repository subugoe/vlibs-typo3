<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2004 Kasper Sk�hj (kasper@typo3.com)
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
 * Module 'ExtDevEval' for the 'extdeveval' extension.
 *
 * $Id: index.php 63722 2012-06-22 14:24:10Z ohader $
 *
 * @author	Kasper Skaarhoj <kasper@typo3.com>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   75: class tx_extdeveval_module1 extends t3lib_SCbase
 *   86:     function init()
 *  108:     function menuConfig()
 *  142:     function main()
 *  233:     function printContent()
 *  246:     function moduleContent()
 *
 *              SECTION: Various helper functions
 *  494:     function getSelectForLocalExtensions()
 *  516:     function getSelectForExtensionFiles()
 *  543:     function getCurrentPHPfileName()
 *  562:     function getCurrentExtDir()
 *
 * TOTAL FUNCTIONS: 9
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */


if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
}

	// DEFAULT initialization of a module [BEGIN]
require_once (PATH_t3lib.'class.t3lib_scbase.php');
$BE_USER->modAccess($MCONF,1);	// This checks permissions and exits if the users has no permission for entry.
	// DEFAULT initialization of a module [END]




/**
 * Script class for the Extension Development Evaluation module
 *
 * @author	Kasper Skaarhoj <kasper@typo3.com>
 * @package TYPO3
 * @subpackage tx_extdeveval
 */
class tx_extdeveval_module1 extends t3lib_SCbase {
	const MODULE_Name = 'tools_txextdevevalM1';

		// Internal, fixed:
	var $localExtensionDir = 'typo3conf/ext/';			// Operate on local extensions (the ext. main dir relative to PATH_site). Can be set to the global and system ext. dirs as well (but should not be needed for the common man...)
	var $modMenu_type = 'ses';

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()	{
		$this->MOD_MENU = Array (
			'function' => Array (
				'0' => '[Select tool]',
				'1' => 'getLL() converter',
				'2' => 'PHP script documentation help',
				'4' => 'Create/Update Extensions PHP API data',
#				'5' => 'Create/Update Extensions TypoScript API data (still empty)',
				'6' => 'Display API from "ext_php_api.dat" file',
				'7' => 'Convert locallang.php files to ll-XML format',
				'19' => 'Convert locallang.xml files to XLIFF',
				//'20' => 'Convert locallang.xlf files to ll-XML format',
				'8' => 'Moving localizations out of ll-XML files and into csh_*',
				'9' => 'Generating ext_autoload.php',
				'3' => 'temp_CACHED files confirmed removal',
				'10' => 'PHP source code tuning',
				'11' => 'Code highlighting',
				'13' => 'CSS analyzer',
				'14' => 'Calculator',
				'12' => 'Table Icon Listing',
				'15' => 'Dump template tables',
				'17' => 'Raw DB Edit',
				'18' => 'Sprite Management',
				'16' => 'phpinfo()',
			),
			'extScope' => array(
				'L' => 'Local Extensions',
				'G' => 'Global Extensions',
				'S' => 'System Extensions',
				'C' => 'Core files (tslib, t3lib)'
			),
			'extSel' => '',
			'phpFile' => '',
			'tuneXHTML' => '',
			'tuneQuotes' => '',
			'tuneBeautify' => '',
		);

			// If TYPO3 version is lower then TYPO3 4.5, remove features:
		if (Tx_Extdeveval_Compatibility::convertVersionNumberToInteger(TYPO3_version) < 4005000) {
			unset($this->MOD_MENU['function']['18']);
		}

		parent::menuConfig();
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 *
	 * @return	void
	 */
	function main()	{
			// Setting scope
		switch((string)$this->MOD_SETTINGS['extScope'])	{
			case 'G':
				$this->localExtensionDir = 'typo3/ext/';
			break;
			case 'S':
				$this->localExtensionDir = 'typo3/sysext/';
			break;
			default:
			case 'L':
				// Local, which is default.
			break;
		}

			// Draw the header.
		$this->doc = t3lib_div::makeInstance('noDoc');
		$this->doc->backPath = $GLOBALS['BACK_PATH'];
		$this->doc->form='<form action="" method="post">';
		$this->doc->docType = 'xhtml_trans';

			// JavaScript
		$this->doc->JScode = $this->doc->wrapScriptTags('
				script_ended = 0;
				function jumpToUrl(URL)	{	//
					if (!URL.match(/[?&]M=/)) {
						URL = URL + "&M=' . self::MODULE_Name . '";
					}
					document.location = URL;
				}
		');

			// Styles:
		$this->doc->inDocStylesArray[]='
			TR.nonSelectedRows { background-color: #cccccc; }

			/* Styles for the API display: */

				DIV#c-APIdoc  A { text-decoration: none; }
				DIV#c-APIdoc  DIV#c-openInNewWindowLink A { text-decoration: underline; }
				DIV#c-APIdoc TABLE TR TD {padding: 1px 3px 1px 3px; }
				DIV#c-APIdoc TABLE TR {background-color: '.$this->doc->bgColor4.'; }
				DIV#c-APIdoc DIV#c-body DIV.c-class TABLE.c-details TR TD.c-Hcell {background-color: '.$this->doc->bgColor2.'; font-weight: bold; }
				DIV#c-APIdoc DIV#c-body DIV.c-function TABLE.c-details TR TD.c-Hcell, DIV#c-APIdoc DIV#c-body DIV.c-class TABLE.c-details TR TD.c-Hcell {background-color: '.$this->doc->bgColor5.'; font-weight: bold; }
				DIV#c-APIdoc DIV#c-openInNewWindowLink { margin: 10px 0px 20px 0px;}

				DIV#c-APIdoc DIV#c-index P.c-fileDescription { margin-left: 30px;  margin-bottom: 10px; font-style: italic; }
				DIV#c-APIdoc DIV#c-index P.c-indexTags { margin-left: 90px; }
				DIV#c-APIdoc DIV#c-index H4 { margin-left: 50px; }
				DIV#c-APIdoc DIV#c-index H4.c-function { margin-left: 70px; }
				DIV#c-APIdoc DIV#c-index H3 { margin-left: 30px; margin-top: 20px;}
				DIV#c-APIdoc DIV#c-index { margin-bottom: 30px; }

				DIV#c-APIdoc DIV#s-index {margin-top: 20px;}
				DIV#c-APIdoc DIV#s-index H3 {background-color: '.$this->doc->bgColor5.'; margin: 0px 0px 0px 30px;}

				DIV#c-APIdoc DIV#c-body DIV.c-class {margin-left: 25px;margin-top: 10px; }
				DIV#c-APIdoc DIV#c-body DIV.c-function TABLE.c-details TR TD.c-vType {font-weight: bold;}
				DIV#c-APIdoc DIV#c-body P.c-funcDescription {font-style: italic;}
				DIV#c-APIdoc DIV#c-body DIV.c-header {background-color: '.$this->doc->bgColor2.'; margin-top: 30px;}
				DIV#c-APIdoc DIV#c-body DIV.c-function { margin-top: 20px; margin-left: 70px; }
				DIV#c-APIdoc DIV#c-body TABLE.c-details {margin-top: 5px; width: 100%; }
				DIV#c-APIdoc DIV#c-body TABLE.c-details TR TD.c-Hcell {width: 25%;}
				DIV#c-APIdoc DIV#c-body TABLE.c-details TR TD.c-vDescr {width: 75%;}
				DIV#c-APIdoc DIV#c-index H3.section { margin-left: 80px;  width: 70%; background-color: '.$this->doc->bgColor4.';}

		';

		$this->content.=$this->doc->startPage('Extension Development Evaluator');
		$this->content.=$this->doc->header('Extension Development Evaluator');
		$this->content.=$this->doc->spacer(5);
		$this->content.=$this->doc->section('',
			$this->doc->funcMenu('',
				t3lib_BEfunc::getFuncMenu($this->id,'SET[extScope]',$this->MOD_SETTINGS['extScope'],$this->MOD_MENU['extScope']).'<br/>'.
				t3lib_BEfunc::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])
			)
		);

			// Shows extension and ext.file selector only for SOME of the tools:
		switch((string)$this->MOD_SETTINGS['function'])	{
			case 1:		// getLL() converter
			case 2:		// PHP script documentation help
			case 6:		// Display API from "ext_php_api.dat" file
			case 7:		// Convert locallang.php files to ll-XML format
			case 8:		// Moving localizations out of ll-XML files and into csh_*
			case 10:	// PHP source code tuning
			case 19:	// Convert locallang.xml files to XLIFF
			//case 20:	// Convert locallang.xlf files to ll-XML format
				switch ($this->MOD_SETTINGS['function']) {
					case 8:
					case 19:
						$extList = 'xml';
						break;
					//case 20:
					//	$extList = 'xlf';
					//	break;
					default:
						$extList = 'php,inc';
						break;
				}
				$this->content .= $this->doc->section('Select Local Extension:', $this->getSelectForLocalExtensions() . '<br />' . $this->getSelectForExtensionFiles($extList));
				$this->content .= $this->doc->divider(5);
				break;
			case 4:		// Create/Update Extensions PHP API data
			case 9:		// Generating ext_autoload.php
				$this->content .= $this->doc->section('Select Local Extension:', $this->getSelectForLocalExtensions());
				$this->content .= $this->doc->divider(5);
			break;
		}

			// Render content:
		$this->moduleContent();


		// ShortCut
		if ($GLOBALS['BE_USER']->mayMakeShortcut()) {
			$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
		}

		$this->content.=$this->doc->spacer(10);
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{
		$this->content .= $this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent()	{
		switch((string)$this->MOD_SETTINGS['function'])	{
			case 1:		// getLL() converter
				$content = 'A tool which helps developers of extensions to (more) easily convert hardcoded labels to labels provided by the localization engine in TYPO3 (using the pi_getLL() functions).<br/><br/>';
				$content.= htmlspecialchars("Example: If you have a label like \$content='<p>SAVED</p>' simply change it to \$content='<p>'.\$GLOBALS['LANG']->getLL('','SAVED').'</p>' (backend, takes priority) or \$content='<p>'.\$this->pi_getLL('','SAVED').'</p>' (frontend) at it will be found and substituted with the correct entry.");

				$this->content.=$this->doc->section('getLL() converter',$content,0,1);
				$phpFile = $this->getCurrentPHPfileName();
				if (is_array($phpFile))	{
					require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_submodgetll.php';

					$inst = t3lib_div::makeInstance('tx_extdeveval_submodgetll');
					$content = $inst->analyseFile($phpFile[0],$this->localExtensionDir);

					$this->content.=$this->doc->section('File: '.basename(current($phpFile)),$content,0,1);
				} else {
					$this->content.=$this->doc->section('NOTICE',$phpFile,0,1,2);
				}
				break;
			case 2:		// PHP script documentation help
				$content = 'A tool which helps to insert JavaDoc comments for PHP functions in a script.';
				$this->content.=$this->doc->section('PHP Script Documentation Help',$content,0,1);
				$phpFile = $this->getCurrentPHPfileName();
				if (is_array($phpFile))	{
					require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_phpdoc.php';

					$inst = t3lib_div::makeInstance('tx_extdeveval_phpdoc');
					$content = $inst->analyseFile($phpFile[0],$this->localExtensionDir);

					$this->content.=$this->doc->section('File: '.basename(current($phpFile)),$content,0,1);
				} else {
					$this->content.=$this->doc->section('NOTICE',$phpFile,0,1,2);
				}
				break;
			case 3:		// temp_CACHED files confirmed removal
				$content = 'A tool which removes the temp_CACHED files from typo3conf/ AND checks if they truely were removed!<br />This is a rather seldom need but if you experience certain problems (with installation/de-installation of extensions) it might be useful to know if the "temp_CACHED_*" files can be removed by the extension management class. This is what this module tests.<hr />';
				$this->content.=$this->doc->section('Remove temp_CACHED files',$content,0,1);

				require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_cachefiles.php';
				$inst = t3lib_div::makeInstance('tx_extdeveval_cachefiles');
				$content = $inst->cacheFiles();
				$this->content.=$this->doc->section('',$content,0,1);
				break;
			case 4:		// Create/Update Extensions PHP API data
				$content = 'A tool which will read JavaDoc data out of PHP scripts in the extension and stores it in a "ext_php_api.dat" file for use on TYPO3.org';
				$this->content.=$this->doc->section('PHP API data creator/updator',$content,0,1);
				$content='';

				require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_phpdoc.php';
				$inst = t3lib_div::makeInstance('tx_extdeveval_phpdoc');
				$path = $this->getCurrentExtDir();
				if ($path)	{
					$content = $inst->updateDat($path,t3lib_div::removePrefixPathFromList(t3lib_div::getAllFilesAndFoldersInPath(array(),$path,'php,inc',0,($this->MOD_SETTINGS['extSel']==='_TYPO3'?0:99)),$path),$this->localExtensionDir);
				}
				$this->content.=$this->doc->section('',$content,0,1);
				break;
			case 6:		// Display API from "ext_php_api.dat" file
				$content = 'Displays the content of an API xml file as a nice HTML page';
				$this->content.=$this->doc->section('Extension PHP API',$content,0,1);

					// Getting the path to the currently selected extension (blank if none):
				$path = $this->getCurrentExtDir();
				if ($path)	{
					if (@is_file($path.'ext_php_api.dat'))	{		// If there is an API file:
						require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_apidisplay.php';
						$inst = t3lib_div::makeInstance('tx_extdeveval_apidisplay');
						$content = '<hr />'.$inst->main(t3lib_div::getUrl($path.'ext_php_api.dat'), $this->MOD_SETTINGS['phpFile']);
					} else {	// No API file:
						$content='<br /><br /><strong>Error:</strong> The file "ext_php_api.dat" (which contains API information) was NOT found for this extension. You can create such a file with the tool from the menu called "Create/Update Extensions PHP API data".';
					}

						// Add content:
					$this->content.=$this->doc->section('',$content,0,1);
				}
				break;
			case 7:		// Convert locallang.php files to ll-XML format
				$content = 'Converts locallang*.php files in extensions to ll-XML based format (utf-8) instead.<hr />';
				$this->content.=$this->doc->section('locallang.php to ll-XML conversion',$content,0,1);

				$phpFile = $this->getCurrentPHPfileName();
				if (is_array($phpFile))	{
					require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_ll2xml.php';
					$inst = t3lib_div::makeInstance('tx_extdeveval_ll2xml');
					$content = $inst->main($phpFile[0],$this->localExtensionDir);
					$this->content.=$this->doc->section('File: '.basename(current($phpFile)),$content,0,1);
				} else {
					$this->content.=$this->doc->section('NOTICE',$phpFile,0,1,2);
				}

				break;
			case 8:		// Moving localizations out of ll-XML files and into csh_*
				$content = 'Moving localizations out of ll-XML files and into csh_* extensions which are installed.<hr />';
				$this->content.=$this->doc->section('ll-XML splitting',$content,0,1);

				$phpFile = $this->getCurrentPHPfileName();
				if (is_array($phpFile))	{
					require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_llxmlsplit.php';
					$inst = t3lib_div::makeInstance('tx_extdeveval_llxmlsplit');
					$content = $inst->main($phpFile[0],$this->localExtensionDir);
					$this->content.=$this->doc->section('File: '.basename(current($phpFile)),$content,0,1);
				} else {
					$this->content.=$this->doc->section('NOTICE',$phpFile,0,1,2);
				}

				break;
			case 9:		// Generating ext_autoload.php
				$content = 'A tool which generates the autoload registry for a given extension or the core.<hr />';
				$this->content.=$this->doc->section('Generate autoload registry',$content,0,1);

				require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_buildautoloadregistry.php';
				$autoloadRegistryBuilder = t3lib_div::makeInstance('tx_extdeveval_buildautoloadregistry');

				$content = '';
				if (!t3lib_div::_POST('build')) {
					$content = '<form action="'.t3lib_div::linkThisScript().'" method="post">
					<p><b>Building the autoload registry can take some seconds. Press "Build" to trigger it.</b></p>
					<p>If pressing "build", the ext_autoconf.php / core_autoconf.php file will be replaced without further notice.</p>
						<input type="submit" name="build" value="Build" />
					</form>';
				} else {
					if ((string)$this->MOD_SETTINGS['extScope'] === 'C') {
						$content = $autoloadRegistryBuilder->createAutoloadRegistryForCore();
					} else {
						$path = $this->getCurrentExtDir();
						if ($path)	{
							$content = $autoloadRegistryBuilder->createAutoloadRegistryForExtension($this->MOD_SETTINGS['extSel'], $path);
						}
					}
				}
				$this->content.=$this->doc->section('',$content,0,1);
				break;
			case 10:	// PHP source code tuning
				$content = 'A tool to tune your source code.<br />';

				$onCLick = "document.location='" . t3lib_BEfunc::getModuleUrl('tools_txextdevevalM1') . "'&SET[tuneQuotes]=".($this->MOD_SETTINGS['tuneQuotes']?'0':'1')."';return false;";
				$content .= '<br /><input type="hidden" name="SET[tuneQuotes]" value="0" />
						<input type="checkbox" name="SET[tuneQuotes]" value="1"'.($this->MOD_SETTINGS['tuneQuotes']?' checked':'').' onclick="'.htmlspecialchars($onCLick).'" /> convert double quotes ( " ) to single quotes ( \' )';

#				$onCLick = "document.location='index.php?SET[tuneXHTML]=".($this->MOD_SETTINGS['tuneXHTML']?'0':'1')."';return false;";
#				$content .= '<br /><input type="hidden" name="SET[tuneXHTML]" value="0" />
#						<input type="checkbox" name="SET[tuneXHTML]" value="1"'.($this->MOD_SETTINGS['tuneXHTML']?' checked':'').' onclick="'.htmlspecialchars($onCLick).'" /> convert to XHTML (silently; use for HTML)';
$this->MOD_SETTINGS['tuneXHTML'] = false;
				$onCLick = "document.location='" . t3lib_BEfunc::getModuleUrl('tools_txextdevevalM1') . "'&SET[tuneBeautify]=".($this->MOD_SETTINGS['tuneBeautify']?'0':'1')."';return false;";
				$content .= '<br /><input type="hidden" name="SET[tuneBeautify]" value="0" />
						<input type="checkbox" name="SET[tuneBeautify]" value="1"'.($this->MOD_SETTINGS['tuneBeautify']?' checked':'').' onclick="'.htmlspecialchars($onCLick).'" /> reformat/beautify PHP source code (not nice with arrays like TCA)';


				$this->content.=$this->doc->section('PHP source code tuning',$content,0,1);
				$phpFile = $this->getCurrentPHPfileName();
				if (is_array($phpFile))	{
					require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_tunecode.php';
					$inst = t3lib_div::makeInstance('tx_extdeveval_tunecode');
					$content = $inst->tune($phpFile[0], $this->localExtensionDir, $this->MOD_SETTINGS);

					$this->content.=$this->doc->section('File: '.basename(current($phpFile)),$content,0,1);
				} else {
					$this->content.=$this->doc->section('NOTICE',$phpFile,0,1,2);
				}
				break;
			case 11:	// Code highlighting
				$content = 'Highlights PHP or TypoScript code for copy/paste into OpenOffice manuals.<br /><br />';
				$this->content.=$this->doc->section('Code highlighting',$content,0,1);

				require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_highlight.php';
				$inst = t3lib_div::makeInstance('tx_extdeveval_highlight');
				$this->content.=$inst->main();
				break;
			case 12:	// Table Icon Listing
				$content = 'A tool which can list all possible icon combinations from a database table.<hr />';
				$this->content.=$this->doc->section('List icon combinations for a table',$content,0,1);

				require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_iconlister.php';
				$inst = t3lib_div::makeInstance('tx_extdeveval_iconlister');
				$content = $inst->main();
				$this->content.=$this->doc->section('',$content,0,1);
				break;
			case 13:	// CSS analyzer
				$content = 'A tool which can analyse HTML source code for the CSS hierarchy inside. Useful to get exact CSS selectors for elements on an HTML page.<hr />';
				$this->content.=$this->doc->section('CSS Analyser',$content,0,1);

				require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_cssanalyzer.php';
				$inst = t3lib_div::makeInstance('tx_extdeveval_cssanalyzer');
				$content = $inst->main();
				$this->content.=$this->doc->section('',$content,0,1);
				break;
			case 14:	// Calculator
				$content = 'A tool with various handy calculation formulars.<hr />';
				$this->content.=$this->doc->section('Calculations',$content,0,1);

				require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_calc.php';
				$inst = t3lib_div::makeInstance('tx_extdeveval_calc');
				$content = $inst->main();
				$this->content.=$this->doc->section('',$content,0,1);
				break;
			case 15:
				$content = 'Dumps all relevant content of TypoScript templates in either "sys_template" or "static_template" tables. This is useful in very rare cases for comparing changes between databases.<hr />';
				$this->content.=$this->doc->section('Dump template tables',$content,0,1);

				require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_tmpl.php';
				$inst = t3lib_div::makeInstance('tx_extdeveval_tmpl');
				$content = $inst->main();
				$this->content.=$this->doc->section('',$content,0,1);
				break;
			case 16:	// phpinfo()
				$content = 'Displays phpinfo() content and environment variables available.<hr />';
				$this->content.=$this->doc->section('System Information',$content,0,1);

					// Get PHPinfo:
				ob_start();
				phpinfo();
				$phpinfo = ob_get_contents();
				ob_end_clean();
				$reg=array();
				preg_match('#<body[^>]*>(.*)</body>#s', $phpinfo, $reg);
				$content = $reg[1];
				$this->content.=$this->doc->section('phpinfo()',$content,0,1);

					// PHP variables:
				$getEnvArray = array();
				$gE_keys = explode(',','QUERY_STRING,HTTP_ACCEPT,HTTP_ACCEPT_ENCODING,HTTP_ACCEPT_LANGUAGE,HTTP_CONNECTION,HTTP_COOKIE,HTTP_HOST,HTTP_USER_AGENT,REMOTE_ADDR,REMOTE_HOST,REMOTE_PORT,SERVER_ADDR,SERVER_ADMIN,SERVER_NAME,SERVER_PORT,SERVER_SIGNATURE,SERVER_SOFTWARE,GATEWAY_INTERFACE,SERVER_PROTOCOL,REQUEST_METHOD,SCRIPT_NAME,PATH_TRANSLATED,HTTP_REFERER,PATH_INFO');
				while(list(,$k)=each($gE_keys))	{
					$getEnvArray[$k] = getenv($k);
				}

				$this->content.=$this->doc->section('t3lib_div::getIndpEnv()',Tx_Extdeveval_Compatibility::viewArray(t3lib_div::getIndpEnv('_ARRAY')),1,1);
				$this->content.=$this->doc->section('getenv()',t3lib_div::view_array($getEnvArray),1,1);
				$this->content.=$this->doc->section('HTTP_ENV_VARS',t3lib_div::view_array($GLOBALS['HTTP_ENV_VARS']),1,1);
				$this->content.=$this->doc->section('HTTP_SERVER_VARS',t3lib_div::view_array($GLOBALS['HTTP_SERVER_VARS']),1,1);
				$this->content.=$this->doc->section('HTTP_COOKIE_VARS',t3lib_div::view_array($GLOBALS['HTTP_COOKIE_VARS']),1,1);
				$this->content.=$this->doc->section('HTTP_GET_VARS',t3lib_div::view_array($GLOBALS['HTTP_GET_VARS']),1,1);

				$sVar=array();
				$sVar['php_sapi_name()']=php_sapi_name();
				$sVar['OTHER: TYPO3_VERSION']=$GLOBALS['TYPO_VERSION'];
				$sVar['OTHER: PHP_VERSION']=phpversion();
				$sVar['imagecreatefromgif()']=function_exists('imagecreatefromgif');
				$sVar['imagecreatefrompng()']=function_exists('imagecreatefrompng');
				$sVar['imagecreatefromjpeg()']=function_exists('imagecreatefromjpeg');
				$sVar['imagegif()']=function_exists('imagegif');
				$sVar['imagepng()']=function_exists('imagepng');
				$sVar['imagejpeg()']=function_exists('imagejpeg');
				$sVar['imagettftext()']=function_exists('imagettftext');
				$sVar['OTHER: IMAGE_TYPES']=imagetypes();
				$sVar['OTHER: memory_limit']=get_cfg_var('memory_limit');
				$this->content.=$this->doc->section('Various',t3lib_div::view_array($sVar),1,1);

				$constants=array();
				$constants['PHP_OS'] = PHP_OS;
				$constants['TYPO3_OS'] = TYPO3_OS;
				$constants['TYPO3_MODE'] = TYPO3_MODE;
				$constants['PATH_thisScript'] = PATH_thisScript;
				$constants['TYPO3_mainDir'] = TYPO3_mainDir;
				$constants['TYPO3_MOD_PATH'] = TYPO3_MOD_PATH;
				$constants['PATH_typo3'] = PATH_typo3;
				$constants['PATH_typo3_mod'] = PATH_typo3_mod;
				$constants['PATH_site'] = PATH_site;
				$constants['PATH_t3lib'] = PATH_t3lib;
				$constants['PATH_typo3conf'] = PATH_typo3conf;
				$this->content.=$this->doc->section('Constants',t3lib_div::view_array($constants),1,1);
				break;
			case 17:	// Raw DB Edit
				$content = 'Quick editing of records on a very raw level.<hr />';
				$this->content.=$this->doc->section('Edit',$content,0,1);

				require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_rawedit.php';
				$inst = t3lib_div::makeInstance('tx_extdeveval_rawedit');
				$content = $inst->main();
				$this->content.=$this->doc->section('',$content,0,1);
				break;
			case 18:	// Sprite Management
				try {
					/** @var $sprites tx_extdeveval_sprites */
					$sprites = t3lib_div::makeInstance('tx_extdeveval_sprites');
					$this->content .= $sprites->renderView($this->doc);
				} catch (tx_extdeveval_exception $exception) {
					$this->content .= 'Error: ' . $exception->getMessage();
				}
				break;
			case 19:	// Convert locallang.xml files to XLIFF
				$content = 'Converts locallang*.xml files in extensions to XLIFF based format instead.<hr />';
				$this->content .= $this->doc->section('locallang.xml to XLIFF conversion',$content,0,1);

				$phpFile = $this->getCurrentPHPfileName();
				if (is_array($phpFile)) {
					require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_llxml2xliff.php';
					/** @var $inst tx_extdeveval_llxml2xliff */
					$inst = t3lib_div::makeInstance('tx_extdeveval_llxml2xliff');
					$content = $inst->main($phpFile[0], $this->localExtensionDir);
					$this->content .= $this->doc->section('File: ' . basename(current($phpFile)), $content, 0, 1);
				} else {
					$this->content .= $this->doc->section('NOTICE', $phpFile, 0, 1, 2);
				}
				break;
			//case 20:	// Convert locallang.xlf files to ll-XML format
			//	$content = 'Converts locallang*.xlf files in extensions to ll-XML based format instead.<hr />';
			//	$this->content .= $this->doc->section('locallang.xlf to ll-XML conversion',$content,0,1);
			//
			//	$phpFile = $this->getCurrentPHPfileName();
			//	if (is_array($phpFile)) {
			//		require_once PATH_tx_extdeveval . 'mod1/class.tx_extdeveval_xliff2llxml.php';
			//		/** @var $inst tx_extdeveval_xliff2llxml */
			//		$inst = t3lib_div::makeInstance('tx_extdeveval_xliff2llxml');
			//		$content = $inst->main($phpFile[0], $this->localExtensionDir);
			//		$this->content .= $this->doc->section('File: ' . basename(current($phpFile)), $content, 0, 1);
			//	} else {
			//		$this->content .= $this->doc->section('NOTICE', $phpFile, 0, 1, 2);
			//	}
			//	break;
			default:
                $this->content.= $this->extObjContent();
				break;
		}
	}















	/*************************************
	 *
	 * Various helper functions
	 *
	 *************************************/

	/**
	 * Generates a selector box with the extension keys locally available for this install.
	 *
	 * @return	string		Selector box for selecting the local extension to work on (or error message)
	 */
	function getSelectForLocalExtensions()	{
		$path = PATH_site.$this->localExtensionDir;
		if (@is_dir($path))	{
			$dirs = $this->extensionList = t3lib_div::get_dirs($path);
			if (is_array($dirs))	{
				sort($dirs);
				$opt=array();
				$opt[]='<option value="">[ Select Local Extension ]</option>';
				foreach($dirs as $dirName)		{
					$selVal = strcmp($dirName,$this->MOD_SETTINGS['extSel']) ? '' : ' selected="selected"';
					$opt[]='<option value="'.htmlspecialchars($dirName).'"'.$selVal.'>'.htmlspecialchars($dirName).'</option>';
				}
				return '<select name="SET[extSel]" onchange="jumpToUrl(\'?SET[extSel]=\'+this.options[this.selectedIndex].value,this);">'.implode('',$opt).'</select>';
			} else return 'ERROR: Could not read directories from path: "'.$path.'"';
		} else return 'ERROR: No local extensions path: "'.$path.'"';
	}

	/**
	 * Generates a selector box with file names of the currently selected extension
	 *
	 * @param	string		List of file extensions to select
	 * @return	string		Selectorbox or error message.
	 */
	function getSelectForExtensionFiles($extList='php,inc')	{
		if ($this->MOD_SETTINGS['extSel'])	{
			$path = $this->getCurrentExtDir();
			if ($path) {
				$phpFiles = t3lib_div::removePrefixPathFromList(t3lib_div::getAllFilesAndFoldersInPath(array(),$path,$extList,0,($this->MOD_SETTINGS['extSel']==='_TYPO3'?0:99)),$path);
				if (is_array($phpFiles))	{
					sort($phpFiles);
					$opt=array();
					$allFilesToComment=array();
					$opt[]='<option value="">[ Select File ]</option>';
					foreach($phpFiles as $phpName)		{
						$selVal = strcmp($phpName,$this->MOD_SETTINGS['phpFile']) ? '' : ' selected="selected"';
						$opt[]='<option value="'.htmlspecialchars($phpName).'"'.$selVal.'>'.htmlspecialchars($phpName).'</option>';
						$allFilesToComment[]=htmlspecialchars($phpName);
					}
					return '<select name="SET[phpFile]" onchange="jumpToUrl(\'?SET[phpFile]=\'+this.options[this.selectedIndex].value,this);">'.implode('',$opt).'</select>'.
							chr(10).chr(10).'<!--'.chr(10).implode(chr(10),$allFilesToComment).chr(10).'-->'.chr(10);
				} else return 'No PHP files found in path: "'.$path.'"';
			} else return 'ERROR: Local extension not found: "'.$this->MOD_SETTINGS['extSel'].'"';
		}
	}

	/**
	 * Returns the currently selected PHP file name according to the selectors with field names SET[extSel] and SET[phpFile]
	 *
	 * @return	mixed		String: Error message. Array: The PHP-file as first value in key "0" (zero)
	 */
	function getCurrentPHPfileName()	{
		if ($this->MOD_SETTINGS['extSel'])	{
			$path = $this->getCurrentExtDir();
			if ($path) {
				if ($this->MOD_SETTINGS['phpFile'])	{
					$currentFile = $path.$this->MOD_SETTINGS['phpFile'];
					if (@is_file($currentFile))	{
						return array($currentFile);
					} else return 'Currently selected PHP file was not found: '.$this->MOD_SETTINGS['phpFile'];
				} else return 'You must select a file from the selector box above.';
			} else return 'ERROR: Local extension not found: "'.$this->MOD_SETTINGS['extSel'].'"';
		} else return 'You must select an extension from the selector box above.';
	}

	/**
	 * Returns the absolute path to the currently selected extension directory.
	 *
	 * @return	string		Returns the directory IF it is also found to be a true directory. Otherwise blank.
	 */
	function getCurrentExtDir()	{
		if ($this->MOD_SETTINGS['extSel'])	{
			$path = PATH_site . $this->localExtensionDir . rtrim($this->MOD_SETTINGS['extSel'], '/') . '/';
			if (@is_dir($path))	{
				return $path;
			}
		}
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/extdeveval/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/extdeveval/mod1/index.php']);
}




// Make instance:
$SOBE = t3lib_div::makeInstance('tx_extdeveval_module1');
$SOBE->init();

// Include files?
reset($SOBE->include_once);
while(list(,$INC_FILE)=each($SOBE->include_once))	{	include_once($INC_FILE);	}
$SOBE->checkExtObj();

$SOBE->main();
$SOBE->printContent();

?>