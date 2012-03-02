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
 * @subpackage ViewHelpers/Auth
 */
abstract class Tx_Fed_ViewHelpers_Auth_AbstractAuthViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractConditionViewHelper {

	/**
	 * @var Tx_Fed_Service_Auth
	 */
	protected $authService;

	/**
	 * @var Tx_Fed_Service_User
	 */
	protected $userService;

	/**
	 * @param Tx_Fed_Service_Auth $authService
	 */
	public function injectAuthService(Tx_Fed_Service_Auth $authService) {
		$this->authService;
	}

	/**
	 * @param Tx_Fed_Service_User $userService
	 */
	public function injectUserService(Tx_Fed_Service_User $userService) {
		$this->userService = $userService;
	}

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerArgument('anyFrontendUser', 'boolean', 'If TRUE, allows any FrontendUser unless other arguments disallows each specific FrontendUser', FALSE, FALSE);
		$this->registerArgument('anyFrontendUserGroup', 'boolean', 'If TRUE, allows any FrontendUserGroup unless other arguments disallows each specific FrontendUser', FALSE, FALSE);
		$this->registerArgument('frontendUser', 'Tx_Extbase_Domain_Model_FrontendUser', 'The FrontendUser to allow/deny');
		$this->registerArgument('frontendUsers', '<Tx_Extbase_Persistence_ObjectStorage>Tx_Extbase_Domain_Model_FrontendUser', 'The FrontendUsers ObjectStorage to allow/deny');
		$this->registerArgument('frontendUserGroup', 'Tx_Extbase_Domain_Model_FrontendUserGroup', 'The FrontendUserGroup to allow/deny');
		$this->registerArgument('frontendUserGroups', '<Tx_Extbase_Persistence_ObjectStorage>Tx_Extbase_Domain_Model_FrontendUserGroup', 'The FrontendUserGroups ObjectStorage to allow/deny');
		$this->registerArgument('anyBackendUser', 'boolean', 'If TRUE, allows any backend user unless other arguments disallows each specific backend user', FALSE, FALSE);
		$this->registerArgument('backendUser', 'integer', 'The uid of a backend user to allow/deny');
		$this->registerArgument('backendUsers', 'mixed', 'The backend users list to allow/deny. If string, CSV of uids is assumed, if array, array of uids is assumed');
		$this->registerArgument('backendUserGroup', 'integer', 'The uid of the backend user group to allow/deny');
		$this->registerArgument('backendUserGroups', 'mixed', 'The backend user groups list to allow/deny. If string, CSV of uids is assumed, if array, array of uids is assumed');
		$this->registerArgument('admin', 'boolean', 'If TRUE, a backend user which is also an admin is required');
		$this->registerArgument('evaluationType', 'string', 'Specify AND or OR (case sensitive) to determine how arguments must be processed. Default is AND, requiring all arguments to be satisfied if used', FALSE, 'AND');
	}

	public function initialize() {
		parent::initialize();
		$objectManager = t3lib_div::makeInstance('Tx_Extbase_Object_ObjectManager');
		$this->authService = $objectManager->get('Tx_Fed_Service_Auth');
		$this->userService = $objectManager->get('Tx_Fed_Service_User');
	}

	/**
	 * Returns TRUE if all conditions from arguments are satisfied. The
	 * type of evaluation (AND or OR) can be set using argument "evaluationType"
	 * @return boolean
	 */
	protected function evaluateArguments() {
		$evaluationType = $this->arguments['evaluationType'];
		$evaluations = array();
		if ($this->arguments['anyFrontendUser']) {
			$evaluations['anyFrontendUser'] = intval($this->authService->assertFrontendUserLoggedIn());
		}
		if ($this->arguments['anyFrontendUserGroup']) {
			$evaluations['anyFrontendUserGroup'] = intval($this->authService->assertFrontendUserGroupLoggedIn());
		}
		if ($this->arguments['frontendUser']) {
			$evaluations['frontendUser'] = intval($this->authService->assertFrontendUserLoggedIn($this->arguments['frontendUser']));
		}
		if ($this->arguments['frontendUserGroup']) {
			$evaluations['frontendUserGroup'] = intval($this->authService->assertFrontendUserGroupLoggedIn($this->arguments['frontendUserGroup']));
		}
		if ($this->arguments['frontendUserGroups']) {
			$evaluations['frontendUserGroups'] = intval($this->authService->assertFrontendUserGroupLoggedIn($this->arguments['frontendUserGroups']));
		}
		if ($this->arguments['anyBackendUser']) {
			$evaluations['anyBackendUser'] = intval($this->authService->assertBackendUserLoggedIn());
		}
		if ($this->arguments['anyBackendUserGrouo']) {
			$evaluations['anyBackendUserGroup'] = intval($this->authService->assertBackendUserGroupLoggedIn());
		}
		if ($this->arguments['backendUser']) {
			$evaluations['backendUser'] = intval($this->authService->assertBackendUserLoggedIn($this->arguments['backendUser']));
		}
		if ($this->arguments['backendUsers']) {
			$evaluations['backendUsers'] = intval($this->authService->assertBackendUserLoggedIn($this->arguments['backendUsers']));
		}
		if ($this->arguments['backendUserGroup']) {
			$evaluations['backendUserGroup'] = intval($this->authService->assertBackendUserGroupLoggedIn($this->arguments['backendUserGroup']));
		}
		if ($this->arguments['backendUserGroups']) {
			$evaluations['backendUserGroups'] = intval($this->authService->assertBackendUserGroupLoggedIn($this->arguments['backendUserGroups']));
		}
		if ($this->arguments['admin']) {
			$evaluations['admin'] = intval($this->authService->assertAdminLoggedIn());
		}
		if ($evaluationType === 'AND') {
			return (count($evaluations) === array_sum($evaluations));
		} else {
			return (count($evaluations) > 0);
		}
	}

}

?>