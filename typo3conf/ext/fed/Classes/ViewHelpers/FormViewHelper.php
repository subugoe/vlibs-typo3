<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2010 Claus Due <claus@wildside.dk>, Wildside A/S
 *
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
 * Form ViewHelper. Enables AJAX validation and automatic submission of valid form
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers
 *
 */
class Tx_Fed_ViewHelpers_FormViewHelper extends Tx_Fluid_ViewHelpers_FormViewHelper {

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 * @inject
	 */
	protected $objectManager;

	/**
	 * @var Tx_Fluid_Core_ViewHelper_TagBuilder
	 */
	protected $tag = NULL;


	/**
	 * @param Tx_Extbase_Object_ObjectManager $objectManager
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * @param Tx_Fluid_Core_ViewHelper_TagBuilder $tagBuilder Tag builder
	 */
	public function injectTagBuilder(Tx_Fluid_Core_ViewHelper_TagBuilder $tagBuilder) {
		$this->tag = $tagBuilder;
	}

	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('validate', 'boolean', 'If TRUE, uses AJAX to validate the form before submission. Requires jQuery', FALSE, FALSE);
		$this->registerArgument('validateMethod', 'string', 'Method of validation - changes the way the form behaves on AJAX validation. Depends on validate=TRUE', FALSE, 'all');
		$this->registerArgument('validateTypeNum', integer, 'If specified, makes validation AJAX requests to this page typeNum (you must register this typeNum in TypoScript for it to work)');
		$this->registerArgument('autosubmit', 'boolean', 'If TRUE and validate TRUE, automatically submits the form when valid', FALSE, FALSE);
	}

	/**
	 * Render the form
	 *
	 * @param string $action Target action
	 * @param array $arguments Arguments
	 * @param string $controller Target controller
	 * @param string $extensionName Target Extension Name (without "tx_" prefix and no underscores). If NULL the current extension name is used
	 * @param string $pluginName Target plugin. If empty, the current plugin name is used
	 * @param integer $pageUid Target page uid
	 * @param mixed $object Object to use for the form. Use in conjunction with the "property" attribute on the sub tags
	 * @param integer $pageType Target page type
	 * @param boolean $noCache set this to disable caching for the target page. You should not need this.
	 * @param boolean $noCacheHash set this to supress the cHash query parameter created by TypoLink. You should not need this.
	 * @param string $section The anchor to be added to the action URI (only active if $actionUri is not set)
	 * @param string $format The requested format (e.g. ".html") of the target page (only active if $actionUri is not set)
	 * @param array $additionalParams additional action URI query parameters that won't be prefixed like $arguments (overrule $arguments) (only active if $actionUri is not set)
	 * @param boolean $absolute If set, an absolute action URI is rendered (only active if $actionUri is not set)
	 * @param boolean $addQueryString If set, the current query parameters will be kept in the action URI (only active if $actionUri is not set)
	 * @param array $argumentsToBeExcludedFromQueryString arguments to be removed from the action URI. Only active if $addQueryString = TRUE and $actionUri is not set
	 * @param string $fieldNamePrefix Prefix that will be added to all field names within this form. If not set the prefix will be tx_yourExtension_plugin
	 * @param string $actionUri can be used to overwrite the "action" attribute of the form tag
	 * @param string $objectName name of the object that is bound to this form. If this argument is not specified, the name attribute of this form is used to determine the FormObjectName
	 * @return string rendered form
	 */
	public function render($action = NULL, array $arguments = array(), $controller = NULL, $extensionName = NULL, $pluginName = NULL, $pageUid = NULL, $object = NULL, $pageType = 0, $noCache = FALSE, $noCacheHash = FALSE, $section = '', $format = '', array $additionalParams = array(), $absolute = FALSE, $addQueryString = FALSE, array $argumentsToBeExcludedFromQueryString = array(), $fieldNamePrefix = NULL, $actionUri = NULL, $objectName = NULL) {
		if ($this->arguments['validate'] === TRUE) {
			if ($this->arguments['id']) {
				$id = $this->arguments['id'];
			} else {
				$id = uniqid('form');
				$this->tag->addAttribute('id', $id);
			}
			$link = $this->controllerContext->getUriBuilder()->uriFor('validate');
			if ($this->arguments['validateTypeNum']) {
				$link .= '&type=' . $this->arguments['validateTypeNum'];
			}
			$prefix = $this->getFieldNamePrefix();
			$loadingIcon = t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Icons/Loading.gif';
			$relData = json_encode(
				array(
					'link' => $link,
					'prefix' => $prefix,
					'objectName' => $objectName,
					'autosubmit' => $this->arguments['autosubmit'],
					'action' => $this->arguments['action']
				)
			);
			$this->tag->addAttribute('class', 'fed-validator fed-validate-' . $this->arguments['validateMethod'] . ' '
				. $this->arguments['class'] . ' ' . ($this->arguments['autoSubmit'] === TRUE ? 'fed-autosubmit' : ''));
			$this->tag->addAttribute('rel', $relData);
			$scripts = array(
				t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/FormValidator.js',
				t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/Utf8Encode.js',
				t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/Utf8Decode.js',
				t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/Serialize.js',
				t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/Unserialize.js'
			);
			$documentHead = $this->objectManager->get('Tx_Fed_Utility_DocumentHead');
			$documentHead->includeFiles($scripts);
			$documentHead->includeHeader('.fed-validator .loading { background-image: url(' . $loadingIcon . '); background-position: right; background-repeat: no-repeat; }', 'css');
		}
		$content = parent::render($action, $arguments, $controller, $extensionName, $pluginName, $pageUid, $object, $pageType, $noCache, $noCacheHash, $section, $format, $additionalParams, $absolute, $addQueryString, $argumentsToBeExcludedFromQueryString, $fieldNamePrefix, $actionUri, $objectName);
		return $content;
	}


}

?>