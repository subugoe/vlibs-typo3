<?php
namespace SJBR\StaticInfoTables\Controller;
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2009 Jochen Rau <jochen.rau@typoplanet.de>
 *  (c) 2011 Bastian Waidelich <bastian@typo3.org>
 *  (c) 2013 Stanislas Rolland <typo3@sjbr.ca>
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
 * Abstract base controller for the StaticInfoTables extension
 */
abstract class AbstractController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController {

	/**
	 * @var string Name of the extension this controller belongs to
	 */
	protected $extensionName = 'StaticInfoTables';

	/**
	 * Override getErrorFlashMessage to presentn nice flash error messages.
	 *
	 * @return string
	 */
	protected function getErrorFlashMessage() {
		return FALSE;
	}

	/**
	 * helper function to render localized flashmessages
	 *
	 * @param string $message
	 * @param integer $severity optional severity code. One of the \TYPO3\CMS\Core\Messaging\FlashMessage constants
	 * @return void
	 */
	protected function addFlashMessage($action, $severity = \TYPO3\CMS\Core\Messaging\FlashMessage::OK) {
		$messageLocallangKey = sprintf('flashmessage.%s.%s', $this->request->getControllerName(), $action);
		$localizedMessage = $this->translate($messageLocallangKey, '[' . $messageLocallangKey . ']');
		$titleLocallangKey = sprintf('%s.title', $messageLocallangKey);
		$localizedTitle = $this->translate($titleLocallangKey, '[' . $titleLocallangKey . ']');
		$this->flashMessageContainer->add($localizedMessage, $localizedTitle, $severity);
	}

	/**
	 * helper function to use localized strings in controllers
	 *
	 * @param string $key locallang key
	 * @param string $default the default message to show if key was not found
	 * @return string
	 */
	protected function translate($key, $defaultMessage = '') {
		$message = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($key, $this->extensionName);
		if ($message === NULL) {
			$message = $defaultMessage;
		}
		return $message;
	}

}

?>