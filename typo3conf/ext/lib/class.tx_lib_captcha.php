<?php

/**
 * A class to provide easy access to captcha generation
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
 * @version    SVN: $Id: class.tx_lib_captcha.php 7333 2007-11-30 12:21:48Z elmarhinz $
 * @since      0.1
 */

/**
 * A class to provide easy access to captcha generation
 *
 * Create a new captcha:
 * <code>
 *  $captchaClassName = tx_div::makeInstanceClassName('tx_lib_captcha');
 *  $captcha = new $captchaClassName($this);
 *  $captcha->createTest($this->getClassName());
 * </code>
 *
 * Check if captcha question has been ansered correctly:
 * <code>
 *  $captchaClassName = tx_div::makeInstanceClassName('tx_lib_captcha');
 *  $captcha = new $captchaClassName($this);
 *  if(!$captcha->ok($this->getClassName())) {
 *    // Ask another question and another question and ...
 *  } else { // Captcha test passed:
 *    // Do something now
 *  }
 * </code>
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 */
class tx_lib_captcha extends tx_lib_object {

	/**
	 * Create a new question for the captcha.
	 *
	 * @param	integer		a unique key for this session to store the answer under
	 * @param	string		name of function to generate the captcha
	 * @return	void
	 */
	function createTest($id, $type = 'math1') {
		session_start();
		$functionName = '_' . $type . 'Test';
		list($question, $input, $answer) = $this->$functionName();
		$this->set('_captchaQuestion', $question);
		$this->set('_captchaInput', $input);
		$this->set('_captchaAnswer', $answer);
		$_SESSION['_captchaAnswer'][$id] = $answer;
	}

	/**
	 * Checks if the user given answer to a captcha is equal to the saved preset.
	 *
	 * @param	integer		a unique key for this session to get the answer from
	 * @return	boolean		true if the anwser is correct, false otherwise
	 */
	function ok($id) {
		session_start();
		$answer = (string) trim($_SESSION['_captchaAnswer'][$id]);
		$try    = (string) trim($this->controller->parameters->get('captcha'));
		return (strlen($try) && $try === $answer);
	}

	/**
	 * Calculates a new question for a captcha and saves the correct answer.
	 *
	 * This is the default captcha generation method.
	 *
	 * @return	array		array of captcha parameters
	 */
	function _math1Test() {
		$value1 = rand(0, 1000);
		$value2 = rand(0, 10);
		$signs = array('%%%minus%%%' => '-', '%%%plus%%%' => '+');
		$key = array_rand($signs);

		$question = $value1 . ' ' . $key . ' ' . $value2;
		$input = '<input name="' . $this->getDefaultDesignator() . '[captcha]" value=""%s />';
		eval('$answer = ' . $value1 . ' ' . $signs[$key] . ' ' . $value2 . ';');
		return array($question, $input, $answer);
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_captcha.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_captcha.php']);
}
?>
