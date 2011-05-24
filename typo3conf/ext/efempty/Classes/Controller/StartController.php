<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Patrick Lobacher <patrick.lobacher@typovision.de>
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
 * The sample Controller called StartController
 *
 * @version $Id:$
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 */
class Tx_Efempty_Controller_StartController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	public function initializeAction() {

	}

	/**
	 * Index action for this controller.
	 *
	 * @return string The rendered view
	 */
	public function indexAction() {
		
		// plain assign
		$this->view->assign('helloworld1', 'Hello World 1!');
		
		// normal array assign
		$array = array('Hello','World','2!');
		$this->view->assign('helloworld2', $array);
		
		// assoziative array assign
		$array = array('first' => 'Hello', 'middle' => 'World', 'last' => '3!');
		$this->view->assign('helloworld3', $array);
		
		// object assign
		$start = new Tx_Efempty_Domain_Model_Start;
       	$start->setTitle("Hello World 4!");
      	$this->view->assign('helloworld4', $start);

	}
	
}

?>