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
 * Testcase for Tx_Fed_Utility_DocumentHead
 *
 * @package TYPO3
 * @subpackage Fed/Utility
 */
class Tx_Fed_Tests_Unit_Utility_DocumentHeadTest extends Tx_Extbase_Tests_Unit_BaseTestCase {

	/**
	 * @var Tx_Fed_Utility_DocumentHead
	 */
	protected $documentHead;

	protected $backup;

	protected $tempFile;

	public function setUp() {
		$this->backup = $GLOBALS['TSFE'];
		$this->tempFile = md5(microtime()*time()) . '.txt';
		$GLOBALS['TSFE'] = new stdClass();
		$GLOBALS['TSFE']->additionalHeaderData = array();
		$this->documentHead = $this->objectManager->get('Tx_Fed_Utility_DocumentHead');
	}

	public function tearDown() {
		$GLOBALS['TSFE'] = $this->backup;
	}

	public function getHeaderContent() {
		return implode("\n", $GLOBALS['TSFE']->additionalHeaderData);
	}

	public function assertHeaderContains($needle) {
		return $this->assertContains($needle, $this->getHeaderContent());
	}

	/**
	 * @test
	 */
	public function canAddRawHeader() {
		$headerContent = '<link rel="fake" />';
		$this->documentHead->includeHeader($headerContent);
		$this->assertHeaderContains($headerContent);
	}

	/**
	 * @test
	 */
	public function canAddJavascriptHeader() {
		$javascript = 'alert("test");';
		$expectedResult = '<script type="text/javascript">' . $javascript . '</script>';
		$this->documentHead->includeHeader($javascript, 'js');
		$this->assertHeaderContains($expectedResult);
	}

	/**
	 * @test
	 */
	public function canAddJavascriptFile() {
		$javascriptFile = '404.js';
		$expectedResult = '<script type="text/javascript" src="' . $javascriptFile . '"></script>';
		$this->documentHead->includeFile($javascriptFile);
		$this->assertHeaderContains($expectedResult);
	}

	/**
	 * @test
	 */
	public function canAddStylesheetHeader() {
		$stylesheet = '.fake { display: none; }';
		$expectedResult = '<style type="text/css">' . $stylesheet . '</style>';
		$this->documentHead->includeHeader($stylesheet, 'css');
		$this->assertHeaderContains($expectedResult);
	}

	/**
	 * @test
	 */
	public function canAddStylesheetFile() {
		$stylesheetFile = '404.css';
		$expectedResult = '<link rel="stylesheet" type="text/css" href="' . $stylesheetFile . '" />';
		$this->documentHead->includeFile($stylesheetFile);
		$this->assertHeaderContains($expectedResult);
	}

	/**
	 * @test
	 */
	public function canAddMultipleFiles() {
		$files = array('404_1.js'. '404_2.js');
		$this->documentHead->includeFiles($files);
		foreach ($files as $javascriptFile) {
			$expectedResult = '<script type="text/javascript" src="' . $javascriptFile . '"></script>';
			$this->assertHeaderContains($expectedResult);
		}
	}

	/**
	 * @test
	 */
	public function canCreateTemporaryFile() {
		$content = md5(microtime()*time());
		$filename = $this->documentHead->saveContentToTempFile($content);
		$contentSaved = file_get_contents(PATH_site . $filename);
		$this->assertEquals($content, $contentSaved);
		$this->assertFileExists(PATH_site . $filename);
	}

	/**
	 * @test
	 */
	public function canReadFilesOfCertainTypeFromPath() {
		$files = $this->documentHead->getFilenamesOfType(PATH_site, 'php');
		$this->assertTrue(is_array($files));
		$this->assertGreaterThan(0, count($files));
		$this->assertContains('.php', array_pop($files));
	}

}
?>