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
 * Template Rendering Controller
 *
 * @package Fed
 * @subpackage Controller
 */
class Tx_Fed_Controller_TemplateController extends Tx_Fed_MVC_Controller_AbstractController {

	/**
	 * @var string
	 */
	protected $defaultViewObjectName = 'Tx_Fed_MVC_View_ExposedTemplateView';

	/**
	 * @param Tx_Fed_MVC_View_ExposedTemplateView $view
	 */
	public function initializeView(Tx_Fed_MVC_View_ExposedTemplateView $view) {
		$json = $this->objectManager->get('Tx_Fed_Utility_JSON');
		$flexform = $this->getFlexForm();
		if ($flexform['templateFile']) {
			$view->setTemplatePathAndFilename(PATH_site . $flexform['templateFile']);
		} else if ($flexform['templateSource']) {
			$source = $flexform['templateSource'];
			$tempFile = tempnam(PATH_site . 'typo3temp/', md5($source));
			file_put_contents($tempFile, $source);
			$view->setTemplatePathAndFilename($tempFile);
		}
		if ($flexform['fluidVars']) {
			$object = $json->decode($flexform['fluidVars']);
			foreach ($object as $k=>$v) {
				$view->assign($k, $v);
			}
		}
	}

	/**
	 * Show template as defined in flexform
	 * @return string
	 */
	public function showAction() {
		try {
			$content = $this->view->render();
		} catch (Exception $e) {
			$content = 'Error rendering template: ' . $e->getMessage();
		}

		return $content;
	}

}

?>