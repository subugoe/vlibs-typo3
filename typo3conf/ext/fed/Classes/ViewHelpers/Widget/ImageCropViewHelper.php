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
 ***************************************************************/

/**
 * Image Crop Widget
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers/Widget
 */
class Tx_Fed_ViewHelpers_Widget_ImageCropViewHelper extends Tx_Fluid_Core_Widget_AbstractWidgetViewHelper {

	/**
	 * @var boolean
	 */
	protected $ajaxWidget = TRUE;

	/**
	 * @var Tx_Fed_ViewHelpers_Widget_Controller_ImagecCropController
	 */
	protected $controller;

	/**
	 * @param Tx_Fed_ViewHelpers_Widget_Controller_ImageCropController $controller
	 */
	public function injectController(Tx_Fed_ViewHelpers_Widget_Controller_ImageCropController $controller) {
		$this->controller = $controller;
	}

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerArgument('id', 'string', 'DOM ID of image element', FALSE, 'cropper');
		$this->registerArgument('url', 'string', 'Alternative URL to use when posting the crop/resize request', FALSE);
		$this->registerArgument('src', 'string', 'Filename of image to crop', TRUE);
		$this->registerArgument('path', 'string', 'Site-relative path to image', TRUE);
		$this->registerArgument('placeholderImage', 'string', 'Optional image to use as placeholder when specified image does not exist', FALSE);
		$this->registerArgument('placeholderText', 'string', 'Text to use as placeholder (and ALT-tag value) when no image is specified or the file does not exist', FALSE, 'File does not exist');
		$this->registerArgument('uploader', 'string', 'DOM ID of plupload instance which uploads images for this cropper (use MultiUploadViewHelper)', FALSE);
		$this->registerArgument('largeWidth', 'integer', 'Width of large image', FALSE, 500);
		$this->registerArgument('largeHeight', 'integer', 'Height of large image', FALSE, 500);
		$this->registerArgument('aspectRatio', 'float', 'Override automatically detected aspect ratio with this decimal ratio', FALSE, 1);
		$this->registerArgument('previewWidth', 'integer', 'Width of preview image', FALSE, 250);
		$this->registerArgument('previewHeight', 'integer', 'Height of preview image', FALSE, 250);
		$this->registerArgument('maxWidth', 'integer', 'Maximum width of large image', FALSE, 1024);
		$this->registerArgument('maxHeight', 'integer', 'Maximum height of large image', FALSE, 768);
		$this->registerArgument('cropButtonLabel', 'string', 'Text for crop button', FALSE, 'Crop image');
		$this->registerArgument('resetButtonLabel', 'string', 'Text for crop button', FALSE, 'Reset');
		$this->registerArgument('sections', 'string', 'CSV list of section names to render, in sequence (Preview,Large,Button)', FALSE, 'Large,Preview,Button');
		$this->registerArgument('memoryLimit', 'string', 'Memory limit to enforce during cropping operations', FALSE, '1024m');
	}

	/**
	 * @return string
	 */
	public function render() {
		return $this->initiateSubRequest();
	}

}

?>