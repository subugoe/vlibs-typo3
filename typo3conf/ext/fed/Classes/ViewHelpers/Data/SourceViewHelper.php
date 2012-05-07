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
class Tx_Fed_ViewHelpers_Data_SourceViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	public function initializeArguments() {
		$this->registerArgument('as', 'string', 'Which template variable name to use');
		$this->registerArgument('source', 'mixed', 'Integer UID or string identifier of DataSource record');
	}

	/**
	 * Render method
	 *
	 * @return string
	 */
	public function render() {
		$repository = $this->objectManager->get('Tx_Fed_Domain_Repository_DataSourceRepository');
		$parser = $this->objectManager->get('Tx_Fed_Utility_DataSourceParser');
		if (is_array($source)) {
			$sources = $repository->findByUids($source);
		} else {
			$sources = $repository->searchByName($source)->toArray();
			if (count($source) == 0) {
				// see íf $source is an uid, if it is then load - and subject to parsing as usual
				$testUid = intval($source);
				if ($testUid > 0) {
					$sources = $repository->findOneByUid($source);
				}
			}
		}

		$sources = $parser->parseSources($sources); // property data is filled in all sources

		if (count($sources) == 1) {
			$source = array_pop($sources);
			$value = $source->getData();
		} else if (count($sources) == 0) {
			return NULL;
		}

		if (count($value) == 1) {
			$value = array_pop($value);
			if (is_array($value) && count($value) == 1) {
				$value = array_pop($value);
			}
		}
		if ($name === NULL) {
			return $value;
		} else {
			if ($this->templateVariableContainer->exists($name)) {
				$this->templateVariableContainer->remove($name);
			}
			$this->templateVariableContainer->add($name, $value);
		}
	}

}

?>