<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Georg Ringer <typo3@ringerge.org>
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
 * ViewHelper to add a google+ button
 * Details: http://www.google.com/webmasters/+1/button/
 *
 * Examples
 * ==============
 *
 * <fed:social.googlePlus />
 * Result: Google Plus Button
 *
 * <fed:social.googlePlus size="small"  href="http://www.mydomain.tld" count="false" />
 * Result: Small Google Plus Button to share www.mydomain.tld without showing the counter
 *
 * @package Fed
 * @subpackage ViewHelpers
 */
class Tx_Fed_ViewHelpers_Social_GooglePlusViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var	string
	 */
	protected $tagName = 'g:plusone';

	/**
	 * Arguments initialization
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerTagAttribute('size', 'string', 'Size of the icon. Can be small,medium,tall.');
		$this->registerTagAttribute('callback', 'string', 'Callback function');
		$this->registerTagAttribute('href', 'string', 'URL to be +1, default is current URL');
		$this->registerTagAttribute('count', 'string', 'Set it to false to hide counter');
		$this->registerArgument('jsCode', 'string', 'Optional Javascript code to use', FALSE, '');
		$this->registerArgument('locale', 'string', 'Optional locale override, default is en_US', FALSE, 'en_US');
	}

	/**
	 * Render the Google+ button
	 *
	 * @return string
	 */
	public function render() {
		$jsCode = $this->arguments['jsCode'];
		$code = '';
		if (empty($jsCode)) {
			$jsCode = 'https://apis.google.com/js/plusone.js';
		} elseif($jsCode != '-1') {
			$jsCode = htmlspecialchars($jsCode);
		}
		$locale = $this->arguments['locale'];
		$code = '<script type="text/javascript" src="' . $jsCode . '">{lang:\'' . $locale . '\'}</script>';
		$this->documentHead->includeHeader($code);
		$this->tag->setContent(' ');
		return $this->tag->render();
	}
}

?>