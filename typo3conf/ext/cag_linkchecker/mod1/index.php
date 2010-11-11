<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 - 2009 Jochen Rieger (j.rieger@connecta.ag)
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
 * Module 'Linkchecker' for the 'cag_linkchecker' extension.
 *
 * @author	Jochen Rieger <j.rieger@connecta.ag>
 */
/*
unset($MCONF);
require_once('conf.php');
require_once($BACK_PATH.'init.php');
require_once($BACK_PATH.'template.php');
*/
//require_once('class.tx_rtehtmlarea_browse_links.php');
//require_once(t3lib_extmgm::extPath('rtehtmlarea').'mod3/class.tx_rtehtmlarea_browse_links.php');
$LANG->includeLLFile('EXT:cag_linkchecker/mod1/locallang.xml');
$BE_USER->modAccess($MCONF, 1);	// This checks permissions and exits if the users has no permission for entry.
require_once (PATH_t3lib . 'class.t3lib_scbase.php');

class tx_caglinkchecker_module1 extends t3lib_SCbase {

	/**
	 * @var template
	 */
	public $doc;

	protected $relativePath;
	protected $pageRecord = array();
	protected $isAccessibleForCurrentUser = false;
	
	protected $searchFields = array(); // array of tables and fields to search for broken links
	protected $pidList = ''; // list of pidlist (rootline downwards)
	protected $linkCounts = array(); // array of tables containing number of external link
	protected $brokenLinkCounts = array(); // array of tables containing number of broken external link
	protected $recordsWithBrokenLinks = array(); // array of tables and records containing broken links
	protected $hookObjectsArr = array(); // array for hooks for own checks
	
	
	// TODO: check if old vars still needed
	protected $pageinfo;
	protected $pid; // Id of actual page
	protected $linkWhere = 'bodytext LIKE \'%<LINK http:%\' AND deleted = 0';
	
	
	/**
	 * Initializes the Module
	 *
	 * @return	void
	 */
	public function initialize() {
		// Hook to handle own checks
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cag_linkchecker']['checkLinks'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cag_linkchecker']['checkLinks'] as $key => $classRef) {
				$this->hookObjectsArr[$key] = &t3lib_div::getUserObj($classRef);
			}
		}

		foreach($this->hookObjectsArr as $key => $hookObj) {
			if (method_exists($hookObj, 'loadLLFile')) {
				$hookObj->loadLLFile();
			}
		}

		parent::init();
		$this->doc = t3lib_div::makeInstance('template');
		$this->doc->setModuleTemplate(t3lib_extMgm::extPath('cag_linkchecker') . 'mod1/mod_template.html');
		$this->doc->backPath = $GLOBALS['BACK_PATH'];

		$this->relativePath = t3lib_extMgm::extRelPath('cag_linkchecker');
		$this->pageRecord = t3lib_BEfunc::readPageAccess($this->id, $this->perms_clause);
		$this->isAccessibleForCurrentUser = (
			$this->id && is_array($this->pageRecord) || !$this->id && $this->isCurrentUserAdmin()
		);

		//don't access in workspace
		if ($GLOBALS['BE_USER']->workspace !== 0) {
			$this->isAccessibleForCurrentUser = false;
		}

		if(!t3lib_div::_GP('checklinks_send') || !is_array(t3lib_div::_GP('checkOption'))) {
			return;
		}
		
		//read configuration
		// TODO: retrieve the right TS Config maybe the following way
		
		// get page TS conf for actual page
		$modTS = t3lib_BEfunc::getModTSconfig($this->id, 'mod.cag_linkchecker');
		$modTS = $modTS['properties'];

		// get the searchFields from TCA
		foreach($GLOBALS['TCA'] as $tablename => $table) {
			if(!empty($table['columns'])) {
				foreach($table['columns'] as $columnname => $column) {
					if($column['config']['type'] == 'text' || $column['config']['type'] == 'input') {
						if(!empty($column['config']['softref']) && (stripos($column['config']['softref'], "typolink") !== FALSE || stripos($column['config']['softref'], "url") !== FALSE)) {
							$this->searchFields[$tablename][] = $columnname;
							//debug($column['config'], $tablename.$columnname);
						}
					}
				}
			}
		}

		// get the searchFields from TypoScript
        if (is_array($modTS['searchFields.'])) {
            foreach ($modTS['searchFields.'] as $table => $fieldList) {
                $fields = t3lib_div::trimExplode(',', $fieldList);
                foreach ($fields as $field) {
                    if (array_key_exists($table, $this->searchFields)) { 
                        if(array_search($field, $this->searchFields[$table]) === FALSE) {
                            $this->searchFields[$table][] = $field;
                        }
                    } else {
                        $this->searchFields[$table][] = $field;
                    }
                }
            }
        }

		$this->pidList = $this->getPidList($this->id);
		
		// actually this is the main function that collects all broken links
		// TODO: maybe separate statistics from collecting... or rename function
		$this->getLinkStatistics();
		
		#t3lib_div::debug($this->searchFields);
		/*
		$modTS = $GLOBALS['BE_USER']->getTSConfig('mod.recycler');
		if ($this->isCurrentUserAdmin()) {
			$this->allowDelete = true;
		} else {
			$this->allowDelete = ($modTS['properties']['allowDelete'] == '1');
		}
		if (isset($modTS['properties']['recordsPageLimit']) && intval($modTS['properties']['recordsPageLimit']) > 0) {
			$this->recordsPageLimit = intval($modTS['properties']['recordsPageLimit']);
		}
		*/
	} // end function initialize()
	
	
	/**
	 * Renders the content of the module.
	 *
	 * @return	void
	 */
	public function render() {
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		if ($this->isAccessibleForCurrentUser) {
			$this->loadHeaderData();
				// div container for renderTo
			#$this->content.= '<div id="caglinkcheckerContent"></div>';
			$this->content = $this->drawBrokenLinksTable();
		} else {
			// If no access or if ID == zero
			$this->content.= $this->doc->spacer(10);
		}
		
	} // end function render()
	
	
	/**
	 * Flushes the rendered content to browser.
	 *
	 * @return	void
	 */
	public function flush() {
		$content = $this->doc->startPage($GLOBALS['LANG']->getLL('title'));
		$content.= $this->doc->moduleBody(
			$this->pageRecord,
			$this->getDocHeaderButtons(),
			$this->getTemplateMarkers()
		);
		$content.= $this->doc->endPage();
		
		// TODO: find out what we need this for - and anyway IF it is needed!
		#$content.= $this->doc->insertStylesAndJS($this->content);

		$this->content = null;
		$this->doc = null;

		echo $content;
		
	} // end function flush()


	protected function getLinkStatistics() {
		$results = array();
		$where = 'deleted = 0 AND pid IN (' . $this->pidList . ')';
		
		// let's traverse all configured tables
		foreach ($this->searchFields as $table => $fields) {
			
			// if table is not configured, we assume the ext is not installed and therefore no need to check it
			if (!is_array($GLOBALS['TCA'][$table])) continue;
			
			// re-init selectFields for table
			$selectFields = 'uid, pid';
			$selectFields.= ', ' . $GLOBALS['TCA'][$table]['ctrl']['label'] . ', ' . implode(', ', $fields);
			
			// TODO: only select rows that have content in at least one of the relevant fields (via OR)
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery($selectFields, $table, $where);
			
			// Get record rows of table
			while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
				#t3lib_div::debug($row);
				
				// array to store urls from relevant field contents
				$urls = array();
				
				// flag whether row contains a broken link in some field or not
				$rowContainsBrokenLink = false;
				
				// put together content of all relevant fields
				$haystack = '';

				// get all references
				foreach ($fields as $field) {
					$haystack.= $row[$field] . ' --- ';
					$conf = $GLOBALS['TCA'][$table]['columns'][$field]['config'];
					if ($conf['softref'] && strlen($row[$field]))	{	// Check if a TCA configured field has softreferences defined (see TYPO3 Core API document)
						$softRefs = t3lib_BEfunc::explodeSoftRefParserList($conf['softref']);		// Explode the list of softreferences/parameters
						foreach($softRefs as $spKey => $spParams)	{	// Traverse soft references
							$softRefObj = &t3lib_BEfunc::softRefParserObj($spKey);	// create / get object
							if (is_object($softRefObj))	{	// If there was an object returned...:
								$resultArray = $softRefObj->findRef($table, $field, $row['uid'], $row[$field], $spKey, $spParams);	// Do processing
								if(!empty($resultArray['elements'])) {
									//debug($resultArray['elements'], $table.':'.$field.':'.$row['uid']);
									foreach($resultArray['elements'] as $element) {
										$r = $element['subst'];
										if(!empty($r)) {
											$type = $r['type'];
											foreach($this->hookObjectsArr as $key => $hookObj) {
												if (method_exists($hookObj, 'fetchType')) {
													$type = $hookObj->fetchType($r, $type);
												}
											}
											$results[$type][$table.':'.$field.':'.$row['uid']]["substr"] = $r;
											$results[$type][$table.':'.$field.':'.$row['uid']]["row"] = $row;
											$results[$type][$table.':'.$field.':'.$row['uid']]["table"] = $table;
											$results[$type][$table.':'.$field.':'.$row['uid']]["field"] = $field;
											$results[$type][$table.':'.$field.':'.$row['uid']]["uid"] = $row['uid'];
										}
									}
								}
							}
						}
					}
				}
			} // end while ($row = ...)
		} // end foreach $table

		//die(debug($results));
		
		foreach($this->hookObjectsArr as $key => $hookObj) {
			$checkOptions = t3lib_div::_GP('checkOption');
			if($results[$key] && $checkOptions[$key]) {
				// ...and count'em and check'em!
				foreach($results[$key] as $entryKey => $entryValue) {
					$table = $entryValue['table'];
					$row = $entryValue['row'];
					$url = $entryValue['substr']['tokenValue'];
					$this->linkCounts[$table]++;

					// check each link
					$checkURL = 1;
					if (method_exists($hookObj, 'checkLink')) {
						$checkURL = $hookObj->checkLink($url, $this);
					}
					
					// broken link found!
					if ($checkURL != 1) {
						$this->brokenLinkCounts[$table]++;
						$row['brokenUrl'] = $url;
						$row['brokenUrlResponse'] = '<span style="color:red">'.$checkURL.'</span>';
						$this->recordsWithBrokenLinks[$table][] = $row;
					} elseif(t3lib_div::_GP('showalllinks')) {
						$this->brokenLinkCounts[$table]++;
						$row['brokenUrl'] = $url;
						$row['brokenUrlResponse'] = '<span style="color:green">OK</span>';
						$this->recordsWithBrokenLinks[$table][] = $row;
					}
				}
			}
		}

		#t3lib_div::debug($this->linkCounts, 'linkCounts');
		#t3lib_div::debug($this->brokenLinkCounts, 'broken linkCounts');
		#t3lib_div::debug($this->recordsWithBrokenLinks, 'broken links');
		
	} // end function getLinkStatistics
	
	
	protected function drawBrokenLinksTable() {

		$content = array();
		
		// table header
		$content[] = $this->startTable();
		
		#t3lib_div::debug($this->recordsWithBrokenLinks);
		
		// table rows containing the broken links
		foreach ($this->recordsWithBrokenLinks as $table => $rows) {
			foreach ($rows as $row) {
				$alter = $alter % 2 ? false : true;
				$content[] = $this->drawTableRow($table, $row, $alter);
			}
 		}
		
		return implode(chr(10), $content);
		
	} // end function drawBrokenLinksTable()
	
	
	protected function startTable() {
		global $LANG;

		// Listing head
		$html = array();
		$html[] = $this->doc->sectionHeader($LANG->getLL('list.header'));
		$html[] = $this->doc->spacer(5);
		$html[] = '<table id="brokenLinksList" border="0" width="100%" cellspacing="1" cellpadding="3" align="center" bgcolor="' . $this->doc->bgColor2 . '">';
		$html[] = '<tr>';
		$html[] = '<td class="head" align="center"></td>';
		$html[] = '<td class="head" align="center"><b>'.$LANG->getLL('list.tableHead.path').'</b></td>';
		$html[] = '<td class="head" align="center"><b>'.$LANG->getLL('list.tableHead.type').'</b></td>';
		$html[] = '<td class="head" align="center"><b>'.$LANG->getLL('list.tableHead.headline').'</b></td>';
		$html[] = '<td class="head" align="center"><b>'.$LANG->getLL('list.tableHead.linktarget').'</b></td>';
		$html[] = '<td class="head" align="center"><b>'.$LANG->getLL('list.tableHead.linkmessage').'</b></td>';
		$html[] = '</tr>';

		return implode(chr(10), $html);
	}
	
	
	protected function drawTableRow($table, $row, $switch) {
		$html = array();
		$params = '&edit[' . $table . '][' . $row['uid'] . ']=edit';
		$actionLinks = '<a href="#" onclick="' . t3lib_BEfunc::editOnClick($params, $GLOBALS['BACK_PATH'], '') . '"><img' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/edit2.gif', 'width="11" height="12"') . ' title="edit" alt="edit" /></a>';
		$elementType = $row[$GLOBALS['TCA'][$table]['ctrl']['label']];

		//Alternating row colors
         if ($switch == true){
             $switch = false;
             $html[] = '<tr bgcolor="' . $this->doc->bgColor3 . '">';
         } elseif($switch == false){
              $switch = true;
              $html[] = '<tr bgcolor="' . $this->doc->bgColor5 . '">';
   		 }

		$html[] = '<td class="content">' . $actionLinks . '</td>';
		$html[] = '<td class="content">' . t3lib_BEfunc::getRecordPath($row['pid'], '', 0, 0) . '</td>';
		#$html[] = '<td class="content">' . $GLOBALS['TCA'][$table]['ctrl']['title'] . '</td>';
		$html[] = '<td class="content" style="text-align: center"><img src="' . t3lib_iconWorks::getIcon($table, $row) . '" alt="' . $elementType . '" title="' . $elementType . '" /></td>';
		$html[] = '<td class="content">' . $elementType . '</td>';
		$html[] = '<td class="content"><a href="' . $row['brokenUrl'] . '" target="_blank">' . $row['brokenUrl'] . '</a></td>';
		$html[] = '<td class="content">' . $row['brokenUrlResponse'] . '</td>';
		$html[] = '</tr>';

		// Return the table html code as string
		return implode(chr(10), $html);
	}
	
	
	/**
	 * Builds the checkboxes out of the hooks array
	 *
	 * @return	void
	 */	
	protected function getCheckOptions() {
		$content = '';
		$checkOptions = t3lib_div::_GP('checkOption') ? t3lib_div::_GP('checkOption') : array();

		if(!empty($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cag_linkchecker']['checkLinks']) && is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cag_linkchecker']['checkLinks'])) {
			foreach($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['cag_linkchecker']['checkLinks'] as $key => $value) {
				$trans = $GLOBALS['LANG']->getLL('hooks.'.$key);
				$trans = $trans ? $trans : $key;
				$checked = "checked";
				if((t3lib_div::_GP('checklinks_send') && !$checkOptions[$key])) {
					$checked = "";
				}
				$singleContent =	'<input type="checkbox" name="checkOption['.$key.']" '. $checked .'>' .
									'<label for="'.$key.'">' . $trans . '</label>';

				$content .= '<div class="cag_linkchecker_singleOption">' . $singleContent . '</div>';
			}
		}
		
		return $content;
	}
	
	
	/**
	 * Loads data in the HTML head section (e.g. JavaScript or stylesheet information).
	 *
	 * @return	void
	 */
	protected function loadHeaderData() {
		// TODO: put needed JS and CSS here
		
			// Load CSS Stylesheets:
		$this->loadStylesheet($this->relativePath . 'res/linkchecker.css');
		/*
			// Load Ext JS:
		$this->doc->getPageRenderer()->loadExtJS();
			// Integrate dynamic JavaScript such as configuration or lables:
		$this->doc->JScode.= t3lib_div::wrapJS('
			Ext.namespace("Recycler");
			Recycler.statics = ' . json_encode($this->getJavaScriptConfiguration()) . ';
			Recycler.lang = ' . json_encode($this->getJavaScriptLabels()) . ';'
		);
			// Load Recycler JavaScript:
		$this->loadJavaScript($this->relativePath . 'res/js/ext_expander.js');
		$this->loadJavaScript($this->relativePath . 'res/js/search_field.js');
		$this->loadJavaScript($this->relativePath . 'res/js/t3_recycler.js');
		*/
	}
	
	
	/**
	 * Loads a stylesheet by adding it to the HTML head section.
	 *
	 * @param	string		$fileName: Name of the file to be loaded
	 * @return	void
	 */
	protected function loadStylesheet($fileName) {
		$fileName = t3lib_div::resolveBackPath($this->doc->backPath . $fileName);
		$this->doc->JScode.= "\t" . '<link rel="stylesheet" type="text/css" href="' . $fileName . '" />' . "\n";
	}
	
	
	/** 
	 * Gets the buttons that shall be rendered in the docHeader.
	 *
	 * @return	array		Available buttons for the docHeader
	 */
	protected function getDocHeaderButtons() {
		$buttons = array(
			'csh'		=> t3lib_BEfunc::cshItem('_MOD_web_func', '', $GLOBALS['BACK_PATH']),
			'shortcut'	=> $this->getShortcutButton(),
			'save'		=> ''
		);

			// SAVE button
		$buttons['save'] = ''; //<input type="image" class="c-inputButton" name="submit" value="Update"' . t3lib_iconWorks::skinImg($GLOBALS['BACK_PATH'], 'gfx/savedok.gif', '') . ' title="' . $GLOBALS['LANG']->sL('LLL:EXT:lang/locallang_core.php:rm.saveDoc', 1) . '" />';

		return $buttons;
	}

	
	/**
	 * Gets the button to set a new shortcut in the backend (if current user is allowed to).
	 *
	 * @return	string		HTML representiation of the shortcut button
	 */
	protected function getShortcutButton() {
		$result = '';
		if ($GLOBALS['BE_USER']->mayMakeShortcut()) {
			$result = $this->doc->makeShortcutIcon('', 'function', $this->MCONF['name']);
		}
		return $result;
	}

	
	/**
	 * Gets the filled markers that are used in the HTML template.
	 *
	 * @return	array		The filled marker array
	 */
	protected function getTemplateMarkers() {
		$markers = array(
			'FUNC_MENU'				=> $this->getFunctionMenu(),
			'CONTENT'				=> $this->content,
			'TITLE'					=> $GLOBALS['LANG']->getLL('title'),
			'LABEL_SEND'			=> $GLOBALS['LANG']->getLL('label_send'),
			'LABEL_SHOWALLLINKS'	=> $GLOBALS['LANG']->getLL('label_showalllinks'),
			'SHOWALLLINKS_CHECKED'	=> t3lib_div::_GP('showalllinks') ? 'checked' : '',
			'CHECKOPTIONS'			=> $this->getCheckOptions(),
		);
		return $markers;
	}
	
	
	/**
	 * Gets the function menu selector for this backend module.
	 *
	 * @return	string		The HTML representation of the function menu selector
	 */
	protected function getFunctionMenu() {
		return t3lib_BEfunc::getFuncMenu(
			0,
			'SET[function]',
			$this->MOD_SETTINGS['function'],
			$this->MOD_MENU['function']
		);
	}
	
	
	/**
	 * Determines whether the current user is admin.
	 *
	 * @return	boolean		Whether the current user is admin
	 */
	protected function isCurrentUserAdmin() {
		return (bool)$GLOBALS['BE_USER']->user['admin'];
	}
	
	
	/**
	 * Gets a list pages that belong to $pid
	 *
	 * @param	integer		ID of page (start of branch)
	 * @return	string		Comma separated list with all IDs belongin to $pid
	 */
	protected function getPidList($pid) {

		// Pidlist (comma separated) that is returned
		$pidList = $pid;

		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid', 'pages', 'pid = '.$pid.' AND deleted = 0');

		while ($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$pidList.= ','.$this->getPidList($row['uid']);
		}

		return $pidList;
	
	} // end function getPidList($pid)
	
} // end class


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cag_linkchecker/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/cag_linkchecker/mod1/index.php']);
}


// Make instance:
$SOBE = t3lib_div::makeInstance('tx_caglinkchecker_module1');
$SOBE->initialize();

// Include files?
foreach($SOBE->include_once as $INC_FILE) {
	include_once($INC_FILE);
}

$SOBE->render();
$SOBE->flush();

?>
