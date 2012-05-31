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
	');
	if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['enableFallbackFluidPageTemplate']) {
		t3lib_extMgm::addTypoScript($_EXTKEY,'setup',
			'[GLOBAL]
			plugin.tx_fed.page.fed {
				enable = 1
				templateRootPath = EXT:fed/Resources/Private/Templates/
				layoutRootPath = EXT:fed/Resources/Private/Layouts/
				partialRootPath = EXT:fed/Resources/Private/Partials/
			}
			plugin.tx_fed.settings.templates.fallbackFluidPageTemplate = EXT:fed/Resources/Private/Templates/Page/Render.html
		');
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
		Tx_Fed_Core::loadRegisteredFluidContentElementTypoScript();
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



if ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF']['fed']['setup']['increaseExtbaseCacheLifetime']) {
	$GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['cache_extbase_reflection']['options']['defaultLifetime'] = 86400;
}

$TYPO3_CONF_VARS['SC_OPTIONS']['t3lib/class.t3lib_tcemain.php']['clearCachePostProc'][] = 'EXT:fed/Classes/Backend/TCEMain.php:&Tx_Fed_Backend_TCEMain->clearCacheCommand';


?>