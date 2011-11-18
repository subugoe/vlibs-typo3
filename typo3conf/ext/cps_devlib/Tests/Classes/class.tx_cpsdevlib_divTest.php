<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Nicole Cordes <cordes@cps-it.de>
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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class tx_cpsdevlib_divTest extends Tx_Phpunit_TestCase {

	/**
	 * @test
	 */
	public function checkRemoveArrayValues() {
		$testArray = array(' Lorem ', 'ipsum', '', ' dolor', 'sit ', 'amet', ' consetetur ', 'sadipscing', 'elitr', ' ', 'sed', 'diam');
		$expectedResultArray = array(' Lorem ', 'ipsum', '', ' dolor', 'sit ', 'amet', ' consetetur ', 'sadipscing', 'elitr', ' ', 'sed', 'diam');
		$actualResultArray = tx_cpsdevlib_div::removeArrayValues($testArray, false, false, 0);

		$this->assertEquals($expectedResultArray, $actualResultArray);
	}

	/**
	 * @test
	 */
	public function checkRemoveArrayValuesAndUseTrim() {
		$testArray = array(' Lorem ', 'ipsum', '', ' dolor', 'sit ', 'amet', ' consetetur ', 'sadipscing', 'elitr', ' ', 'sed', 'diam');
		$expectedResultArray = array('Lorem', 'ipsum', '', 'dolor', 'sit', 'amet', 'consetetur', 'sadipscing', 'elitr', '', 'sed', 'diam');
		$actualResultArray = tx_cpsdevlib_div::removeArrayValues($testArray, true, false, 0);

		$this->assertEquals($expectedResultArray, $actualResultArray);
	}

	/**
	 * @test
	 */
	public function checkRemoveArrayValuesAndRemoveEmptyValues() {
		$testArray = array(' Lorem ', 'ipsum', '', ' dolor', 'sit ', 'amet', ' consetetur ', 'sadipscing', 'elitr', ' ', 'sed', 'diam');
		$expectedResultArray = array(' Lorem ', 'ipsum', ' dolor', 'sit ', 'amet', ' consetetur ', 'sadipscing', 'elitr', ' ', 'sed', 'diam');
		$actualResultArray = tx_cpsdevlib_div::removeArrayValues($testArray, false, true, 0);

		$this->assertEquals($expectedResultArray, $actualResultArray);
	}

	/**
	 * @test
	 */
	public function checkRemoveArrayValuesAndUseTrimAndRemoveEmptyValues() {
		$testArray = array(' Lorem ', 'ipsum', '', ' dolor', 'sit ', 'amet', ' consetetur ', 'sadipscing', 'elitr', ' ', 'sed', 'diam');
		$expectedResultArray = array('Lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consetetur', 'sadipscing', 'elitr', 'sed', 'diam');
		$actualResultArray = tx_cpsdevlib_div::removeArrayValues($testArray, true, true, 0);

		$this->assertEquals($expectedResultArray, $actualResultArray);
	}

	/**
	 * @test
	 */
	public function checkStringToListArray() {
		$testString = 'Lorem ipsum dolor sit amet consetetur sadipscing elitr sed diam';
		$expectedResultArray = array('Lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consetetur', 'sadipscing', 'elitr', 'sed', 'diam');
		$actualResultArray = tx_cpsdevlib_div::toListArray($testString, ' ', false, false, false, 0);

		$this->assertEquals($expectedResultArray, $actualResultArray);
	}

	/**
	 * @test
	 */
	public function checkStringToListArrayAndUseTrim() {
		$testString = 'Lorem, ipsum, dolor, sit, amet, consetetur, sadipscing, elitr, sed, diam';
		$expectedResultArray = array('Lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consetetur', 'sadipscing', 'elitr', 'sed', 'diam');
		$actualResultArray = tx_cpsdevlib_div::toListArray($testString, ',', true, false, false, 0);

		$this->assertEquals($expectedResultArray, $actualResultArray);
	}

	/**
	 * @test
	 */
	public function checkStringToListArrayAndRemoveEmptyValues() {
		$testString = 'Lorem,,ipsum,,dolor,sit,amet,consetetur,sadipscing,elitr,,sed,diam';
		$expectedResultArray = array('Lorem', 'ipsum', 'dolor', 'sit', 'amet', 'consetetur', 'sadipscing', 'elitr', 'sed', 'diam');
		$actualResultArray = tx_cpsdevlib_div::toListArray($testString, ',', false, true, false, 0);

		$this->assertEquals($expectedResultArray, $actualResultArray);
	}

	/**
	 * @test
	 */
	public function checkStringToListString() {
		$testString = 'Lorem ipsum dolor sit amet consetetur sadipscing elitr sed diam';
		$expectedResultString = 'Lorem,ipsum,dolor,sit,amet,consetetur,sadipscing,elitr,sed,diam';
		$actualResultString = tx_cpsdevlib_div::toListString($testString, ',', ' ', false, false, false, false);

		$this->assertEquals($expectedResultString, $actualResultString);
	}

	/**
	 * @test
	 */
	public function checkStringToListStringAndUseTrim() {
		$testString = 'Lorem - ipsum - dolor - sit - amet - consetetur - sadipscing - elitr - sed - diam';
		$expectedResultString = 'Lorem,ipsum,dolor,sit,amet,consetetur,sadipscing,elitr,sed,diam';
		$actualResultString = tx_cpsdevlib_div::toListString($testString, ',', '-', true, false, false, false);

		$this->assertEquals($expectedResultString, $actualResultString);
	}

	/**
	 * @test
	 */
	public function checkStringToListStringAndRemoveEmptyValues() {
		$testString = 'Lorem,,ipsum,,dolor,sit,amet,consetetur,sadipscing,elitr,,sed,diam';
		$expectedResultString = 'Lorem,ipsum,dolor,sit,amet,consetetur,sadipscing,elitr,sed,diam';
		$actualResultString = tx_cpsdevlib_div::toListString($testString, ',', ',', false, true, false, false);

		$this->assertEquals($expectedResultString, $actualResultString);
	}

	/**
	 * @test
	 */
	public function checkStringToIntListArray() {
		$testString = '1a,2,3xyz,4,5,,6 AND 1=1';
		$expectedResultArray = array(1,2,3,4,5,0,6);
		$actualResultArray = tx_cpsdevlib_div::toIntListArray($testString, ',', false, false, false, 0);

		$this->assertEquals($expectedResultArray, $actualResultArray);

	}

	/**
	 * @test
	 */
	public function checkStringToIntListString() {
		$testString = '1a,2,3xyz,4,5,,6 AND 1=1';
		$expectedResultString = '1,2,3,4,5,0,6';
		$actualResultString = tx_cpsdevlib_div::toIntListString($testString, ',', ',', false, false, false, 0);

		$this->assertEquals($expectedResultString, $actualResultString);
	}

	/**
	 * @test
	 */
	public function checkStringToIntListStringAndUseTrim() {
		$testString = '1a, 2, 3xyz, 4, 5, , 6 AND 1=1';
		$expectedResultString = '1,2,3,4,5,0,6';
		$actualResultString = tx_cpsdevlib_div::toIntListString($testString, ',', ',', true, false, false, 0);

		$this->assertEquals($expectedResultString, $actualResultString);
	}

	/**
	 * @test
	 */
	public function checkStringToIntListStringAndRemoveEmptyValues() {
		$testString = '1a,2,,3xyz,4,5,,6 AND 1=1';
		$expectedResultString = '1,2,3,4,5,6';
		$actualResultString = tx_cpsdevlib_div::toIntListString($testString, ',', ',', false, true, false, 0);

		$this->assertEquals($expectedResultString, $actualResultString);
	}

	/**
	 * @test
	 */
	public function checkStringToIntListStringAndUseTrimAndRemoveEmptyValues() {
		$testString = '1a, 2, , 3xyz, 4, 5, , 6 AND 1=2';
		$expectedResultString = '1,2,3,4,5,6';
		$actualResultString = tx_cpsdevlib_div::toIntListString($testString, ',', ',', true, true, false, 0);

		$this->assertEquals($expectedResultString, $actualResultString);
	}
}

?>