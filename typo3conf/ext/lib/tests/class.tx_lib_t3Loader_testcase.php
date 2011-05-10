<?php 

// Set configuration first
$key = 'lib';
$class = 'tx_lib_t3Loader_testcase';

// Fix part to set before class definition
error_reporting (E_ALL ^ E_NOTICE);
require_once ('PHPUnit2/Framework/TestCase.php');
require_once ('PHPUnit2/Framework/TestSuite.php');

/**
 * Test class for tx_lib_t3Loader
 */
class tx_lib_t3Loader_testcase extends PHPUnit2_Framework_TestCase {

    /****************************************************************
     * Class variables
     ****************************************************************/

    // general
    private $dir = 'directory';
    private $file = 'file';
    private $prefix = 'myPrefix.';
    private $suffix = '.inc.php';
    
    // alternative extensionkey
    private $alt = 'foo_bar_alt';
    private $unloaded = 'foo_bar_unloaded';

    // 3 different extesionkeys
    private $key1 = 'foo_bar1';
    private $key2 = 'foobar2';
    private $key3 = 'fooBar3';

    // classes
    private $tx1 = 'tx_foobar1';
    private $tx2 = 'tx_foobar2';
    private $tx3 = 'tx_fooBar3';

    /****************************************************************
     * Pure classnames
     ****************************************************************/

    // tx_key > ext/key/class.tx_key.php
    public function testClassKey1(){ $this->classKeyTest($this->tx1, $this->key1); }
    public function testClassKey2(){ $this->classKeyTest($this->tx2, $this->key2); }
    public function testClassKey3(){ $this->classKeyTest($this->tx3, $this->key3); }

     // tx_key_aaa > ext/key/class.tx_key_file.php
    public function testClassKeyFile1(){ $this->classKeyFileTest($this->tx1, $this->key1); }
    public function testClassKeyFile2(){ $this->classKeyFileTest($this->tx2, $this->key2); }
    public function testClassKeyFile3(){ $this->classKeyFileTest($this->tx3, $this->key3); }

    // tx_key_aaa > ext/key/dir/class.tx_key_dir.php
    public function testClassKeyDir1(){ $this->classKeyDirTest($this->tx1, $this->key1); }
    public function testClassKeyDir2(){ $this->classKeyDirTest($this->tx2, $this->key2); }
    public function testClassKeyDir3(){ $this->classKeyDirTest($this->tx3, $this->key3); }

    // tx_key_aaa_bbb > ext/key/dir/class.tx_key_dir_file.php
    public function testClassKeyDirFile1(){$this->classKeyDirFileTest($this->tx1, $this->key1);}
    public function testClassKeyDirFile2(){$this->classKeyDirFileTest($this->tx2, $this->key2);}
    public function testClassKeyDirFile3(){$this->classKeyDirFileTest($this->tx3, $this->key3);}


    /****************************************************************
     * Alternative Pathes, Prefix, Suffix
     ****************************************************************/

    // tx_key > ext/key/class.tx_key.php
    public function testAltKey1(){ $this->altKeyTest($this->tx1); }
    public function testAltKey2(){ $this->altKeyTest($this->tx2); }
    public function testAltKey3(){ $this->altKeyTest($this->tx3); }

     // tx_key_aaa > ext/key/class.tx_key_file.php
    public function testAltKeyFile1(){ $this->altKeyFileTest($this->tx1); }
    public function testAltKeyFile2(){ $this->altKeyFileTest($this->tx2); }
    public function testAltKeyFile3(){ $this->altKeyFileTest($this->tx3); }

    // tx_key_aaa > ext/key/dir/class.tx_key_dir.php
    public function testAltKeyDir1(){ $this->altKeyDirTest($this->tx1); }
    public function testAltKeyDir2(){ $this->altKeyDirTest($this->tx2); }
    public function testAltKeyDir3(){ $this->altKeyDirTest($this->tx3); }

    // tx_key_aaa_bbb > ext/key/dir/class.tx_key_dir_file.php
    public function testAltKeyDirFile1(){$this->altKeyDirFileTest($this->tx1);}
    public function testAltKeyDirFile2(){$this->altKeyDirFileTest($this->tx2);}
    public function testAltKeyDirFile3(){$this->altKeyDirFileTest($this->tx3);}

    /****************************************************************
     * Selftests
     ****************************************************************/

    public function testExtensionCreation(){
        $file = $this->file . '.php';
        $dir = $this->dir . '/';
        $array = array($this->key1, $file);
        $ext   = t3lib_extMgm::extPath($this->key1);
        $path  = $ext . $file;
        $this->makeClass($array);
        self::assertTrue(is_file($path));
        $this->removeClass($array);
        self::assertFalse(is_dir($ext));
    }

    public function testDirectoryCreation(){
        $file = $this->file . '.php';
        $dir = $this->dir . '/';
        $array = array($this->key1, $file, $dir);
        $ext   = t3lib_extMgm::extPath($this->key1);
        $path  = $ext . $dir . $file;
        $this->makeClass($array);
        self::assertTrue(is_file($path));
        $this->removeClass($array);
        self::assertFalse(is_dir($ext . $dir ));
        self::assertFalse(is_dir($ext));
    }    

    /****************************************************************
     * Functions for tests
     ****************************************************************/
    // Pure key funktions
    // 1.) tx_key         > ext/key/class.tx_key.php
    // 2.) tx_key_aaa     > ext/key/class.tx_key_file.php
    // 3.) tx_key_aaa     > ext/key/dir/class.tx_key_dir.php
    // 4.) tx_key_aaa_bbb > ext/key/dir/class.tx_key_dir_file.php

    // 1.) tx_key > ext/key/class.tx_key.php
    public function classKeyTest($tx, $key){
        $class = $tx;
        $file  = 'class.' . $tx . '.php';
        $this->setup = array($key, $file);
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path = t3lib_extMgm::extPath($key) . $file));
        self::assertEquals(tx_lib_t3Loader::_find($class), $path);
        self::assertEquals(tx_lib_t3Loader::_find($path), $path);
    }

     // 2.) tx_key_aaa > ext/key/class.tx_key_file.php
    public function classKeyFileTest($tx, $key){
        $class = $tx . '_' . $this->file;
        $file  = 'class.' . $tx . '_' . $this->file. '.php';
        $this->setup = array($key, $file);
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path = t3lib_extMgm::extPath($key) . $file));
        self::assertEquals(tx_lib_t3Loader::_find($class), $path);
        self::assertEquals(tx_lib_t3Loader::_find($path), $path);
    }

    // 3.) tx_key_aaa > ext/key/dir/class.tx_key_dir.php
    public function classKeyDirTest($tx, $key){
        $class = $tx . '_' . $this->dir;
        $file  = 'class.' . $tx . '_' . $this->dir . '.php';
        $dir = $this->dir . '/';
        $this->setup = array($key, $file, $dir);
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path = t3lib_extMgm::extPath($key) . $dir . $file));
        self::assertEquals(tx_lib_t3Loader::_find($class), $path);
        self::assertEquals(tx_lib_t3Loader::_find($path), $path);
    }

    // 4.) tx_key_aaa_bbb > ext/key/dir/class.tx_key_dir_file.php
    public function classKeyDirFileTest($tx, $key){
        $class = $tx . '_' . $this->dir . '_' . $this->file;
        $file  = 'class.' . $tx . '_' . $this->dir . '_' . $this->file. '.php';
        $dir = $this->dir . '/';
        $this->setup = array($key, $file, $dir);
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path = t3lib_extMgm::extPath($key) . $dir . $file));
        self::assertEquals(tx_lib_t3Loader::_find($class), $path);
        self::assertEquals(tx_lib_t3Loader::_find($path), $path);
    }

    // Alternatitve prefix, suffix, key
    //
    // 1.) tx_key         > ext/key/class.tx_key.php
    // 2.) tx_key_aaa     > ext/key/class.tx_key_file.php
    // 3.) tx_key_aaa     > ext/key/dir/class.tx_key_dir.php
    // 4.) tx_key_aaa_bbb > ext/key/dir/class.tx_key_dir_file.php

    // 1.) tx_key > ext/key/class.tx_key.php
    public function altKeyTest($tx){
        $class = $tx;
        $file  = $this->prefix . $tx . $this->suffix;
        $this->setup = array($this->alt, $file);
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path = t3lib_extMgm::extPath($this->alt) . $file));
        self::assertEquals(tx_lib_t3Loader::_find($class, $this->alt, $this->prefix, $this->suffix), $path);
        self::assertEquals(tx_lib_t3Loader::_find($path, $this->alt, $this->prefix, $this->suffix), $path);
    }

     // 2.) tx_key_aaa > ext/key/class.tx_key_file.php
    public function altKeyFileTest($tx){
        $class = $tx . '_' . $this->file;
        $file  = $this->prefix . $tx . '_' . $this->file . $this->suffix;
        $this->setup = array($this->alt, $file);
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path = t3lib_extMgm::extPath($this->alt) . $file));
        self::assertEquals(tx_lib_t3Loader::_find($class, $this->alt, $this->prefix, $this->suffix), $path);   
        self::assertEquals(tx_lib_t3Loader::_find($path, $this->alt, $this->prefix, $this->suffix), $path);
    }

    // 3.) tx_key_aaa > ext/key/dir/class.tx_key_dir.php
    public function altKeyDirTest($tx){
        $class = $tx . '_' . $this->dir;
        $file  = $this->prefix . $tx . '_' . $this->dir . $this->suffix;
        $dir = $this->dir . '/';
        $this->setup = array($this->alt, $file, $dir);
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path = t3lib_extMgm::extPath($this->alt) . $dir . $file));
        self::assertEquals(tx_lib_t3Loader::_find($class, $this->alt, $this->prefix, $this->suffix), $path);
        self::assertEquals(tx_lib_t3Loader::_find($path, $this->alt, $this->prefix, $this->suffix), $path);
    }

    // 4.) tx_key_aaa_bbb > ext/key/dir/class.tx_key_dir_file.php
    public function altKeyDirFileTest($tx){
        $class = $tx . '_' . $this->dir . '_' . $this->file;
        $file  = $this->prefix . $tx . '_' . $this->dir . '_' . $this->file . $this->suffix;
        $dir = $this->dir . '/';
        $this->setup = array($this->alt, $file, $dir);
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path = t3lib_extMgm::extPath($this->alt) . $dir . $file));
        self::assertEquals(tx_lib_t3Loader::_find($class, $this->alt, $this->prefix, $this->suffix), $path);
        self::assertEquals(tx_lib_t3Loader::_find($path, $this->alt, $this->prefix, $this->suffix), $path);
    }

    /****************************************************************
     * Helpers
     ****************************************************************/
    private function popDir($path){
        if(preg_match('|(.*/)[^/]*/|', $path, $matches)){
            return $matches[1];            
        } else {
            return FALSE;
        }
    }

    private function makeClass($array){
        $class = 'testClass';
        $ext = t3lib_extMgm::extPath($array[0]);
        $dir = t3lib_extMgm::extPath($array[0]) . $array[2];
        $file = t3lib_extMgm::extPath($array[0]) . $array[2] . $array[1];
        if(is_dir($ext)){
            die('tx_div_testcase->makeClass: The extension ' . $ext . ' already exists ');
        }else {
            mkdir($ext);
        }
        if( !is_dir($dir) ) {
            mkdir($dir);
        }
        if($fh = fopen($file, 'w') ){
            fwrite($fh, chr(10) . ' class ' . $class . '{}' . chr(10));
            fclose($fh);
        }        
    }

    private function removeClass($array){  
        $ext = t3lib_extMgm::extPath($array[0]);
        $dir = t3lib_extMgm::extPath($array[0]) . $array[2];
        $file = t3lib_extMgm::extPath($array[0]) . $array[2] . $array[1];
        //        self::v($file);
        unlink($file);
        if(is_dir($dir) && substr($dir, -3) != '../'){
            rmdir($dir);
        }
        clearstatcache();        
        if(is_dir($ext)) {
            rmdir($ext);
        }
        clearstatcache();        
    }

    private function v($value){
        print chr(10);
        print_r($value);
        print chr(10);
    }

    /****************************************************************
     * main, setUP, tearDown
     ****************************************************************/

    /**
     * Sets up the fixture, for example, open a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp() {
        $GLOBALS['TYPO3_LOADED_EXT']['div']['siteRelPath'] = 'typo3conf/ext/div/';
        $GLOBALS['TYPO3_LOADED_EXT'][$this->key1]['siteRelPath'] = 'typo3conf/ext/' . $this->key1 . '/';
        $GLOBALS['TYPO3_LOADED_EXT'][$this->key2]['siteRelPath'] = 'typo3conf/ext/' . $this->key2 . '/';
        $GLOBALS['TYPO3_LOADED_EXT'][$this->key3]['siteRelPath'] = 'typo3conf/ext/' . $this->key3 . '/';
        $GLOBALS['TYPO3_LOADED_EXT'][$this->alt ]['siteRelPath'] = 'typo3conf/ext/' . $this->alt  . '/';
        /*
        */
        require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_t3Loader.php');
    }

    /**
     * Tears down the fixture, for example, close a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown() {
        if(count($this->setup) > 0 ){
            $this->removeClass($this->setup);
        }
        unset($this->setup);
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
if(!defined('PATH_site')) { // If running from command line

    // Setup environment
    $path = realpath($_SERVER['PWD'] .'/'. $_SERVER['SCRIPT_NAME']);
    if(!preg_match('|(.*)(typo3conf.*)(' . $key . '/test)|', $path, $matches)) {
        if(! preg_match('|(.*)(typo3/sysext.*)(' . $key . '/test)|', $path, $matches))
            exit(chr(10) . 'Unknown installation path' . chr(10). $path . chr(10));
    }
    define('PATH_site', $matches[1]);
    $GLOBALS['TYPO3_LOADED_EXT'][$key]['siteRelPath']= $matches[2] . $key . '/';
    define('PATH_t3lib', PATH_site . 't3lib/');
    require_once(PATH_t3lib . 'class.t3lib_div.php');
    require_once(PATH_t3lib . 'class.t3lib_extmgm.php');

    // define main method if neede
    if (!defined('PHPUnit2_MAIN_METHOD')) {
        define('PHPUnit2_MAIN_METHOD', $class . '::main');
    }

    // Call main() 
    if (PHPUnit2_MAIN_METHOD == $class . '::main') {
        tx_lib_t3Loader_testcase::main();
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/' . $key . '/tests/class.' . $class . '.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/' . $key . '/tests/class.' . $class . '.php']);
}

?>