{namespace k=Tx_ExtbaseKickstarter_ViewHelpers}<?php
########################################################################
# Extension Manager/Repository config file for ext: "{extension.extensionKey}"
#
# Auto generated by Extbase Kickstarter <f:format.date>now</f:format.date>
#
# Manual updates:
# Only the data in the array - anything else is removed by next write.
# "version" and "dependencies" must not be touched!
########################################################################

$EM_CONF[$_EXTKEY] = array(
	'title' => '<k:quoteString>{extension.name}</k:quoteString>',
	'description' => '<k:quoteString>{extension.description}</k:quoteString>',
	'category' => 'plugin',
	'author' => '<k:listObjectsByPropertyCSV objects="{extension.persons}" property="name" />',
	'author_email' => '<k:listObjectsByPropertyCSV objects="{extension.persons}" property="email" />',
	'author_company' => '<k:listObjectsByPropertyCSV objects="{extension.persons}" property="company" />',
	'shy' => '',
	'dependencies' => 'extbase,fluid',
	'conflicts' => '',
	'priority' => '',
	'module' => '',
	'state' => '{extension.readableState}',
	'internal' => '',
	'uploadfolder' => 0,
	'createDirs' => '',
	'modify_tables' => '',
	'clearCacheOnLoad' => 0,
	'lockType' => '',
	'version' => '0.0.0',
	'constraints' => array(
		'depends' => array(
			'extbase' => '',
			'fluid' => '',
		),
		'conflicts' => array(
		),
		'suggests' => array(
		),
	),
);

?>