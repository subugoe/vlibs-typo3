<?php

$TYPO3_CONF_VARS['EXTCONF']['kickstarter']['sections']['mvc'] = array(
    'classname'   => 'tx_kickstarter_section_mvc',
    'filepath'    => 'EXT:kickstarter__mvc/sections/class.tx_kickstarter_section_mvc.php',
    'title'       => 'MVC: Frontend Plugin',
    'description' => 'Create frontend plugins. Plugins are web applications running on the website itself (not in the backend of TYPO3). eFaq, bananas, cherries, apples are examples of mvc plugins.',
    'singleItem'  => '',
);

$TYPO3_CONF_VARS['EXTCONF']['kickstarter']['sections']['mvccontroller'] = array(
    'classname'   => 'tx_kickstarter_section_mvc_controller',
    'filepath'    => 'EXT:kickstarter__mvc/sections/class.tx_kickstarter_section_mvc_controller.php',
    'title'       => 'MVC: Controller',
    'description' => '',
    'singleItem'  => '',
);

$TYPO3_CONF_VARS['EXTCONF']['kickstarter']['sections']['mvcmodel'] = array(
    'classname'   => 'tx_kickstarter_section_mvc_model',
    'filepath'    => 'EXT:kickstarter__mvc/sections/class.tx_kickstarter_section_mvc_model.php',
    'title'       => 'MVC: Models',
    'description' => '',
    'singleItem'  => '',
);

$TYPO3_CONF_VARS['EXTCONF']['kickstarter']['sections']['mvctemplate'] = array(
    'classname'   => 'tx_kickstarter_section_mvc_template',
    'filepath'    => 'EXT:kickstarter__mvc/sections/class.tx_kickstarter_section_mvc_template.php',
    'title'       => 'MVC: Templates',
    'description' => '',
    'singleItem'  => '',
);

$TYPO3_CONF_VARS['EXTCONF']['kickstarter']['sections']['mvcview'] = array(
    'classname'   => 'tx_kickstarter_section_mvc_view',
    'filepath'    => 'EXT:kickstarter__mvc/sections/class.tx_kickstarter_section_mvc_view.php',
    'title'       => 'MVC: Views',
    'description' => '',
    'singleItem'  => '',
);

$TYPO3_CONF_VARS['EXTCONF']['kickstarter']['sections']['mvcaction'] = array(
    'classname'   => 'tx_kickstarter_section_mvc_action',
    'filepath'    => 'EXT:kickstarter__mvc/sections/class.tx_kickstarter_section_mvc_action.php',
    'title'       => 'MVC: Actions',
    'description' => '',
    'singleItem'  => '',
);

?>