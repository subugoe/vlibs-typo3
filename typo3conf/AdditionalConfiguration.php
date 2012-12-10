<?php
$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['realurl']['_DEFAULT'] = array (
	'init' => array (
		'appendMissingSlash' => 'ifNotFile,redirect[301]',
		'respectSimulateStaticURLs' => true,
		'enableCHashCache' => true,
		'enableUrlDecodeCache' => false,
		'enableUrlEncodeCache' => false,
	),
	'preVars' => array (
		0 => array (
			'GETvar' => 'L',
			'valueMap' => array (
				'en' => '2',
			),
			'noMatch' => 'bypass',
		),
	),
	'pagePath' => array (
		'type' => 'user',
		'userFunc' => 'EXT:realurl/class.tx_realurl_advanced.php:&tx_realurl_advanced->main',
		'spaceCharacter' => '-',
		'languageGetVar' => 'L',
		'rootpage_id' => '12',
	),
	'fixedPostVars' => array (
		'xmlinclude' => array (
			array(
				'GETvar' => 'tx_xmlinclude_xmlinclude[URL]',
				'userFunc' => 'EXT:xmlinclude/Classes/RealURL/tx_xmlinclude_realurl.php:&tx_xmlinclude_realurl->main'
			)
		),
		'71' => 'xmlinclude',
		'80' => 'xmlinclude',
		'84' => 'xmlinclude',
	),
);
?>