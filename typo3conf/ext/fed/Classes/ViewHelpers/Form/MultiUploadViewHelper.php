<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * ************************************************************* */

/**
 *
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers/Form
 */
class Tx_Fed_ViewHelpers_Form_MultiUploadViewHelper extends Tx_Fluid_ViewHelpers_Form_UploadViewHelper {

	/**
	 * @var string
	 */
	protected $tagName = 'div';

	/**
	 * @var string
	 */
	protected $uniqueId = 'plupload';

	/**
	 * @var string
	 */
	protected $editorId;

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $infoService;

	/**
	 * @var Tx_Fed_Utility_DocumentHead
	 */
	protected $documentHead;

	/**
	 * @var Tx_Fed_Utility_JSON
	 */
	protected $jsonService;

	/**
	 * @var Tx_Fluid_Core_ViewHelper_TagBuilder
	 */
	protected $tag = NULL;

	/**
	 * @param Tx_Fed_Utility_DomainObjectInfo $infoService
	 */
	public function injectInfoService(Tx_Fed_Utility_DomainObjectInfo $infoService) {
		$this->infoService = $infoService;
	}

	/**
	 * @param Tx_Fed_Utility_DocumentHead $documentHead
	 */
	public function injectDocumentHead(Tx_Fed_Utility_DocumentHead $documentHead) {
		$this->documentHead = $documentHead;
	}

	/**
	 * @param Tx_Fed_Utility_JSON $jsonService
	 */
	public function injectJsonService(Tx_Fed_Utility_JSON $jsonService) {
		$this->jsonService = $jsonService;
	}

	/**
	 * @param Tx_Fluid_Core_ViewHelper_TagBuilder $tagBuilder Tag builder
	 */
	public function injectTagBuilder(Tx_Fluid_Core_ViewHelper_TagBuilder $tagBuilder) {
		$this->tag = $tagBuilder;
	}

	/**
	 * Initialize the arguments.
	 *
	 * @return void
	 * @author Sebastian Kurfürst <sebastian@typo3.org>
	 * @api
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('buttons', 'string', 'CSV list of buttons to render (browse,start,stop)', FALSE, 'browse,start,stop');
		$this->registerArgument('runtimes', 'string', 'CSV list of allowed runtimes - see plupload doc', FALSE, 'html5,flash,gears,silverlight,browserplus,html4');
		$this->registerArgument('url', 'string', 'If specified, overrides built-in uploader with one you created and placed at this URL');
		$this->registerArgument('autostart', 'boolean', 'If TRUE, queue automatically starts uploading as soon as a file is added.', FALSE, FALSE);
		$this->registerArgument('maxFileSize', 'string', 'Maxium allowed file size', FALSE, '10mb');
		$this->registerArgument('chunkSize', 'string', 'Chunk size when uploading in chunks', FALSE, '1mb');
		$this->registerArgument('uniqueNames', 'boolean', 'If TRUE, obfuscates and randomizes file names. Default behavior is to use TYPO3 unique filename features', FALSE, FALSE);
		$this->registerArgument('resizeWidth', 'integer', 'If set, uses client side resizing of any added images width', FALSE);
		$this->registerArgument('resizeHeight', 'integer', 'If set, uses client side resizing of any added images height', FALSE);
		$this->registerArgument('resizeQuality', 'integer', 'Range 0-100, quality of resized image', FALSE, 90);
		$this->registerArgument('filters', 'array', 'Array label=>csvAllowedExtensions of file types to browse for', FALSE, array('title' => 'All files', 'extensions' => '*'));
		$this->registerArgument('uploadfolder', 'string', 'If specified, uses this site relative path as target upload folder. If a form object exists and this argument is not present, TCA uploadfolder is used as defined in the named field definition');
		$this->registerArgument('preinit', 'array', 'Array of preinit event listener methods - see plupload documentation for reference. The default event which sets the contents of the hidden field is always fired.', FALSE, array());
		$this->registerArgument('init', 'array', 'Array of init event listener methods - see plupload documentation for reference. The default event which sets the contents of the hidden field is always fired.', FALSE, array());
		$this->registerArgument('header', 'boolean', 'If FALSE, suppresses the header which is normally added to the upload widget', FALSE, TRUE);
		$this->registerArgument('headerTitle', 'string', 'Text for header title, if different from default');
		$this->registerArgument('headerSubtitle', 'string', 'Text for header subtitle, if different from default');
	}

	/**
	 * Renders a multi-upload field using plupload. Posts value as simple string.
	 *
	 * @return string
	 */
	public function render() {
		$name = $this->getName();
		$value = $this->getValue();
		$this->uniqueId = $this->arguments['id'] ? $this->arguments['id'] : uniqid('plupload');
		$this->setErrorClassAttribute();
		$this->registerFieldNameForFormTokenGeneration($name);
		$html = array(
			'<input id="' . $this->uniqueId . '-field" type="hidden" name="' . $name . '" value="' . $value . '" class="value-holder" />',
			'<div id="' . $this->uniqueId . '" class="fed-plupload plupload_container"></div>',
		);
		$this->tag->addAttribute('id', '');
		$this->tag->setContent(implode(LF, $html));
		$this->addScript();
		return $this->tag->render();
	}

	/**
	 * @return string
	 */
	protected function getPreinitEventsJson() {
		return $this->getEventsJson($this->arguments['preinit']);
	}

	/**
	 * Adds necessary scripts to header
	 * @return void
	 */
	protected function addScript() {
		$scriptPath = t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/';
		$pluploadPath = $scriptPath . 'com/plupload/js/';
		$value = $this->getPropertyValue();
		$value = trim($value, ',');
		if (strlen($value) > 0) {
			$existingFiles = explode(',', trim($this->getPropertyValue(), ','));
		} else {
			$existingFiles = array();
		}
		$propertyName = $this->arguments['property'];
		$formObject = $this->viewHelperVariableContainer->get('Tx_Fluid_ViewHelpers_FormViewHelper', 'formObject');
		$uploadFolder = $this->infoService->getUploadFolder($formObject, $propertyName);

		$this->documentHead->includeFiles(array(
			$scriptPath . 'GearsInit.js',
			$pluploadPath . 'plupload.full.js',
			$pluploadPath . 'jquery.plupload.queue/jquery.plupload.queue.js',
			$pluploadPath . 'jquery.ui.plupload/jquery.ui.plupload.js',
			$pluploadPath . 'jquery.ui.plupload/css/jquery.ui.plupload.css',
			$pluploadPath . 'jquery.plupload.queue/css/jquery.plupload.queue.css',
			$scriptPath . 'FileListEditor.js',
			t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Stylesheet/MultiUpload.css'
		));

			// create JSON objects for each existing file
		foreach ($existingFiles as $k=>$file) {
			$size = (string) intval(filesize(PATH_site . $uploadFolder . DIRECTORY_SEPARATOR . $file));
			$existingFiles[$k] = array(
				'id' => "f$k",
				'name' => $file,
				'size' => $size,
				'percent' => 100,
				'completed' => $size,
				'status' => 1,
				'existing' => TRUE
			);
		}

		$buttons = explode(',', $this->arguments['buttons']);
		$resize = array();
		if ($this->arguments['resizeWidth']) {
			$resize['width'] = $this->arguments['resizeWidth'];
		}
		if ($this->arguments['resizeHeight'] > 0) {
			$resize['height'] = $this->arguments['resizeHeight'];
		}
		if (count($resize) > 0) {
			$resize['quality'] = $this->arguments['resizeQuality'];
		}

		$options = array(
			'url' => $this->getUrl(),
			'runtimes' => $this->arguments['runtimes'],
			'autostart' => $this->arguments['autostart'],
			'filters' => $this->arguments['filters'],
			'files' => $existingFiles,
			'max_file_size' => $this->arguments['maxFileSize'],
			'chunk_size' => $this->arguments['chunkSize'],
			'header' => $this->arguments['header'],
			'header_title' => $this->arguments['headerTitle'],
			'header_subtitle' => $this->arguments['headerSubtitle'],
			'resize' => $resize,
			'buttons' => array(
				'browse' => in_array('browse', $buttons),
				'start' => in_array('start', $buttons),
				'stop' => in_array('stop', $buttons),
			)
		);
		$optionsJson = $this->jsonService->encode($options);
		// remove last }
		$optionsJson = substr($optionsJson,0, -1);
		if(!empty($this->arguments['preinit'])) {
			$optionsJson .= ',"preinit":{';
			$preInitHandler = array();
			foreach($this->arguments['preinit'] as $preInitEvent => $preInitEventHandler) {
				$preInitHandler[] = $preInitEvent . ':' . $preInitEventHandler;
			}
			$optionsJson .= implode(',',$preInitHandler) . '}';
		}
		if(!empty($this->arguments['init'])) {
			$optionsJson .= ',"init":{';
			$initHandler = array();
			foreach($this->arguments['init'] as $initEvent => $initEventHandler) {
				$initHandler[] = $initEvent . ':' . $initEventHandler;
			}
			$optionsJson .= implode(',',$initHandler) . '}';
		}
		$optionsJson .= '}';
		$this->documentHead->includeHeader("
			var {$this->uniqueId} = null;
			var {$this->uniqueId}options = {$optionsJson};
			jQuery(document).ready(function() { {$this->uniqueId} = jQuery('#{$this->uniqueId}').fileListEditor({$this->uniqueId}options); });", 'js'
		);
	}

	/**
	 * Returns a URL appropriate for the current controller and Domain Object
	 * to use the "upload" action
	 * @return string
	 */
	public function getUrl() {
		$formObject = $this->viewHelperVariableContainer->get('Tx_Fluid_ViewHelpers_FormViewHelper', 'formObject');
		$propertyName = $this->arguments['property'];
		if ($this->arguments['url']) {
			$url = $this->arguments['url'];
		} else if ($formObject && $propertyName) {
			$formObjectClass = get_class($formObject);
			$controllerName = $this->controllerContext->getRequest()->getControllerName();
			$pluginName = $this->controllerContext->getRequest()->getPluginName();
			$extensionName = $this->controllerContext->getRequest()->getControllerExtensionName();
			$arguments = array(
				'objectType' => $formObjectClass,
				'propertyName' => $propertyName
			);
			$url = $this->controllerContext->getUriBuilder()
				->uriFor('upload', $arguments, $controllerName, $extensionName, $pluginName);
			$url = '/' . $url; // Why, O why, must baseUrl not be respected in JS in browsers?
		} else {
			throw new Tx_Fluid_Exception('Multiupload ViewHelper requires either url argument or associated form object', 1312051527);
		}
		return $url;
	}

}
?>