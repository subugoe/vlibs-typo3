<?php


require_once(t3lib_extMgm::extPath('cl_jquery') . 'cljquerylib.class.php');


if (TYPO3_MODE != 'BE') {

    require_once(t3lib_extMgm::extPath('cl_jquery').'class.tx_cljquery.php');
    $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_pagerenderer.php']['render-preProcess'][] = "EXT:cl_jquery/class.tx_cljquery.php:&tx_cljquery->getJavaScriptAssets";
  
}