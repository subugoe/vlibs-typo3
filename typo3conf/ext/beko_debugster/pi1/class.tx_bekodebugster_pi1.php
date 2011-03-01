<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004 Clemens Prerovsky (Clemens.Prerovsky@beko.at)
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

if (!defined('PATH_tslib')) {
	if (@is_dir(PATH_site.'typo3/sysext/cms/tslib/')) {
		define('PATH_tslib', PATH_site.'typo3/sysext/cms/tslib/');
	} elseif (@is_dir(PATH_site.'tslib/')) {
		define('PATH_tslib', PATH_site.'tslib/');
	}
}
require_once(PATH_tslib . 'class.tslib_pibase.php');



/**
 * Plugin 'Debugster' for the 'beko_debugster' extension.
 *
 * @author	Clemens Prerovsky <Clemens.Prerovsky@beko.at>
 */
class tx_bekodebugster_pi1 extends tslib_pibase {
	var $prefixId = "tx_bekodebugster_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_bekodebugster_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "beko_debugster";	// The extension key.
	var $content;
	var $conf; // will contain various configuration options
	var $searchResults = array();
	var $rDepth = 0; // contains current recursion depth
	var $maxRDepth = 0; // contains the maximum recursion depth reached during parse
	var $title;
	var $backtrace; // will contain debug_backtrace;
	var $bt_key = 1; // this is the key of the backtrace element which contains the debugster call
	// contains all debugster options available
	var $options = array(
				'autofetch' 							=> 0,
				'steps_back'							=> false,
				'exit'										=> false,
				'htmlspecialchars'				=> true,
				'linenum'									=> false,
				'methods'									=> true,
				'info'										=> false,
				'int2date'								=> false,
				'recursion'								=> -1,
				'search'									=> '',
				'title'										=> '',
				'wide'										=> false,
			);
	// will describe options set above. array contains data type + descriptive text
	var $options_descr = array(
				'autofetch' 							=> array('integer', 'will cause debugster to fetch n rows automatically, if the variable passed is a mysql result resource'),
				'steps_back'							=> array('integer', 'depth of backtrace: how many steps of the backtrace are displayed in the debugster title. hint: move with mouse over the filename to display the complete path.'),
				'exit'										=> array('boolean', 'exit() script after debugster output'),
				'htmlspecialchars'				=> array('boolean', 'will convert string output with htmlspecialchars() if true'),
				'linenum'									=> array('boolean', 'enable display of line numbers in strings'),
				'methods'									=> array('boolean', 'display class methods when debugging a class'),
				'info'										=> array('boolean', 'display info on current debugster settings'),
				'int2date'								=> array('boolean', 'will threat all integers as timestamp and append time display'),
				'recursion'								=> array('integer', 'causes to stop debugster after recurring n times into an array -&gt; display depth'),
				'search'									=> array('string', 'searches for your string; case sensitive'),
				'title'										=> array('string', 'customize debugster`s title'),
				'wide'										=> array('boolean', 'enable wide mode - all tables are streched to 100% width. may look, ehm, ugly'),
			);

	function tx_bekodebugster_pi1($var,$conf) {
		$this->bt_key++; // php 4
		$this->__construct($var,$conf);
	}

	function __construct(&$var,&$conf) {
		$extConf = isset($GLOBALS["TYPO3_CONF_VARS"]["EXT"]["extConf"]["beko_debugster"]) ? unserialize($GLOBALS["TYPO3_CONF_VARS"]["EXT"]["extConf"]["beko_debugster"]) : array();
		$ip = isset($extConf['ip_mask']) ? $extConf['ip_mask'] : '*';
		
		/* EDIT START hostname-resolver, possible hostname-entry will be parsed to an IP */
		
		if($ip != '*') {
			$resolvedHostNames = array();
			$ipArr = t3lib_div::trimExplode(',', $ip, true);
			foreach($ipArr as $ipt) {
				$resolvedHostNames[] = gethostbyname(trim($ipt));
			}
			$ip = join(',', $resolvedHostNames);
		}
		
		/* EDIT END */
		
		if (!t3lib_div::cmpIP(t3lib_div::getIndpEnv('REMOTE_ADDR'), $ip)) { return false; }
		$this->setConfig($conf);
		$this->backtrace = debug_backtrace();

		if ($this->conf['info']) {
			unset($var);
			$title = 'DEBUGSTER OPTIONS';
			$var = $this->conf;
		}

		$this->title = $this->resolveTitle();

		// $GLOBALS['GLOBALS'] -> unlimited recursion protection
		if (strstr($this->title,'$GLOBALS') && is_array($var) && is_array($var['GLOBALS'])) {
			unset($var['GLOBALS']);
		}

		$debug = true; // will affect if we do real debug output or just some debugster info

		$content = $this->addStyleDefinition();
		$content .= '<table class="colorDebugMain" cellpadding="1" cellspacing="2">';
		$content .= '<tr><td class="fileinfo"><!-- FILE INFO -->' . $this->getFileInfo() . '<!-- FILE INFO END --></td></tr>';
		$content .= '<tr><td class="title"><!-- TITLE --><strong> ^ ' . $this->title . ' ^ </strong><!-- TITLE END --></td></tr>';
		$content .= '<tr><td><!-- MAIN DEBUG TABLE -->';
		$content .= '<table class="colorDebug" cellpadding="1" cellspacing="1">';

		if ($this->conf['search'] != '') {
			$content .= $this->searchString($var);
			$debug = false;
		}
		if (is_string($var) && strcmp($var,'help') === 0) {
			$content .= $this->displayHelp();
			$debug = false;
		}

		if ($debug) {
			$content .= $this->switchType($var);
			$content .= $this->addWarnings();
			$content .= $this->addRDepthInfo();
		}
		$content .= '</table><!-- MAIN DEBUG TABLE END --></td></tr></table>'."\n";

		$this->content = $content;
	}

	/**
	 * display infotext corresponding to options
	 */
	function displayHelp() {
		$html = '
			<tr>
				<td class="title">OPTION</td>
				<td class="title">DATA TYPE</td>
				<td class="title">DESCRIPTION</td>
				<td class="title">CURRENT VALUE</td>
			</tr>';
		foreach($this->conf as $key => $value) {
			$html .= '
			<tr>
				<td class="string">' . $key . '</td>
				<td class="string">' . $this->options_descr[$key][0] . '</td>
				<td class="string">' . $this->options_descr[$key][1] . '</td>
				<td class="integer"><table class="colorDebug" cellpadding="1" cellspacing="2">' . $this->switchType($value) . '</table></td>
			</tr>';
		}
		return $html;
	}

	function setConfig($conf) {
		$this->conf = $this->options;
		if (is_array($conf)) {
			$this->conf = array_merge($this->conf,$conf);
		} else {
			$this->conf['title'] = (string)$conf;
		}
	}

	/**
	 * Search for a string in a variable
	 *
	 * @param			string			search string
	 * @return		string			html code
	 */
	function searchString(&$var) {
		$this->switchType_search($var);
		return $this->renderSearchResults();
	}

	function renderSearchResults() {
		if (count($this->searchResults) == 0) return '<tr><td colspan="3">Sorry - no matches.</td></tr>';
		#krsort($this->searchResults);
		foreach($this->searchResults as $key => $value) {
			$content .= '
			<tr>
				<td>' . $key . '</td>
				<td>=</td>
				<td>' . $value . '</td>
			</tr>
			';
		}
		return $content;
	}

	function switchType_search($var) {
		$type = gettype($var);
		switch($type) {
			case 'array': $this->searchArray($var); break;
			case 'object': $this->searchObject($var); break;
			case 'resource': break;
			default: $this->searchValue($var); break;
		}

	}

	function searchValue($var) {
		if (strlen($var) == 0) return;
		if (strstr($var,$this->conf['search'])) {
			$this->searchResults[implode('',$this->searchPath)] = str_replace(
				$this->conf['search'],
				'<span class="search_hit">' . $this->conf['search'] . '</span>',
				nl2br(htmlspecialchars($var))
			);
		}
	}

	function searchKey($key,$hitValue) {
		if (strstr($key,$this->conf['search'])) {
			$key = str_replace($this->conf['search'],
												 '<span class="search_hit">' . $this->conf['search'] . '</span>',
												 nl2br(htmlspecialchars(implode('',$this->searchPath)))
			);
			$this->searchResults[$key] = $hitValue;
		}
	}

	function searchObject($var) {
		$objVars = get_object_vars($var);
		foreach ($objVars as $key => $value) {
			$this->searchPath[] = '->' . $key;
			$this->searchKey($key,'OBJECT PROPERTY');
			$this->switchType_search($value);
			array_pop($this->searchPath);
		}
		$objFuncs = get_class_methods($var);
		foreach ($objFuncs as $function) {
			$this->searchPath[] = '->' . $function . '()';
			// search function name
			$this->searchKey($function,'OBJECT METHOD');
			array_pop($this->searchPath);
		}
	}

	function searchArray($var) {
		foreach ($var as $key => $value) {
			$this->searchPath[] = '['.$this->arrayKeyEscape($key).']';
			$this->searchKey($key,'ARRAY KEY');
			$this->switchType_search($value);
			array_pop($this->searchPath);
		}
	}

	function arrayKeyEscape($key) {
		if (is_int($key)) return $key;
		return "'" . $key . "'";
	}

	/**
	 * Get File info from debug_backtrace
	 *
	 * @return		string			html code for file info
	 */
	function getFileInfo() {
		$hops = array();
		$extConf = isset($GLOBALS["TYPO3_CONF_VARS"]["EXT"]["extConf"]["beko_debugster"]) ? unserialize($GLOBALS["TYPO3_CONF_VARS"]["EXT"]["extConf"]["beko_debugster"]) : array();
		$steps = (isset($this->conf['steps_back']) && (int)$this->conf['steps_back']) ? (int)$this->conf['steps_back'] : 0;
		if (!$steps) { $steps = (isset($extConf['steps_back']) && (int)$extConf['steps_back']) ? (int)$extConf['steps_back'] : 3; }
		for ($i=0;$i<$steps;$i++) {
			$bt = $this->backtrace[$this->bt_key + $i];
			if (!$bt) { break; }
			$hops[] = sprintf('<span title="%s">%s:%d</span>', $bt['file'], basename($bt['file']), $bt['line']);
		}
		$result = join (' &rarr; ', array_reverse($hops));
		return $result;
	}

	/**
	 * Will return Debugster's title
	 *
	 * @param			string			title
	 * @return		string			title for debugster box
	 */
	function resolveTitle() {
		$title = &$this->conf['title'];
		if ($title != '') return $title;
		if ($title == '' && $this->conf['search'] != '') return '<tr><td colspan="3" class="title">DEBUGSTER - search results for ' . $this->conf['search'] . '</td></tr>' . "\n";

		// ...so we have to retrieve the variable name from the file
		$file = file($this->backtrace[$this->bt_key]['file']);
		$filename = basename($this->backtrace[$this->bt_key]['file']);
		$debugsterCall = htmlspecialchars(trim($file[($this->backtrace[$this->bt_key]['line']-1)]));
		#preg_match('/debugster *\((.*)\b/',$debugsterCall,$reg);
		return $debugsterCall;
	}

	/**
	 * Output the debugsters contents
	 *
	 * @return		void
	 */
	function output() {
		echo $this->content;
		if ($this->conf['exit'] === true) exit();
	}

	/**
	 * Set Debugster's configuration options
	 *
	 * @param			array		configuration array
	 * @return		void
	 */
	function configure($conf) {
		$this->conf = $this->conf_initValues();
	}

	/**
	 * Determine type of variable
	 *
	 * @param			mixed		variable to determine type of
	 * @param			string	if array fill in array key here
	 * @param			string	css class for key
	 * @return		string	html
	 */
	function switchType(&$var,$key=null,$keyclass='') {
		if (!is_null($key)) { $key = '<td class="'.$keyclass.'">[' . $key . ']</td>'."\n"; }
		$type = gettype($var);
		switch($type) {
			case 'boolean':  $content = $this->displayBoolean($var,$key); break;
			case 'integer':  $content = $this->displayInteger($var,$key); break;
			case 'double':   $content = $this->displayDouble($var,$key); break;
			case 'string':   $content = $this->displayString($var,$key); break;
			case 'resource': $content = $this->displayResource($var,$key); break;
			case 'NULL':     $content = $this->displayNull($var,$key); break;
			// descend into array and keep an eye on the maximum recursion depth
			case 'array':
				$this->rDepth++;
				if ($this->conf['recursion'] == -1 || $this->conf['recursion'] >= $this->rDepth) {
					if ($this->rDepth > $this->maxRDepth) $this->maxRDepth = $this->rDepth;
					$content = $this->displayArray($var,$key);
				} else {
					$content = $this->displayDefault('Array (recursion depth exceeded)',$key,'array');
				}
				$this->rDepth--;
				break;
			// descend into object and keep an eye on the maximum recursion depth
			case 'object':
				$this->rDepth++;
				if ($this->conf['recursion'] == -1 || $this->conf['recursion'] >= $this->rDepth) {
					if ($this->rDepth > $this->maxRDepth) $this->maxRDepth = $this->rDepth;
					$content = $this->displayObject($var,$key);
				} else {
					$content = $this->displayDefault('Object (recursion depth exceeded)',$key,'object');
				}
				$this->rDepth--;
			break;
			default:         break;
		}
		return $content;
	}

	/**
	 * Append recursion depth info
	 *
	 * @return		string		html
	 */
	function addRDepthInfo() {
		if ($this->conf['recursion'] == -1) return;
		$content .= '<table class="colorDebugMain" cellpadding="1" cellspacing="2">';
		$content .= '<tr><td class="title">Maximum recursion depth reached during parse is ' . $this->maxRDepth . '.</td></tr>';
		$content .= '</table>';
		$content .= '</td></tr></table>';
		return $content;
	}

	/**
	 * Append list of warnings to debugster
	 *
	 * @return		string		html
	 */
	function addWarnings() {
		if (!isset($this->warnings) || (is_array($this->warnings) && count($this->warnings) <= 0)) { return ''; }
		$content = '<table class="colorDebugMain" cellpadding="1" cellspacing="2"><tr><td class="title">WARNING</td></tr><tr><td>';
		$content .= '<table class="colorDebugMain" cellpadding="1" cellspacing="2">';
		foreach ($this->warnings as $warning) {
			$content .= '<tr><td class="title">' . $warning . '</td></tr>';
		}
		$content .= '</table>';
		$content .= '</td></tr></table>';
		return $content;
	}

	/**
	 * Renders an object
	 *
	 * @param			object		object to display
	 * @return		string 		html code
	 */
	function displayObject(&$object,$key) {
		$objVars = get_object_vars($object);
		foreach ($objVars as $valkey => $value) {
			$object_properties .= $this->switchType($value,$valkey,'object');
		}
		$content .= "	<tr>
		$key
		<td class=\"object\">object of class<br />\"" . get_class($object) . "\"</td>
		<td class=\"object\"><table class=\"colorDebug\" cellpadding=\"1\" cellspacing=\"2\">
				<tr>
					<td class=\"object\">PROPERTIES</td>
					<td><table class=\"colorDebug\" cellpadding=\"1\" cellspacing=\"2\">$object_properties</table></td>
				</tr>";

		// only display methods if enabled
		if ($this->conf['methods'] === true) {
			$objFuncs = get_class_methods($object);
			foreach ($objFuncs as $valkey => $value) {
				$object_methods .= "	<tr>
			<td class=\"object\">$valkey</td>
			<td class=\"object\" colspan=\"2\">$value</td>
		</tr>";
			}
			$content .= "				<tr>
					<td class=\"object\">METHODS</td>
					<td><table class=\"colorDebug\" cellpadding=\"1\" cellspacing=\"2\">$object_methods</table></td>
				</tr>";
		}
		$content .= '			</table></td>
	</tr>';
		return $content;
	}

	/**
	 * Renders an array
	 *
	 * @param			array			array to display
	 * @return		string 		html code
	 */
	function displayArray(&$var,$key) {
		$count = count($var);
		if ($count == 0) {
			$array_content = 'EMPTY';
		} else {
			$i = 0;
			foreach ($var as $valkey => $value) {
				$array_content .= $this->switchType($value,$valkey,'array');
			}
		}
		$content .= "	<tr>
		$key
		<td class=\"array\">array($count)</td>
		<td class=\"array\"><table class=\"colorDebug\" cellpadding=\"1\" cellspacing=\"2\">$array_content</table></td>
	</tr>";
		return $content;
	}

	/**
	 * Default display of a variable
	 * Utilized for displaying integers, strings, doubles, etc.
	 *
	 * @param			mixed			variable to display
	 * @return		string 		html code
	 */
	function displayDefault($var,$key,$class, $length=false) {
		if ($length !== false) $length = "($length)";
		return "	<tr>
		$key
		<td class=\"$class\">$class $length</td>
		<td class=\"$class\">$var</td>
	</tr>";
	}

	/**
	 * Renders a string
	 *
	 * @param			string		string to display
	 *
	 * @return		string 		html code
	 */
	function displayString($var,$key) {
		$length = strlen($var);
		// htmlspecialchars display
		if ($this->conf['htmlspecialchars'] === true) {
			$var = nl2br(str_replace(' ', '&nbsp;', htmlspecialchars($var)));
		}
		// display line numbers... only if we got more than one line to display
		if ($this->conf['linenum'] && strpos($var,"\n") !== false) {
			$string_arr = explode("\n",$var);
			unset($var);
			$linenum_width = strlen(count($string_arr));
			while(list($num,$line)=each($string_arr)) {
				$var .= sprintf('%0' . $linenum_width . 'd %s', $num + 1, $line);
			}
		}
		return $this->displayDefault($var,$key,'string',$length);
	}

	function displayDouble($var,$key) {
		return $this->displayDefault($var,$key,'double');
	}

	function displayInteger($var,$key) {
		// append integer converted to date if option is enabled
		if ($this->conf['int2date'] === true) $var .= ' &rarr; ' . date('Y-m-d H:i:s (\wW)',$var);
		return $this->displayDefault($var,$key,'integer');
	}

	function displayNull($var,$key) {
		return $this->displayDefault('NULL',$key,'null');
	}

	function displayResource($var,$key) {
		$type = get_resource_type($var);
		$display = $var . ' ('.$type.')';
		if ($this->conf['autofetch'] && $_GET['fetchmysql'] == '') $_GET['fetchmysql'] = $this->conf['autofetch'];
		$fetchnum = ($_GET['fetchmysql'] != '') ? $_GET['fetchmysql'] : 3;
		if($type == 'mysql result') {
			$display .= '<hr/><form class="debugster"><fieldset><legend>Fetch a number of rows from result</legend><input type="text" name="fetchmysql" value="'.$fetchnum.'" style="width: 70px;"> Rows &nbsp;&nbsp;<input type="submit" value="&raquo; fetch result"></fieldset>'.$this->generateHiddenFields('fetchmysql').'</form>';
			if ($_GET['fetchmysql'] > 0) {
				if (mysql_num_rows($var) > 0) {
					while($row = mysql_fetch_assoc($var)) {
						$i++;
						$mysql_fetched[] = $row;
						if ($i >= $_GET['fetchmysql']) break;
					}
				}
				$display .= "<table class=\"colorDebug\" cellpadding=\"0\" cellspacing=\"0\">" . $this->switchType($mysql_fetched) . '</table>';
			}
			mysql_data_seek($var,0); // reset pointer
		}
		return $this->displayDefault($display,$key,'resource');
	}

	function generateHiddenFields($exclude=false) {
		if($_GET) {
			foreach($_GET as $get_key => $get_value) {
				if ($get_key != $exclude) $hiddenfields .= '<input type="hidden" name="'.$get_key.'" value="'.$get_value.'">'."\n";
			}
		}
		return $hiddenfields;
	}

	function displayBoolean($var,$key) {
		$displayVar = ($var) ? 'TRUE' : 'FALSE';
		return $this->displayDefault($displayVar,$key,'boolean');
	}

	function addStyleDefinition() {
		// wolo mod: prevent sending styles multiple times when debugster called more than once
		if ($GLOBALS['debugster']['css_sent'])  {
			return '';
		}
		$GLOBALS['debugster'] = array('css_sent' => true);

		if ($this->conf['wide'] === true) {
			$css_width = 'width: 100%;';
		}
		return  $content .= '<style>
	table.colorDebugMain {
		font-family: "Courier New", Monospace, sans;
		font-size: 13px;
		background-color: #EFEFEF;
		border: 0px inset;
		'.$css_width.'
	}
	table.colorDebugMain td {
		border: 1px inset;
		'.$css_width.'
	}
	table.colorDebugMain td.title {
		text-align: center;
		color: #990000;
	}
	table.colorDebugMain td.fileinfo {
 		font-size: 11px;
	}
	table.colorDebug {
		font-family: "Courier New", Monospace, sans;
		font-size: 11px;
		background-color: white;
		border: 1px inset;
	}
	table.colorDebug td {
		border: 0px solid white;
		padding: 1px;
	}
	table.colorDebug td.integer {
		color: #990000;
		background-color: #FFEFEF;
	}
	table.colorDebug td.double,td.float{
		color: #990000;
		background-color: #FFEFEF
	}
	table.colorDebug td.string {
		color: #000088;
		background-color: #EFEFFF
	}
	table.colorDebug td.boolean,td.null {
		color: #EE7700;
		background-color: #FFEECC;
	}
	table.colorDebug td.array {
		padding: 0px;
		color: #007700;
		background-color: #EFFFEF;
	}
	table.colorDebug td.object,td.resource {
		padding: 0px;
		color: #990099;
		background-color: #FFEFFF;
	}
	form.debugster legend {
		border: 1px groove;
	}
	span.search_hit {
		color: #990000;
		background-color: #FFEFEF;
		border: 0px solid #990000;
		border-top-width: 1px;
		border-bottom-width: 1px;
	}
</style>
';
	}

	/**
	 * Main function - just a dummy with no use
	 */
	function main($content,$conf)	{
		return "Hello World!<hr />
			This is the Debugster.";
	}
}

/**
 * This function provides a shortcut so you don't have to use the class directly
 */
function debugster($var,$conf='') {
	unset($myOneAndOnlyDebugster);
	$myOneAndOnlyDebugster = new tx_bekodebugster_pi1($var,$conf);
	$myOneAndOnlyDebugster->output();
}

if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/beko_debugster/pi1/class.tx_bekodebugster_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/beko_debugster/pi1/class.tx_bekodebugster_pi1.php"]);
}


?>
