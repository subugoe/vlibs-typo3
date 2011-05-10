<?php

/**
 * Base class for simple <form> generation
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
 * @version    SVN: $Id: class.tx_lib_formBase.php 7333 2007-11-30 12:21:48Z elmarhinz $
 * @since      0.1
 */

/**
 * Base class for simple <form> generation
 *
 * Example how to use this class in your template:
 * <code>
 *  $form = $this->form;
 *  $form->setRowPattern('%1$s%4$s<dt>%2$s</dt>%4$s<dd>%3$s</dd>%4$s');
 *  $form->requireListControl('<dl>', '</dl>');
 *  
 *  print $form->begin('cherriesInputFunctions');
 *    print $form->hidden('hidden', array('value' => 'aHiddenValue'));
 *
 *    print $form->fieldsetBegin('fieldsetCheckbox', array(), 'Checkbox');
 *    	print $form->checkboxRow('checkbox', 'Checkbox', array('value' =>'Checkbox'));
 *    print $form->fieldsetEnd();
 *  
 *    print $form->fieldsetBegin('fieldsetSingleSelections', array(), 'Single selections');
 *      print $form->selectRow('select', 'Select', array(), array(1 => 'Option 1', 2 => 'Option 2'));
 *      print $form->fieldsetBegin('fieldsetRadioGroup', array(), 'Radio group');
 *        print $form->radioRow('radio1', 'Radio 1', array('name' => 'radio', 'value' => 'value1'));
 *        print $form->radioRow('radio2', 'Radio 2', array('name' => 'radio', 'value' => 'value2'));
 *  	print $form->fieldsetEnd();
 *    print $form->fieldsetEnd();
 *  
 *  print $form->end();
 * </code>
 *
 * For more and more complex examples look into EXT:cherries.
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 */
class tx_lib_formBase extends tx_lib_viewBase {

	var $idPrefix;
	var $method = 'post';
	var $_optionLists = array();
	var $_listBeginTag;
	var $_listEndTag;
	var $_listOpenedFlag = FALSE;
	var $_rowPattern = '%1$s<dt>%2$s</dt>%4$s<dd>%3$s</dd>';

	/**
	 * Add an option list to the internal option list array
	 *
	 * Takes an array or object of the hash type, with the values as keys and the labels as values.
	 *
	 * @param   mixed array or object of the hash type 
	 * @return	void
	 */
	function addOptionList($key, $hashArrayOrObject) {
		$this->_optionLists[$key] = tx_div::toHashArray($hashArrayOrObject);
	}

	/**
	 * Get an option list from the internal option list arra
	 *
	 * @param   mixed array or object of the hash type 
	 * @return	void
	 */
	function getOptionList($key) {
		return $this->_optionLists[$key]; 
	}

	/**
	 * Returns the current request url with all parameters.
	 *
	 * @return	string		current request url
	 */
	function action() {
		return htmlspecialchars(t3lib_div::getIndpEnv('TYPO3_REQUEST_URL'));
	}

	/**
	 * Returns the opening tag of a form.
	 *
	 * @param	string		id for that form
	 * @param	array		attributes to the form tag as key=>value
	 * @return	string		the opening form tag of the form
	 */
	function begin($key, $attributes = array()) {
		$this->setIdPrefix($key);
		$attributes['id'] = $key;
		$attributes['action'] = $this->action();
		$attributes['method'] = $this->method();
		$attributes = $this->_makeAttributes($attributes);
		return sprintf(chr(10) . '<form%s>' . chr(10), $attributes);
	}

	/**
	 * Returns the HTML code of a checkbox.
	 *
	 * Notice: You have to set an entry "value" in the $attributes array
	 * to get this function working.
	 *
	 * @param	string		the id for that checkbox
	 * @param	array		attributes to the input tag as key=>value
	 * @return	string		checkbox html code
	 */
	function checkbox($key, $attributes = array()) {
		if(!isset($attributes['value']))
			$this->_die('Please set a value attribute for checkboxes.', __FILE__, __LINE__);
		$attributes['type'] = 'checkbox';
		if($this->_checked($key, $attributes['value'])) $attributes['checked'] = 'checked';
		return $this->input($key, $attributes);
	}

	/**
	 * Return the HTML code of a table row containing a label and a checkbox.
	 *
	 * Notice: You have to set an entry "value" in the $attributes array
	 * to get this function working.
	 *
	 * @param	string		the id for that row
	 * @param	string		the text of the label
	 * @param	array		attributes to the input tag as key=>value
	 * @return	string		checkbox row html code
	 */
	function checkboxRow($key, $labelText, $attributes = array()) {
		return $this->_makeRow($key, $this->checkbox($key, $attributes), $labelText );
	}

	/**
	 * Return the checked attribute as html code.
	 *
	 * @param	string		id of checkbox to check
	 * @param	mixed		the comparision value
	 * @return	string		html code
	 */
	function checked($key, $value) {
		if($this->_checked($key, $value))
			return ' checked="checked"';
		else
			return '';
	}

	/**
	 * Return the closing tag of the form.
	 *
	 * @return	string		form closing tag html code
	 */
	function end() {
		return $this->_queryListEnd() . chr(10) . '</form>' . chr(10);
	}

	/**
	 * Return the opening tag of a fieldset.
	 *
	 * @param	string		id of that fieldset
	 * @param	array		attributes to the fieldset tag as key=>value
	 * @param	string		the text of the legend
	 * @return	string		fieldset opening tag html code
	 */
	function fieldsetBegin($key, $attributes = array(), $legend) {
		$attributes = $this->_addId($key, $attributes);
		$attributes = $this->_makeAttributes($attributes);
		return sprintf('%s%s<fieldset%s>%s<legend>%s</legend>%s',
			$this->_queryListEnd(), chr(10), $attributes, chr(10), $legend, chr(10));
	}

	/**
	 * Return the closing tag of a fieldset.
	 *
	 * @return	string		fieldset closing tag html code
	 */
	function fieldsetEnd() {
		return sprintf('%s%s</fieldset>%s', $this->_queryListEnd(), chr(10), chr(10));
	}

	/**
	 * Return an hidden field.
	 *
	 * @param	string		id of the hidden field
	 * @param	array		attributes to the input tag as key=>value
	 * @return	string		hidden field html code
	 */
	function hidden($key, $attributes = array()) {
		$attributes['type'] = 'hidden';
		return $this->input($key, $attributes);
	}

	/**
	 * Return an input field.
	 *
	 * @param	string		id of the input field
	 * @param	array		attributes to the input tag as key=>value
	 * @return	string		input field html code
	 */
	function input($key, $attributes = array()) {
		$attributes = $this->_addId($key, $attributes);
		$attributes = $this->_addValue($key, $attributes);
		$attributes = $this->_addName($key, $attributes);
		$attributes = $this->_makeAttributes($attributes);
		return sprintf('<input%s/>', $attributes);
	}

	/**
	 * Return the HTML code of a table row containing a label and a input field.
	 *
	 * @param	string		id of that row
	 * @param	string		the text of the label
	 * @param	array		attributes to the input tag as key=>value
	 * @return	string		input field row html code
	 */
	function inputRow($key, $labelText, $attributes = array()) {
		return $this->_makeRow($key, $this->input($key, $attributes), $labelText);
	}

	/**
	 * Return a label.
	 *
	 * @param	string		id of the label
	 * @param	array		attributes to the label tag as key=>value
	 * @param	string		the text of the label
	 * @return	string		label tag html code
	 */
	function label($key, $attributes = array(), $text = NULL) {
		$attributes = $this->_addFor($key, $attributes);
		$attributes = $this->_makeAttributes($attributes);
		return sprintf('<label%s>%s</label>', $attributes, $text);
	}

	/**
	 * Return the method of this form.
	 *
	 * @return	string		the method
	 */
	function method() {
		return $this->method;
	}

	/**
	 * Return a checkbox list that allows the selection of multiple entries.
	 *
	 * This works analoguous to a multi select, but looks different.  
	 *
	 * The third parameter options is optional. If it is given, it is used.
	 * By default the key is used to fetch the options from the internal option list array.
	 *
	 * @param	string		id 
	 * @param	array		attributes 
	 * @param	array		array in value=>text format for select options
	 * @return	string		select tag html code
	 */
	function multicheckboxes($key, $attributes = array(), $options = NULL) {
		$options = $options ? $options : $this->getOptionList($key); 
		$out = '';
		$a = 0;
		foreach((array) $options as $value => $text) {
			$loopAttributes = $attributes;  // reset
			if($this->_checked($key, $value)) 
				$loopAttributes['checked'] = 'checked'; 
			$loopAttributes['value']= $value;
			$loopAttributes['type'] = 'checkbox';
			$loopAttributes['id'] = $this->_makeIdPrefix() . $key . '_' . ++$a;
			$loopAttributes = $this->_addName($key, $loopAttributes, TRUE);
			$loopAttributes = $this->_addValue($key, $loopAttributes);
			$attributeString = $this->_makeAttributes($loopAttributes);
			$out .= sprintf('<li><input %s><label for="%s">%s</label></li>%s', $attributeString, $loopAttributes['id'], $text, chr(10));
		}
		return sprintf('<ul>%s%s</ul>%s', chr(10), $out, chr(10));
	}

	/**
	 * Return a select that allows the selection of multiple entries.
	 *
	 * The third parameter options is optional. If it is given, it is used.
	 * By default the key is used to fetch the options from the internal option list array.
	 *
	 * @param	string		id of the multiselect
	 * @param	array		attributes to the select tag as key=>value
	 * @param	array		array in value=>text format for select options
	 * @return	string		select tag html code
	 */
	function multiselect($key, $attributes = array(), $options = NULL) {
		$attributes['multiple'] = 'multiple';
		return $this->select($key, $attributes, $options);
	}

	/**
	 * Return the HTML code of a table row containing a label and a multi checkbox list.
	 *
	 * This works analoguous to a multi select, but looks different.  
	 * 
	 * @param	string		id of that row
	 * @param	string		text for the label
	 * @param	array		attributes to the select tag as key=>value
	 * @param	array		array in value=>text format for select options
	 * @return	string		select tag row html code
	 */
	function multicheckboxesRow($key, $labelText, $attributes = array(), $options) {
		return $this->_makeRow($key, $this->multicheckboxes($key, $attributes, $options), $labelText);
	}

	/**
	 * Return the HTML code of a table row containing a label and a multi select.
	 *
	 * @param	string		id of that row
	 * @param	string		text for the label
	 * @param	array		attributes to the select tag as key=>value
	 * @param	array		array in value=>text format for select options
	 * @return	string		select tag row html code
	 */
	function multiselectRow($key, $labelText, $attributes = array(), $options) {
		return $this->_makeRow($key, $this->multiselect($key, $attributes, $options), $labelText);
	}

	/**
	 * Return a input field usable for passwords or other secret things.
	 *
	 * @param	string		id of the input tag
	 * @param	array		attributes to the input tag as key=>value
	 * @return	string		input tag html code
	 */
	function password($key, $attributes = array()) {
		$attributes['type'] = 'password';
		return $this->input($key, $attributes);
	}

	/**
	 * Return the HTML code of a table row containing a label and a password input field.
	 *
	 * @param	string		id of that row
	 * @param	string		text for the label
	 * @param	array		attributes to the input tag as key=>value
	 * @return	string		input tag row html code
	 */
	function passwordRow($key, $labelText, $attributes = array()) {
		return $this->_makeRow($key, $this->password($key, $attributes), $labelText);
	}

	/**
	 * Return a form reset button.
	 *
	 * @param	string		id of the input tag
	 * @param	array		attributes to the input tag as key=>value
	 * @return	string		input tag html code
	 */
	function reset($key, $attributes = array()) {
		$attributes['type'] = 'reset';
		return $this->input($key, $attributes);
	}

	/**
	 * Return the HTML code of a table row containing a label and a form reset button.
	 *
	 * @param	string		id of that row
	 * @param	string		text for the label
	 * @param	array		attributes to the input tag as key=>value
	 * @return	string		input tag row html code
	 */
	function resetRow($key, $labelText, $attributes = array()) {
		return $this->_makeRow($key, $this->reset($key, $attributes), $labelText);
	}

	/**
	 * Return a radio button.
	 *
	 * You have to provide a "name" and a "value" entry in the attributes array.
	 * The "name" is used to define the group this button belongs to and the "value"
	 * is the value this button represents.
	 *
	 * @param	string		id of the input tag
	 * @param	array		attributes to the input tag as key=>value
	 * @return	string		radio button html code
	 */
	function radio($key, $attributes = array()) {
		if(!isset($attributes['name'])) $this->_die('Please set a name attribute for radio controlls.', __FILE__, __LINE__);
		if(!isset($attributes['value'])) $this->_die('Please set a value attribute for radio controlls.', __FILE__, __LINE__);
		$attributes['type'] = 'radio';
		if($this->_checked($attributes['name'], $attributes['value'])) $attributes['checked'] = 'checked';
		return $this->input($key, $attributes);
	}

	/**
	 * Return the HTML code of a table row containing a label and a radio button.
	 *
	 * You have to provide a "name" and a "value" entry in the attributes array.
	 * The "name" is used to define the group this button belongs to and the "value"
	 * is the value this button represents.
	 *
	 * @param	string		id of that row
	 * @param	string		text for the label
	 * @param	array		attributes to the input tag as key=>value
	 * @return	string		radio button row html code
	 */
	function radioRow($key, $labelText, $attributes = array()) {
		return $this->_makeRow($key, $this->radio($key, $attributes), $labelText);
	}

	/**
	 * Return a select box.
	 *
	 * The third parameter options is optional. If it is given, it is used.
	 * By default the key is used to fetch the options from the internal option list array.
	 *
	 * @param	string		id of the select tag
	 * @param	array		attributes to the select tag as key=>value
	 * @param	array		array in value=>text format for select options
	 * @return	string		select tag html code
	 */
	function select($key, $attributes = array(), $options = NULL) {
		$attributes = $this->_addId($key, $attributes);
		$attributes = $this->_addName($key, $attributes, (bool) $attributes['multiple']);
		$attributes = $this->_makeAttributes($attributes);
		$options = $options ? $options : $this->getOptionList($key); 
		foreach((array) $options as $value => $text ) {
			$value = strlen($value) ? $value : $text;
			$selected = $this->selected($key, $value);
			$value = sprintf(' value="%s"', $value);
			$body .= sprintf('<option%s%s>%s</option>' . chr(10) , $value, $selected, $text);
		}
		return sprintf(chr(10) . '<select%s>%s</select>' . chr(10), $attributes, chr(10) . $body);
	}

	/**
	 * Return the HTML code of a table row containing a label and a select box.
	 *
	 * @param	string		id of that row
	 * @param	string		text for the label
	 * @param	array		attributes to the select tag as key=>value
	 * @param	array		array in value=>text format for select options
	 * @return	string		select tag row html code
	 */
	function selectRow($key, $labelText, $attributes = array(), $options) {
		return $this->_makeRow($key, $this->select($key, $attributes, $options), $labelText);
	}

	/**
	 * Return the selected attribute as html code.
	 *
	 * @param	string		id of selectbox to check
	 * @param	mixed		the comparision value
	 * @return	string		html code
	 */
	function selected($key, $value) {
		if($this->_checked($key, $value))
			return ' selected="selected"';
		else
			return '';
	}

	/**
	 * Set a string to be used as prefix to all given ids.
	 *
	 * @param	string		id prefix string
	 * @return	void
	 */
	function setIdPrefix($idPrefix) {
		$this->idPrefix = $idPrefix;
	}

	/**
	 * Sets the template of the row.
	 *
	 * This template is used to compile the resulting html code in the *Row()
	 * functions. The template has to be valid sprintf() format code.
	 *
	 * The default is: <samp> %1$s<dt>%2$s</dt>%4$s<dd>%3$s</dd> </samp>
	 *
	 * @param	string		the new pattern (sprintf format code)
	 * @return	void
	 */
	function setRowPattern($pattern) {
		$this->_rowPattern = $pattern;
	}

	/**
	 * Return a submit button.
	 *
	 * @param	string		id of input tag
	 * @param	array		attributes to the input tag as key=>value
	 * @return	string		input tag html code
	 */
	function submit($key, $attributes = array()) {
		$attributes['type'] = 'submit';
		return $this->input($key, $attributes);
	}

	/**
	 * Return the HTML code of a table row containing a label and a submit button.
	 *
	 * @param	string		id of that row
	 * @param	string		text for the label
	 * @param	array		attributes to the input tag as key=>value
	 * @return	string		input tag row html code
	 */
	function submitRow($key, $labelText, $attributes = array()) {
		return $this->_makeRow($key, $this->submit($key, $attributes), $labelText);
	}

	/**
	 * Return a textarea.
	 *
	 * @param	string		id of textarea tag
	 * @param	array		attributes to the textarea tag as key=>value
	 * @return	string		textarea tag html code
	 */
	function textarea($key, $attributes = array()) {
		$attributes = $this->_addId($key, $attributes);
		$attributes = $this->_addName($key, $attributes);
		$attributes = $this->_makeAttributes($attributes);
		$value = $this->value($key);
		return sprintf('<textarea%s>%s</textarea>' . chr(10), $attributes, $value);
	}

	/**
	 * Return the HTML code of a table row containing a label and a submit button.
	 *
	 * @param	string		id of textarea tag
	 * @param	string		text for the label
	 * @param	array		attributes to the textarea tag as key=>value
	 * @return	string		textarea tag row html code
	 */
	function textareaRow($key, $labelText, $attributes = array()) {
		return $this->_makeRow($key, $this->textarea($key, $attributes), $labelText);
	}

	/**
	 * Return the value of a given form element.
	 *
	 * @param	string		the id of the form element
	 * @return	mixed		the value
	 */
	function value($key) {
		return htmlspecialchars($this->_getValue($key));
	}

	/**
	 * Sets the opening and closing tags to be used during automatic list generation.
	 *
	 * This function should be used at the top at the form.
	 *
	 * @param	string		the opening tag to be used
	 * @param	string		the closing tag to be used
	 * @return	void
	 */
	function requireListControl($beginTag = '<dl>', $endTag = '</dl>') {
		$this->_listBeginTag = $beginTag;
		$this->_listEndTag = $endTag;
		$this->_listOpenedFlag = FALSE;
	}

	//--------------------------------------------------------------------------------
	// Protected methods
	//--------------------------------------------------------------------------------

	/**
	 * Add an "id" entry to the given array.
	 *
	 * The id will be prepended with a prefix. The prefix can be set with setIdPrefix().
	 *
	 * @see		setIdPrefix()
	 * @access	protected
	 * @param	string		id of element
	 * @param	array		the array to add the entry to
	 * @return	array		the modified array
	 */
	function _addId($key, $array) {
		$array['id'] = $this->_makeIdPrefix() . $key;
		return $array;
	}

	/**
	 * Add a "for" entry to the given array.
	 *
	 * @access	protected
	 * @param	string		id of element the for entry should point to
	 * @param	array		the array to add the entry to
	 * @return	array		the modified attributes array
	 */
	function _addFor($key, $array) {
		$array['for'] = $this->_makeIdPrefix() . $key;
		return $array;
	}

	/**
	 * Add the name of the element converted to PHP-array notation into the given array.
	 *
	 * The given name (in array) is split at "-" to build the array notation.
	 * If no name is given, the id will be taken and also split.
	 * The name is prepended with the designator of the extension.
	 *
	 * @access	protected
	 * @param	string		id of the element
	 * @param	array		the attributes array (key=>value)
	 * @return	array		the modified attributes array
	 */
	function _addName($key, $array, $multiple = FALSE) {
		$nameParts = explode('-', $array['name'] ? $array['name'] : $key);
		$array['name'] = $this->controller->getDesignator();
		foreach($nameParts as $part) $array['name'] .= '[' . $part . ']';
		$array['name'] .= $multiple ? '[]' : ''; 
		return $array;
	}

	/**
	 * Add the "value" entry into the given array.
	 * If the "value" entry is already present, it only gets htmlspecialchar()ed,
	 * otherwise the value is taken from the internal data array from the field
	 * pointed to by parameter "key".
	 *
	 * The added value gets htmlspecialchar()ed.
	 *
	 * @access	protected
	 * @param	string		key to internal data array
	 * @param	array		attributes array (key=>value)
	 * @return	array		the modified attributes array
	 */
	function _addValue($key, $array) {
		if($array['type'] != 'password')
			$array['value'] = isset($array['value'])
				? htmlspecialchars($array['value'])
				: htmlspecialchars($this->_getValue($key));
		return $array;
	}

	/**
	 * Check wether the value referenced by name is equal to the comparision
	 * value or not. If the value is an array, this function tries to look up
	 * the comparision in that array, otherwise both values are converted to
	 * string and compared afterwards.
	 *
	 * @access	protected
	 * @param	string		name of data field to get value from
	 * @param	mixed		the value to compare with
	 * @return	boolean		true if checked, false otherwise
	 */
	function _checked($key, $value) {
		$options = $this->get($key);
		if( is_array($options) && in_array((string) $value, $options) || (string) $options == (string) $value) 
			return TRUE;
		else
			return FALSE;
	}

	/**
	 * Return the value of element "key" from internal data array.
	 *
	 * @access	protected
	 * @param	string		the id of the element to get the value from
	 * @return	mixed		the value
	 */
	function _getValue($key) {
		return $this->get($key);
	}

	/**
	 * Return the attributes in array as html string.
	 *
	 * @access	protected
	 * @param	array		attributes array (key=>value)
	 * @return	string		attributes string
	 */
	function _makeAttributes($array) {
		$array = (array) $array;
		ksort($array);
		$out = '';
		foreach((array) $array as $key => $value) {  
			// Attributes with numeric keys are allowed to be used as message flags.
			if(!is_integer($key)) $out .= sprintf(' %s="%s"', $key, $value);
		}
		return $out;
	}

	/**
	 * Return the prefix for IDs. If no prefix is set, return a empty string.
	 *
	 * @access	protected
	 * @return	string		id prefix or empty string
	 */
	function _makeIdPrefix() {
		return $this->idPrefix ? $this->idPrefix . '-' : '';
	}

	/**
	 * Compile a complete row with id, label and input element.
	 *
	 * @access	protected
	 * @param	string		id of the row
	 * @param	string		the input element (html code)
	 * @param	string		text of the label
	 * @return	string		row html code
	 */
	function _makeRow($key, $input, $labelText) {
		$label = $this->label($key, array(), $labelText);
		$listBegin = $this->_queryListBegin();
		$break = chr(10);
		return sprintf( $this->_rowPattern, $listBegin, $label, $input, $break);
	}

	/**
	 * Return the list opening tag if needed.
	 *
	 * @access	protected
	 * @return	string		the opening tag
	 */
	function _queryListBegin() {
		if(!$this->_listOpenedFlag && $this->_listBeginTag) {
			$this->_listOpenedFlag = TRUE;
			return chr(10) . $this->_listBeginTag . chr(10);
		}
		return '';
	}

	/**
	 * Return the list closing tag if needed.
	 *
	 * @access	protected
	 * @return	string		the closing tag
	 */
	function _queryListEnd() {
		if($this->_listOpenedFlag && $this->_listEndTag) {
			$this->_listOpenedFlag = FALSE;
			return chr(10) . $this->_listEndTag . chr(10);
		}
		return '';
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_formBase.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_formBase.php']);
}
?>
