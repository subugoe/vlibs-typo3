<?php

/**
 * Renders a phpTemplate and fills data into it
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
 * @version    SVN: $Id: class.tx_lib_phpTemplateEngine.php 7333 2007-11-30 12:21:48Z elmarhinz $
 * @since      0.1
 */

/**
 * Renders a phpTemplate and fills data into it
 *
 * <b> Abstract </b>
 *
 * 1. A reference to the controller is set, to give access to configurations, parameters and context.
 * 2. The object is filled with data by the controller.
 * 3. A simple PHP template is rendered to be populated with the data. 
 * $model = ...
 * $view = new tx_lib_phpTemplateEngine($this, $model);  // Typically a child class of tx_lib_phpTemplateEngine
 * $view->render($templateName);                         // Template file in the template directory
 * $translator = new tx_lib_translator($this, $view);    // Feeding the object to the next element of the processing chain
 * $translator ...
 * </code>
 *
 * PHP templates:
 *
 * - PHP templates are plain php/html files with the simple filename pattern "xxxx.php".
 * - The templates are stored in the $pathToTemplateDirectory, the template path is configurable. 
 * - Rendering methods can by defined in the view object to be used in the template: i.e. $this->printTitle();
 * - Many rendering methods are already definded in the parent classes.
 *
 * <b> Advantages and disadvanantages of PHP templates </b>
 *
 * Advantages:
 *
 * - You don't need to learn a extra templating language if you know PHP. You quickly come to results.
 * - You can provide any function you think usefull for the usage within the templates.
 * - For the template customizer (typically a webdesigner) it is as easy to learn this simple PHP 
 *   as it is to learn any other advanced templating language that contains some logic to controll the template output.
 * - Opposit to a mere templating language, PHP knowlage is reusable.
 *
 * Disadvantages:
 *
 * - There are a few more characters to type.
 * - Anybody who is allowed to edit the templates has the full power of PHP in his hand. 
 *   This can become a security hole, when not set up correctly.
 *
 * Depends on: tx_lib_object
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 */
class tx_lib_phpTemplateEngine extends tx_lib_viewBase {

	// -------------------------------------------------------------------------------------
	// Getters
	// -------------------------------------------------------------------------------------

	/**
	 * Render the PHP template to populate it with the data. 
	 *
	 * The internal data is exchanged against the result, that is stored under the special key '_content'. 
	 *
	 * The parameter can be a key of an element in the $configurations object, that points to a filename.
	 * The parameter can be a filename. The ".php" ending is added if missing.
	 * 
	 * Usage: 
	 * 
	 * 1.) $view->render('exampleTemplateKey');
	 * 2.) $view->render('exampleTemplateFileName.php');
	 *
	 * @param	  string    configuration key or filename of template file
	 * @return  string    typically an (x)html string
	 */
	function render($configurationKeyOrFileName) {
		$this->checkController(__FILE__, __LINE__);                              // The controller has to be set before.
		$path = $this->getPathToTemplateDirectory();                             // Path can have the format EXT:path/. 
		$path .= substr($path, -1, 1) == '/' ? '': '/';                          // If missing, ad a ending slash to the path. 
		$path .= strlen($this->controller->configurations->get($configurationKeyOrFileName))     // Try the parameter as controller key.
			? $this->controller->configurations->get($configurationKeyOrFileName) : $configurationKeyOrFileName;
		$path .= substr($path, -4, 4) == '.php' ? '' :  '.php';                  // Append .php ending if missing.
		ob_start();                                                              // Run the template ... 
		include($path);
		$out = ob_get_clean();                                                   // ... and catch the result.
		$this->set('_content', $out);                                            // Used i.e. by the tranlator.
		return $out;
	}

	// -------------------------------------------------------------------------------------
	// Printers typically called within the template
	// -------------------------------------------------------------------------------------

	/**
	 * Print a human readability and localized date for a timestamp out of the internal data array.
	 *
	 * Behaves analogous to $this->asDate().
	 *
	 * @param	mixed		key of internal data array
	 * @param	mixed		format string or key of configuraton or empty
	 * @return	void
	 * @see		asDate()
	 */
	function printAsDate($key, $format = '') {
		print $this->asDate($key, $format);
	}

	/**
	 * Print an external Typolink to an email address.
	 *
	 * Behaves analogous to $this->asEmail().
	 *
	 * If no label key is given the email address is displayed as label.
	 * If the label is available but no email the label is returned
	 * without the tag. If both fails nothing is returned.
	 *
	 * @param	mixed		key of the email field
	 * @param	mixed		key of the label field
	 * @return	void
	 * @see		asEmail()
	 */
	function printAsEmail($emailKey, $labelKey = null) {
		print $this->asEmail($emailKey, $labelKey);
	}

	/**
	 * Print a formatted float value out of the internal data array.
	 *
	 * Behaves analogous to $this->asFloat().
	 *
	 * @param	mixed		key of internal data array
	 * @param	string		format string
	 * @return	void
	 * @see		asFloat()
	 */
	function printAsFloat($key, $format = null) {
		print $this->asFloat($key, $format);
	}

	/**
	 * Print a formatted form out of the internal data array.
	 *
	 * Behaves analogous to $this->asForm().
	 *
	 * @param	mixed		key of internal data array
	 * @return	void
	 * @see		asForm()
	 */
	function printAsForm($key) {
		print $this->asForm($key);
	}

	/**
	 * Print a string parsed for standard html input (parseFunc).
	 *
	 * Behaves analogous to $this->asHtml().
	 *
	 * @param	mixed		key of the internal data array
	 * @return	void
	 * @see		asHtml()
	 */
	function printAsHtml($key) {
		print $this->asHtml($key);
	}

	/**
	 * Print an ingeter from the internal data array by key.
	 *
	 * @param	mixed		key of the internal data array
	 * @return	void
	 * @see		asInteger()
	 */
	function printAsInteger($key) {
		print $this->asInteger($key, $format);
	}

	/**
	 * Print a raw value from the internal data array by key.
	 *
	 * @param	mixed		key of the internal data array
	 * @return	void
	 * @see		asRaw()
	 */
	function printAsRaw($key) {
		print $this->asRaw($key);
	}

	/**
	 * Print a String parsed for RTE input (parseFunc_RTE).
	 *
	 * Behaves analogous to $this->asRte().
	 *
	 * @param	mixed		key of the internal data array
	 * @return	void
	 * @see		asRte()
	 */
	function printAsRte($key) {
		print $this->asRte($key);
	}

	/**
	 * Print a string parsed for standard text input (parseFunc).
	 *
	 * Behaves analogous to $this->asText().
	 *
	 * @param	mixed		key of the internal data array
	 * @return	void
	 * @see		asText()
	 */
	function printAsText($key) {
		print $this->asText($key);
	}

	/**
	 * Print a human readability and localized time for a 
	 * timestamp out of the internal data array.
	 *
	 * Behaves analogous to $this->asTime().
	 *
	 * @param	mixed		key of internal data array
	 * @param	mixed		format string or key of configuraton or empty
	 * @return	void
	 * @see		asTime()
	 */
	function printAsTime($key, $format = '') {
		print $this->asTime($key, $format);
	}

	/**
	 * Print an external Typolink to an Url.
	 *
	 * Behaves analogous to $this->asUrl().
	 *
	 * If no label key is given the url is displayed as label.
	 * If the label is available but no url the label is returned
	 * without the tag. If both fails nothing is returned.
	 *
	 * @param	mixed		key of the email field
	 * @param	mixed		key of the label field
	 * @return	void
	 * @see		asUrl()
	 */
	function printAsUrl($urlKey, $labelKey = null) {
		print $this->asUrl($urlKey, $labelKey);
	}

	/**
	 * Print the error list.
	 *
	 * @param	string		class name
	 * @param	string		key
	 * @return	void
	 */
	function printErrorList($class = 'errors', $key = '_errorList') {
		print $this->getErrorList($class, $key) ;
	}

	/**
	 * Print hidden fields.
	 *
	 * @return	void
	 */
	function printHiddenFields() {
		print $this->getHiddenFields();
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_phpTemplateEngine.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_phpTemplateEngine.php']);
}
?>
