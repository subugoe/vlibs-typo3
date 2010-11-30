<?php

/***************************************************************
*  Copyright notice
*  
*  © 2003 Boris Nicolai (boris.nicolai@andavida.com)
*  © 2009 Sigfried Arnold (s.arnold@rebell.at)
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
***************************************************************/

require_once(t3lib_extMgm::extPath('autometa') . 'pi1/class.tx_autometa_pi1.php');
class tx_autometa_fehook extends tslib_pibase {
	function intPages(&$params, &$that) {
		if (!$GLOBALS['TSFE']->isINTincScript()) { return; }
		$tx_autometa_pi1 = t3lib_div::makeInstance('tx_autometa_pi1');
		$tx_autometa_pi1->main (
			$params['pObj']->content,
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['autometa.'],
			unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['autometa'])
		);
	}
	
	function noIntPages(&$params,&$that) {
		if ($GLOBALS['TSFE']->isINTincScript()) { return; }
		$tx_autometa_pi1 = t3lib_div::makeInstance('tx_autometa_pi1');
		$tx_autometa_pi1->main (
			$params['pObj']->content,
			$GLOBALS['TSFE']->tmpl->setup['plugin.']['autometa.'],
			unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['autometa'])
		);
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/autometa/class.tx_autometa_fehook.php']){
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/autometa/class.tx_autometa_fehook.php']);
}
?>