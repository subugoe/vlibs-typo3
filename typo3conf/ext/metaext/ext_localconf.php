<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$_EXTCONF = unserialize($_EXTCONF);	// unserializing the configuration so we can use it here:

### switch on/off postprocessing 
if ($_EXTCONF['postprocessing'] == '1') {
	$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['contentPostProc-output']['tx_metaext'] = 'EXT:metaext/lib/class.tx_metaext_postprocess.php:&tx_metaext_postprocess->formatOutput';
}
### switch on/off patch of constant editor (adding several new subcategories)
if ($_EXTCONF['extconsteditor'] == '1') {
    $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_tsparser_ext.php'] = t3lib_extMgm::extPath('metaext') . 'lib/class.ux_t3lib_tsparser_ext.php';
}
### switch on/off patch 6637 of tslib menu to introduce menu special=rootline .reverseOrder feature. obsolete with Typo3v4 >= 4.3 as this will be included in the core distribution then.
if ($_EXTCONF['patch6637'] == '1') {
    $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['tslib/class.tslib_menu.php'] = t3lib_extMgm::extPath('metaext') . 'lib/class.ux_tslib_menu.php';
}
### disable no_cache
if ($_EXTCONF['disableNoCacheParameter'] == '1') {
	$TYPO3_CONF_VARS['FE']['disableNoCacheParameter'] = '1';
}

### pages, who are lacking a translation will be hidden in the page tree by default (rootline, menues etc)
#	note:
#	BE Web->Page->Edit Page Properties->Localization settings(Options Tab)->
#		'Hide page if no translation for current language exists'
#	changes to:
#		'Show page even if no translation exists'
#	i.e. if a page was formerly marked exclusively hidden, it will then be exclusively UNhidden !
#
if ($extconf['hideUntranslatedPages']) {
	$TYPO3_CONF_VARS['FE']['hidePagesIfNotTranslatedByDefault'] = 1;
}

### add realurl config for sitemaps 
### MAKE SURE metaext is loaded AFTER realur! or this won't work
### -> check $TYPO3_CONF_VARS['EXT']['extList'] in your localconf.php! 
###
$sitemaps = $_EXTCONF['sitemaplist'];
if (!empty($sitemaps)) {

	$realurlconfig = $TYPO3_CONF_VARS['EXTCONF']['realurl'];
	$map = split(',',$sitemaps);


	if( is_array($realurlconfig) && count($map) )	{
		foreach($map as $idx => $mapvalue) {
			list($mapname, $typenum ) = split(':',trim($mapvalue));
			# loosely check if supplied mapname and typenum are valid
			if (!empty($mapname) && intval($typenum) > 0) {
				foreach ($realurlconfig as $host => $config) {
					if ( !is_array($realurlconfig[$host]['fileName']) ) { $realurlconfig[$host]['fileName'] = array(); }
					$realurlconfig[$host]['fileName']['index'][$mapname]['keyValues']['type'] = intval($typenum);
				}
			}
		};
		$TYPO3_CONF_VARS['EXTCONF']['realurl'] = $realurlconfig;
	}

}

### adding the additional overlay fields to the configuration
$GLOBALS['TYPO3_CONF_VARS']['FE']['pageOverlayFields'] .= ',tx_metaext_geoplacename,tx_metaext_copyright,tx_metaext_alttitle';

?>
