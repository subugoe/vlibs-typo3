<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c)  2007 Christian Welzel (gawain@camlann.de)  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/
/**
 * @author  Christian Welzel <gawain@camlann.de>
 */

require_once (t3lib_extMgm::extPath( 'kickstarter__mvc' ) . 'renderer/class.tx_kickstarter_renderer_base.php');

class tx_kickstarter_switched_renderer extends tx_kickstarter_renderer_base {

    /**
     * Generates the setup.txt
     *
     * @param       string           $extKey: current extension key
     * @param       integer          $k: current number of plugin
     */
    function generateSetup($extKey, $k) {
        $lines = array ();
        $incls = array ();
        $acts = array ();

        $cN = $this->pObj->returnName( $extKey, 'class', '' );

        $lines[] = '
# Common configuration
plugin.' . $cN . '_mvc' . $k . '.configurations {
  pathToTemplateDirectory = EXT:'.$extKey.'/templates/
  pathToLanguageFile = EXT:'.$extKey.'/locallang.xml
}

includeLibs.tx_div = EXT:div/class.tx_div.php
includeLibs.tx_lib_switch = EXT:lib/class.tx_lib_switch.php';

        $controllers = $this->pObj->wizard->wizArray['mvccontroller'];
        if (! is_array( $controllers ))
            $controllers = array ();
        foreach( $controllers as $kk => $contr ) {
            if ($contr[plugin] != $k)
                continue;
            $contr_title = $this->generateName(
                    $contr['title'],
                    0,
                    0,
                    $contr[freename] );
            if (! trim( $contr_title ))
                continue;

            $c[] = '    ' . $contr_title . ' = ' . ($contr[plus_user_obj] ? 'USER_INT' : 'USER') . '
    ' . $contr_title . ' {
       userFunc = ' . $cN . '_controller_' . $contr_title . '->main
       setupPath = plugin.' . $cN . '_mvc' . $k . '.configurations.
       configurations < plugin.' . $cN . '_mvc' . $k . '.configurations
       configurations.defaultAction = ' . $this->getDefaultAction( $kk ) . '
    }';
            $incls[] = 'includeLibs.' . $cN . '_controller_' . $contr_title . ' = ' . 'EXT:' . $extKey . '/controllers/class.' . $cN . '_controller_' . $contr_title . '.php';
        }
        $lines = array_merge( $lines, $incls );

        $lines[] = '
# The controller switch
plugin.' . $cN . '.controllerSwitch = USER
plugin.' . $cN . '.controllerSwitch {
    userFunc = tx_lib_switch->main
';

        $lines = array_merge( $lines, $c );
        $lines[] = '}
tt_content.list.20.' . $extKey . '_mvc' . $k . ' =< plugin.' . $cN . '.controllerSwitch
';

        $ajaxed = $this->checkForAjax( $k );
        if (count( $ajaxed )) {
            $lines[] = $this->getXajaxPageSwitch(
                    '110124',
                    $ajaxed,
                    $cN );
        }

        $this->pObj->addFileToFileArray(
                'configurations/mvc' . $k . '/setup.txt',
                implode( "\n",
                        $lines ) );
    }

    /**
     * Generates the class.tx_*_configuration.php
     *
     * @param       string           $extKey: current extension key
     * @param       integer          $k: current number of plugin
     */
    function generateConfigClass($extKey, $k) {

        $cN = $this->pObj->returnName( $extKey, 'class', '' );

        $indexContent = '
tx_div::load(\'tx_lib_configurations\');

class ' . $cN . '_configurations extends tx_lib_configurations {
        var $setupPath = \'plugin.' . $cN . '_mvc' . $k . '.configurations.\';
}';

        $this->pObj->addFileToFileArray(
                'configurations/class.' . $cN . '_configuration.php',
                $this->pObj->PHPclassFile(
                        $extKey,
                        'configurations/class.' . $cN . '_configuration.php',
                        $indexContent,
                        'Class that handles TypoScript configuration.' ) );
    }

    /**
     * Generates the flexform for this plugin
     *
     * @param       string           $extKey: current extension key
     * @param       integer          $k: current number of plugin
     */
    function generateFlexform($extKey, $k) {
        $flexform = t3lib_div::getUrl(
                t3lib_extMgm::extPath(
                        'kickstarter__mvc' ) . 'templates/template_flexform_switched.xml' );
        $flexform = str_replace( '###LABEL###',
                $this->pObj->getSplitLabels_reference(
                        array ('title' => 'Select subcontroller' ),
                        'title',
                        'flexform.controllerSelection' ),
                $flexform );
        $tmp = '';
        $i = 1;

        $controllers = $this->pObj->wizard->wizArray['mvccontroller'];
        if (! is_array( $controllers ))
            $controllers = array ();
        foreach( $controllers as $kk => $contr ) {
            if ($contr[plugin] != $k)
                continue;
            $contr_title = $this->generateName(
                    $contr['title'],
                    0,
                    0,
                    $contr[freename] );
            if (! trim( $contr_title ))
                continue;

            $label = $this->pObj->getSplitLabels_reference(
                    array ('title' => $contr[title] ),
                    'title',
                    'flexform.controllerSelection.' . $contr_title );
            $tmp .= '
            <numIndex index="' . ($i ++ * 10) . '" type="array">
                <numIndex index="0">' . $label . '</numIndex>
                <numIndex index="1">' . $contr_title . '</numIndex>
            </numIndex>';
        }
        $this->pObj->addFileToFileArray(
                'configurations/mvc' . $k . '/flexform.xml',
                str_replace(
                        '###ITEMS###',
                        $tmp,
                        $flexform ) );
    }

}

// Include ux_class extension?
if (defined( 'TYPO3_MODE' ) && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter__mvc/renderer/class.tx_kickstarter_switched_renderer.php']) {
    include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter__mvc/renderer/class.tx_kickstarter_switched_renderer.php']);
}

?>
