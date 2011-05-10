<?php

/**
 * The class that controls request and response processing.
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2007 Elmar Hinz
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package    TYPO3
 * @subpackage lib
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @copyright  2006-2007 Elmar Hinz
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    SVN: $Id: class.tx_lib_controller.php 7333 2007-11-30 12:21:48Z elmarhinz $
 * @since      0.1
 */

/**
 * The class that controls request and response processing.
 * 
 * This is a member of the central quad: $controller, $parameters, $configurations, $context.<br>
 * Extend this class to build your controller. Address it from every controlled object as: 
 * <samp>$this->controller</samp>.
 *
 *
 * <b> This is a replacement of tslib::pi_base() by MVC architecture </b>
 *
 * Controllers of this kind can be used as plugin. Plugins are called from TS Setup 
 * in the typical plugin position <samp> tt_content.list.20.pluginKey </samp>. 
 *
 * The pluginKey is defined by the function t3lib_extMgm::addPlugin() within the file ext_tables.php 
 * as second element of the array that is handled as first parameter to the function:
 *
 *    <samp>t3lib_extMgm::addPlugin(array(pluginLabel,pluginKey), list_type)</samp>
 *
 * Just like tslib::pi_base() you can also use this controller as content element 
 * or as simpel USER_(INT) called from typoScript.
 *
 *
 * <b> You can also call this as a subcontroller from other controllers.</b>
 *
 *
 * <b> Flexible to extend by other extensions via registration instead of XCLASSing </b>
 *
 *
 * <b> Easily controlled by the action parameter </b>
 *
 * The controller dispatches the requests to action functions controlled by the action parameter.
 * The action parameter can come in from 3 sources:
 * 1. POST-request of a form.
 * 2. GET-request of a link.
 * 3. Statically set in the incomming TS configuration array.
 *
 *
 * <b> The parameter array name: designator </b>
 *
 * According to the coding guidelines parameters of plugins have to be send as array with a unique
 * identifier to keep them in their own namespace. In tslib_pibase this identifier is called:
 * $prefixId. In tx_lib it is called $designator.
 *
 * The easy way is to share a common designator throughout a whole extension. Unless the designator is
 * explicitly set as class variable the function getDesignator() defaults to the extension key. 
 * You can exchange the default designator by the function setDefaultDesignator(). (See: tx_lib_selfAwarness)
 *
 *
 * $TYPO3_CONF_VARS['CONTROLLERS']. (See: _findControllerAndAction()).
 *
 *
 * Depends on: tx_div, tx_lib_object <br>
 * Used by: inherited controllers of almost every plugin using this library
 *
 * @todo	   provide a function to register plugins.
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 */
class tx_lib_controller extends tx_lib_object {

	var $contextClassName = 'tx_lib_context'; 
	var $configurationsClassName = 'tx_lib_configurations'; // You may overwrite this in your subclass with an own configurations class.
	var $parametersClassName = 'tx_lib_parameters'; // Typically you don't need to make a subclass of this.
	var $context; // Object containing the context accessing object.
	var $configurations; // Object containing the configuration.
	var $parameters; // Object containing the request parameters.
	var $defaultAction = 'defaultAction'; // Default action.
	var $action; // Determined action.
	var $input; // The input string.
	var $output; // The output string.

	/**
	 * Main function of the controller
	 *
	 * Set up $context, $parameters and $configurations.
	 * Find action and registered extensions of the controller.
	 * Run controller and action.
	 *
	 * This function can be called in different ways:
	 *   a) As plugin or content element.
	 *   b) Directly included from typoScript.
	 *   c) As subcontroller from a controller.
	 *
	 * @param	 string   Incomming content if any. 
	 * @param  mixed    Array with the local TypoScript configuration. Object if called as subcontroller.
	 * @param  object   Context object if called as subcontroller.
	 * @param  object   Parameters object if called as subcontroller.
	 * @return string   The complete result of the plugin, typically it's (x)html
	 */
	function main($input, $configurations, $context=NULL, $parameters=NULL) {
		$this->input = $input;
		$this->context = is_object($context) ? $context : $this->_createContext();
		$this->parameters = is_object($parameters) ? $parameters : $this->_createParameters();
		$this->configurations = is_object($configurations) ? $configurations : $this->_createConfigurations($configurations);
		return $this->_runControllerAndAction($this->_findControllerAndAction());
	}

	/**
	 * Prototype for the default Action
	 *
	 * Please overwrite this as needed, or better define another action as default
	 * by setting the $defaultAction class variable.
	 *
	 * @return	string		the generated code
	 */
	function defaultAction() {
		return '<p>Default Action</p>';
	}

	/**
	 * This is returned, if an invalid action has been send.
	 *
	 * @return	string		error text
	 */
	function undefinedAction() {
		return '<p class="warning">Undefined action.</p>
			<p>Mind: All valid action functions have to end with "Action".</p>';
	}

	/**
	 * Pre action stub to be customized.
	 *
	 * Overwrite this method, to do common things before the action. 
	 * I.e. you can do access controll or input preprocessing here.
	 * You could also change the action.
	 * 
	 * @return void
	 */
	function doPreActionProcessings() { }

	/**
	 * Post action stub to be customized.
	 *
	 * Overwrite this method, to do common things after the action. 
	 * I.e. you could postprocess the output or clean up.
	 * 
	 * @return void
	 */
	function doPostActionProcessings() { }

	// -------------------------------------------------------------------------------------
	// GetSetters for the central quad 
	// -------------------------------------------------------------------------------------

	/**
	 * Set and get the configurations object
	 *
	 * @param	object		tx_lib_configurations or child
	 * @return	object		tx_lib_configurations or child
	 */
	function configurations($object = NULL) {
		$this->configurations = is_object($object) ? $object : $this->configurations;
		if($this->configurations) return $this->configurations;
		else $this->_die('Missing the configurations object.',  __FILE__,  __LINE__);
	}

	/**
	 * Set and get the parameters object
	 *
	 * @param	object		tx_lib_parameters or child
	 * @return	object		tx_lib_parameters or child
	 */
	function parameters($object = NULL) {
		$this->parameters = is_object($object) ? $object : $this->parameters;
		if($this->parameters) return $this->parameters;
		else $this->_die('Missing the parameters object.',  __FILE__,  __LINE__);
	}

	/**
	 * Set and get the context object
	 *
	 * @param	object		tx_lib_context or child
	 * @return	object		tx_lib_context or child
	 */
	function context($object = NULL) {
		$this->context = is_object($object) ? $object : $this->context;
		if($this->context) return $this->context;
	 	else $this->_die('Missing the context object.',  __FILE__,  __LINE__);
	}

	//------------------------------------------------------------------------------------
	// Other methods 
	//------------------------------------------------------------------------------------

	/**
	 * Create objects of tx_lib_object type in the typical way 
	 *
	 * Usful for objects that inherit the tx_lib_object constructor.  
	 *
	 * - The controller is set to the object.
	 * - The file is loaded automatically. 
	 * - XCLASS are used if available.
	 *
	 * @param string  class to create
	 * @param mixed   Incomming SPL data for the constructor.
	 * @return object The created object.
	 * @see tx_div::makeInstanceClassName.
	 */
	function makeInstance($className, $arrayOrObject = NULL) {
		$className = tx_div::makeInstanceClassName($className);
		return new $className($this, $arrayOrObject);
	}

	//------------------------------------------------------------------------------------
	// Protected methods
	//------------------------------------------------------------------------------------

	/**
	 * Instantiate the extending controller
	 *
	 * Creates a controller of the given name.
	 * Sets all properties from the current object (controller) to it. 
	 *
	 * @param	  array	      ControllerName and actionName.
	 * @return	object      The Controller.
	 * @access	protected
	 */
	function _buildController($controllerAndAction) {
		list($controllerName, $action) = $controllerAndAction;
		$this->action = $action;
		$controller = tx_div::makeInstance($controllerName);
		// Set all values to the new controller
    foreach(array_keys(get_class_vars(get_class($this))) as $key) $controller->$key =& $this->$key; 
		// Rebuild the central quad
		$controller->context->controller =& $controller;
		$controller->parameters->controller =& $controller;
		$controller->configurations->controller =& $controller;
		return $controller;
	}

	/**
	 * Checks whether the class given as first argument contains the requested
	 * or not.
	 *
	 * @param	string		the name of the controller class
	 * @param	string		name of the requested action
	 * @return	boolean		true if action exists, false otherwise
	 * @access	protected
	 */
	function _controllerHasAction($controllerName, $action) {
		$hasAction = FALSE;
		foreach((array)get_class_methods($controllerName) as $method)
			if(strtolower($method) == strtolower($action))
				$hasAction = TRUE;
		return $hasAction;
	}

	/**
	 * Creates a configurations object using the array given as parameter.
	 *
	 * @param	array		the local configuration array provided by the outer tslib framework
	 * @return  the configuration object	
	 * @access	protected
	 */
	function _createConfigurations($configurationArray) {
		$object = tx_div::makeInstance($this->configurationsClassName);
		$object->controller($this); 
		$object->setTypoScriptConfiguration($configurationArray);
		if(is_object($this->cObj)) $object->setFlexFormConfiguration($this->cObj->data['pi_flexform']); 
		return $object;
	}

	/**
	 * Creates a context object 
	 *
	 * @return  the context object	
	 * @access	protected
	 */
	function _createContext() {
		$object = tx_div::makeInstance($this->contextClassName);
		$object->controller($this); 
		if(is_object($this->cObj)) $object->setContentObject($this->cObj); 
		return $object;
	}

	/**
	 * Creates a parameters object.
	 *
	 * @return  the parameters object	
	 * @access	protected
	 */
	function _createParameters() {
		$className = tx_div::makeInstanceClassName($this->parametersClassName);
		return new $className($this);
	}

	/**
	 * Find the action to handle the request
	 *
	 * Order: classDefaultAction < configurationDefaultAction < parametersAction < configurationsAction
	 *
	 * 1. Ultima ratio: $this->defaultAction is the fallback if nothing else is given.
	 * 2. The defaultAction can also be initialized in TS, supersided by flexform as usual.
	 * 3. Typically: The parametersAction is sent from submit or link click.
	 * 4. The configurationAction can force a fixed view of a context element.
	 *
	 * @param	object		the local configuration array 
	 * @return	string		the action
	 * @access	protected
	 */
	function _findAction() {
		$configurations = $this->configurations->getHashArray();
		// 1. + 2.) A defaultAction can be set as class property or by TS.
		$action = $configurations['defaultAction'] ? $configurations['defaultAction']   : $this->defaultAction;
		// 3.) The action can result from link or submit event.
		$action = $this->_getParameterAction() ? $this->_getParameterAction() : $action;
		// 4.) The action can be forced by the TS setting "action".
		$action = $configurations['action'] ? $configurations['action'] : $action;
		// The "Action" suffix can be dropped.
		if(substr($action, -6, 6) != 'Action') $action .= 'Action';
		return $action;
	}

	/**
	 * Find the controller for the action by inspection.
	 *
	 * Tries the given controller and the registered child controllers.
	 *
	 * @return	array		Resulting controllerName and action.
	 * @access	protected
	 */
	function _findControllerAndAction() {
		global $TYPO3_CONF_VARS;
		$classname = $this->getClassName();

		// Find action.
		$action = $this->_findAction();

		// Is the action in the given controller?
		$controllerName = $this->_controllerHasAction($classname, $action) ? $classname : NULL;

		// Is the action defined in TS? Which precedence is best here?
		$controllerName = $this->_typoScriptHasAction($action) ? $classname : $controllerName;

		// Is the action in one of it's childs? The last childs have precedence.
		foreach(array_keys((array)$TYPO3_CONF_VARS['CONTROLLERS'][$classname]) as $childName)
			$controllerName = $this->_controllerHasAction($childName, $action)
			? $childName : $controllerName;

		// If we didn't find a controller for it, the action is not known
		if($controllerName == NULL) { $controllerName = $classname; $action = 'undefinedAction'; }

		return array($controllerName, $action);
	}

	/**
	 * Find the action from parameter string or array
	 *
	 * The action value can be sent in two forms:
	 * 1. designator[action] = actionValue
	 * 2. designator[action][actionValue] = something
	 *
	 * Form 2. is usfull Form HTML forms with multiple submit buttons.
	 * You shouldn't use the button label as action value,
	 * because it is language dependant.
	 *
	 * @return	string		the action value
	 * @access	protected
	 */
	function _getParameterAction() {
		$action = $this->parameters->get('action');
		if(!is_array($action)) {
			return $action;
		} else {
			return key($action);
		}
	}

	/**
	 * Run the action.
	 *
	 * The action can either return the output or set it internally to $this->output.
	 * In both cases it will be stored into $this->output. You can postprocess 
	 * this variable within the function doPostActionProcessings(). 
	 *
	 * @param	  object    Controller
	 * @return	object    Controller
	 * @access	protected
	 */
	function _runAction($controller) {
		if(method_exists($controller, $controller->action)) {
			$output = $controller->{$controller->action}();
			if(strlen($output)) $controller->output = $output;
		}
		return $controller;
	}

	/**
	 * Controll common processes after the action call.
	 *
	 * @param	  object    Controller
	 * @return	object    Controller
	 * @access	protected
	 */
	function _runAfterAction($controller) {
		if(method_exists($controller, 'doPostActionProcessings')) {
			$controller->doPostActionProcessings();
		}
		return $controller;
	}

	/**
	 * Controll common processes before the action call.
	 *
	 * @param	  object    Controller
	 * @return	object    Controller
	 * @access	protected
	 */
	function _runBeforeAction($controller) {
		if(method_exists($controller, 'doPreActionProcessings')) {
			$controller->doPreActionProcessings();
		}
		return $controller;
	}

	/**
	 * Instantiate the controller and call the action.
	 *
	 * Finally controller and action have been identified. Now instantiate and run 
	 * them. For reasons of consistency we reinstantiate the controller, even if 
	 * it is not a registered extension, but the original class. 
	 *
	 * Additionally we controll preprocessings and postprocessings here, that can be 
	 * customized. 
	 *
	 * @param	  array       ControllerName and actionName.
	 * @return	string      Rendered result string ready to be displayed.
	 * @access	protected
	 */
	function _runControllerAndAction($controllerAndAction) {
		$controller = $this->_buildController($controllerAndAction);
		$controller = $this->_runBeforeAction($controller);
		$controller = $this->_runAction($controller);
		$controller = $this->_runAfterAction($controller);
		return $controller->output;	
	}

	/**
	 * Checks whether the class given as first argument contains the requested
	 * or not.
	 *
	 * @param	string		the name of the controller class
	 * @param	string		name of the requested action
	 * @return	boolean		true if action exists, false otherwise
	 * @access	protected
	 */
	function _typoScriptHasAction($action) {
		return (strlen($action) && is_object($this->configurations) && in_array($action, (array) $this->configurations->get('actions.')));
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_controller.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_controller.php']);
}
?>
