<?php

	// DO NOT REMOVE OR CHANGE THESE 2 LINES:
if (!strstr(t3lib_extMgm::extRelPath('metatags_manager'),'typo3conf')) {
  $BACK_PATH='../../../../typo3/';
  define('TYPO3_MOD_PATH', '../typo3conf/ext/metatags_manager/mod1/');
} else {
  $BACK_PATH='../../../../typo3/';
  define('TYPO3_MOD_PATH', '../typo3conf/ext/metatags_manager/mod1/');
}		

$MCONF['name'] = 'web_txmetatagsmanagerM1';
$MCONF['script'] = '_DISPATCH';
	
$MCONF['access'] = 'user,group';

$MLANG['default']['tabs_images']['tab'] = 'moduleicon.gif';
$MLANG['default']['ll_ref'] = 'LLL:EXT:metatags_manager/mod1/locallang_mod.xml';
?>