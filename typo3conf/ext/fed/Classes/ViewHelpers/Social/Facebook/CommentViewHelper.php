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
 * ViewHelper to comment content
 * Details: http://developers.facebook.com/docs/reference/plugins/comments
 *
 * Examples
 * ==============
 * <fed:facebook.comment appId="165193833530000" xid="item-{object.uid}" />
 * Result: Facebook widget to comment an article
 *
 * @package Fed
 * @subpackage ViewHelpers
 */
class Tx_Fed_ViewHelpers_Social_Facebook_CommentViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var	string
	 */
	protected $tagName = 'fb:comments';

	/**
	 * Arguments initialization
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerTagAttribute('xid', 'string', 'An id associated with the comments object (defaults to URL-encoded page URL)');
		$this->registerTagAttribute('numposts', 'integer', 'the number of comments to display, or 0 to hide all comments');
		$this->registerTagAttribute('width', 'integer', 'The width of the plugin in px');
		$this->registerTagAttribute('publishFeed', 'boolean', 'Whether the publish feed story checkbox is checked., default = TRUE');
		$this->registerTagAttribute('href', 'string', 'URL for comment plugin discussion', TRUE);
		$this->registerArgument('appId', 'string', 'An id associated with the comments object (defaults to URL-encoded page URL)', FALSE, NULL, TRUE);
		$this->registerArgument('locale', 'string', 'Optional locale override, default is en_US', FALSE, 'en_US');
	}

	/**
	 * Render facebook comment viewhelper
	 *
	 * @return string
	 */
	public function render() {
		$appId = $this->arguments['appId'];
		$locale = $this->arguments['locale'];
		$code = '<div id="fb-root"></div>';
		$scriptFile = 'http://connect.facebook.net/' . $locale . '/all.js#appId=' . htmlspecialchars($appId) . '&amp;xfbml=1';
		$scriptTag = $this->documentHead->wrap(NULL, $scriptFile, 'js');
		$this->documentHead->includeHeader($scriptTag, NULL, 'fed-facebook-comment');
		$code .= $this->tag->render();
		return $code;
	}

}

?>