<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
t3lib_extMgm::addPageTSConfig('
	plugin.mc_autokeywords.autogenerate = 1
	plugin.mc_autokeywords.stopWords = der,in,und,die,sich,Sie,wie,ist,
	plugin.mc_autokeywords.count = 150
');

/**
  *  Put modify of document in Hook
  */
    
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass'][] = 'EXT:mc_autokeywords/class.tx_mcautokeywords.php:&tx_mcautokeywords';

/**
  *  Put delete of document in Hook
  */
    
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass'][] = 'EXT:mc_autokeywords/class.tx_mcautokeywords.php:&tx_mcautokeywords';
?>