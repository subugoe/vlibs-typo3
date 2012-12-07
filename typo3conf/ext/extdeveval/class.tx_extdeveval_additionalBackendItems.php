<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2008 Steffen Kamper <info@sk-typo3.de>
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
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/
/**
 * Adding developer links to toolbar of backend.php
 *
 * @author	Steffen Kamper <info@sk-typo3.de>
 * @package TYPO3
 * @subpackage tx_extdeveval
 */
class tx_extdevevalDevLinks implements backend_toolbarItem {

	/**
	 * reference back to the backend object
	 *
	 * @var	TYPO3backend
	 */
	private $backendReference;

	private $docLinks;
	private $EXTKEY = 'extdeveval';

	/**
	 * constructor
	 *
	 * @return	void
	 */
	public function __construct(TYPO3backend &$backendReference = null) {
		$this->backendReference = $backendReference;
		$this->docLinks = array(
			array('t3lib', t3lib_extMgm::extRelPath($this->EXTKEY).'apidocs/t3lib_api.html','gfx/i/tt_bookstore_books.gif'),
			array('div', t3lib_extMgm::extRelPath($this->EXTKEY).'apidocs/t3lib_div.html','gfx/i/tt_bookstore_books.gif'),
			array('extMgm', t3lib_extMgm::extRelPath($this->EXTKEY).'apidocs/t3lib_extmgm.html','gfx/i/tt_bookstore_books.gif'),
			array('BEfunc', t3lib_extMgm::extRelPath($this->EXTKEY).'apidocs/t3lib_befunc.html','gfx/i/tt_bookstore_books.gif'),
			array('DB', t3lib_extMgm::extRelPath($this->EXTKEY).'apidocs/t3lib_db.html','gfx/i/tt_bookstore_books.gif'),
			array('template', t3lib_extMgm::extRelPath($this->EXTKEY).'apidocs/typo3_template.html','gfx/i/tt_bookstore_books.gif'),
			array('lang', t3lib_extMgm::extRelPath($this->EXTKEY).'apidocs/typo3_lang.html','gfx/i/tt_bookstore_books.gif'),

				// Frontend:
			array('pibase', t3lib_extMgm::extRelPath($this->EXTKEY).'apidocs/tslib_pibase_api.html','gfx/i/tt_bookstore_books.gif'),
			array('cObj', t3lib_extMgm::extRelPath($this->EXTKEY).'apidocs/tslib_content_api.html','gfx/i/tt_bookstore_books.gif'),

			array('TSref', 'http://typo3.org/documentation/document-library/references/doc_core_tsref/current/view/','gfx/i/link.gif'),
			array('TSConfig', 'http://typo3.org/documentation/document-library/references/doc_core_tsconfig/current/view/','gfx/i/link.gif'),
			array('CoreTS', 'http://typo3.org/documentation/document-library/core-documentation/doc_core_ts/current/view/','gfx/i/link.gif'),
			array('CoreAPI', 'http://typo3.org/documentation/document-library/core-documentation/doc_core_api/current/view/','gfx/i/link.gif'),

				// TYPO3.org
			array('TYPO3.org', 'http://typo3.org/','gfx/i/link.gif'),
		);
	}


	/**
	 * sets the backend reference
	 *
	 * @param TYPO3backend backend object reference
	 */
	public function setBackend(&$backendReference) {
		$this->backendReference = $backendReference;
	}

	public function render() {
		$this->addJavascriptToBackend();
		$this->addCssToBackend();

		$devLinks = array();

		$devLinks[] = '<a href="#" class="toolbar-item">&nbsp;<img'.t3lib_iconWorks::skinImg($this->backPath,t3lib_extMgm::extRelPath($this->EXTKEY).'bomb.png','width="16" height="16"').' title="Developer Links" alt="" /></a>';
		$devLinks[] = '<ul class="toolbar-item-menu" style="display: none;">';

		foreach($this->docLinks as $linkConf)	{
			$icon = '<img'.t3lib_iconWorks::skinImg($this->backPath, $linkConf[2], 'width="16" height="16"').' title="'.$linkConf[0].'" alt="" /> ';
			$devLinks[] = '<li><a href="'.$linkConf[1].'" target="_blank">'.$icon.htmlspecialchars($linkConf[0]).'</a></li>';
		}
		$devLinks[] = '</ul>';

		return implode("\n", $devLinks);

	}

	/**
	 * returns additional attributes for the list item in the toolbar
	 *
	 * @return	string		list item HTML attibutes
	 */
	public function getAdditionalAttributes() {
		return ' id="dev-links-actions-menu"';
	}

	/**
	 * adds the neccessary javascript ot the backend
	 *
	 * @return	void
	 */
	private function addJavascriptToBackend() {
		$this->backendReference->addJavascriptFile(t3lib_extMgm::extRelPath($this->EXTKEY).'devlinks.js');
	}

	private function addCssToBackend() {
		$this->backendReference->addCssFile('extdeveval',t3lib_extMgm::extRelPath($this->EXTKEY).'devlinks.css');
	}

	/**
	 * Checks if user has access to this item
	 *
	 * @return	boolean	true if has access
	 */
	public function checkAccess() {
		return $GLOBALS['BE_USER']->isAdmin();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['Ext:extdeveval/class.tx_extdeveval_additionalBackendItems.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['Ext:extdeveval/class.tx_extdeveval_additionalBackendItems.php']);
}


?>