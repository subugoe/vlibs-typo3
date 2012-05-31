<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Oliver Hader <oliver.hader@typo3.org>
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
 * Test cases for tx_extdeveval_calc.
 */
class tx_extdeveval_calcTest extends tx_phpunit_testcase {
	/**
	 * @var tx_extdeveval_calc
	 */
	protected $calc;

	/**
	 * Sets up this test case.
	 *
	 * @return void
	 */
	protected function setUp() {
		$this->calc = t3lib_div::makeInstance('tx_extdeveval_calc');
	}

	/**
	 * Cleans up this test case.
	 *
	 * @return void
	 */
	protected function tearDown() {
		unset($this->calc);
	}

	/**
	 * Tests whether a unix timestamp can be converted to a date.
	 *
	 * @return void
	 * @test
	 */
	public function isTimestampConvertedToDate() {
		$this->calc->cmd = 'unixTime_toTime';
		$this->calc->inputCalc['unixTime']['seconds'] = 13;

		$this->assertContains(
			'value="01-01-1970 00:00:13"',
			$this->calc->calc_unixTime()
		);
	}

	/**
	 * Tests whether a date can be converted to a unix timestamp.
	 *
	 * @return void
	 * @test
	 */
	public function isDateConvertedToTimeStamp() {
		$this->calc->cmd = 'unixTime_toSeconds';
		$this->calc->inputCalc['unixTime']['time'] = '01-01-1970 00:00:13';

		$this->assertContains(
			'value="13"',
			$this->calc->calc_unixTime()
		);
	}

	/**
	 * Tests whether a date can be converted to a unix timestamp.
	 *
	 * @return void
	 * @test
	 */
	public function isDateWithWhitespacesConvertedToTimeStamp() {
		$this->calc->cmd = 'unixTime_toSeconds';
		$this->calc->inputCalc['unixTime']['time'] = '01 - 01 - 1970 00:00:13';

		$this->assertContains(
			'value="13"',
			$this->calc->calc_unixTime()
		);
	}

	/**
	 * Tests whether a date can be converted to a unix timestamp
	 * even if only day, month and year is given.
	 *
	 * @return void
	 * @test
	 */
	public function isIncompleteDateUsedCorrectly() {
		$this->calc->cmd = 'unixTime_toSeconds';
		$this->calc->inputCalc['unixTime']['time'] = '01-01-1970';

		$this->assertContains(
			'value="0"',
			$this->calc->calc_unixTime()
		);

		$this->assertContains(
			'value="01-01-1970 00:00:00"',
			$this->calc->calc_unixTime()
		);
	}
}
