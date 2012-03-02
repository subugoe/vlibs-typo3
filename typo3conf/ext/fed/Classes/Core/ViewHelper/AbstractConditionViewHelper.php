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
 * Provides injected services and methods for easier implementation in
 * subclassing ViewHelpers
 *
 * @package Fed
 * @subpackage Core\ViewHelper
 */
abstract class Tx_Fed_Core_ViewHelper_AbstractConditionViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractConditionViewHelper {

	/**
	 * @param integer $index
	 * @return mixed
	 */
	protected function getNeedleAtIndex($index) {
		if ($index < 0) {
			return NULL;
		}
		$haystack = $this->arguments['haystack'];
		if (is_array($haystack)) {
			$asArray = $haystack;
		} elseif ($haystack instanceof Tx_Extbase_Persistence_ObjectStorage) {
			$asArray = $haystack->toArray();
		} elseif ($haystack instanceof Tx_Extbase_Persistence_LazyObjectStorage) {
			$asArray = $haystack->toArray();
		} elseif ($haystack instanceof Tx_Extbase_Persistence_QueryResult) {
			$asArray = $haystack->toArray();
		}
		return $asArray[$index];
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return mixed
	 */
	protected function assertHaystackIsQueryResultAndHasNeedle($haystack, $needle) {
		if ($needle instanceof Tx_Extbase_DomainObject_DomainObjectInterface) {
			$needle = $needle->getUid();
		}
		foreach ($haystack as $index=>$candidate) {
			if ($candidate->getUid() == $needle) {
				return $index;
			}
		}
		return FALSE;
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return mixed
	 */
	protected function assertHaystackIsObjectStorageAndHasNeedle($haystack, $needle) {
		$index = 0;
		foreach ($haystack as $candidate) {
			if ($needle instanceof Tx_Extbase_DomainObject_AbstractDomainObject) {
				$needle = $needle->getUid();
			}
			if ($candidate->getUid() == $needle) {
				return $index;
			}
			$index++;
		}
		return FALSE;
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return boolean
	 */
	protected function assertHaystackIsArrayAndHasNeedle($haystack, $needle) {
		if ($needle instanceof Tx_Extbase_DomainObject_DomainObjectInterface === FALSE) {
			return array_search($needle, $haystack);
		} else {
			foreach ($haystack as $index=>$straw) {
				if ($straw->getUid() == $needle->getUid()) {
					return $index;
				}
			}
			return FALSE;
		}
	}

	/**
	 * @param mixed $haystack
	 * @param mixed $needle
	 * @return boolean
	 */
	protected function assertHaystackIsStringAndHasNeedle($haystack, $needle) {
		return strpos($haystack, $needle);
	}

}

?>