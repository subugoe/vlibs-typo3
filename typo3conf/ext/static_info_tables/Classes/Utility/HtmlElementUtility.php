<?php
namespace SJBR\StaticInfoTables\Utility;
/***************************************************************
*  Copyright notice
*
*  (c) 2013 StanislasRolland <typo3@sjbr.ca>
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
 * HTML form element utility functions
 */
class HtmlElementUtility {

	/**
	 * Buils a HTML drop-down selector of countries, country subdivisions, currencies or languages
	 *
	 * @param array $items: An array of couples ('name', 'value) where the names will be the texts of an <option> tags and values will be the values of the tags
	 * @param array $selected: The values of the code of the entries to be pre-selected in the drop-down selector
	 * @param array $outSelected: resulting array of keys of selected items	 
	 * @param string $name: A value for the name attribute of the <select> tag
	 * @param string $class: A value for the class attribute of the <select> tag
	 * @param string $id: A value for the id attribute of the <select> tag
	 * @param string $title: A value for the title attribute of the <select> tag
	 * @param string $onChange: A value for the onChange attribute of the <select> tag
	 * @param integer $size: max elements that can be selected. Default: 1
	 * @return string A set of HTML <select> and <option> tags
	 */
	public static function selectConstructor ($items, $selected = array(), &$outSelected = array(), $name = '', $class = '', $id = '', $title = '', $onChange = '', $size = 1) {
		$selector = '';
		if (is_array($items) && count($items) > 0) {

			$renderCharset = TYPO3_MODE === 'FE' ? $GLOBALS['TSFE']->renderCharset : 'utf-8';
			$idAttribute = (trim($id)) ? 'id="' . htmlspecialchars(trim($id), ENT_COMPAT, $renderCharset) . '" ' : '';
			$nameAttribute = (trim($name)) ? 'name="' . htmlspecialchars(trim($name), ENT_COMPAT, $renderCharset) . '" ' : '';
			$titleAttribute = (trim($title)) ? 'title="' . htmlspecialchars(trim($title), ENT_COMPAT, $renderCharset) . '" ' : '';
			$classAttribute = (trim($class)) ? 'class="' . htmlspecialchars(trim($class), ENT_COMPAT, $renderCharset) . '" ' : '';
	
			if ($onChange) {
				$onChangeAttribute = $onChange;
				$onChangeAttribute = str_replace('"', '\'', $onChangeAttribute);
				$onChangeAttribute = self::quoteJsValue($onChangeAttribute);
				$onChangeAttribute = 'onchange=' . $onChangeAttribute . ' ';
			} else {
				$onChangeAttribute = '';
			}
	
			if ($size > 1) {
				$multiple = 'multiple="multiple" ';
				$name .= '[]';
			} else {
				$multiple = '';
			}

			$selector = '<select size="' . $size . '" ' . $idAttribute . $nameAttribute . $titleAttribute . $classAttribute . $onChangeAttribute . $multiple . '>' . LF;
			$selector .= self::optionsConstructor($items, $selected, $outSelected);
			$selector .= '</select>' . LF;
		}
		return $selector;
	}

	/**
	 * Builds a list of <option> tags
	 *
	 * @param array $items: An array where the values will be the texts of an <option> tags and keys will be the values of the tags
	 * @param  string $selected: array of pre-selected values: if the value appears as a key, the <option> tag will bear a 'selected' attribute
	 * @param array $outSelected: resulting array of keys of selected items
	 * @return string A string of HTML <option> tags
	 */
	public static function optionsConstructor ($items, $selected = array(), &$outSelected = array()) {
		$options = '';
		foreach ($items as $item) {
			$options  .= '<option value="' . $item['value'] . '"';
			if (in_array($item['value'], $selected)) {
				$options  .= ' selected="selected"';
				$outSelected[] = $item['value'];
			}
			$options  .= '>' . LocalizationUtility::convertCharset($item['name'], 'utf-8') . '</option>' . LF;
		}
		if (!isset($outSelected) || count($outSelected) == 0)	{
			reset($items);
			$outSelected = array($items[0]['value']);
		}
		return $options;
	}

	/**
	 * Quotes a string for usage as JS parameter.
	 *
	 * @param	string		The string to encode.
	 * @return	string		The encoded value already quoted
	 */
	protected static function quoteJsValue ($value) {
		$value = addcslashes($value, '"' . LF . CR);
		$value = htmlspecialchars($value, ENT_COMPAT, TYPO3_MODE === 'FE' ? $GLOBALS['TSFE']->renderCharset : 'utf-8');
		return '"' . $value . '"';
	}
}
?>