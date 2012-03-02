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
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage Utility
 */
class Tx_Fed_Utility_DataComparison implements t3lib_Singleton {

	/**
	 * Compare any number of arguments against the first argument given.
	 * For example, compare($origArr, $modifiedArr1, $modifiedArr2) to see how
	 * $modifiedArr1 and $modifiedArr2 each compare to $origArr. If you specify
	 * more than two arguments, each additional argument is compared against
	 * the original using the same compare() method until there are no more
	 * arguments to compare.
	 *
	 * Uses Tx_Fed_Utility_DataComparison
	 *
	 * Returns the human-readable string comparison verdict.
	 *
	 * @return string
	 * @api
	 */
	public function compare() {
		$args = func_get_args();
		$comparison = "";
		return $comparison;
	}

	/**
	 * Inspects any number of variable - meaning generates a human-readable
	 * description of the variable
	 * @return string
	 * @api
	 */
	public function inspect() {
		$args = func_get_args();
		$inspection = "";
		return $inspection;
	}



}

?>
