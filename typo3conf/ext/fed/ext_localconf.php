<?php
if (!defined ('TYPO3_MODE')) {
	die ('Access denied.');
}

$GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup'] = unserialize($_EXTCONF);

Tx_Extbase_Utility_Extension::configurePlugin(
	$_EXTKEY,
	'API',
	array(
		'Page' => 'render',
		'Hash' => 'request',
	),
	array(
		'Hash' => 'request',
	)
);

t3lib_extMgm::addTypoScript($_EXTKEY, 'setup', "
	[GLOBAL]
	plugin.tx_fed.fce.fed {
		templateRootPath = EXT:fed/Resources/Private/Elements/
		partialRootPath = EXT:fed/Resources/Private/Partials/
		layoutRootPath = EXT:fed/Resources/Private/Layouts/
	}
	config.tx_extbase.persistence.classes.Tx_Fed_Persistence_FileObjectStorage.mapping {
		tableName = 0
	}
	FedFrameworkBridge = PAGE
	FedFrameworkBridge {
		typeNum = 4815162342
		config {
			no_cache = 1
			disableAllHeaderCode = 1
		}
		headerData >
		4815162342 = USER_INT
		4815162342 {
			userFunc = tx_fed_core_bootstrap->run
			extensionName = Fed
			pluginName = API
		}
	}

");

if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableFluidContentElements']) {
	Tx_Extbase_Utility_Extension::configurePlugin(
		$_EXTKEY,
		'Fce',
		array(
			'FlexibleContentElement' => 'render',
		),
		array(
		),
		Tx_Extbase_Utility_Extension::PLUGIN_TYPE_CONTENT_ELEMENT
	);
}


if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableFrontendPlugins']) {

	Tx_Extbase_Utility_Extension::configurePlugin(
		$_EXTKEY,
		'Template',
		array(
			'Template' => 'show',
		),
		array(
		),
		Tx_Extbase_Utility_Extension::PLUGIN_TYPE_CONTENT_ELEMENT
	);


	Tx_Extbase_Utility_Extension::configurePlugin(
		$_EXTKEY,
		'Datasource',
		array(
			'DataSource' => 'list,show,rest',
		),
		array(
			'DataSource' => 'rest',
		),
		Tx_Extbase_Utility_Extension::PLUGIN_TYPE_CONTENT_ELEMENT
	);


}

if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableFluidPageTemplates']) {
	t3lib_extMgm::addTypoScript($_EXTKEY,'setup',
		'[GLOBAL]
		page = PAGE
		page.typeNum = 0
		page.5 = USER
		page.5.userFunc = tx_fed_core_bootstrap->run
		page.5.extensionName = Fed
		page.5.pluginName = API
		page.10 >
	', TRUE);
	if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableFallbackFluidPageTemplate']) {
		t3lib_extMgm::addTypoScript($_EXTKEY,'setup',
			'[GLOBAL]
			plugin.tx_fed.page.fed {
				enable = 0
				templateRootPath = EXT:fed/Resources/Private/Templates/
				layoutRootPath = EXT:fed/Resources/Private/Layouts/
				partialRootPath = EXT:fed/Resources/Private/Partials/
			}
			plugin.tx_fed.settings.templates.fallbackFluidPageTemplate = EXT:fed/Resources/Private/Templates/Page/Render.html
		', TRUE);
	}
	$GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] .= ($GLOBALS['TYPO3_CONF_VARS']['FE']['addRootLineFields'] == '' ? '' : ',') . 'tx_fed_page_controller_action,tx_fed_page_controller_action_sub,tx_fed_page_flexform,';
}

if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableSolrFeatures']) {
	Tx_Extbase_Utility_Extension::configurePlugin(
		$_EXTKEY,
		'Solr',
		array(
			'Solr' => 'form,search',
		),
		array(
			'Solr' => 'search',
		),
		Tx_Extbase_Utility_Extension::PLUGIN_TYPE_CONTENT_ELEMENT
	);
	t3lib_extMgm::addTypoScript($_EXTKEY, 'setup', "
		FedSolrBridge = PAGE
		FedSolrBridge {
			typeNum = 1324054607
			config {
				no_cache = 1
				disableAllHeaderCode = 1
			}
			headerData >
			1324054607 = USER_INT
			1324054607 {
				userFunc = tx_fed_core_bootstrap->run
				extensionName = Fed
				pluginName = Solr
			}
		}");
}

$fedWizardElements = array();
if (TYPO3_MODE == 'BE') {
	if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableSolrFeatures']
	|| $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableFluidContentElements']
	|| $GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableFrontendPlugins']
	) {
		array_push($fedWizardElements, 'template');
		array_push($fedWizardElements, 'datasource');
		array_push($fedWizardElements, 'solr');
	}

	if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableFluidContentElements']) {
		$GLOBALS['TYPO3_DB'] = new t3lib_DB();
		$GLOBALS['TYPO3_DB']->connectDB();
		$template = t3lib_div::makeInstance("t3lib_tsparser_ext");
		$template->tt_track = 0;
		$template->init();
		$sys_page = t3lib_div::makeInstance("t3lib_pageSelect");
		$rootLine = $sys_page->getRootLine(intval(t3lib_div::_GP('id')));
		$template->runThroughTemplates($rootLine);
		$template->generateConfig();
		$allTemplatePaths = $template->setup['plugin.']['tx_fed.']['fce.'];
		$allTemplatePaths = Tx_Fed_Utility_Path::translatePath($allTemplatePaths);
		unset($GLOBALS['TYPO3_DB']);
		foreach ($allTemplatePaths as $key => $templatePathSet) {
			$key = trim($key, '.');
			$files = Tx_Fed_Utility_Path::getFiles($templatePathSet['templateRootPath'], TRUE);
			if (count($files) > 0) {
				$groupLabel = '';
				if (!t3lib_extMgm::isLoaded($key)) {
					$groupLabel = ucfirst($key);
				} else {
					$emConfigFile = t3lib_extMgm::extPath($key, 'ext_emconf.php');
					require $emConfigFile;
					$groupLabel = empty($EM_CONF['']['title']) ? ucfirst($key) : $EM_CONF['']['title'];
				}
				foreach ($files as $fileRelPath) {
					$contentConfiguration = array();
					$templateFilename = $templatePathSet['templateRootPath'] . DIRECTORY_SEPARATOR . $fileRelPath;
					$templateContents = file_get_contents($templateFilename);
					$matches = array();
					$pattern = '/<flux\:flexform[^\.]([^>]+)/';
					preg_match_all($pattern, $templateContents, $matches);
					foreach (explode('" ', trim($matches[1][0], '"')) as $valueStringPair) {
						list ($name, $value) = explode('="', trim($valueStringPair, '"'));
						$contentConfiguration[$name] = $value;
					}
					if ($contentConfiguration['enabled'] === 'FALSE') {
						continue;
					}
					$icon = $config['icon'] ? $config['icon'] : '../typo3conf/ext/fed/Resources/Public/Icons/Plugin.png';
					$id = md5($templateFilename);
					t3lib_extMgm::addPageTSConfig('
						mod.wizards.newContentElement.wizardItems.fed.elements.' . $id . ' {
							icon = ' . $icon . '
							title = ' . $contentConfiguration['label'] . '
							description = ' . $contentConfiguration['description'] . '
							tt_content_defValues {
								CType = fed_fce
								tx_fed_fcefile = ' . $key . ':' . $fileRelPath . '
							}
						}
					');
					array_push($fedWizardElements, $id);
				}
			}
		}
	}

	if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableFrontendPlugins']) {

		t3lib_extMgm::addPageTSConfig('
			mod.wizards.newContentElement.wizardItems.fed.elements.template {
				icon = ../typo3conf/ext/fed/Resources/Public/Icons/Plugin.png
				title = Template Display
				description = Flexible Content Element using a Fluid template
				tt_content_defValues {
					CType = list
					list_type = fed_template
				}
			}
			mod.wizards.newContentElement.wizardItems.fed.elements.datasource {
				icon = ../typo3conf/ext/fed/Resources/Public/Icons/Plugin.png
				title = DataSource Display
				description = DataSource Display through Fluid Template
				tt_content_defValues {
					CType = list
					list_type = fed_datasource
				}
			}
		');
	}

	if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableSolrFeatures']) {

		t3lib_extMgm::addPageTSConfig('
			mod.wizards.newContentElement.wizardItems.fed.elements.solr {
				icon = ../typo3conf/ext/fed/Resources/Public/Icons/Plugin.png
				title = Solr AJAX Search Form and Results
				description = Inserts a Solr search form configured by TypoScript settings for the "solr" extension.
				tt_content_defValues {
					CType = fed_solr
				}
			}
		');
	}

	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processDatamapClass']['fed'] = 'EXT:fed/Classes/Backend/TCEMain.php:Tx_Fed_Backend_TCEMain';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['processCmdmapClass']['fed'] = 'EXT:fed/Classes/Backend/TCEMain.php:Tx_Fed_Backend_TCEMain';
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['moveRecordClass']['fed'] = 'EXT:fed/Classes/Backend/TCEMain.php:Tx_Fed_Backend_TCEMain';

	if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableIntegratedBackendLayouts']) {
		//$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/classes/class.tx_cms_backendlayout.php']['tx_cms_BackendLayout']['fed'] = 'EXT:fed/Classes/Backend/BackendLayout.php:Tx_Fed_Backend_BackendLayout';
	}
}

if (count($fedWizardElements) > 0) {
	t3lib_extMgm::addPageTSConfig('
		mod.wizards.newContentElement.wizardItems.fed {
			header = Fluid Content Elements
			show = ' . implode(',', $fedWizardElements) . '
			position = 0
		}');
}

if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['increaseExtbaseCacheLifetime']) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_extbase_reflection']['options']['defaultLifetime'] = 86400;
}

?>
