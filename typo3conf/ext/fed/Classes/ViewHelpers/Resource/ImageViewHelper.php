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
 *
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers/Resource
 *
 */
class Tx_Fed_ViewHelpers_Resource_ImageViewHelper extends Tx_Fed_ViewHelpers_Resource_FileViewHelper {


	/**
	 * Initialize arguments relevant for image-type resource ViewHelpers
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('exif', 'boolean', 'Read exif metadata', FALSE, FALSE);
		$this->registerArgument('resolution', 'boolean', 'Read resolution metadata', FALSE, TRUE);
	}

	/**
	 * Render / process
	 *
	 * @return string
	 */
	public function render() {
		// if no "as" argument and no child content, return linked list of files
		// else, assign variable "as"
		$pathinfo = pathinfo($this->arguments['path']);
		if ($this->arguments['file']) {
			$files = array($this->arguments['path'] . $this->arguments['file']);
			$files = $this->arrayToFileObjects($files);
			$file = array_pop($files);
			if ($this->arguments['as']) {
				$this->templateVariableContainer->add($this->arguments['as'], $file);
				return;
			} else if ($this->arguments['return'] === TRUE) {
				return $file;
			} else {
				$this->templateVariableContainer->add('image', $file);
				$content = $this->renderChildren();
				$this->templateVariableContainer->remove('image');
				if (strlen(trim($content)) === 0) {
					return $this->renderFileList($files);
				} else {
					return $content;
				}
			}
		} else if ($this->arguments['files']) {
			$files = $this->arguments['files'];
			if ($this->arguments['path']) {
				foreach ($files as $k=>$file) {
					$files[$k] = $this->arguments['path'] . $file;
				}
			}
		} else if ($pathinfo['filename'] === '*') {
			$files = $this->documentHead->getFilenamesOfType($pathinfo['dirname'], $pathinfo['extension']);
		} else if (is_dir($pathinfo['dirname'] . '/' . $pathinfo['basename'])) {
			$files = scandir($pathinfo['dirname'] . '/' . $pathinfo['basename']);
			foreach ($files as $k=>$file) {
				$file = $pathinfo['dirname'] . '/' . $pathinfo['basename'] . '/' . $file;
				if (is_dir($file)) {
					unset($files[$k]);
				} else if (substr($file, 0, 1) === '.') {
					unset($files[$k]);
				} else {
					$files[$k] = $file;
				}
			}
		} else {
			return '';
			//throw new Exception('Invalid path given to Resource ViewHelper', $code);
		}
		$files = $this->arrayToFileObjects($files);
		$files = $this->sortFiles($files);

		// rendering
		$content = "";
		if ($this->arguments['as']) {
			$this->templateVariableContainer->add($this->arguments['as'], $files);
		} else if ($this->arguments['return'] === TRUE) {
			return $files;
		} else {
			$this->templateVariableContainer->add('images', $files);
			$content = $this->renderChildren();
			$this->templateVariableContainer->remove('images');
			// possible return: HTML file list
			if (strlen(trim($content)) === 0) {
				return $this->renderFileList($files);
			} else {
				return $content;
			}
		}
	}

	/**
	 * @param array $files
	 * @return array
	 */
	protected function arrayToFileObjects(array $files) {
		$files = parent::arrayToFileObjects($files);
		if ($this->arguments['exif'] === TRUE) {
			$files = $this->applyExifData($files);
		}
		if ($this->arguments['resolution'] === TRUE) {
			$files = $this->applyResolutionData($files);
		}
		return $files;
	}

	/**
	 * Adds support for sorting on new extended sort properties "size" and "exif"
	 * @param type $src
	 * @return type
	 */
	protected function getSortValue($src) {
		$field = $this->arguments['sortBy'];
		list ($field, $subfield) = explode(':', $field);
		switch ($field) {
			case 'size':
				if (is_file(PATH_site . $src) === FALSE) {
					return 0;
				}
				list ($w, $h) = getimagesize(PATH_site . $src);
				switch ($subfield) {
					case 'w': return $w;
					case 'h': return $h;
					default: return ($w*$h);
				}
			case 'exif': return $this->readExifInfoField(PATH_site . $src, $subfield);
			default: return parent::getSortValue($src);
		}
	}

	/**
	 * Applies resolution information to metadata for all $images
	 *
	 * @param array $images
	 */
	protected function applyResolutionData(array $images) {
		foreach ($images as $k=>$image) {
			$metadata = (array) $image->getMetadata();
			$resolution = getimagesize($image->getAbsolutePath());
			$resolution['width'] = $resolution[0];
			$resolution['height'] = $resolution[1];
			$metadata['resolution'] = $resolution;
			$images[$k]->setMetadata($metadata);
		}
		return $images;
	}

	/**
	 * Applies EXIF information to metadata for all $images
	 *
	 * @param array $images
	 */
	protected function applyExifData(array $images) {
		foreach ($images as $k=>$image) {
			$metadata = (array) $image->getMetadata();
			$metadata['exif'] = exif_read_data($image->getAbsolutePath());
			$images[$k]->setMetadata($metadata);
		}
		return $images;
	}

}

?>
