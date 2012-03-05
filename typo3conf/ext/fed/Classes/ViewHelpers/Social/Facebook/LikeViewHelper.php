<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Georg Ringer <typo3@ringerge.org>
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
 * ViewHelper to add a like button
 * Details: http://developers.facebook.com/docs/reference/plugins/like
 *
 * Examples
 * ==============
 *
 * <fed:social.facebook.like />
 * Result: Facebook widget to share the current URL
 *
 * <fed:social.facebook.like href="http://www.typo3.org" width="300" font="arial" />
 * Result: Facebook widget to share www.typo3.org within a plugin styled with
 * width 300 and arial as font
 *
 * @package Fed
 * @subpackage ViewHelpers
 */
class Tx_Fed_ViewHelpers_Social_Facebook_LikeViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 * @var	string
	 */
	protected $tagName = 'fb:like';

	/**
	 * @param Tx_Extbase_Object_ObjectManager $objectManager
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * Arguments initialization
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerTagAttribute('layout', 'string', 'Either: standard, button_count or box_count');
		$this->registerTagAttribute('width', 'integer', 'With of widget, default none');
		$this->registerTagAttribute('font', 'string', 'Font, options are: arial, lucidia grande, segoe ui, tahoma, trebuchet ms, verdana');
		$this->registerTagAttribute('javaScript', 'string', 'JS URL. If not set, default is used, if set to -1 no Js is loaded');
		$this->registerArgument('locale', 'string', 'Optional locale override, default is en_US', FALSE, 'en_US');
	}

	/**
	 * Render the facebook like viewhelper
	 *
	 * @return string
	 */
	public function render() {
		$code = '';
			// -1 means no JS
		if ($this->arguments['javaScript'] == '' || $this->arguments['javaScript'] == '-1') {
			$locale = $this->arguments['locale'];
			$file = 'http://connect.facebook.net/' . $locale . '/all.js#xfbml=1';
		} else {
			$file = htmlspecialchars($this->arguments['javaScript']);
		}
		$scriptTag = $this->documentHead->wrap(NULL, $file, 'js');
		$this->documentHead->includeHeader($scriptTag, NULL, 'fed-facebook-like');

			// seems as if a div with id fb-root is needed this is just a dirty
			// workarround to make things work again Perhaps we should
			// use the iframe variation.
		$code .= '<div id="fb-root"></div>' . $this->tag->render();
		return $code;
	}

}

?>