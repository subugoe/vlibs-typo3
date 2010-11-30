<?php


$GLOBALS['TCA']['sys_template']['columns']['include_extjs'] = array(
  'label' => 'LLL:EXT:cl_jquery/locallang_db.xml:sys_template.javascript.include_extjs',
  'config' => array(
    'type' => 'select',
    'size' => 10,
    'maxitems' => 100,
    'items' => array(
    ),
    'softref' => 'ext_fileref'
  )
);


$GLOBALS['TCA']['sys_template']['columns']['jquery_version'] = array(
  'label' => 'LLL:EXT:cl_jquery/locallang_db.xml:sys_template.javascript.jquery_version',
  'config' => array(
    'type' => 'select',
    'items' => array(
    ),
    'default' => '1.4.2'
  )
);

$GLOBALS['TCA']['sys_template']['columns']['jquery_noconflict'] = array(
  'label' => 'LLL:EXT:cl_jquery/locallang_db.xml:sys_template.javascript.jquery_noconflict',
  'config' => array(
    'type' => 'check',
    'default' => '0'
  )
);

$GLOBALS['TCA']['sys_template']['columns']['jquery_ui_version'] = array(
  'label' => 'LLL:EXT:cl_jquery/locallang_db.xml:sys_template.javascript.jquery_ui_version',
  'config' => array(
    'type' => 'select',
    'items' => array(
    ),
    'default' => '0'
  )
);

$GLOBALS['TCA']['sys_template']['columns']['jquery_ui_modules'] = array(
  'label' => 'LLL:EXT:cl_jquery/locallang_db.xml:sys_template.javascript.jquery_ui_modules',
  'config' => array(
    'type' => 'select',
    'size' => 10,
    'maxitems' => 100,
    'items' => array(
    ),
    'softref' => 'ext_fileref'
  )
);


$GLOBALS['TCA']['sys_template']['types']['1']['showitem'] .= ',
      --div--;LLL:EXT:cl_jquery/locallang_db.xml:sys_template.tabs.javascript, include_extjs, jquery_version, jquery_noconflict, jquery_ui_version, jquery_ui_modules, js_debug';


cljquerylib::loadOptions();


