<?php
/**
 * Collection of static functions for flexforms
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2007 Fabien Udriot
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
 * @subpackage div
 * @author     Fabien Udriot <fudriot@omic.ch>
 * @copyright  2006-2007 Fabien Udriot
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    SVN: $Id: class.tx_div_ff.php 5742 2007-06-22 09:25:18Z sir_gawain $
 * @since      0.1
 */

/**
 * Collection of static functions for flexforms
 *
 * This class contains diverse static functions to support flexform handling.
 *
 * @package    TYPO3
 * @subpackage div
 * @author     Fabien Udriot <fudriot@omic.ch>
 */
class tx_div_ff{

	/**
	 * The current loaded flexform
	 *
	 * @var array
	 */
	static $flexForm = array(); //the current loaded flexform

	/**
	 * A set of flexforms that are stored in case they are going to be use.
	 *
	 * @var array
	 */
	static $flexForms = array();

	/**
	 * Load a flexform in memory
	 *
	 * @param   mixed       $flexForm can be an xml string or an array or a key array.
	 * @param   string      $flexFormName (optinal) give a name to the flexform in order to be stored for further use.
	 * @return  void
	 */
	function load($flexForm,$flexFormName = ''){
		//handle the case $flexForm is a string. It can be a xml string or key array
		if(is_string($flexForm)){
			//test if $flexForm already exists in the memory. In this case load the flexform according to its key
			if(array_key_exists($flexForm,tx_div_ff::$flexForms)){
				self::$flexForm = tx_div_ff::$flexForms[$flexForm];
			}
			else{
				//if false, it means it is *still* a string to convert in an array
				self::$flexForm = t3lib_div::xml2array($flexForm);
			}
		}
		else{
			//else it is right away an array, load it in memory
			self::$flexForm = $flexForm;
		}

		//true when the flexform is going to be stored for further use
		if($flexFormName != ''){
			self::setFlexForm($flexFormName,self::$flexForm);
		}
	}

	/**
	 * Add a flexform in memory
	 *
	 * @param   string     the flexForm name
	 * @param   array      the flexForm
	 * @return  void
	 */
	function setFlexForm($flexFormName,$flexForm){
		self::$flexForms[$flexFormName] = $flexForm;
	}

	/**
	 * Get a flexform from memory
	 *
	 * @param   string     the flexForm name
	 * @return  array      the flexform
	 */
	function getFlexForm($flexFormName){
		$result = false;
		if(array_key_exists($flexFormName,self::$flexForms)){
			$result = self::$flexForms[$flexFormName];
		}
		return $result;
	}

	/**
	 * Return value from somewhere inside the loaded flexForm structure
	 *
	 * @param   mixed      $flexForm, (optional) a flexForm array or a key array that contains a flexform
	 * @param   string     $fieldName, Field name to extract. Can be given like "test/el/2/test/el/field_templateObject" where each part will dig a level deeper in the FlexForm data.
	 * @param   string     $sheet Sheet pointer, eg. "sDEF"
	 * @param   string     $lang Language pointer, eg. "lDEF"
	 * @param   string     $value Value pointer, eg. "vDEF"
	 * @return  array      The content.
	 */
	function get(){

		//true when the first arguement is a flexForm or a reference to flexForm
		if(is_array(func_get_arg(0)) || array_key_exists(func_get_arg(0),tx_div_ff::$flexForms)){
			//case 1, $args 1 is an array...     case 2, $args 1 is a key array that contains a flexform
			is_array(func_get_arg(0)) ? $_flexForm = func_get_arg(0) : $_flexForm =& tx_div_ff::getFlexForm(func_get_arg(0));
			$index = 1;
		}
		else{
			$_flexForm =& self::$flexForm;
			$index = 0;
		}
		$fieldName = func_get_arg($index);
		@func_get_arg($index+1) ? $sheet = func_get_arg($index+1) : $sheet='sDEF';
		@func_get_arg($index+2) ? $lang = func_get_arg($index+2) : $lang='lDEF';
		@func_get_arg($index+3) ? $value = func_get_arg($index+3) : $value='vDEF';
		
		is_array($_flexForm) ? $sheetArray = $_flexForm['data'][$sheet][$lang] : $sheetArray = '';
		$result = null;
		if (is_array($sheetArray)){
			$result = self::_getFFValueFromSheetArray($sheetArray,explode('/',$fieldName),$value);
		}
		return $result;
	}

	/**
	 * Returns part of $sheetArray pointed to by the keys in $fieldNameArray
	 *
	 * @param   array      Multidimensiona array, typically FlexForm contents
	 * @param   array      Array where each value points to a key in the FlexForms content - the input array will have the value returned pointed to by these keys. All integer keys will not take their integer counterparts, but rather traverse the current position in the array an return element number X (whether this is right behavior is not settled yet...)
	 * @param   string     Value for outermost key, typ. "vDEF" depending on language.
	 * @return  mixed      The value, typ. string. private
	 */
	function _getFFValueFromSheetArray($sheetArray,$fieldNameArr,$value){
		$tempArr=$sheetArray;
		foreach($fieldNameArr as $k => $v){
			if (t3lib_div::testInt($v)){
				if (is_array($tempArr)){
					$c=0;
					foreach($tempArr as $values){
						if ($c==$v){
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

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/div/class.tx_div_ff.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/div/class.tx_div_ff.php']);
}
?>
