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
 * Image ViewHelper
 *
 * In addition to doing all that f:image does, this ViewHelper supports:
 *
 * - multi-image rendering using wildcard filenames, path + CSV-of-filenames or
 *   array of files.
 * - automatic click-enlarge version of multiple images through a single tag.
 * - use of alternative image (for all images) if "src" is not a file
 *
 *
 *
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers
 *
 */
class Tx_Fed_ViewHelpers_ImageViewHelper extends Tx_Fluid_ViewHelpers_ImageViewHelper {

	/**
	 * @var Tx_Fed_Utility_DocumentHead
	 */
	protected $documentHead;

	/**
	 * @param Tx_Fed_Utility_DocumentHead $documentHead
	 * @return void
	 */
	public function injectDocumentHead(Tx_Fed_Utility_DocumentHead $documentHead) {
		$this->documentHead = $documentHead;
	}

	/**
	 * Initialize arguments
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerTagAttribute('alt', 'string', 'Specifies an alternate text for an image', FALSE, NULL);
		$this->registerTagAttribute('ismap', 'string', 'Specifies an image as a server-side image-map. Rarely used. Look at usemap instead', FALSE);
		$this->registerTagAttribute('longdesc', 'string', 'Specifies the URL to a document that contains a long description of an image', FALSE);
		$this->registerTagAttribute('usemap', 'string', 'Specifies an image as a client-side image-map', FALSE);
		$this->registerArgument('src', 'mixed', 'Filename(s) to render', TRUE);
		$this->registerArgument('width', 'mixed', 'Width of image. Supports "200c" notation - see cObj IMAGE in TSref');
		$this->registerArgument('height', 'mixed', 'Height of image. Supports "200c" notation - see cObj IMAGE in TSref');
		$this->registerArgument('minWidth', 'integer', 'Minimum allowed width of image. Supports "200c" notation - see cObj IMAGE in TSref');
		$this->registerArgument('minHeight', 'integer', 'Minimum allowed height of image. Supports "200c" notation - see cObj IMAGE in TSref');
		$this->registerArgument('maxWidth', 'integer', 'Maximum allowed width of image. Supports "200c" notation - see cObj IMAGE in TSref');
		$this->registerArgument('maxHeight', 'integer', 'Maximum allowed height of image. Supports "200c" notation - see cObj IMAGE in TSref');
		$this->registerArgument('path', 'string', 'Using this triggers CSV filename parsing but still allows rendering a single image if only one is specified', FALSE, NULL);
		$this->registerArgument('altsrc', 'string', 'Displays this image if "src" is not a file', FALSE, NULL);
		$this->registerArgument('divider', 'string', 'String divider to insert between images', FALSE, NULL);
		$this->registerArgument('largeWidth', 'string', 'Specify this to render a large version of files too, for switch-viewing', FALSE);
		$this->registerArgument('largeHeight', 'string', 'Specify this to render a large version of files too, for switch-viewing', FALSE);
		$this->registerArgument('largePosition', 'string', 'Controls where large image goes. Use top, left, right or bottom - is added as class on large img', FALSE, 'left');
		$this->registerArgument('sortBy', 'string', 'Sort field of multiple files. Possible: filename, filesize, modified, created, size, size:x, size:y, exif:<fieldname> - "size" mode means (w+h) size becomes sort value', FALSE, NULL);
		$this->registerArgument('sortDirection', 'string', 'Direction to sort', FALSE, 'ASC');
		$this->registerArgument('clickenlarge', 'boolean', 'Change to FALSE if you do not want actions and script added if large version is rendered', FALSE, TRUE);
		$this->registerArgument('limit', 'integer', 'Specify to limit the number of images which may be rendered');
		$this->registerArgument('lightbox', 'boolean', 'If TRUE, creates a lightbox from the tag content', FALSE, FALSE);
		$this->registerArgument('mouseoverSuffix', 'string', 'Suffix for the mouseover image. The picture must be in the same folder like the default image.', FALSE, '');

	}

	/**
	 * Render the image(s) to HTML
	 *
	 * @return string
	 */
	public function render() {
		$pathinfo = pathinfo($this->arguments['src']);
		if ($pathinfo['filename'] === '*') {
			$images = $this->documentHead->getFilenamesOfType($pathinfo['dirname'], $pathinfo['extension']);
		} else if ($this->arguments['path']) {
			$src = trim(trim($this->arguments['src']), ',');
			if (strlen($src) === 0) {
				return '';
			}
			$images = explode(',', $src);
			// patch for CSV files missing relative pathnames and possible missing files
			foreach ($images as $k=>$v) {
				$images[$k] = $this->arguments['path'] . $v;
			}
		} else if (is_array($this->arguments['src'])) {
			$images = $this->arguments['src'];
		} else {
			$images = array($this->arguments['src']);
		}
		if ($this->arguments['sortBy'] !== NULL) {
			$images = $this->sortImages($images);
		}

		if ($this->arguments['limit'] > 0) {
			$images = array_slice($images, 0, $this->arguments['limit']);
		}


		if (count($images) === 0) {
			return '';
		}

		// use altsrc for any image not present
		foreach ($images as $k=>$v) {
			if (is_file(PATH_site . $images[$k]) === FALSE) {
				$images[$k] = $this->arguments['altsrc'];
			}
		}
		return $this->renderImages($images);
	}

	/**
	 * Render the images into HTML
	 *
	 * @param array $files
	 * @param boolean $returnConverted
	 * @return string
	 */
	protected function renderImages(array $images, $returnConverted=FALSE) {
		global $TYPO3_CONF_VARS;
		$converted = array();
		$lines = array();
		$setup = array(
			'width' => $this->arguments['width'],
			'height' => $this->arguments['height'],
			'minW' => $this->arguments['minWidth'],
			'minH' => $this->arguments['minHeight'],
			'maxW' => $this->arguments['maxWidth'],
			'maxH' => $this->arguments['maxHeight'],
		);
		if ($this->arguments['clickenlarge'] === TRUE) {
			$this->addScript();
		}

		if ($this->arguments['id']) {
			$uniqid = $this->arguments['id'];
		} else {
			$uniqid = uniqid('fed-xl-');
		}
		if ($this->arguments['largeWidth'] > 0 || $this->arguments['largeHeight'] > 0) {
			$largeSetup = array(
				'width' => $this->arguments['largeWidth'],
				'height' => $this->arguments['largeHeight'],
				'minW' => $this->arguments['largeWidth'],
				'minH' => $this->arguments['largeHeight'],
				'maxW' => $this->arguments['largeWidth'],
				'maxH' => $this->arguments['largeHeight'],
			);
			$large = array();
			foreach ($images as $image) {
				$large[] = $this->renderImage($image, $largeSetup);
			}
			$convertedImageFilename = $this->renderImage($images[0], $largeSetup);
			$this->tag->addAttribute('width', $this->arguments['largeWidth']);
			$this->tag->addAttribute('height', $this->arguments['largeHeight']);
			$this->tag->addAttribute('class', 'large ' . $this->arguments['largePosition']);
			$this->tag->addAttribute('id', $uniqid);
			$this->tag->addAttribute('src', $convertedImageFilename[0]);
			$lines[] = $this->tag->render();
			$this->tag->removeAttribute('id');
		}
		foreach ($images as $k=>$image) {
			$convertedImageFilename = $this->renderImage($image, $setup);
			$imagesize = getimagesize(PATH_site . $convertedImageFilename[0]);
			$this->tag->addAttribute('width', $imagesize[0]);
			$this->tag->addAttribute('height', $imagesize[1]);
			$this->tag->addAttribute('src', $convertedImageFilename[0]);
			if ($large && $this->arguments['clickenlarge'] === TRUE) {
				$this->tag->addAttribute('onclick', 'fedImgXL(\'' . $uniqid . '\', \'' . $large[$k][0] . '\');');
				$this->tag->addAttribute('class', 'small');
				$this->tag->removeAttribute('id'); // avoid DOM ID collisions
			}
			if ($this->arguments['mouseoverSuffix'] != '') {
				$this->tag->addAttribute('onmouseover', 'this.src="' . $convertedImageFilename[1] . '"');
				$this->tag->addAttribute('onmouseout', 'this.src="' . $convertedImageFilename[0] . '"');
			}
			$lines[] = $this->tag->render();
		}
		$html = implode($this->arguments['divider'], $lines);
		return $html;
	}

	/**
	 * Sort the images as defined by arguments
	 *
	 * @param array $images
	 * @return array
	 */
	protected function sortImages(array $images) {
		$sorted = array();
		foreach ($images as $image) {
			$index = $this->getSortValue($image);
			while (isset($sorted[$index])) {
				$index = $this->findNewIndex($index);
			}
			$sorted[$index] = $image;
		}
		if ($this->arguments['sortDirection'] === 'ASC') {
			ksort($sorted);
		} else {
			krsort($sorted);
		}
		return array_values($sorted);
	}

	/**
	 * Support for sortImages - finds new index for array
	 *
	 * @param mixed $index
	 * @return mixed
	 */
	protected function findNewIndex($index) {
		if (is_numeric($index)) {
			return $index+1;
		} else {
			return $index . 'a';
		}
	}

	/**
	 * Gets the value used for sort index for this image
	 *
	 * @param string $src
	 * @return mixed
	 */
	protected function getSortValue($src) {
		$field = $this->arguments['sortBy'];
		$src = (string) $src;
		list ($field, $subfield) = explode(':', $field);
		switch ($field) {
			case 'filesize': return (is_file(PATH_site . $src) ? filesize(PATH_site . $src) : 0);
			case 'mofified': return (is_file(PATH_site . $src) ? filemtime(PATH_site . $src) : 0);
			case 'created': return (is_file(PATH_site . $src) ? filectime(PATH_site . $src) : 0);
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
			case 'filename':
			default: return $src;
		}
	}

	/**
	 * Reads EXIF info in $field for $src
	 *
	 * @param string $src
	 * @return mixed
	 */
	protected function readExifInfoField($src, $field) {
		$exif = $this->readExifInfo($src);
		if (is_array($exif)) {
			return $exif[$field];
		} else {
			return NULL;
		}
	}

	/**
	 * Reads EXIF info for $src
	 *
	 * @param string $src
	 * @return array
	 */
	protected function readExifInfo($src) {
		return @exif_read_data($src);
	}

	/**
	 * Returns the proper new src value for an img tag
	 *
	 * @param string $src
	 * @param array $setup
	 * @return string
	 */
	protected function renderImage($src, $setup) {
		if (TYPO3_MODE === 'BE' && substr($src, 0, 3) === '../') {
			$src = substr($src, 3);
		}

		$imageInfo = $this->contentObject->getImgResource($src, $setup);

		$GLOBALS['TSFE']->lastImageInfo = $imageInfo;
		if (!is_array($imageInfo)) {
			throw new Tx_Fluid_Core_ViewHelper_Exception('Could not get image resource for "' . htmlspecialchars($src) . '".' , 1253191060);
		}

		$imageInfo[3] = t3lib_div::png_to_gif_by_imagemagick($imageInfo[3]);

		$GLOBALS['TSFE']->imagesOnPage[] = $imageInfo[3];

		$imageSource = $GLOBALS['TSFE']->absRefPrefix . t3lib_div::rawUrlEncodeFP($imageInfo[3]);
		if (TYPO3_MODE === 'BE') {
			$imageSource = '../' . $imageSource;
			$this->resetFrontendEnvironment();
		}

		if($this->arguments['mouseoverSuffix'] != '') {
			$srcImg = explode('.',$src);
			$srcMouseoverImg = $srcImg[0].$this->arguments['mouseoverSuffix'].'.'.$srcImg[1];
			$imageInfoMouseover = $this->contentObject->getImgResource($srcMouseoverImg, $setup);
			if (!is_array($imageInfoMouseover)) {
				throw new Tx_Fluid_Core_ViewHelper_Exception('Could not get image resource for "' . htmlspecialchars($srcMouseoverImg) . '".' , 1253191060);
			}
			$imageInfoMouseover[3] = t3lib_div::png_to_gif_by_imagemagick($imageInfoMouseover[3]);
			$GLOBALS['TSFE']->imagesOnPage[] = $imageInfoMouseover[3];
			$imageSourceOver = $GLOBALS['TSFE']->absRefPrefix . t3lib_div::rawUrlEncodeFP($imageInfoMouseover[3]);
			if (TYPO3_MODE === 'BE') {
				$imageSourceOver = '../' . $imageSourceOver;
				$this->resetFrontendEnvironment();
			}
		}
		return array($imageSource,$imageSourceOver);
	}

	/**
	 * Attach the scripts necessary for clickenlarge
	 *
	 * @return void
	 */
	protected function addScript() {
		$script = "function fedImgXL(parent, filename) { document.getElementById(parent).src = filename; };";
		$this->documentHead->includeHeader($script, 'js');
	}

}

?>
