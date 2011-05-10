<?php

/**
 * This class is a wrapper for the TS object IMAGE
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
 * @version    SVN: $Id: class.tx_lib_image.php 5733 2007-06-21 15:27:25Z sir_gawain $
 * @since      0.1
 */

/**
 * This class is a wrapper for the TS object IMAGE
 *
 * With this class a tag with the typical TYPO3 IMAGE functionality can be
 * generated using the lib/div object and setters style.
 *
 * Different from the original IMAGE no title tag will be generated for an image,
 * if no title text is provided. The typical behaviour of IMAGE to copy the alt text
 * is considered to be a disadvantage for accessibilty.
 *
 * This class only offers basical functionality for simple image generation. Feel free
 * to improve the funcitonality by creating inherited classes within your extension.
 *
 * <code>
 *  $imageClassName = tx_div::makeInstanceClassName('tx_lib_image');
 *  $image = new $imageClassName();
 *  $image->alt('Test image'):
 *  $image->width(340);
 *  $image->path('fileadmin/templates/test.gif');
 *  echo $image->make();
 * </code>
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 */
class tx_lib_image {
	/**
	 * @var tslib_cObj
	 */
	var $cObject;
	/**#@+
	 * @var integer
	 */
	var $heightInteger;
	var $maxHeightInteger;
	var $maxWidthInteger;
	var $widthInteger;
	/**#@+
	 * @var string
	 */
	var $altString;
	var $pathString;
	var $titleString;
	/**#@-*/

	// -------------------------------------------------------------------------------------
	// Setters
	// -------------------------------------------------------------------------------------

	/**
	 * Set the alt text.
	 *
	 * If no alt tag is given at all, an empty alt tag will be generated.
	 *
	 * @param	string		alt text
	 * @return	void
	 */
	function alt($string) {
		$this->altString = $string;
	}

	/**
	 * Set the image height.
	 *
	 * @param	integer		image height
	 * @return	void
	 */
	function height($integer) {
		$this->heightInteger = $integer;
	}

	/**
	 * Set the maximal height.
	 *
	 * @param	integer		maximal height
	 * @return	void
	 */
	function maxHeight($integer) {
		$this->maxHeightInteger = $integer;
	}

	/**
	 * Set the maximal width.
	 *
	 * @param	integer		maximal width
	 * @return	void
	 */
	function maxWidth($integer) {
		$this->maxWidthInteger = $integer;
	}

	/**
	 * Set the image path.
	 *
	 * @param	string		image path
	 * @return	void
	 */
	function path($string) {
		$this->pathString = $string;
	}

	/**
	 * Set the title text.
	 *
	 * If no tilte is provided at all, no title attribute will be generated.
	 * This differs from the typical behaviour of TS Object IMAGE.
	 *
	 * @param	string		title text
	 * @return	void
	 */
	function title($string) {
		$this->titleString = $string;
	}

	/**
	 * Set the  width.
	 *
	 * @param	integer		width
	 * @return	void
	 */
	function width($integer) {
		$this->widthInteger = $integer;
	}

	// -------------------------------------------------------------------------------------
	// Getters
	// -------------------------------------------------------------------------------------

	/**
	 * Render the image.
	 *
	 * @return	string		image tag
	 */
	function make() {
		return $this->_render();
	}

	// -------------------------------------------------------------------------------------
	// Private functions
	// -------------------------------------------------------------------------------------

	/**
	 * Returns a valid tslib_cObj.
	 *
	 * Implements Singleton-Pattern.
	 *
	 * @return	tslib_cObj		a tslib_CObj
	 * @access	protected
	 */
	function _findCObject() {
		if(!$this->cObject){
			$this->cObject = t3lib_div::makeInstance('tslib_cObj');
		}
		return	$this->cObject;
	}

	/**
	 * Generates the HTML code for the image using IMAGE() function of tslib_cObj.
	 *
	 * @return	string		<img>-HTML code
	 * @access	protected
	 */
	function _render() {
		$setup = '
			file = %s
			file.width = %s
			file.height = %s
			file.maxW = %s
			file.maxH = %s
			altText = %s
			titleText = %s
		';
		$setup = sprintf(
			$setup,
			$this->pathString,
			$this->widthInteger,
			$this->heightInteger,
			$this->maxWidthInteger,
			$this->maxHeightInteger,
			$this->altString,
			$this->titleString
		);
		require_once(PATH_t3lib.'class.t3lib_tsparser.php');
		$TSparserObject = t3lib_div::makeInstance('t3lib_tsparser');
		$TSparserObject->parse($setup);
		$setup = $TSparserObject->setup;
		$cObject = $this->_findCObject();
		$image = $cObject->cObjGetSingle('IMAGE', $setup);

		// The default behaviour of the IMAGE  is to make an empty alt altribut if alt is not given. That is fine.
		// If title is not given it takes the alt text for the title tag. That is not fine.
		// We need to strip the title tag if it is empty:

		if(!$this->titleString) {
			$pattern = '/title\w*=\w*"[^"]*"/';
			$image = preg_replace($pattern, '', $image);
		}
		return $image;
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_image.php']) {
  include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_image.php']);
}

?>
