<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Claus Due <claus@wildside.dk>
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

require_once t3lib_extMgm::extPath('fluid', '/Tests/Unit/ViewHelpers/ViewHelperBaseTestcase.php');
/**
 * Testcase for Tx_Fed_ViewHelpers_Data_FuncViewHelper
 *
 * @package TYPO3
 * @subpackage Fed/ViewHelpers/Data
 */
class Tx_Fed_Tests_Unit_ViewHelpers_Data_FuncViewHelperTest extends Tx_Fluid_ViewHelpers_ViewHelperBaseTestcase {

	/**
	 * var Tx_Fluid_ViewHelpers_RenderViewHelper
	 */
	protected $viewHelper;

	public function setUp() {
		parent::setUp();
		$this->templateVariableContainer = new Tx_Fluid_Core_ViewHelper_TemplateVariableContainer();
		$this->renderingContext->injectTemplateVariableContainer($this->templateVariableContainer);
		$this->viewHelper = $this->getAccessibleMock('Tx_Fed_ViewHelpers_Data_FuncViewHelper', array('dummy', 'renderChildren'));
		$this->injectDependenciesIntoViewHelper($this->viewHelper);
	}

	/**
	 * @test
	 */
	public function canExecuteFunction() {
		$this->viewHelper->setArguments(array('func' => 'strtolower', 'arguments' => array('FOO')));
		$this->viewHelper->initialize();
		$this->assertEquals('foo', $this->viewHelper->render());
	}

	/**
	 * @test
	 */
	public function canExecuteReference() {
		$myNamedStrtolower = function($value = '') {
			return strtolower($value);
		};

		$this->viewHelper->setArguments(array('func' => $myNamedStrtolower, 'arguments' => array('FOO')));
		$this->viewHelper->initialize();
		$this->assertEquals('foo', $this->viewHelper->render());
	}

	/**
	 * @test
	 */
	public function canExecuteObjectMethod() {
		$this->viewHelper->setArguments(array('instance' => $this, 'func' => 'myStrtolower', 'arguments' => array('FOO')));
		$this->viewHelper->initialize();
		$this->assertEquals('foo', $this->viewHelper->render());
	}

	/**
	 * @test
	 */
	public function canExecuteStaticMethod() {
		$this->viewHelper->setArguments(array('instance' => 'Tx_Fed_Tests_Unit_ViewHelpers_Data_FuncViewHelperTest', 'func' => 'myStaticStrtolower', 'arguments' => array('FOO')));
		$this->viewHelper->initialize();
		$this->assertEquals('foo', $this->viewHelper->render());
	}

	/**
	 * @test
	 */
	public function canAssignResult() {
		$this->viewHelper->setArguments(array('instance' => 'Tx_Fed_Tests_Unit_ViewHelpers_Data_FuncViewHelperTest', 'func' => 'myStaticStrtolower', 'arguments' => array('FOO'), 'as' => 'res'));
		$this->viewHelper->initialize();
		$this->assertEquals('', $this->viewHelper->render());
		$this->assertEquals('foo', $this->templateVariableContainer->get('res'));
	}

	public function myStrtolower($value = '') {
		return strtolower($value);
	}

	static public function myStaticStrtolower($value = '') {
		return strtolower($value);
	}

}
?>