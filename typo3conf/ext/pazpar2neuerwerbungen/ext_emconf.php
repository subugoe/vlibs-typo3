<?php

########################################################################
# Extension Manager/Repository config file for ext "pazpar2_neuerwerbungen".
#
# Auto generated 10-12-2010 16:53
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'pazpar2 Neuerwerbungen',
	'description' => 'Interface to indexdata’s pazpar2 metasearch middleware to display newly acquired books',
	'category' => 'fe',
	'shy' => '',
	'version' => '0.1.0',
	'dependencies' => 'extbase,fluid,pazpar2',
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
			'pazpar2' => '0.2.0-0.0.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:6:{s:17:"ext_localconf.php";s:4:"ab36";s:14:"ext_tables.php";s:4:"aa32";s:54:"Classes/Controller/Pazpar2NeuerwerbungenController.php";s:4:"3b2d";s:41:"Configuration/FlexForms/flexform_list.xml";s:4:"4ab9";s:40:"Resources/Private/Language/locallang.xml";s:4:"99f6";s:60:"Resources/Private/Templates/Pazpar2Neuerwerbungen/Index.html";s:4:"75d6";}',
	'suggests' => array(
	),
);

?>
