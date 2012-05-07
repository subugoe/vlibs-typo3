<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
 *
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
 * ************************************************************* */

/**
 * ViewHelper to make a breadcrumb link set from a pageUid, automatic or manual
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers/Page
 */
class Tx_Fed_ViewHelpers_Page_BreadCrumbViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'ul';

	/**
	 * @var t3lib_pageSelect
	 */
	protected $pageSelect;

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerArgument('tagName', 'string', 'Tag name to use for enclsing container', FALSE, 'ul');
		$this->registerArgument('tagNameChildren', 'string', 'Tag name to use for child nodes surrounding links', FALSE, 'li');
		$this->registerArgument('entryLevel', 'integer', 'Optional entryLevel TS equivalent of the breadcrumb trail', FALSE, 0);
		$this->registerArgument('pageUid', 'integer', 'Optional parent page UID to use as start of breadcrumbtrail/rootline - if left out, $GLOBALS[TSFE]->id is used', FALSE, NULL);
		$this->registerArgument('bullet', 'string', 'Piece of text/html to insert before each item', FALSE);
		$this->registerArgument('useShortcutTarget', 'boolean', 'Optional param for using shortcut target instead of shortcut itself for current link', FALSE, FALSE);
		$this->registerArgument('resolveExclude', 'boolean', 'Exclude link if realurl/cooluri flag tx_realurl_exclude is set', FALSE, FALSE);
	}

	/**
	 * @return string
	 */
	public function render() {
		$this->tagName = $this->arguments['tagName'];
		$this->pageSelect = new t3lib_pageSelect();
		$pageUid = $this->arguments['pageUid'];
		$entryLevel = $this->arguments['entryLevel'];
		$rootLine = $this->pageSelect->getRootLine($GLOBALS['TSFE']->id);
		$rootLine = array_reverse($rootLine);
		$rootLine = array_slice($rootLine, $this->arguments['entryLevel']);
		$rootLine = $this->parseMenu($rootLine);
		$backupVars = array('rootLine', 'page');
		$backups = array();
		foreach ($backupVars as $var) {
			if ($this->templateVariableContainer->exists($var)) {
				$backups[$var] = $this->templateVariableContainer->get($var);
				$this->templateVariableContainer->remove($var);
			}
		}
		$this->templateVariableContainer->add('rootLine', $rootLine);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove('rootLine');
		if (strlen(trim($content)) === 0) {
			$content = $this->autoRender($rootLine);
			$this->tag->setContent($content);
			$content = $this->tag->render();
		}
		if (count($backups) > 0) {
			foreach ($backups as $var=>$value) {
				$this->templateVariableContainer->add($var, $value);
			}
		}
		return $content;
	}

	/**
	 * Use default rendering approach
	 *
	 * @param array $rootLine
	 * @return string
	 */
	protected function autoRender($rootLine) {
		$tagName = $this->arguments['tagNameChildren'];
		$html = array();
		foreach ($rootLine as $page) {
			$link = $this->getItemLink($page['uid']);
			$class = $page['class'] ? ' class="' . $page['class'] . '"' : '';
			$html[] = '<' . $tagName . $class .'>' . $this->arguments['bullet'] . '<a href="' . $page['url'] . '"' . $class . '>' . $page['title'] . '</a></' . $tagName . '>';
		}
		return implode(LF, $html);
	}

	/**
	* Select the navigation title
	*
	* @param integer $pageUid
	* return string
	*/
	protected function getNavigationTitle($pageUid) {
		$getLL = t3lib_div::_GP('L');
		if($getLL){
			$pageOverlay = $this->pageSelect->getPageOverlay($pageUid,$getLL);
			$title = ($pageOverlay['nav_title']) ? $pageOverlay['nav_title'] : $pageOverlay['title'];
		}else {
			$page = $this->pageSelect->getPage($pageUid);
			$title = ($page['nav_title']) ? $page['nav_title'] : $page['title'];
		}
		return $title;
	}

	/**
	* Get a list from allowed doktypes for pages
	*
	* @return array
	*/
	protected function allowedDoktypeList() {
		return array(
		constant('t3lib_pageSelect::DOKTYPE_DEFAULT'),
		constant('t3lib_pageSelect::DOKTYPE_LINK'),
		constant('t3lib_pageSelect::DOKTYPE_SHORTCUT'),
		constant('t3lib_pageSelect::DOKTYPE_MOUNTPOINT')
		);
	}

	/**
	 * Create the href of a link for page $pageUid
	 *
	 * @param integer $pageUid
	 * @param integer $doktype
	 * @return string
	 */
	protected function getItemLink($pageUid, $doktype) {
		if ($this->arguments['useShortcutTarget'] && ($doktype == constant('t3lib_pageSelect::DOKTYPE_SHORTCUT') || $doktype == constant('t3lib_pageSelect::DOKTYPE_LINK'))) {
			$pageArray = $this->pageSelect->getPage_noCheck($pageUid);
			$shortcut = ($doktype == constant('t3lib_pageSelect::DOKTYPE_SHORTCUT')) ? $pageArray['shortcut'] : $pageArray['url'];
			$pageUid = $shortcut;
		}
		$config = array(
			'parameter' => $pageUid,
			'returnLast' => 'url',
			'additionalParams' => '',
			'useCacheHash' => FALSE
		);
		return $GLOBALS['TSFE']->cObj->typoLink('', $config);
	}

	/**
	* Filter the fetched menu according to visibility etc.
	*
	* @param array $rootLine
	* @return array
	*/
	protected function parseMenu(array $rootLine) {
		$filtered = array();
		foreach($rootLine as $key => $val) {
			$doktype = $val['doktype'];
			$pageUid = $val['uid'];
			$exclude = ($val['tx_realurl_exclude'] && $this->arguments['resolveExclude']) ? TRUE : FALSE;
			if (in_array($doktype, $this->allowedDoktypeList()) && !$exclude) {
				$rootArr['uid'] = $pageUid;
				$rootArr['title'] = $this->getNavigationTitle($pageUid);
				$rootArr['url'] = $this->getItemLink($pageUid, $doktype);
				$filtered[] = $rootArr;
			}
		}
		return $filtered;
	}

}
?>