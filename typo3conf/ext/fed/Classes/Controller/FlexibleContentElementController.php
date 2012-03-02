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
*  the Free Software Foundation; either version 3 of the License, or
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
 * Flexible Content Element Plugin Rendering Controller
 *
 * @package Fed
 * @subpackage Controller
 */
class Tx_Fed_Controller_FlexibleContentElementController extends Tx_Fed_Core_AbstractController {

	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'Tx_Flux_MVC_View_ExposedTemplateView';

	/**
	 * @param Tx_Fed_MVC_View_ExposedTemplateView $view
	 */
	public function initializeView(Tx_Flux_MVC_View_ExposedTemplateView $view) {
		$cObj = $this->request->getContentObjectData();
		$this->flexform->setContentObjectData($cObj);
		$configurationManager = $this->objectManager->get('Tx_Fed_Configuration_ConfigurationManager');
		list ($extensionName, $filename) = explode(':', $cObj['tx_fed_fcefile']);
		$paths = $configurationManager->getContentConfiguration($extensionName);
		$absolutePath = $paths['templateRootPath'] . DIRECTORY_SEPARATOR . $filename;
		$view->setLayoutRootPath($paths['layoutRootPath']);
		$view->setPartialRootPath($paths['partialRootPath']);
		$view->setTemplatePathAndFilename($absolutePath);
		$config = $view->getStoredVariable('Tx_Flux_ViewHelpers_FlexformViewHelper', 'storage', 'Configuration');
		$view->assignMultiple($this->flexform->getAllAndTransform($config['fields']));
		$view->assign('page', $GLOBALS['TSFE']->page);
		$view->assign('record', $cObj);
	}

	/**
	 * Show template as defined in flexform
	 * @return string
	 */
	public function renderAction() {
		return $this->view->render();
	}

}

?>