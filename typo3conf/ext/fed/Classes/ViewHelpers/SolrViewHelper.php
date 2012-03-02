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
 * @author Claus Due <claus@wildside.dk>, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers
 */
class Tx_Fed_ViewHelpers_SolrViewHelper extends Tx_Fed_Core_ViewHelper_AbstractJQueryViewHelper {

	/**
	 * @var Tx_Extbase_Configuration_FrontendConfigurationManager
	 */
	protected $configurationManager;

	/**
	 * @param Tx_Extbase_Configuration_FrontendConfigurationManager $configurationManager
	 */
	public function injectConfigurationManager(Tx_Extbase_Configuration_FrontendConfigurationManager $configurationManager) {
		$this->configurationManager = $configurationManager;
	}

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerArgument('template', 'string', 'Optional filename of custom template to use - see manual', FALSE, t3lib_extMgm::extPath('fed', 'Resources/Private/Templates/Solr/Index.html'));
		$this->registerArgument('layoutRootPath', 'string', 'Full path to layout(s) which can render SOLR templates', FALSE, t3lib_extMgm::extPath('fed', 'Resources/Private/Templates/Solr/Layouts/'));
		$this->registerArgument('stylesheet', 'string', 'Relative URI of stylesheet to use', FALSE, (t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Stylesheet/Solr.css'));
		$this->registerArgument('script', 'string', 'Relative URI of script to use', FALSE, (t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/DefaultSolrApp.js'));
	}

	/**
	 * Render
	 */
	public function render() {
		$jsFile = t3lib_extMgm::siteRelPath('fed') . 'Resources/Public/Javascript/SolrService.js';
		$tsAll  = $this->configurationManager->getTypoScriptSetup();
		$tsAll = Tx_Extbase_Utility_TypoScript::convertTypoScriptArrayToPlainArray($tsAll);
		$json = $this->jsonService->encode($tsAll['plugin']['tx_solr']);
		$script = "jQuery(document).ready(function() { FED.SOLR.setConfig({$json}); });";
		$this->includeFile($jsFile);
		$this->includeHeader($script, 'js');
		if ($this->arguments['script']) {
			$this->includeFile($this->arguments['script']);
		}
		if ($this->arguments['stylesheet']) {
			$this->includeFile($this->arguments['stylesheet']);
		}
		$view = $this->documentHead->getTemplate($this->arguments['template']);
		$view->setLayoutRootPath($this->arguments['layoutRootPath']);
		$view->assignMultiple($tsAll['plugin']['tx_solr']);
		$view->assign('config', $json);
		return $view->render();
	}


}

?>
