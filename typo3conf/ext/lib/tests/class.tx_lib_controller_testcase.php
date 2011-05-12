<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2006 Elmar Hinz
 *  Contact: 2006
 *  All rights reserved
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 ***************************************************************/

// Configuration first
$key = 'lib';   // extension key
$class = 'tx_lib_controller_testcase'; // class name

// Fix part to set before class definition
error_reporting (E_ALL ^ E_NOTICE);
if(!defined('PATH_site')) {              // If running from command line
    tx_t3unit_init($key);
}
require_once(t3lib_extMgm::extPath('t3unit') . 'class.tx_t3unit_testcase.php');

/**
 * Test class for tx_lib_controller
 */
class tx_lib_controller_testcase extends tx_t3unit_testcase {

    /****************************************************************
     * Class variables
     ****************************************************************/

    var $fooController;
    var $barController;

    /****************************************************************
     * Testimplementations check
     ****************************************************************/

    function testFooController(){
        $controller = $this->fooController;
        self::assertTrue(method_exists($controller,'fooAction'));
        self::assertTrue(method_exists($controller,'barAction'));
        self::assertFalse(method_exists($controller,'barFooAction'));
        self::assertTrue(method_exists($controller,'parentSerializeParametersAction'));
        self::assertFalse(method_exists($controller,'childSerializeParametersAction'));
        self::assertEquals('Foo->foo', $controller->fooAction($out, $conf, $vars));
        self::assertEquals('Foo->bar', $controller->barAction($out, $conf, $vars));
    }

    function testBarController(){
        $controller = $this->barController;
        self::assertFalse(method_exists($controller,'fooAction'));
        self::assertTrue(method_exists($controller,'barAction'));
        self::assertTrue(method_exists($controller,'barFooAction'));
        self::assertFalse(method_exists($controller,'parentSerializeParametersAction'));
        self::assertTrue(method_exists($controller,'childSerializeParametersAction'));
        self::assertEquals('Bar->bar', $controller->barAction($out, $conf, $vars));
        self::assertEquals('Bar->barFoo', $controller->barFooAction($out, $conf, $vars));
    }

    /****************************************************************
     * Direct controller usage
     ****************************************************************/

    function testParentDefault(){
        $controller = $this->fooController;
        self::assertEquals('Default Action', $controller->main($out, $conf));
    }

    function testParentFoo(){
        $controller = $this->fooController;
        $_GET['tx_lib_tests_controllers_fooController']['action'] = 'foo';
        self::assertEquals('Foo->foo', $controller->main($out, $conf));
    }

    function testParentFooBar(){
        $controller = $this->fooController;
        $_GET['tx_lib_tests_controllers_fooController']['action'] = 'fooBar';
        self::assertEquals('Error 404', $controller->main($out, $conf));
    }

    function testParentParameters(){
        $controller = $this->fooController;
        $out = 'neverUsed';
        $conf = array( 'a' => array ('a', 'b', 'c'), 'b' => '23');
        $array = array( 'x' => array ('x', 'y', 'z'), 'y' => '88');
        $array['action'] = 'parentSerializeParameters';
        $_GET['tx_lib_tests_controllers_fooController'] = $array;
        self::assertEquals($out . serialize($conf) . serialize($array), 
                           $controller->main($out, $conf));
    }

    /****************************************************************
     * Inheritence and Overwriting
     ****************************************************************/

    // Overwriting
    function testChildBar(){
        $controller = $this->fooController;
        $_GET['tx_lib_tests_controllers_fooController']['action'] = 'bar';
        self::assertEquals('Bar->bar', $controller->main($out, $conf));        
    }

    // Adding
    function testChildBarFoo(){
        $controller = $this->fooController;
        $_GET['tx_lib_tests_controllers_fooController']['action'] = 'barFoo';
        self::assertEquals('Bar->barFoo', $controller->main($out, $conf));
    }

    function testChildParameters(){
        $controller = $this->fooController;
        $out = 'neverUsed';
        $conf = array( 'a' => array ('a', 'b', 'c'), 'b' => '23');
        $pars = array( 'x' => array ('x', 'y', 'z'), 'y' => '88');
        $pars['action'] = 'childSerializeParameters';
        $_GET['tx_lib_tests_controllers_fooController'] = $pars;
        self::assertEquals($controller->main($out, $conf), 
                           $out . serialize($conf) . serialize($pars));
    }



    /****************************************************************
     * main, setUP, tearDown
     ****************************************************************/

    public function __construct ($name) {
        parent::__construct ($name);
    }

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() { 
        global $TYPO3_CONF_VARS;
        tx_t3unit_load('div');
        require_once(t3lib_extMgm::extPath('lib') . 'tests/controllers/class.tx_lib_tests_controllers_fooController.php');
        require_once(t3lib_extMgm::extPath('lib') . 'tests/controllers/class.tx_lib_tests_controllers_barController.php');
        $this->fooController = new tx_lib_tests_controllers_fooController();
        $this->barController = new tx_lib_tests_controllers_barController();
        $TYPO3_CONF_VARS['CONTROLLERS']['tx_lib_tests_controllers_fooController']['tx_lib_tests_controllers_barController'] = 1;
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
    }

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        global $class;
        require_once "PHPUnit2/TextUI/TestRunner.php";
        $suite  = new PHPUnit2_Framework_TestSuite($class);
        $result = PHPUnit2_TextUI_TestRunner::run($suite);
    }
}

// Fix part to set after class definition 
if(T3UNIT_commandline === TRUE){
    if (!defined('PHPUnit2_MAIN_METHOD')) {
        define('PHPUnit2_MAIN_METHOD', $class . '::main');
    }
    if (PHPUnit2_MAIN_METHOD == $class . '::main') {
        eval($class . '::main();');
    }
}

/****************************************************************
 * global functions
 */ 

function tx_t3unit_init($key){
    define('T3UNIT_commandline', TRUE);  // Remember it
    $path = realpath($_SERVER['PWD'] .'/'. $_SERVER['SCRIPT_NAME']);
    if(!preg_match('|(.*)(typo3conf.*)(' . $key . '/test)|', $path, $matches))
        if(! preg_match('|(.*)(typo3/ext.*)(' . $key . '/test)|', $path, $matches))
            if(! preg_match('|(.*)(typo3/sysext.*)(' . $key . '/test)|', $path, $matches))
                exit(chr(10) . 'Unknown installation path' . chr(10). $path . chr(10));
    $GLOBALS['TYPO3_LOADED_EXT'][$key]['siteRelPath']= $matches[2] . $key . '/';

    define('PATH_site', $matches[1]);
    define('PATH_t3lib', PATH_site . 't3lib/');
    require_once(PATH_t3lib . 'class.t3lib_div.php');
    require_once(PATH_t3lib . 'class.t3lib_extmgm.php');
    tx_t3unit_load('t3unit');
}

function tx_t3unit_load($key){
    if(is_dir(PATH_site . 'typo3conf/ext/' . $key . '/')) {
        $GLOBALS['TYPO3_LOADED_EXT']['' . $key . '']['siteRelPath']
            = 'typo3conf/ext/' . $key . '/';
    }elseif(is_dir(PATH_site . 'typo3/ext/' . $key . '/')) {
        $GLOBALS['TYPO3_LOADED_EXT']['' . $key . '']['siteRelPath'] 
            = 'typo3/ext/' . $key . '/';
    }elseif(is_file(PATH_site . 'typo3/sysext/' . $key . '/')) {
        $GLOBALS['TYPO3_LOADED_EXT']['' . $key . '']['siteRelPath']
            = 'typo3/sysext/' . $key . '/';
    }else{
        exit(chr(10) . 'Unknown installation path for ' . $key . '');
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/' . $key . '/tests/class.' . $class . '.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/' . $key . '/tests/class.' . $class . '.php']);
}

?>