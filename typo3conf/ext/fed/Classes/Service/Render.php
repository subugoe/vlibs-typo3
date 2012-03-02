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
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage Service
 */
class Tx_Fed_Service_Render implements t3lib_Singleton {

	/**
	 * @var Tx_Extbase_Object_ObjectManager
	 */
	protected $objectManager;

	/**
	 * @param Tx_Extbase_Object_ObjectManager $objectManager
	 */
	public function injectObjectManager(Tx_Extbase_Object_ObjectManager $objectManager) {
		$this->objectManager = $objectManager;
	}

	/**
	 * Renders a relative-path partial template, fx from fileadmin/templates/
	 * Passes arguments to template
	 *
	 * @param string $templatePath The relative path to the Fluid template file
	 * @param array $arguments The arguments (template vars) for the template
	 * @return string
	 * @api
	 */
	public function renderTemplateFile($templateFile, $arguments=NULL) {
		$view = $this->objectManager->get('Tx_Fluid_View_StandaloneView');
		$view->setTemplatePathAndFilename(PATH_site . $templateFile);
		if ($arguments) {
			$view->assignMultiple($arguments);
		}
		return $view->render();
	}

}

?>