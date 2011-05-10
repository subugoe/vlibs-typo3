<?php

/**
 * Class to switch between different controllers controlled by a TypoScript.
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2007 Elmar Hinz
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package    TYPO3
 * @subpackage lib
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @copyright  2006-2007 Elmar Hinz
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    SVN: $Id: class.tx_lib_switch.php 7333 2007-11-30 12:21:48Z elmarhinz $
 * @since      0.1
 */

/**
 * Class to switch between different controllers controlled by a TypoScript.
 *
 * - You can also switch to different actions within the
 *   same controller class.
 * - You may even switch to the same controller or action as
 *   USER or USER_INT alternatively.
 *
 * It is a technology to enable a mix of cached and non cached
 * TS objects (USER and USER_INT) within one wrapping plugin.
 * The selection is done by a flexform. This enables the possiblity
 * to split your plugins in multiple parts of USER and USER_INT.
 * with few effort and to do caching more accurate by this.
 *
 * This switch is an option for experienced developers and
 * specially usefull for bigger projects. For beginners we recommend
 * to simply use different plugins within one extension without
 * this switch.
 *
 * The usage of this technology is demonstrated by the extension efaq.
 *
 * <code>
 * // Including the required classes
 * includeLibs.tx_lib_switch = EXT:lib/class.tx_lib_switch.php
 * includeLibs.tx_efaq_controllers_faq = EXT:efaq/controllers/class.tx_efaq_controllers_faq.php
 * includeLibs.tx_efaq_controllers_search = EXT:efaq/controllers/class.tx_efaq_controllers_search.php
 *
 * // Configuring the switch
 * plugin.tx_efaq.controllerSwitch = USER
 * plugin.tx_efaq.controllerSwitch {
 *   userFunc = tx_lib_switch->main
 *   userFunc {
 *     // List of questions (cached)
 *     questions = USER
 *     questions {
 *       userFunc = tx_faq_controllers_faq->main
 *       // Fix action parameter
 *       action = listQuestions
 *  		}
 *     // List of answers (cached)
 *     answers = USER
 *     answers {
 *       userFunc = tx_efaq_controllers_faq->main
 *       // Fix action parameter
 *       action = listAnswers
 *     }
 *     // Searchform with resultlist (non cached)
 *     search = USER_INT
 *     search {
 *       userFunc = tx_efaq_controllers_search->main
 *       // Here the action is send by the form.
 *     }
 *   }
 * }
 * // Handling the configured switch to the plugin entry point by reference.
 * tt_content.list.20.efaq =< plugin.tx_efaq.controllerSwitch
 * </code>
 *
 * The original idea of this technology was proposed by Jo Hasenau.
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @see	       Extension: efaq
 * @package    TYPO3
 * @subpackage lib
 */
class tx_lib_switch{
	var $flexFormSheetName = 'mainSheet';
	var $flexFormFieldName = 'controller';

	/**
	 * Main entry point of the plugin.
	 *
	 * It is called from TS Setup in the typical position
	 * "tt_content.list.20.key" exactly
	 * like the traditional tslib::pi_base() plugins.
	 *
	 * The "key" is typically set by the function t3lib_extMgm::addPlugin()
	 * within the file ext_tables.php as second element of the array
	 * that is handled as first parameter to the function.
	 *
	 * t3lib_extMgm::addPlugin(array(label,key), list_type)
	 *
	 * Switches to the subcontroller configured by TS setup and
	 * flexform and selected by the user within the flexform.
	 * Takes the results of the subcontroller, wraps the result
	 * optionally with stdWrap and returns that.
	 *
	 * For further information see the intro text of this class.
	 *
	 * @param	string		incomming content, empty for plugins
	 * @param	array		the part of the TS configuration set for that part of the switch
	 * @return	string		the content typically (x)html
	 */
	function main($content, $conf){
		$sheetName = $conf['flexFormSheetName'] ? $conf['flexFormSheetName'] : $this->flexFormSheetName;
		$fieldName = $conf['flexFormFieldName'] ? $conf['flexFormFieldName'] : $this->flexFormFieldName;
		$this->_initFlexForm('pi_flexform');
		$key = $this->_getFlexFormValue($this->cObj->data['pi_flexform'], $fieldName, $sheetName);
		$return = $this->cObj->cObjGetSingle($conf[$key], $conf[$key.'.']);
		$return = $this->cObj->stdWrap($return,$conf[$key.'.']['stdWrap.']);
		return $return;
	}

	//------------------------------------------------------------------------------------
	// Private functions
	//------------------------------------------------------------------------------------

	/**
	 * Converts $this->cObj->data['pi_flexform'] from XML string to flexForm array.
	 *
	 * @param	string		Field name to convert
	 * @return	void
	 * @access	private
	 */
	function _initFlexForm($field) {
		// Converting flexform data into array:
		if (!is_array($this->cObj->data[$field]) && $this->cObj->data[$field]) {
			$this->cObj->data[$field] = t3lib_div::xml2array($this->cObj->data[$field]);
			if (!is_array($this->cObj->data[$field]))	$this->cObj->data[$field]=array();
		}
	}

	/**
	 * Return value from somewhere inside a FlexForm structure.
	 *
	 * Note: The fieldName can be given like "test/el/2/test/el/field_templateObject" where each part 
	 * will dig a level deeper in the FlexForm data. 
	 *
	 * @param	array		FlexForm data
	 * @param	string		Name of Field to extract.
	 * @param	string		Sheet pointer, eg. "sDEF"
	 * @param	string		Language pointer, eg. "lDEF"
	 * @param	string		Value pointer, eg. "vDEF"
	 * @return	string		The content
	 * @access	private
	 */
	function _getFlexFormValue($T3FlexForm_array, $fieldName, $sheet='sDEF', $lang='lDEF', $value='vDEF')	{
		$sheetArray = is_array($T3FlexForm_array) ? $T3FlexForm_array['data'][$sheet][$lang] : '';
		if (is_array($sheetArray))	{
			return $this->_getFlexFormValueFromSheetArray($sheetArray,explode('/',$fieldName),$value);
		}
	}

	/**
	 * Returns part of $sheetArray pointed to by the keys in $fieldNameArray
	 *
	 * Note: fieldNameArray is an array where each value points to a key in the FlexForms content - 
	 * the input array will have the value returned pointed to by these keys. All integer keys will 
	 * not take their integer counterparts, but rather traverse the current position in the array 
	 * an return element number X (whether this is right behavior is not settled yet...)
	 *
	 * @param	array		Multidimensiona array, typically FlexForm contents
	 * @param	array		see function description
	 * @param	string		Value for outermost key, typ. "vDEF" depending on language
	 * @return	mixed		The value, typ. string
	 * @see		getFlexFormValue()
	 * @access	private
	 */
	function _getFlexFormValueFromSheetArray($sheetArray, $fieldNameArr, $value) {
		$tempArr=$sheetArray;
		foreach($fieldNameArr as $k => $v)	{
			if (t3lib_div::testInt($v))	{
				if (is_array($tempArr))	{
					$c=0;
					foreach($tempArr as $values)	{
						if ($c==$v)	{
							$tempArr=$values;
							break;
						}
						$c++;
					}
				}
			} else {
				$tempArr = $tempArr[$v];
			}
		}
		return $tempArr[$value];
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_switch.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_switch.php']);
}

?>
