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
 * Testcase for Tx_Fed_Utility_CDN
 *
 * @package TYPO3
 * @subpackage Fed/Utility
 */
class Tx_Fed_Tests_Unit_Utility_CDNTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_Fed_Utility_CDN
	 */
	protected $cdnService;

	protected $backup;

	/**
	 *
	 */
	public function setUp() {
		$this->backup = $GLOBALS['TSFE'];
		$GLOBALS['TSFE'] = new stdClass();
		$GLOBALS['TSFE']->additionalHeaderData = array();
		$this->cdnService = $this->objectManager->get('Tx_Fed_Utility_CDN');
	}

	public function tearDown() {
		$GLOBALS['TSFE'] = $this->backup;
	}

	/**
	 * @test
	 */
	public function includeJQueryNoConflictWorks() {
		$this->cdnService->includeJQueryNoConflict();
		$script = 'jQuery.noConflict();';
		$this->assertContains($script, implode('', $GLOBALS['TSFE']->additionalHeaderData));
	}

	/**
	 * @test
	 */
	public function buildPackageUriReturnsUri() {
		$uri = $this->cdnService->buildPackageUri('jquery', '1.8', 'jquery.min.js');
		$this->assertContains('jquery/1.8/jquery.min.js', $uri);
		$this->assertStringStartsWith('https://', $uri);
		$this->assertStringEndsWith('.js', $uri);
	}

	/**
	 * @test
	 */
	public function includeJQueryWorksWithoutArguments() {
		$this->cdnService->includeJQuery();
		$this->assertContains('jquery.min.js', implode('', $GLOBALS['TSFE']->additionalHeaderData));
	}

	/**
	 * @test
	 */
	public function includeJQueryWorksWithArguments() {
		$includeTheme = 'sometheme.css';
		$includeCompat = TRUE;
		$this->cdnService->includeJQuery('1', '1.6', $includeTheme, $includeCompat);
		$output = implode('', $GLOBALS['TSFE']->additionalHeaderData);
		$this->assertContains('jquery.min.js', $output);
		$this->assertContains('jquery-ui.min.js', $output);
		$this->assertContains('.css', $output);
		$this->assertContains('jQuery.noConflict();', $output);
	}

	/**
	 * @test
	 */
	public function includeJQueryWorksWithoutJQueryUIVersion() {
		$this->cdnService->includeJQuery();
		$this->assertContains('jquery.min.js', implode('', $GLOBALS['TSFE']->additionalHeaderData));
	}

	/**
	 * @test
	 */
	public function includeJQueryWorksWithJQueryUIVersion() {
		$this->cdnService->includeJQuery('1', '1.6');
		$this->assertContains('jquery-ui.min.js', implode('', $GLOBALS['TSFE']->additionalHeaderData));
	}



}
?>
