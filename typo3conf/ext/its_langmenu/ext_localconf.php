<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

  ## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_itslangmenu_pi1 = < plugin.tx_itslangmenu_pi1.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi1/class.tx_itslangmenu_pi1.php','_pi1','list_type',1);

## Extending TypoScript from static template uid=43 to set up userdefined tag:
t3lib_extMgm::addTypoScript($_EXTKEY,'editorcfg','
	tt_content.CSS_editor.ch.tx_itslangmenu_pi2 = < plugin.tx_itslangmenu_pi2.CSS_editor
',43);


t3lib_extMgm::addPItoST43($_EXTKEY,'pi2/class.tx_itslangmenu_pi2.php','_pi2','list_type',0);




$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tstemplate.php']['linkData-PostProc']['tx_0its_lang'] = "EXT:$_EXTKEY/hook/class.tx_itslangmenu.php:&tx_itslangmenu->checklang";
if (isset($TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tstemplate.php']['linkData-PostProc']['tx_realurl'])) {
	$temp = $TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tstemplate.php']['linkData-PostProc']['tx_realurl'];
	unset ($TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tstemplate.php']['linkData-PostProc']['tx_realurl']);
	$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tstemplate.php']['linkData-PostProc']['tx_realurl'] = $temp;
	//Order of the hooks is important
}



?>