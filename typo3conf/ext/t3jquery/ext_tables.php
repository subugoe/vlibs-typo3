<?php
if (!defined ('TYPO3_MODE')) {
	die('Access denied.');
}

if (TYPO3_MODE == 'BE') {
	// get extension configuration
	$confArr = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['t3jquery']);

	if ($confArr['enableStyleStatic']) {
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/bootstrap',         'T3JQUERY Style: Bootstrap default');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/mobile',            'T3JQUERY Style: Mobiles default');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/blitzer',        'T3JQUERY Style: UI Blitzer');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/cupertino',      'T3JQUERY Style: UI Cupertino');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/dark-hive',      'T3JQUERY Style: UI Dark-Hive');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/eggplant',       'T3JQUERY Style: UI Eggplant');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/excite-bike',    'T3JQUERY Style: UI Excite-Bike');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/flick',          'T3JQUERY Style: UI Flick');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/hot-sneaks',     'T3JQUERY Style: UI Hot-Sneaks');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/humanity',       'T3JQUERY Style: UI Humanity');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/le-frog',        'T3JQUERY Style: UI Le-Frog');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/overcast',       'T3JQUERY Style: UI Overcast');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/pepper-grinder', 'T3JQUERY Style: UI Pepper-Grinder');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/redmond',        'T3JQUERY Style: UI Redmond');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/smoothness',     'T3JQUERY Style: UI Smoothness');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/south-street',   'T3JQUERY Style: UI South-Street');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/start',          'T3JQUERY Style: UI Start');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/sunny',          'T3JQUERY Style: UI Sunny');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/ui-darkness',    'T3JQUERY Style: UI UI-Darkness');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/ui-lightness',   'T3JQUERY Style: UI UI-Lightness');
		t3lib_extMgm::addStaticFile($_EXTKEY, 'static/ui/vader',          'T3JQUERY Style: UI Vader');
	}

	if (! $confArr['integrateFromCDN']) {
		t3lib_extMgm::addModule('tools', 'txt3jqueryM1', '', t3lib_extMgm::extPath($_EXTKEY).'mod1/');
	}
}

?>