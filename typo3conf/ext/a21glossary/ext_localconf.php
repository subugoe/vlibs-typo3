<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
t3lib_extMgm::addPageTSConfig('

	# ***************************************************************************************
	# CONFIGURATION of RTE in table "tx_a21glossary_main", field "description"
	# ***************************************************************************************
RTE.config.tx_a21glossary_main.description {
  hidePStyleItems = H1, H4, H5, H6
  proc.exitHTMLparser_db=1
  proc.exitHTMLparser_db {
    keepNonMatchedTags=1
    tags.font.allowedAttribs= color
    tags.font.rmTagIfNoAttrib = 1
    tags.font.nesting = global
  }
}
');
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-all']['tx_a21glossary'] = 'EXT:a21glossary/class.tx_a21glossary.php:&tx_a21glossary->convertGlossaryWords';
?>