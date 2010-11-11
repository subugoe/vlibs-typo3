<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2005 Juraj Sulek (juraj@sulek.sk)
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
 * Plugin realurlmanagement.
 *
 * $Id: class.tx_SC_mod_tools_log_index.php,v 0.2.0 2005/28/12 20:00:00 typo3 Exp $
 *
 * @author	Juraj Sulek <juraj@sulek.sk>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *
 *
 *
 *   46: class ux_SC_mod_tools_log_index extends SC_mod_tools_log_index
 *   53:     function menuConfig()
 *
 * TOTAL FUNCTIONS: 1
 * (This index is automatically created/updated by the extension "extdeveval")
 *
 */
	class ux_SC_mod_tools_log_index extends SC_mod_tools_log_index {

	/**
	 * Menu configuration
	 *
	 * @return	void
	 */
	function menuConfig()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

			// MENU-ITEMS:
			// If array, then it's a selector box menu
			// If empty string it's just a variable, that'll be saved.
			// Values NOT in this array will not be saved in the settings-array for the module.
		$this->MOD_MENU = array(
			'users' => array(
				0 => 'All users',
				'-1' => 'Self'
			),
			'time' => array(
				0 => 'This week',
				1 => 'Last week',
				2 => 'Last 7 days',
				10 => 'This month',
				11 => 'Last month',
				12 => 'Last 31 days',
				20 => 'No limit'
			),
			'max' => array(
				20 => '20',
				50 => '50',
				100 => '100',
				200 => '200',
				500 => '500'
			),
			'action' => array(
				0 => 'All',
				1 => 'Database',
				2 => 'File',
				33 => 'RealUrl',
				254 => 'Settings',
				255 => 'Login',
				'-1' => 'Errors'
			)
		);

			// Adding groups to the users_array
		$groups = t3lib_BEfunc::getGroupNames();
		if (is_array($groups))	{
			while(list(,$grVals)=each($groups))	{
				$this->MOD_MENU['users'][$grVals['uid']] = 'Group: '.$grVals['title'];
			}
		}

			// CLEANSE SETTINGS
		$this->MOD_SETTINGS = t3lib_BEfunc::getModuleData($this->MOD_MENU, t3lib_div::_GP('SET'), $this->MCONF['name']);
	}
	}

	if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/realurlmanagement/class.ux_SC_mod_tools_log_index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/realurlmanagement/class.ux_SC_mod_tools_log_index.php']);
}
?>