<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2003-2005 Kasper Skaarhoj (kasper@typo3.com)
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
 * Adding content to display in top frame; Listing of links for developers
 *
 * @author	Kasper Skaarhoj <kasper@typo3.com>
 * @coauthor	Michael Stucki <michael@typo3.org>
 * @package TYPO3
 * @subpackage tx_extdeveval
 */
class tx_extdeveval_altTopMenuDummy {
	function fetchContentTopmenu_processContent (&$pObj)	{
		$output='';

		if ($GLOBALS['BE_USER']->isAdmin())	{
				// Render the links from the script options in TYPO3_CONF_VARS
			$links=array();
			if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/extdeveval/class.ux_sc_alt_topmenu_dummy.php']['links'])) {
				foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['ext/extdeveval/class.ux_sc_alt_topmenu_dummy.php']['links'] as $linkConf) {
					$aOnClick = "return top.openUrlInWindow('".$linkConf[1]."','ShowAPI');";
					$links[]='<a href="#" onclick="'.htmlspecialchars($aOnClick).'">'.htmlspecialchars($linkConf[0]).'</a>';
				}
			}

			$output.='<strong>Dev links:</strong> ' . implode(' | ',$links);
		}

		return $output;
	}
}

?>
