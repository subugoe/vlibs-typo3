<?php
	$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT'] = array(
		'init' => array(
			'appendMissingSlash' => 'ifNotFile,redirect[301]',
			'respectSimulateStaticURLs' => true,
			'enableCHashCache' => true,
			'enableUrlDecodeCache' => false,
			'enableUrlEncodeCache' => false,
		),
		'preVars' => array(
			array(
				'GETvar' => 'L',
				'valueMap' => array(
					'en' => 2,
				),
				'noMatch' => 'bypass'
			),
		),
		'pagePath' => array(
			'type' => 'user',
			'userFunc' => 'EXT:realurl/class.tx_realurl_advanced.php:&tx_realurl_advanced->main',
			'spaceCharacter' => '-',
			'languageGetVar' => 'L',
			'rootpage_id' => '2',
		)
	);
?>