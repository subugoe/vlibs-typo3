<?php



class cljquerylib {

  private $conf = null;


  public static $extKey = 'cl_jquery';


  function init() {

    $this->conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf'][cljquerylib::$extKey]);


  }

  public function getConfigDir() {
    return $this->conf['configDir'].(preg_match("/$\//", $this->conf['configDir']) ? "" : "/");
  }

  public function getFullConfigDir() {
    return t3lib_div::getIndpEnv('TYPO3_DOCUMENT_ROOT') . '/' . $this->conf['configDir'].(preg_match("/$\//", $this->conf['configDir']) ? "" : "/");
  }



  private static function extractT3jqueryTXTFiles($t3jqfile) {
    $lines = file($t3jqfile);
    $path = dirname($t3jqfile);
    $files = array();

    foreach ($lines as $line) {
      $tmp = explode('=', $line);
      $option = strtolower(trim($tmp[0]));
      $params = explode(',', $tmp[1]);
      switch ($option) {
        case 'script' : {
          foreach ($params as $file) {
            if (is_file($path.'/'.trim($file))) {
              $files[] = substr($path.'/'.trim($file),strlen(PATH_site.'typo3conf/ext/'));
            }
          }
          break;
        }
      }
    }
    return $files;
  }



  private static function getExtensionJavaScriptFiles() {

    $exts = array();

    $path = PATH_site.'typo3conf/ext/';
    if (@is_dir($path)) {
      $dirs = t3lib_div::get_dirs($path);
      if (is_array($dirs)) {
        sort($dirs);
        foreach ($dirs as $dirName) {
          // only display loaded extensions
          if (t3lib_extMgm::isLoaded($dirName)) {
            if (@file_exists($path.$dirName.'/t3jquery.txt')) {
              $exts[$dirName] = self::extractT3jqueryTXTFiles(t3lib_extMgm::extPath($dirName)."/t3jquery.txt");
            }
          }
        }
      }
    }

    return $exts;

  }

  private static function getJQueryUIVersions() {
    $path = PATH_site.'typo3conf/ext/'.self::$extKey.'/js/ui';
    if (@is_dir($path)) {
      return t3lib_div::get_dirs($path);
    }
    return array();
  }

  private static function getJQueryVersions() {
    $v = array();
    $path = PATH_site.'typo3conf/ext/'.self::$extKey.'/js';
    if (@is_dir($path)) {
      $files = t3lib_div::getFilesInDir($path,'js');
      foreach ($files as $file) {
        $v[] = substr($file,7,-3);
      }
    }
    return $v;
  }

  private static function getJQueryUIModules($version = '1.8') {

    $v = array();
    $path = PATH_site.'typo3conf/ext/'.self::$extKey.'/js/ui/'.$version;
    if (@is_dir($path)) {
      $files = t3lib_div::getFilesInDir($path,'js');
      foreach ($files as $file) {
        $v[] = substr($file,7,-3);
      }
    }
    return $v;

  }


  public static function loadOptions() {
    global $TCA;
    t3lib_div::loadTCA('sys_template');

    // Extension JS files
    $exts = self::getExtensionJavaScriptFiles();
    //$jsfiles = array(array('clear','clear'));
    
    foreach ($exts as $ext => $files) {
      foreach ($files as $file) {
        $jsfiles[] = array($ext." - ".substr($file,strrpos($file,"/")+1),$file);
      }
    }
    $TCA['sys_template']['columns']['include_extjs']['config']['items'] = $jsfiles;

    // jquery versions
    $jqversions = array();
    $jqversions[] = array('LLL:EXT:cl_jquery/locallang_db.xml:sys_template.javascript.jquery_version.none', '');
    $versions = self::getJQueryVersions();
    foreach ($versions as $version) {
      $jqversions[] = array($version,$version);
    }

    $TCA['sys_template']['columns']['jquery_version']['config']['items'] = $jqversions;

    // ui version
    $jquiversions = array();
    $jquiversions[] = array('LLL:EXT:cl_jquery/locallang_db.xml:sys_template.javascript.jquery_ui_version.none', '');
    $versions = self::getJQueryUIVersions();
    foreach ($versions as $version) {
      $jquiversions[] = array($version,$version);
    }

    $TCA['sys_template']['columns']['jquery_ui_version']['config']['items'] = $jquiversions;

    // ui modules
    $jquimodules = array();
    $modules = self::getJQueryUIModules();
    foreach ($modules as $module) {
      $jquimodules[] = array($module,$module);
    }
    sort($jquimodules);
    
    $TCA['sys_template']['columns']['jquery_ui_modules']['config']['items'] = $jquimodules;

  }



}