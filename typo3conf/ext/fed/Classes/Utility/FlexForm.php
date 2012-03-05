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
 * FlexForm integration Service
 *
 * Capable of returning instances of DomainObjects or ObjectStorage from
 * FlexForm field values if the type of field is a database relation and the
 * table it uses is one associated with Extbase.
 *
 * @package Fed
 * @subpackage Utility
 * @deprecated Will be removed in 1.6 - use extension Flux, Service FlexForm
 */
class Tx_Fed_Utility_FlexForm extends Tx_Flux_Service_FlexForm implements t3lib_Singleton {

	/**
	 * Gets a DomainObject or ObjectStorage of $dataType
	 *
	 * @param type $dataType
	 * @param type $uids
	 */
	protected function getObjectOfType($dataType, $uids) {
		$identifiers = explode(',', $uids);
			// fast decisions
		if (is_subclass_of($dataType, 'Tx_Fed_Resource_AbstractResource')) {
			return $this->objectManager->get($dataType, $identifier);
		} else if (strpos($dataType, '_Domain_Model_') !== FALSE && strpos($dataType, '<') === FALSE) {
			$repository = $this->infoService->getRepositoryInstance($dataType);
			$uid = array_pop($identifiers);
			return $repository->findOneByUid($uid);
		} else if (class_exists($dataType)) {
				// using constructor value to support objects like DateTime
			return $this->objectManager->get($dataType, $uids);
		}
			// slower decisions with support for type-hinted collection objects
		list ($container, $object) = explode('<', trim($dataType, '>'));
		if ($container && $object) {
			$container = $this->objectManager->get($container);
			if (strpos($object, '_Domain_Model_') !== FALSE) {
				$repository = $this->infoService->getRepositoryInstance($object);
				foreach ($identifiers as $identifier) {
					$member = $repository->findOneByUid($identifier);
					$container->attach($member);
				}
			} else if (is_subclass_of($object, 'Tx_Fed_Resource_AbstractResource')) {
				foreach ($identifiers as $identifier) {
					$member = $this->objectManager->get($object, $identifier);
					$container->attach($member);
				}
			}
			return $container;
		} else {
				// passthrough; not an object, nor a type hinted collection object
			return $uids;
		}
	}

}

?>