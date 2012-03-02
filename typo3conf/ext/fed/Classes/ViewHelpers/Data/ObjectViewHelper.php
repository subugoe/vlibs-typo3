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
 * @subpackage ViewHelpers\Data
 */
class Tx_Fed_ViewHelpers_Data_ObjectViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $infoService;

	/**
	 * @param Tx_Fed_Utility_DomainObjectInfo $infoService
	 */
	public function injectInfoService(Tx_Fed_Utility_DomainObjectInfo $infoService) {
		$this->infoService = $infoService;
	}

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerArgument('type', 'string', 'Class name of Model Object to load', TRUE);
		$this->registerArgument('uid', 'integer', 'UID of the record to load', TRUE);
		$this->registerArgument('as', 'string', 'If specified, inserts result in this Fluid template variable, if not - returns object instance');
	}

	public function render() {
		$type = $this->arguments['type'];
		$uid = $this->arguments['uid'];
		$repository = $this->infoService->getRepositoryInstance($type);
		if ($repository) {
			$query = $repository->createQuery();
			$query->getQuerySettings()->setRespectStoragePage(FALSE);
			$query->matching($query->equals('uid', $uid));
			$object = $query->execute()->getFirst();
		}
		if ($this->arguments['as']) {
			$this->templateVariableContainer->add($this->arguments['as'], $object);
			return $this->renderChildren();
		} else {
			return $object;
		}
	}

}

?>