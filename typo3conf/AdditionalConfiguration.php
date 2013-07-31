<?php
	$TYPO3_CONF_VARS['EXTCONF']['realurl']['_DEFAULT'] = array(
		'init' => array(
			'appendMissingSlash' => 'ifNotFile,redirect[301]',
			'postVarSet_failureMode' => '',
			'emptyUrlReturnValue' => TRUE,
			'respectSimulateStaticURLs' => 1,
			'enableCHashCache' => 1,
			'enableUrlDecodeCache' => 0,
			'enableUrlEncodeCache' => 0,
		),
		'preVars' => array(
			array(
				'GETvar' => 'L',
				'valueMap' => array(
					'en' => 2,
				),
				'noMatch' => 'bypass'
			),
			array(
				'GETvar' => 'no_cache',
				'valueMap' => array(
					'no_cache' => 1,
				),
				'noMatch' => 'bypass'
			),
		),
		'pagePath' => array(
			'type' => 'user',
			'userFunc' => 'EXT:realurl/class.tx_realurl_advanced.php:&tx_realurl_advanced->main',
			'spaceCharacter' => '-',
			'segTitleFieldList' => 'tx_realurl_pathsegment,nav_title,title',
			'languageGetVar' => 'L',
			'expireDays' => 3,
			'rootpage_id' => '2',
		)
	);
?>