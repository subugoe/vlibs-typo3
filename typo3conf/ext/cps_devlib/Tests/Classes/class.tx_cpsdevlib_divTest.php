<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Nicole Cordes <cordes
 * @cps-it.de>
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

	private $fixture;

	/**
	 * Set up fixture structures
	 */
	public function setUp() {
		$this->fixture = array(
			'testArray' => array(' Lorem ', ' ', '24', 'ipsum', '0', ' dolor', 'sit ', 'amet', 24, ' consetetur ', 'sadipscing', '', 0, 'elitr ', ' sed', ' diam', ' 0 '),
			'testString' => ' Lorem , -24,ipsum,0. dolor&sit ,amet,24: consetetur -sadipscing+,0,elitr : sed; diam+ 0 ',
			'testQueryString' => 'tx_cpsdevlib_pi1[listid]=5&tx_cpsdevlib_pi1[pageid]=1&id=10&amp;L=3&amp;no_cache=1',
		);
	}

	/**
	 * Unset all fixtures
	 */
	public function tearDown() {
		unset($this->fixture);
	}

	/*
	 #####################################################################################################
	 #                                                                                                   #
	 # Tests for removeArrayValues($theArray, $useTrim = true, $removeEmptyValues = true, $theLimit = 0) #
	 #                                                                                                   #
	 #####################################################################################################
	 */

	/**
	 * Data provider for removeArrayValues
	 *
	 * @return array
	 */
	public function testRemoveArrayValuesDataProvider() {
		$theLimit = 12;

		return array(
			'array values stay the same' => array(false, false, 0, array(' Lorem ', ' ', '24', 'ipsum', '0', ' dolor', 'sit ', 'amet', 24, ' consetetur ', 'sadipscing', '', 0, 'elitr ', ' sed', ' diam', ' 0 ')),
			'array with trimmed values' => array(true, false, 0, array('Lorem', '', '24', 'ipsum', '0', 'dolor', 'sit', 'amet', 24, 'consetetur', 'sadipscing', '', 0, 'elitr', 'sed', 'diam', '0')),
			'array without empty values' => array(false, true, 0, array(' Lorem ', ' ', '24', 'ipsum', ' dolor', 'sit ', 'amet', 24, ' consetetur ', 'sadipscing', 'elitr ', ' sed', ' diam', ' 0 ')),
			'array values with limit greater zero' => array(false, false, $theLimit, array(' Lorem ', ' ', '24', 'ipsum', '0', ' dolor', 'sit ', 'amet', 24, ' consetetur ', 'sadipscing', '')),
			'array values with limit lower zero' => array(false, false, -5, array(0, 'elitr ', ' sed', ' diam', ' 0 ')),
			'array with trimmed but without empty values' => array(true, true, 0, array('Lorem', '24', 'ipsum', 'dolor', 'sit', 'amet', 24, 'consetetur', 'sadipscing', 'elitr', 'sed', 'diam')),
			'array with trimmed and limited values' => array(true, false, $theLimit, array('Lorem', '', '24', 'ipsum', '0', 'dolor', 'sit', 'amet', 24, 'consetetur', 'sadipscing', '')),
			'array values trimmed but without empty values limited' => array(true, true, $theLimit, array('Lorem', '24', 'ipsum', 'dolor', 'sit', 'amet', 24, 'consetetur', 'sadipscing', 'elitr', 'sed', 'diam')),
		);
	}

	/**
	 * @test
	 * @dataProvider testRemoveArrayValuesDataProvider
	 * @param boolean $useTrim
	 * @param boolean $removeEmptyValues
	 * @param integer $theLimit
	 * @param array $expectedReturnArray
	 */
	public function checkRemoveArrayValues($useTrim, $removeEmptyValues, $theLimit, $expectedReturnArray) {
		$this->assertEquals($expectedReturnArray, tx_cpsdevlib_div::removeArrayValues($this->fixture['testArray'], $useTrim, $removeEmptyValues, $theLimit));
	}

	/*
	 #########################################################################################################################
	 #                                                                                                                       #
	 # Tests for explode($theString, $theDelims = ',;\.:\-\+&\/', $useTrim = true, $removeEmptyValues = true, $theLimit = 0) #
	 #                                                                                                                       #
	 #########################################################################################################################
	 */

	/**
	 * Data provider for removeArrayValues
	 *
	 * @return array
	 */
	public function testExplodeDataProvider() {
		$theDelims = ',;\.:\-\+&\/';
		$theLimit = 12;

		return array(
			'string to array' => array($theDelims, false, false, 0, array(' Lorem ', ' ', '24', 'ipsum', '0', ' dolor', 'sit ', 'amet', '24', ' consetetur ', 'sadipscing', '', '0', 'elitr ', ' sed', ' diam', ' 0 ')),
			'string to array with trimmed values' => array($theDelims, true, false, 0, array('Lorem', '', '24', 'ipsum', '0',  'dolor', 'sit', 'amet', '24', 'consetetur', 'sadipscing', '', '0', 'elitr', 'sed', 'diam', '0')),
			'string to array without empty values' => array($theDelims, false, true, 0, array(' Lorem ', ' ', '24', 'ipsum', ' dolor', 'sit ', 'amet', '24', ' consetetur ', 'sadipscing', 'elitr ', ' sed', ' diam', ' 0 ')),
			'string to array with limited values greater zero' => array($theDelims, false, false, $theLimit, array(' Lorem ', ' ', '24', 'ipsum', '0', ' dolor', 'sit ', 'amet', '24', ' consetetur ', 'sadipscing', '')),
			'string to array with limited values lower zero' => array($theDelims, false, false, -5, array('0', 'elitr ', ' sed', ' diam', ' 0 ')),
			'string to array with trimmed and without empty values' => array($theDelims, true, true, 0, array('Lorem', '24', 'ipsum', 'dolor', 'sit', 'amet', '24', 'consetetur', 'sadipscing', 'elitr', 'sed', 'diam')),
			'string to array with trimmed and limited values' => array($theDelims, true, false, $theLimit, array('Lorem', '', '24', 'ipsum', '0', 'dolor', 'sit', 'amet', '24', 'consetetur', 'sadipscing', '')),
			'string to array with trimmed and without empty values limited' => array($theDelims, true, true, $theLimit, array('Lorem', '24', 'ipsum', 'dolor', 'sit', 'amet', '24', 'consetetur', 'sadipscing', 'elitr', 'sed', 'diam')),
		);
	}

	/**
	 * @test
	 * @depends checkRemoveArrayValues
	 * @dataProvider testExplodeDataProvider
	 * @param string $theDelims
	 * @param boolean $useTrim
	 * @param boolean $removeEmptyValues
	 * @param integer $theLimit
	 * @param array $expectedReturnArray
	 */
	public function checkExplode($theDelims, $useTrim, $removeEmptyValues, $theLimit, $expectedReturnArray) {
		$this->assertEquals($expectedReturnArray, tx_cpsdevlib_div::explode($this->fixture['testString'], $theDelims, $useTrim, $removeEmptyValues, $theLimit));
	}

	/*
	 ##################################################################################################################################################
	 #                                                                                                                                                #
	 # Tests for toListArray($theData, $theDelims = ',;\.:\-\+&\/', $useTrim = true, $removeEmptyValues = true, $useArrayKeys = false, $theLimit = 0) #
	 #                                                                                                                                                #
	 ##################################################################################################################################################
	 */

	/**
	 * Data provider for toListArray
	 *
	 * @return array
	 */
	public function testArrayToListArrayDataProvider() {
		$theLimit = 12;

		return array(
			'array values stay the same' => array(false, false, false, 0, array(' Lorem ', ' ', '24', 'ipsum', '0', ' dolor', 'sit ', 'amet', 24, ' consetetur ', 'sadipscing', '', 0, 'elitr ', ' sed', ' diam', ' 0 ')),
			'array with trimmed values' => array(true, false, false, 0, array('Lorem', '', '24', 'ipsum', '0', 'dolor', 'sit', 'amet', 24, 'consetetur', 'sadipscing', '', 0, 'elitr', 'sed', 'diam', '0')),
			'array without empty values' => array(false, true, false, 0, array(' Lorem ', ' ', '24', 'ipsum', ' dolor', 'sit ', 'amet', 24, ' consetetur ', 'sadipscing', 'elitr ', ' sed', ' diam', ' 0 ')),
			'array with keys as values' => array(false, false, true, 0, array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16)),
			'array with limited values greater zero' => array(false, false, false, $theLimit, array(' Lorem ', ' ', '24', 'ipsum', '0', ' dolor', 'sit ', 'amet', 24, ' consetetur ', 'sadipscing', '')),
			'array with limited values lower zero' => array(false, false, false, -5, array(0, 'elitr ', ' sed', ' diam', ' 0 ')),
			'array with trimmed but without empty values' => array(true, true, false, 0, array('Lorem', '24', 'ipsum', 'dolor', 'sit', 'amet', 24, 'consetetur', 'sadipscing', 'elitr', 'sed', 'diam')),
			'array with trimmed keys as values' => array(true, false, true, 0, array(0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16)),
			'array with trimmed values limited' => array(true, false, false, $theLimit, array('Lorem', '', '24', 'ipsum', '0', 'dolor', 'sit', 'amet', 24, 'consetetur', 'sadipscing', '')),
			'array with trimmed keys as values without empty ones' => array(true, true, true, 0, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16)),
			'array with trimmed but without empty values limited' => array(true, true, false, $theLimit, array('Lorem', '24', 'ipsum', 'dolor', 'sit', 'amet', 24, 'consetetur', 'sadipscing', 'elitr', 'sed', 'diam')),
			'array with trimmed keys as values without empty ones limited' => array(true, true, true, $theLimit, array(1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12)),
		);
	}

	/**
	 * @test
	 * @depends checkRemoveArrayValues
	 * @dataProvider testArrayToListArrayDataProvider
	 * @param boolean $useTrim
	 * @param boolean $removeEmptyValues
	 * @param boolean $useArrayKeys
	 * @param integer $theLimit
	 * @param array $expectedReturnArray
	 */
	public function checkArrayToListArray($useTrim, $removeEmptyValues, $useArrayKeys, $theLimit, $expectedReturnArray) {
		$this->assertEquals($expectedReturnArray, tx_cpsdevlib_div::toListArray($this->fixture['testArray'], '', $useTrim, $removeEmptyValues, $useArrayKeys, $theLimit));
	}

	/**
	 * @test
	 * @depends checkExplode
	 * @dataProvider testExplodeDataProvider
	 * @param string $theDelims
	 * @param boolean $useTrim
	 * @param boolean $removeEmptyValues
	 * @param integer $theLimit
	 * @param array $expectedReturnArray
	 */
	public function checkStringToListArray($theDelims, $useTrim, $removeEmptyValues, $theLimit, $expectedReturnArray) {
		$this->assertEquals($expectedReturnArray, tx_cpsdevlib_div::toListArray($this->fixture['testString'], $theDelims, $useTrim, $removeEmptyValues, false, $theLimit));
	}

	/*
	 ###############################################################################################################################
	 #                                                                                                                             #
	 # Tests for queryStringToArray($theString, $removeKeys = '', $theSeparator = '&', $equalChar = '=', $altSeparators = '&amp;') #
	 #                                                                                                                             #
	 ###############################################################################################################################
	 */

	/**
	 * Data provider for removeArrayValues
	 *
	 * @return array
	 */
	public function testQueryStringToArrayDataProvider() {
		return array(
			'Split query string to array' => array('', '&', '=', '&amp;', array('tx_cpsdevlib_pi1' => array('listid' => '5', 'pageid' => '1'), 'id' => '10', 'L' => '3', 'no_cache' => '1')),
			'Remove keys from array' => array('id,tx_cpsdevlib_pi1[pageid]', '&', '=', '&amp;', array('tx_cpsdevlib_pi1' => array('listid' => '5'), 'L' => '3', 'no_cache' => '1')),
			'Change separator' => array('', ',', '=', '&amp;', array('tx_cpsdevlib_pi1' => array('listid' => '5', 'pageid' => '1'), 'id' => '10', 'L' => '3', 'no_cache' => '1')),
			'Change equal character' => array('', '&', '%3D', '&amp;', array('tx_cpsdevlib_pi1' => array('listid' => '5', 'pageid' => '1'), 'id' => '10', 'L' => '3', 'no_cache' => '1')),
			'Change alternative separator' => array('', '&', '=', '%26', array('tx_cpsdevlib_pi1' => array('listid' => '5', 'pageid' => '1'), 'id' => '10', 'L' => '3', 'no_cache' => '1')),
		);
	}

	/**
	 * @test
	 * @depends checkStringToListArray
	 * @dataProvider testQueryStringToArrayDataProvider
	 * @param string $removeKeys
	 * @param string $theSeparator
	 * @param string $equalChar
	 * @param string $altSeparators
	 * @param array $expectedReturnArray
	 */
	public function checkQueryStringToArray($removeKeys, $theSeparator, $equalChar, $altSeparators, $expectedReturnArray) {
		$this->assertEquals($expectedReturnArray, tx_cpsdevlib_div::queryStringToArray(str_replace('+', $altSeparators, str_replace('=', $equalChar, str_replace('&', $theSeparator, str_replace('&amp;', '+', $this->fixture['testQueryString'])))), $removeKeys, $theSeparator, $equalChar, $altSeparators));
	}
}

?>