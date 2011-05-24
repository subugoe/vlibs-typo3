<?php

########################################################################
# Extension Manager/Repository config file for ext "efempty".
#
# Auto generated 23-05-2011 18:11
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'An empty container to play with Extbase and Fluid',
	'description' => 'This extension just contains a Controller (Start) an Action (index) and a view (index.html). Nothing more. So you can use this as a base foundation for your own experiments with Extbase and Fluid',
	'category' => 'plugin',
	'shy' => 0,
	'version' => '1.0.4',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'stable',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Patrick Lobacher',
	'author_email' => 'patrick.lobacher@typovision.de',
	'author_company' => 'typovision',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.3.0-4.5.99',
			'extbase' => '0.0.0-0.0.0',
			'fluid' => '0.0.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:10:{s:12:"ext_icon.gif";s:4:"8c8e";s:17:"ext_localconf.php";s:4:"13ea";s:14:"ext_tables.php";s:4:"09ec";s:38:"Classes/Controller/StartController.php";s:4:"7aa0";s:30:"Classes/Domain/Model/Start.php";s:4:"9051";s:40:"Resources/Private/Language/locallang.xml";s:4:"ca47";s:44:"Resources/Private/Layouts/defaultLayout.html";s:4:"98dc";s:42:"Resources/Private/Partials/formErrors.html";s:4:"669f";s:44:"Resources/Private/Templates/Start/Index.html";s:4:"4826";s:14:"doc/manual.sxw";s:4:"f02d";}',
);

?>