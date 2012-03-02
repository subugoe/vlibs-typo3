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
 * Image Crop Widget Controller
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers/Widget
 */
class Tx_Fed_ViewHelpers_Widget_Controller_ImageCropController extends Tx_Fluid_Core_Widget_AbstractWidgetController {

	/**
	 * Initialize action
	 */
	public function initializeAction() {
	}

	/**
	 * @return string
	 */
	public function indexAction() {
		$transferArguments = array(
			'id', 'url',
			'path', 'src', 'placeholderImage', 'placeholderText',
			'uploader', 'largeWidth', 'largeHeight', 'preview', 'previewWidth', 'previewHeight',
			'maxWidth', 'maxHeight', 'aspectRatio',
			'cropButtonLabel', 'resetButtonLabel', 'sections'
		);
		foreach ($transferArguments as $argumentName) {
			$this->view->assign($argumentName, $this->widgetConfiguration[$argumentName]);
		}
		return $this->view->render();
	}

	/**
	 * Crop $imageFile according to $cropData
	 *
	 * @param string $imageFile
	 * @param array $cropData
	 * @return string
	 */
	public function cropAction($imageFile, array $cropData) {
		$filename = PATH_site . $imageFile;
		$pathinfo = pathinfo($filename);
		$filenameCropped = $pathinfo['dirname'] . '/crop_' . basename($filename);
		$memoryLimit = ini_set('memory_limit', $this->widgetConfiguration['memoryLimit']);
		$returnValue = 0;
		$extension = strtolower($pathinfo['extension']);
		if ($extension == 'png') {
			$im = imagecreatefrompng($filename);
		} elseif ($extension == 'jpg' || $extension == 'jpeg') {
			$im = imagecreatefromjpeg($filename);
		} else {
			$im = imagecreatefromstring(file_get_contents($filename));
		}
		if ($im) {
			if (file_exists($filenameCropped)) {
				unlink($filenameCropped);
			}
			foreach ($cropData as $index=>$value) {
				if ($index != 'scale') {
					$cropData[$index] = intval($value);
				}
			}
			$maximumWidth = $this->widgetConfiguration['maxWidth'];
			if ($cropData['w'] > $maximumWidth) {
				$ratio = $maximumWidth / $cropData['w'];
			} else {
				$ratio = 1;
			}
			$cropped = imagecreatetruecolor(intval($cropData['w'] * $ratio), intval($cropData['h'] * $ratio));
			$copied = imagecopyresampled(
				$cropped,
				$im,
				0,
				0,
				$cropData['x'],
				$cropData['y'],
				$cropData['w'] * $ratio,
				$cropData['h'] * $ratio,
				$cropData['w'],
				$cropData['h']
			);
			switch (strtolower($pathinfo['extension'])) {
				case 'gif':
					imagegif($cropped, $filenameCropped);
					break;
				case 'png':
					imagepng($cropped, $filenameCropped);
					break;
				default:
					imagejpeg($cropped, $filenameCropped);
			}
			$returnValue = basename($filenameCropped);
		}
		ini_set('memory_limit', $memoryLimit);
		return $returnValue;
	}

}

?>