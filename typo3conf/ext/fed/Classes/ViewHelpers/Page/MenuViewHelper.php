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
 * ViewHelper for rendering TYPO3 menus in Fluid
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers/Page
 */
class Tx_Fed_ViewHelpers_Page_MenuViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

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
		$this->registerArgument('entryLevel', 'integer', 'Optional entryLevel TS equivalent of the menu', FALSE, 0);
		$this->registerArgument('pageUid', 'integer', 'Optional parent page UID to use as top level of menu. If left out will be detected from rootLine using $entryLevel', FALSE, NULL);
		$this->registerArgument('classActive', 'string', 'Optional class name to add to active links', FALSE, 'active');
		$this->registerArgument('classCurrent', 'string', 'Optional class name to add to current link', FALSE, 'current');
		$this->registerArgument('classHasSubpages', 'string', 'Optional class name to add to links which have subpages', FALSE, 'sub');
		$this->registerArgument('useShortcutTarget', 'boolean', 'Optional param for using shortcut target instead of shortcut itself for current link', FALSE, FALSE);
		$this->registerArgument('classFirst', 'string', 'Optional class name for the first menu elment', FALSE, '');
		$this->registerArgument('classLast', 'string', 'Optional class name for the last menu elment', FALSE, '');
		$this->registerArgument('substElementUid', 'boolean', 'Optional parameter for wrapping the link with the uid of the page', FALSE, '');
		$this->registerArgument('includeSpacers', 'boolean', 'Wether or not to include menu spacers in the page select query', FALSE, FALSE);
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$this->tagName = $this->arguments['tagName'];
		$this->pageSelect = new t3lib_pageSelect();
		$pageUid = $this->arguments['pageUid'];
		$entryLevel = $this->arguments['entryLevel'];
		$rootLine = $this->pageSelect->getRootLine($GLOBALS['TSFE']->id);
		if (!$pageUid) {
			$pageUid = $rootLine[$entryLevel]['uid'];
		}
		$menu = $this->pageSelect->getMenu($pageUid);
		$menu = $this->parseMenu($menu, $rootLine);
		$backupVars = array('menu', 'rootLine');
		$backups = array();
		foreach ($backupVars as $var) {
			if ($this->templateVariableContainer->exists($var)) {
				$backups[$var] = $this->templateVariableContainer->get($var);
				$this->templateVariableContainer->remove($var);
			}
		}
		$this->templateVariableContainer->add('menu', $menu);
		$this->templateVariableContainer->add('rootLine', $rootLine);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove('menu');
		$this->templateVariableContainer->remove('rootLine');
		if (strlen(trim($content)) === 0) {
			$content = $this->autoRender($menu, $rootLine);
			$this->tag->setContent($content);
			$this->tag->forceClosingTag(TRUE);
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
	 * Automatically render a menu
	 *
	 * @param array $menu
	 * @param array $rootLine
	 */
	protected function autoRender($menu, $rootLine) {
		$tagName = $this->arguments['tagNameChildren'];
		$substElementUid = $this->arguments['substElementUid'];
		$html = array();
		foreach ($menu as $key => $page) {
			$class = trim($page['class'])!='' ? ' class="' . $page['class'] . '"' : '';
			$elementID = $substElementUid ? ' id="elem_' . $page['uid'] . '"' : '';
			$html[] = '<' . $tagName . $elementID . $class .'><a href="' . $page['link'] . '"' . $class . '>' . $page['title'] . '</a></' . $tagName . '>';
			$i++;
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
	 * @param integer $pageUid
	 * @param array $rootLine
	 */
	protected function isCurrent($pageUid, $rootLine) {
		return $pageUid == $GLOBALS['TSFE']->id;
	}

	/**
	 * @param pageUid $pageUid
	 * @param array $rootLine
	 */
	protected function isActive($pageUid, $rootLine) {
		foreach ($rootLine as $page) {
			if ($page['uid'] == $pageUid) {
				return TRUE;
			}
		}
		return FALSE;
	}

	/**
	 * Create the href of a link for page $pageUid
	 *
	 * @param integer $pageUid
	 * @param array $rootLine
	 * @param integer $stortcut
	 * @param integer $doktype
	 * @return string
	 */
	protected function getItemLink($pageUid, $rootLine, $shortcut, $doktype) {
		if ($this->arguments['useShortcutTarget'] && ($doktype == constant('t3lib_pageSelect::DOKTYPE_SHORTCUT') || $doktype == constant('t3lib_pageSelect::DOKTYPE_LINK'))) {
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
	 * Get the combined item CSS class based on menu item state and VH arguments
	 *
	 * @param array $pageRow
	 * @return array
	 */
	protected function getItemClass($pageRow) {
		$class = array();
		if ($pageRow['active']) {
			$class[] = $this->arguments['classActive'];
		}
		if ($pageRow['current']) {
			$class[] = $this->arguments['classCurrent'];
		}
		if ($pageRow['hasSubPages']) {
			$class[] = $this->arguments['classHasSubpages'];
		}
		return $class;
	}

	/**
	 * Get a list from allowed doktypes for pages
	 *
	 * @return array
	 */
	protected function allowedDoktypeList() {
		$types = array(
			constant('t3lib_pageSelect::DOKTYPE_DEFAULT'),
			constant('t3lib_pageSelect::DOKTYPE_LINK'),
			constant('t3lib_pageSelect::DOKTYPE_SHORTCUT'),
			constant('t3lib_pageSelect::DOKTYPE_MOUNTPOINT')
		);
		if ($this->arguments['includeSpacers']) {
			array_push($types, constant('t3lib_pageSelect::DOKTYPE_SPACER'));
		}
		return $types;
	}

	/**
	 * Filter the fetched menu according to visibility etc.
	 *
	 * @param array $menu
	 * @param array $rootLine
	 * @return array
	 */
	protected function parseMenu($menu, $rootLine) {
		$classFirst = $this->arguments['classFirst'];
		$classLast = $this->arguments['classLast'];
		$filtered = array();
		foreach ($menu as $page) {
			$pageUid = $page['uid'];
			$doktype = $page['doktype'];
			$shortcut = ($doktype == constant('t3lib_pageSelect::DOKTYPE_SHORTCUT')) ? $page['shortcut'] : $page['url'];
			if ($page['hidden'] == 1) {

			} else if ($page['nav_hide'] == 1) {

			} else if (in_array($doktype, $this->allowedDoktypeList())) {
				$page['active'] = $this->isActive($pageUid, $rootLine);
				$page['current'] = $this->isCurrent($pageUid, $rootLine);
				$page['hasSubPages'] = (count($this->pageSelect->getMenu($pageUid)) > 0) ? 1 : 0;
				$page['link'] = $this->getItemLink($pageUid, $rootLine, $shortcut, $doktype);
				$page['class'] = implode(' ', $this->getItemClass($page));
				$page['title'] = $this->getNavigationTitle($pageUid);
				$page['doktype'] = $doktype;
				$filtered[] = $page;
			}
		}
		if($classFirst) {
			$filtered[0]['class'] = trim($filtered[0]['class'] . ' ' . $classFirst);
		}
		if($classLast) {
			$length = count($filtered);
			$filtered[$length-1]['class'] = trim($filtered[$length-1]['class'] . ' ' . $classLast);
		}
		return $filtered;
	}

}
?>
