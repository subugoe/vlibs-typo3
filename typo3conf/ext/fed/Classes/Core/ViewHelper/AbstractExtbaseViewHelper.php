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
 * @subpackage ViewHelpers\Extbase
 */
class Tx_Fed_Core_ViewHelper_AbstractExtbaseViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Arguments which are added to template variable container for tag content rendering
	 * @var array $transferArguments
	 */
	protected $transferArguments = array('object', 'controllerName', 'extensionName', 'pluginName', 'action', 'pageUid');

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $infoService;

	/**
	 * @var string $action
	 */
	protected $action;

	/**
	 * @var string
	 */
	protected $controllerName;

	/**
	 * @var string
	 */
	protected $extensionName;

	/**
	 * @var string
	 */
	protected $pluginName;

	/**
	 * @var Tx_Extbase_DomainObject_AbstractEntity
	 */
	protected $object;

	/**
	 * Register arguments for this ViewHelper
	 *
	 * @author Claus Due, Wildside A/S
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerArgument('class', 'string', 'Classname (CSS) for rendered container');
		$this->registerArgument('extensionName', 'string', 'Name of the extension which contains a handling Controller', FALSE, $this->getControllerName());
		$this->registerArgument('pluginName', 'string', 'Plugin name - overrides detection from specified object(s)', FALSE, $this->getPluginName());
		$this->registerArgument('controllerName', 'string', 'Controller name - overrides detection', FALSE, $this->getControllerName());
		$this->registerArgument('action', 'string', 'Action on the controller', TRUE, 'index');
		$this->registerArgument('displayType', 'string', 'Javascript class name to use', FALSE, $this->getDisplayType());
		$this->registerArgument('pageUid', 'int', 'Optional page on which the handling plugin is installed', FALSE, $GLOBALS['TSFE']->id);
		$this->registerArgument('typeNum', 'int', 'Optional page type to use for requests', FALSE, 4815162342);
		$this->registerArgument('name', 'string', 'Optional name of the instance', FALSE);
		$this->registerArgument('config', 'array', 'Optional base configuration - each value willl be overridden by other parameters if specified', FALSE, array());
	}

	/**
	 * @param Tx_Fed_Utility_DomainObjectInfo $infoService
	 */
	public function injectInfoService(Tx_Fed_Utility_DomainObjectInfo $infoService) {
		$this->infoService = $infoService;
	}

	/**
	 * Get name of (the current) Controller
	 *
	 * @return string
	 * @api
	 */
	public function getControllerName() {
		return $this->controllerName;
	}

	/**
	 * @param string $controllerName
	 */
	public function setControllerName($controllerName) {
		$this->controllerName = $controllerName;
	}

	/**
	 * Get name of (the current) Extension
	 *
	 * @author Claus Due, Wildside A/S
	 * @return string
	 * @api
	 */
	public function getExtensionName() {
		return $this->extensionName;
	}

	/**
	 * @param string $extensionName
	 */
	public function setExtensionName($extensionName) {
		$this->extensionName = $extensionName;
	}

	/**
	 * Get name of (the current) Plugin
	 *
	 * @author Claus Due, Wildside A/S
	 * @return string
	 * @api
	 */
	public function getPluginName() {
		return $this->pluginName;
	}

	/**
	 * @param string $pluginName
	 */
	public function setPluginName($pluginName) {
		$this->pluginName = $pluginName;
	}

	/**
	 * Get the currently selected controller action
	 *
	 * @return string
	 */
	public function getAction() {
		return $this->action;
	}

	/**
	 * Set the current controller action
	 *
	 * @param string $action
	 * @return void
	 */
	public function setAction($action) {
		$this->action = $action;
	}

	/**
	 * @param Tx_Extbase_DomainObject_AbstractEntity $object
	 * @return void
	 */
	public function setObject(Tx_Extbase_DomainObject_AbstractEntity $object) {
		$parts = explode('_', get_class($object));
		$extensionName = $parts[1];
		$controllerName = array_pop($controllerName);
		$controllerName = str_replace('Controller', '', $controllerName);
		$pluginName = Tx_Extbase_Utility_Extension::getPluginNameByAction($extensionName, $controllerName, $this->getAction());
		$this->setPluginName($pluginName);
		$this->setControllerName($controllerName);
		$this->setExtensionName($extensionName);
		$this->object = $object;
	}

	/**
	 * @return Tx_Extbase_DomainObject_AbstractEntity
	 */
	public function getObject() {
		return $this->object;
	}

	/**
	 * Renders tag content with managed template variables
	 * @return string
	 */
	public function renderChildren() {
		$defined = array();
		foreach ($this->transferArguments as $argument) {
			if ($this->templateVariableContainer->exists($argument) === FALSE) {
				$this->templateVariableContainer->add($argument, $this->arguments[$argument]);
				array_push($defined, $argument);
			}
		}
		$content = parent::renderChildren();
		foreach ($defined as $argument) {
			$this->templateVariableContainer->remove($argument);
		}
		return $content;
	}

	/**
	 * Get a prefix for a HTTP GET/POST request maching configuration
	 *
	 * @return string
	 */
	public function getRequestPrefix() {
		$ext = $this->getExtensionName();
		$ext = Tx_Extbase_Utility_Extension::convertCamelCaseToLowerCaseUnderscored($ext);
		$pi = $this->getPluginName();
		$pi = Tx_Extbase_Utility_Extension::convertCamelCaseToLowerCaseUnderscored($pi);
		$prefix = "tx_{$ext}_{$pi}";
		return $prefix;
	}

}

?>