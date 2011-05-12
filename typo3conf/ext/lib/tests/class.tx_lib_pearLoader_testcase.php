<?php
error_reporting (E_ALL ^ E_NOTICE);
if (!defined('PHPUnit2_MAIN_METHOD')) {
    define('PHPUnit2_MAIN_METHOD', 'tx_lib_pearLoader_testcase::main');
}

require_once ('PHPUnit2/Framework/TestCase.php');
require_once ('PHPUnit2/Framework/TestSuite.php');

// You may remove the following line when all tests have been implemented.
require_once "PHPUnit2/Framework/IncompleteTestError.php";

// Defining global test constants

if(!defined('PATH_site')) {
    $path = realpath($_SERVER['PWD'] .'/'. $_SERVER['SCRIPT_NAME']);
    if(!preg_match('|(.*)(typo3conf.*)(lib/test)|', $path, $matches)) {
        if(! preg_match('|(.*)(typo3/sysext.*)(lib/test)|', $path, $matches))
            exit(chr(10) . 'Unknown installation path' . chr(10). $path . chr(10));
    }
    define('PATH_site', $matches[1]);
    $GLOBALS['TYPO3_LOADED_EXT']['lib']['siteRelPath']= $matches[2] . 'lib/';
    define('PATH_t3lib', PATH_site . 't3lib/');
    require_once(PATH_t3lib . 'class.t3lib_div.php');
    require_once(PATH_t3lib . 'class.t3lib_extmgm.php');
}

/**
 * Test class for tx_lib_pearLoader
 */
class tx_lib_pearLoader_testcase extends PHPUnit2_Framework_TestCase {

    // 3 different extesionkeys
    private $key1 = 'foo_bar1';
    private $key2 = 'foobar2';    
    private $key3 = 'fooBar3';

    // alternative extensionkey
    private $alt = 'foo_bar_alt';

    // class in common extensions root directory
    private $root1 = 'tx_foobar1';
    private $root2 = 'tx_foobar2';
    private $root3 = 'tx_fooBar3';

    // class in root or the extension
    private $ext1 = 'tx_foobar1_file';
    private $ext2 = 'tx_foobar2_file';
    private $ext3 = 'tx_fooBar3_file';

    // class in subdirectory of the extension
    private $dir1 = 'tx_foobar1_directory_file';
    private $dir2 = 'tx_foobar2_directory_file';
    private $dir3 = 'tx_fooBar3_directory_file';

    // Files inside root directory equal extension names without underscore
    private $file1_suff = 'foobar1.inc.php';
    private $file1 = 'foobar1.php';
    private $file2 = 'foobar2.php';    
    private $file3 = 'fooBar3.php';

    // general names
    private $dir = 'directory/';
    private $file = 'file.php';
    private $file_suff = 'file.inc.php';
    private $prefix = 'class.';
    private $suffix = '.inc.php';
    
    // a path specification set in the tests to tear down
    private $setup = array();

    /**
     * Runs the test methods of this class.
     *
     * @access public
     * @static
     */
    public static function main() {
        require_once "PHPUnit2/TextUI/TestRunner.php";
        $suite  = new PHPUnit2_Framework_TestSuite("tx_lib_pearLoader_testcase");
        $result = PHPUnit2_TextUI_TestRunner::run($suite);
    }

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
        require_once(t3lib_extMgm::extPath('lib') . 'class.tx_lib_pearLoader.php');
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

    /****************************************************************
     * Selftests
     ****************************************************************/

    public function testRootCreation(){
        $array = array($this->key1, $this->file1, '../');
        $ext   = t3lib_extMgm::extPath($this->key1);
        $path  = self::popDir($ext) . $this->file1;
        $this->makeClass($array);
        self::assertTrue(is_file($path));
        $this->removeClass($array);
        self::assertFalse(is_dir($ext));
    }

    public function testExtensionCreation(){
        $array = array($this->key1, $this->file);
        $ext   = t3lib_extMgm::extPath($this->key1);
        $path  = $ext . $this->file;
        $this->makeClass($array);
        self::assertTrue(is_file($path));
        $this->removeClass($array);
        self::assertFalse(is_dir($ext));
    }

    public function testDirectoryCreation(){
        $array = array($this->key1, $this->file, $this->dir);
        $ext   = t3lib_extMgm::extPath($this->key1);
        $path  = $ext . $this->dir . $this->file;
        $this->makeClass($array);
        self::assertTrue(is_file($path));
        $this->removeClass($array);
        self::assertFalse(is_dir($ext . $this->dir ));
        self::assertFalse(is_dir($ext));
    }

    /****************************************************************
     * Files without prefix
     ****************************************************************/

    public function testFileInExt1Unloaded(){
        $this->setup = array($this->key1, $this->file);
        $ext = t3lib_extMgm::extPath($this->key1);
        $path = $ext . $this->file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        // Same like before but with unloaded extension
        unset($GLOBALS['TYPO3_LOADED_EXT'][$this->key1]['siteRelPath']);
        self::assertFalse(tx_lib_pearLoader::_find($this->ext1));
        // Reload for removing
        $GLOBALS['TYPO3_LOADED_EXT'][$this->key1]['siteRelPath'] = 'typo3conf/ext/' . $this->key1 . '/';
    }

    public function testFileInRoot1(){
        $file  = $this->file1;
        $this->setup = array($this->key1, $file, '../');
        $ext   = t3lib_extMgm::extPath($this->key1);
        $path  = $this->popDir($ext) . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->root1), $path);
    }

    public function testFileInRoot2(){
        $file  = $this->file2;
        $this->setup = array($this->key2, $file, '../');
        $ext   = t3lib_extMgm::extPath($this->key2);
        $path  = $this->popDir($ext) . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->root2), $path);
    }

    public function testFileInRoot3(){
        $file  = $this->file3;
        $this->setup = array($this->key3, $file, '../');
        $ext   = t3lib_extMgm::extPath($this->key3);
        $path  = $this->popDir($ext) . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->root3), $path);
    }

    public function testFileInExt1(){
        $file  = $this->file;
        $this->setup = array($this->key1, $file);
        $ext   = t3lib_extMgm::extPath($this->key1);
        $path  = $ext . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->ext1), $path);
    }

    public function testFileInExt2(){
        $file  = $this->file;
        $this->setup = array($this->key2, $file);
        $ext   = t3lib_extMgm::extPath($this->key2);
        $path  = $ext . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->ext2), $path);
    }

    public function testFileInExt3(){
        $file  = $this->file;
        $this->setup = array($this->key3, $file);
        $ext   = t3lib_extMgm::extPath($this->key3);
        $path  = $ext . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->ext3), $path);
    }

    public function testFileInDir1(){
        $file  = $this->file;
        $this->setup = array($this->key1, $file, $this->dir);
        $path = t3lib_extMgm::extPath($this->key1) . $this->dir . $file;
        $this->makeClass($this->setup);
        self::assertEquals(tx_lib_pearLoader::_find($this->dir1), $path);
    }

    public function testFileInDir2(){
        $file  = $this->file;
        $this->setup = array($this->key2, $file, $this->dir);
        $path = t3lib_extMgm::extPath($this->key2) . $this->dir . $file;
        $this->makeClass($this->setup);
        self::assertEquals(tx_lib_pearLoader::_find($this->dir2), $path);
    }

    public function testFileInDir3(){
        $file  = $this->file;
        $this->setup = array($this->key3, $file, $this->dir);
        $path = t3lib_extMgm::extPath($this->key3) . $this->dir . $file;
        $this->makeClass($this->setup);
        self::assertEquals(tx_lib_pearLoader::_find($this->dir3), $path);
    }

    /****************************************************************
     * Files with prefix
     ****************************************************************/

    public function testPrefixedFileInRoot1(){
        $file  = $this->prefix . $this->file1;
        $this->setup = array($this->key1, $prefix . $file, '../');
        $ext   = t3lib_extMgm::extPath($this->key1);
        $path  = $this->popDir($ext) . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->root1, null, $this->prefix), $path);
    }

    public function testPrefixedFileInRoot2(){
        $file  = $this->prefix . $this->file2;
        $this->setup = array($this->key2, $prefix . $file, '../');
        $ext   = t3lib_extMgm::extPath($this->key2);
        $path  = $this->popDir($ext) . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->root2, null, $this->prefix), $path);
    }

    public function testPrefixedFileInRoot3(){
        $file  = $this->prefix . $this->file3;
        $this->setup = array($this->key3, $prefix . $file, '../');
        $ext   = t3lib_extMgm::extPath($this->key3);
        $path  = $this->popDir($ext) . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->root3, null, $this->prefix), $path);
    }

    public function testPrefixedFileInExt1(){
        $file  = $this->prefix . $this->file;
        $this->setup = array($this->key1, $file);
        $ext   = t3lib_extMgm::extPath($this->key1);
        $path  = $ext . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->ext1, null, $this->prefix), $path);
    }

    public function testPrefixedFileInExt2(){
        $file  = $this->prefix . $this->file;
        $this->setup = array($this->key2, $file);
        $ext   = t3lib_extMgm::extPath($this->key2);
        $path  = $ext . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->ext2, null, $this->prefix), $path);
    }

    public function testPrefixedFileInExt3(){
        $file  = $this->prefix . $this->file;
        $this->setup = array($this->key3, $file);
        $ext   = t3lib_extMgm::extPath($this->key3);
        $path  = $ext . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->ext3, null, $this->prefix), $path);
    }

    public function testPrefixedFileInDir1(){
        $file  = $this->prefix . $this->file;
        $this->setup = array($this->key1, $file, $this->dir);
        $path = t3lib_extMgm::extPath($this->key1) . $this->dir . $file;
        $this->makeClass($this->setup);
        self::assertEquals(tx_lib_pearLoader::_find($this->dir1, null, $this->prefix), $path);
    }

    public function testPrefixedFileInDir2(){
        $file  = $this->prefix . $this->file;
        $this->setup = array($this->key2, $file, $this->dir);
        $path = t3lib_extMgm::extPath($this->key2) . $this->dir . $file;
        $this->makeClass($this->setup);
        self::assertEquals(tx_lib_pearLoader::_find($this->dir2, null, $this->prefix), $path);
    }

    public function testPrefixedFileInDir3(){
        $file  = $this->prefix . $this->file;
        $this->setup = array($this->key3, $file, $this->dir);
        $path = t3lib_extMgm::extPath($this->key3) . $this->dir . $file;
        $this->makeClass($this->setup);
        self::assertEquals(tx_lib_pearLoader::_find($this->dir3, null, $this->prefix), $path);
    }

    /****************************************************************
     * Files with suffix
     ****************************************************************/

    public function testSuffixedFileInRoot1(){
        $file  = $this->file1_suff;
        $this->setup = array($this->key1,  $file, '../');
        $ext   = t3lib_extMgm::extPath($this->key1);
        $path  = $this->popDir($ext) . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->root1, null, null, $this->suffix), $path);
    }

    public function testSuffixedFileInExt1(){
        $file  = $this->file_suff;
        $this->setup = array($this->key1, $file);
        $ext   = t3lib_extMgm::extPath($this->key1);
        $path  = $ext . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->ext1, null, null, $this->suffix), $path);
    }

    public function testSuffixedFileInDir1(){
        $file  = $this->file_suff;
        $this->setup = array($this->key1, $file, $this->dir);
        $path = t3lib_extMgm::extPath($this->key1) . $this->dir . $file;
        $this->makeClass($this->setup);
        self::assertEquals(tx_lib_pearLoader::_find($this->dir1, null, null, $this->suffix), $path);
    }

    /****************************************************************
     * Files with alternative key
     ****************************************************************/

    public function testFileInRoot1Alternative(){
        $file  = $this->file1;
        $this->setup = array($this->alt,  $file, '../');
        $ext   = t3lib_extMgm::extPath($this->alt);
        $path  = $this->popDir($ext) . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->root1, $this->alt), $path);
    }

    public function testFileInExt1Alternative(){
        $file  = $this->file;
        $this->setup = array($this->alt, $file);
        $ext   = t3lib_extMgm::extPath($this->alt);
        $path  = $ext . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->ext1, $this->alt), $path);
    }

    public function testFileInDir1Alternative(){
        $file  = $this->file;
        $this->setup = array($this->alt, $file, $this->dir);
        $path = t3lib_extMgm::extPath($this->alt) . $this->dir . $file;
        $this->makeClass($this->setup);
        self::assertEquals(tx_lib_pearLoader::_find($this->dir1, $this->alt), $path);
    }

    /****************************************************************
     * Files with all
     ****************************************************************/

    public function testWithAllFileInRoot1(){
        $file  = $this->prefix . $this->file1_suff;
        $this->setup = array($this->alt,  $file, '../');
        $ext   = t3lib_extMgm::extPath($this->alt);
        $path  = $this->popDir($ext) . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->root1, $this->alt, 
                                                    $this->prefix, $this->suffix), $path);
    }

    public function testWithAllInExt1(){
        $file  = $this->prefix . $this->file_suff;
        $this->setup = array($this->alt, $file);
        $ext   = t3lib_extMgm::extPath($this->alt);
        $path  = $ext . $file;
        $this->makeClass($this->setup);
        self::assertTrue(is_file($path));
        self::assertEquals(tx_lib_pearLoader::_find($this->ext1, $this->alt, 
                                                    $this->prefix, $this->suffix), $path);
    }

    public function testWithAllInDir1(){
        $file  = $this->prefix . $this->file_suff;
        $this->setup = array($this->alt, $file, $this->dir);
        $path = t3lib_extMgm::extPath($this->alt) . $this->dir . $file;
        $this->makeClass($this->setup);
        self::assertEquals(tx_lib_pearLoader::_find($this->dir1, $this->alt, $this->prefix, 
                                                    $this->suffix), $path);
    }

    /****************************************************************
     * Extern Tests
     ****************************************************************/

    public function testExternWithoutTxAndFirstPartReplacement(){
        $key = 'pear_phpunit2';
        $class = 'PHPUnit2_Tests_Error';
        $relPath = 'Tests/Error.php';
        $GLOBALS['TYPO3_LOADED_EXT'][$key]['siteRelPath'] = 'typo3conf/ext/' .$key . '/';
        $path = t3lib_extMgm::extPath($key) . $relPath;
        self::assertEquals(tx_lib_pearLoader::_find($class, $key), $path);
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
}

// Call main() if this source file is executed directly.
if (PHPUnit2_MAIN_METHOD == "tx_lib_pearLoader_testcase::main") {
    tx_lib_pearLoader_testcase::main();
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/tests/class.tx_lib_pearLoader_testcase.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/tests/class.tx_lib_pearLoader_testcase.php']);
}


?>