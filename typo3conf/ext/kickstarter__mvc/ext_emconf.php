<?php

########################################################################
# Extension Manager/Repository config file for ext "kickstarter__mvc".
#
# Auto generated 23-11-2010 14:22
#
# Manual updates:
# Only the data in the array - everything else is removed by next
# writing. "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => 'Kickstarter for lib/div MVC framework',
	'description' => 'This is an addon to the kickstarter and generates code for the lib/div extension development framework of ECT. Please report bugs to http://bugs.typo3.org section kickstarter__mvc.',
	'category' => 'be',
	'shy' => 0,
	'version' => '0.0.9',
	'dependencies' => '',
	'conflicts' => '',
	'priority' => '',
	'loadOrder' => '',
	'module' => '',
	'state' => 'alpha',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearcacheonload' => 0,
	'lockType' => '',
	'author' => 'Christian Welzel',
	'author_email' => 'gawain@camlann.de',
	'author_company' => 'schech.net',
	'CGLcompliance' => '',
	'CGLcompliance_note' => '',
	'constraints' => array(
		'depends' => array(
			'cms' => '0.0.0',
			'kickstarter' => '0.4.0',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
	'_md5_values_when_last_written' => 'a:19:{s:9:"ChangeLog";s:4:"c30d";s:10:"README.txt";s:4:"aa0b";s:12:"ext_icon.gif";s:4:"b4e6";s:17:"ext_localconf.php";s:4:"12a5";s:14:"doc/manual.sxw";s:4:"b457";s:47:"renderer/class.tx_kickstarter_renderer_base.php";s:4:"093d";s:49:"renderer/class.tx_kickstarter_simple_renderer.php";s:4:"cb69";s:51:"renderer/class.tx_kickstarter_switched_renderer.php";s:4:"d8cb";s:45:"sections/class.tx_kickstarter_section_mvc.php";s:4:"495f";s:52:"sections/class.tx_kickstarter_section_mvc_action.php";s:4:"42e3";s:50:"sections/class.tx_kickstarter_section_mvc_base.php";s:4:"d1a7";s:56:"sections/class.tx_kickstarter_section_mvc_controller.php";s:4:"fa40";s:51:"sections/class.tx_kickstarter_section_mvc_model.php";s:4:"c55f";s:54:"sections/class.tx_kickstarter_section_mvc_template.php";s:4:"51e5";s:50:"sections/class.tx_kickstarter_section_mvc_view.php";s:4:"d336";s:29:"templates/phpViewTemplate.php";s:4:"824f";s:32:"templates/smartyViewTemplate.txt";s:4:"3c60";s:31:"templates/template_flexform.xml";s:4:"fa28";s:40:"templates/template_flexform_switched.xml";s:4:"95a7";}',
);

?>