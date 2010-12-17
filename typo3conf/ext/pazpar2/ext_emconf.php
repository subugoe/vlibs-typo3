<?php

########################################################################
# Extension Manager/Repository config file for ext "pazpar2".
#
# Auto generated 10-12-2010 16:52
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'pazpar2',
	'description' => 'Interface to indexdata’s pazpar2 metasearch middleware',
	'category' => 'fe',
	'shy' => '',
	'version' => '0.2.0',
	'dependencies' => 'extbase,fluid',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'internal' => '',
	'state' => 'alpha',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'author' => 'Sven-S. Porst',
	'author_email' => 'porst@sub.uni-goettingen.de',
	'author_company' => 'Göttingen State and University Library, Germany http://sub.uni-goettingen.de',
	'constraints' => array(
		'depends' => array(
			'php' => '5.2.0-0.0.0',
			'typo3' => '4.4.0-4.5.99',
			'extbase' => '1.2.0-0.0.0',
			'fluid' => '1.2.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
			't3jquery' => '1.8.0-',
		),
	),
	'_md5_values_when_last_written' => 'a:10:{s:17:"ext_localconf.php";s:4:"9b0f";s:14:"ext_tables.php";s:4:"b6c9";s:12:"t3jquery.txt";s:4:"1cac";s:40:"Classes/Controller/Pazpar2Controller.php";s:4:"4b65";s:41:"Configuration/FlexForms/flexform_list.xml";s:4:"4ab9";s:40:"Resources/Private/Language/locallang.xml";s:4:"99f6";s:46:"Resources/Private/Templates/Pazpar2/Index.html";s:4:"75d6";s:30:"Resources/Public/pz2-client.js";s:4:"996e";s:24:"Resources/Public/pz2.css";s:4:"dbf8";s:23:"Resources/Public/pz2.js";s:4:"6a0c";}',
	'suggests' => array(
	),
);

?>