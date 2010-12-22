<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Christian Bülter <buelter@kennziffer.com>
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
 * This class helps generating menus for the typo3 backend, fx. nice tab menus
 *
 * @author	Christian Bülter <buelter@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_kestats
 */

define (NULL_VALUE, 'EMPTYVALUE');

class backendMenu {
	var $menuNames = array();

	/**
	 * Generates a TYPO3 backend module tab menu
	 * allowedValues: see getSelectedValue
	 * 
	 * @param mixed $menuArray 
	 * @param string $selectedValue 
	 * @param string $menuName 
	 * @param string $additionalParams
	 * @param array $allowedValues
	 * @return string
	 */
	function generateTabMenu($menuArray,$menuName='default',$additionalParams='',$allowedValues=array()) {/*{{{*/
		foreach ($this->menuNames as $name) {
			if ($name != $menuName) {
				$additionalParams .= $this->getSelectedValueAsMenuParam($name,$allowedValues);
			}
		}
		$selectedValue = $this->getSelectedValue($menuName,$allowedValues);
		$menuHTML = '<ul class="basictab">';
		foreach ($menuArray as $menuValue => $menuDescription) {
			$menuHTML .= '<li ';
			if ($selectedValue == $menuValue) $menuHTML .= 'class="selected" ';
			$menuHTML .= '><a ';
			$menuHTML .= 'href="index.php?id='.t3lib_div::_GET('id').'&'.$menuName.'='.$menuValue;
			$menuHTML .= $additionalParams;
			$menuHTML .= '">';
			$menuHTML .= $menuDescription;
			$menuHTML .= '</a></li>';
		}
		$menuHTML .= '</ul>';
		return $menuHTML;
	}/*}}}*/

	/**
	 * generateLinkMenu 
	 *
	 * Generate a menu with simple links
	 *
	 * this is important for the csv export links:
	 * additionalParams are APPENDED so we can use them to overwrite given values
	 * adds a "descr"-parameter to the link which contains the linktext
	 * 
	 * @param mixed $menuArray 
	 * @param string $menuName 
	 * @param string $additionalParams 
	 * @param array $allowedValues 
	 * @access public
	 * @return string
	 */
	function generateLinkMenu($menuArray,$menuName='default',$additionalParams='',$allowedValues=array()) {/*{{{*/
		foreach ($this->menuNames as $name) {
			if ($name != $menuName) {
				$additionalParams = $this->getSelectedValueAsMenuParam($name,$allowedValues) . $additionalParams;
			}
		}
		foreach ($menuArray as $menuValue => $menuDescription) {
			$menuHTML .= '<a ';
			$menuHTML .= 'href="index.php?id=' . t3lib_div::_GET('id') . '&' . $menuName . '=' . $menuValue;
			$menuHTML .= '&' . $additionalParams;
			$menuHTML .= '&descr=' . $menuDescription;
			$menuHTML .= '">';
			$menuHTML .= $menuDescription;
			$menuHTML .= '</a><br />';
		}
		return $menuHTML;
	}/*}}}*/

	/**
	 * Generates a TYPO3 backend module dropdown menu
	 * 
	 * @param mixed $menuArray 
	 * @param string $selectedValue 
	 * @param string $menuName 
	 * @param string $additionalParams
	 * @return string
	 */
	function generateDropDownMenu($menuArray,$menuName='default',$additionalParams='') {/*{{{*/
		foreach ($this->menuNames as $name) {
			if ($name != $menuName) {
				$additionalParams .= $this->getSelectedValueAsMenuParam($name);
			}
		}

		// extract the allowed values from the menu-array
		$allowedValues = array();
		foreach ($menuArray as $menuValue => $menuDescription) {
			// transform 0 to NULL_VALUE
			// otherwise 0 would not be a possible value to select in the form
			if ($menuValue==0) {
				$menuValue = NULL_VALUE;
			}
			$allowedValues[] = $menuValue;
		}

		// get the selected value for this menu
		$selectedValue = $this->getSelectedValue($menuName,$allowedValues);

		// render the form
		$formURL = 'index.php?id='.t3lib_div::_GET('id').$additionalParams;
		$formName = 'form_'.$menuName;
		$menuHTML = '<form name="'.$formName.'" id="'.$formName.'" method="post" action="'.$formURL.'">';
		$menuHTML .= '<select name="'.$menuName.'" size="1" class="basicselect" onchange="document.'.$formName.'.submit();">';
		foreach ($menuArray as $menuValue => $menuDescription) {
			$menuHTML .= '<option ';
			if ($selectedValue == $menuValue) $menuHTML .= 'class="selected" selected ';
			// transform 0 to NULL_VALUE
			// otherwise 0 would not be a possible value to select in the form
			if ($menuValue==0) {
				$menuValue = NULL_VALUE;
			}
			$menuHTML .= 'value="'.$menuValue.'">';
			$menuHTML .= $menuDescription;
			$menuHTML .= '</option>';
		}
		$menuHTML .= '</select>';
		$menuHTML .= '</form>';
		return $menuHTML;
	}/*}}}*/

	/**
	 * Returns the selected value from a given menu
	 * if the allowedValues array is given, the selected value will be checked against its values
	 * if the value does not exist in the array, the first value in the array will be selected
	 * 
	 * @param string $menuName 
	 * @param array $menuArray 
	 * @param bool $transformNullValue 
	 * @return void
	 */
	function getSelectedValue($menuName='default',$allowedValues=array(), $transformNullValue=1) {/*{{{*/
		$value = t3lib_div::_GP($menuName);
		if (empty($value) && $this->tabmenuPresetValues[$menuName]) $value = $this->tabmenuPresetValues[$menuName];

		// check if the selected value really exists in the options
		// otherwise select the first value
		if (sizeof($allowedValues) > 0) {
			if (!in_array($value,$allowedValues)) {
				$value = $allowedValues[0];
			}
		}

		// transform NULL_VALUE back to 0
		if ($value == NULL_VALUE && $transformNullValue) {
			$value = 0;
		}

		return $value;
	}/*}}}*/

	/**
	 * Returns the select value of a menu
	 * 
	 * @param string $menuName 
	 * @param array $allowedValues
	 * @return string
	 */
	function getSelectedValueAsMenuParam($menuName='default',$allowedValues=array()) {/*{{{*/
		$value = $this->getSelectedValue($menuName,$allowedValues,0);
		// transform 0 to NULL_VALUE
		// otherwise 0 would not be a possible value to select in the form
		/*
		if ($value==0) {
			$value = NULL_VALUE;
		}
		*/
		return '&'.$menuName.'='.$value;
	}/*}}}*/

	/**
	 * presets values for a given tab menu
	 * 
	 * @param mixed $value 
	 * @param string $menuName 
	 * @access public
	 * @return void
	 */
	function initMenu($menuName='default',$value='') {/*{{{*/
		$this->menuNames[] = $menuName;
		$this->tabmenuPresetValues[$menuName] = $value;
	}/*}}}*/

	/**
	 * sets some additional style sheets
	 * 
	 * @return string
	 */
	function getStyleSheet() {/*{{{*/
		return '

/*Tab Menu Styles*/
/*Credits: Dynamic Drive CSS Library */
/*URL: http://www.dynamicdrive.com/style/ */

.basicselect {
	float:left;
	margin: .5em 3px 0 0;
	font-size: 10px;
	font-weight: bold; 
	border-bottom: 1px solid gray;
}

.basictab {
	clear:left;
	padding: 3px 0;
	margin: .5em 0 0 0;
	font-size: 10px;
	font-weight: bold; 
	border-bottom: 1px solid gray;
	list-style-type: none;
	text-align: left; 
}

.basictab li {
	display: inline;
	margin: 0;
}

.basictab li a {
	text-decoration: none;
	padding: 3px 7px;
	margin-right: 3px;
	border: 1px solid gray;
	border-bottom: none;
	background-color: #E2E2E9;
	color: #2d2b2b;
}

.basictab li a:visited {
	color: #2d2b2b;
}

.basictab li a:hover {
	background-color: #B7B7CC;
	color: black;
}

.basictab li a:active {
	color: black;
}

.basictab li.selected a { 
	position: relative;
	top: 1px;
	padding-top: 4px;
	background-color: #B7B7CC;
	color: black;
}';
	}/*}}}*/
}
?>
