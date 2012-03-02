<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Claus Due <claus@wildside.dk>
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
 * Testcase for Tx_Fed_Utility_JSON
 *
 * @package TYPO3
 * @subpackage Fed/Utility
 */
class Tx_Fed_Tests_Unit_Utility_JSONTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_Fed_Utility_JSON
	 */
	protected $json;

	public function setUp() {
		$this->json = $this->objectManager->get('Tx_Fed_Utility_JSON');
	}

	/**
	 * @test
	 */
	public function canEncode() {
		$data = new stdClass();
		$data->test = 'test';
		$expectedResult = '{"test":"test"}';
		$result = $this->json->encode($data);
		$this->assertEquals($expectedResult, $result);
	}

	/**
	 * @test
	 */
	public function canDecode() {
		$expectedResult = new stdClass();
		$expectedResult->test = 'test';
		$data = '{"test":"test"}';
		$result = $this->json->decode($data);
		$this->assertEquals($expectedResult, $result);
	}


}

?>