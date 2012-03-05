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
class Tx_Fed_ViewHelpers_JQuery_LightboxViewHelper extends Tx_Fed_ViewHelpers_Resource_ImageViewHelper {
	
	/**
	 * Initialize
	 */
	public function initializeArguments() {
		parent::initializeArguments();
		$this->registerArgument('thumbnailWidth', 'string', 'Width of thumbnail images, supports "300c" notations - see TS IMAGE', TRUE);
		$this->registerArgument('thumbnailHeight', 'string', 'Height of thumbnail images, supports "300c" notations - see TS IMAGE', TRUE);
		$this->registerArgument('template', 'string', 'Fluid template file (partial) to use for rendering images', FALSE, t3lib_extMgm::siteRelPath('fed') . 'Resources/Private/Partials/Lightbox.html');
		$this->registerArgument('script', 'string', 'Site-relative path to Javascript file containing lightbox code', FALSE, t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/com/jquery/plugins/jquery.slimbox.min.js');
		$this->registerArgument('style', 'string', 'Site-relative path to Stylesheet file for lightbox', FALSE, t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Stylesheet/Slimbox.css');
	}
	
	/**
	 * Render a simple list of files with links to that file
	 *
	 * @param array $files
	 * @return string
	 */
	public function renderFileList(array $files) {
		$content = $this->renderChildren();
		$this->includeFile($this->arguments['script']);
		$this->includeFile($this->arguments['style']);
		if (count($files) == 0) {
			return '';
		} else if (strlen(trim($content)) > 0) {
			return $content;
		} else {
			$view = $this->objectManager->get('Tx_Fluid_View_StandaloneView');
			$view->setTemplatePathAndFilename(PATH_site . $this->arguments['template']);
			$view->assign('files', $files);
			$view->assign('arguments', $this->arguments);
			return $view->render();
		}
		return $html;
	}
	
	
	
}
?>