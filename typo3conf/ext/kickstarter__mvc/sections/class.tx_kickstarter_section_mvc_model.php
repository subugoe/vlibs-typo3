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

require_once(t3lib_extMgm::extPath('kickstarter__mvc').'sections/class.tx_kickstarter_section_mvc_base.php');

class tx_kickstarter_section_mvc_model extends tx_kickstarter_section_mvc_base {
    var $sectionID = 'mvcmodel';

    /**
     * Renders the form in the kickstarter; this was add_cat_pi()
     *
     * @return	HTML
     */
    function render_wizard() {
        $lines=array();

        $action = explode(':',$this->wizard->modData['wizAction']);
        if ($action[0]=='edit')	{
            $this->regNewEntry($this->sectionID, $action[1]);
            $lines = $this->catHeaderLines($lines, $this->sectionID, $this->wizard->options[$this->sectionID], '<strong>Edit Model #'.$action[1].'</strong>', $action[1]);
            $piConf   = $this->wizard->wizArray[$this->sectionID][$action[1]];
            $ffPrefix = '['.$this->sectionID.']['.$action[1].']';

                // Enter title of the plugin
            $subContent='<strong>Enter a title for the model:</strong><br />'.
                $this->renderStringBox($ffPrefix.'[title]',$piConf['title']);
            $lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

            $subContent='<strong>Enter a short description for the model:</strong><br />'.
                $this->renderTextareaBox($ffPrefix.'[description]',$piConf['description']);
            $lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

            $lines[] = '<tr><td><strong>Generate model for table:</strong></td></tr>';

            $optValues = array(
                'tt_content' => 'tt_content (Content)',
                'fe_users' => 'fe_users (Frontend Users)',
                'fe_groups' => 'fe_groups (Frontend Groups)',
                'be_users' => 'be_users (Backend Users)',
                'be_groups' => 'be_groups (Backend Groups)',
                'pages' => 'pages (Pages)',
            );
            if(is_array($this->wizard->wizArray['tables'])) {
                $tables = $this->wizard->wizArray['tables'];
                foreach($tables as $kk => $fC)	{
                    $optValues[$kk] = (
                        $fC['tablename'] ||
                        $fC['title'] ?
                            $fC['title'].' ('.$this->returnName($this->wizard->extKey,'tables').
                            ($fC['tablename'] ? '_'.$fC['tablename'] : '' )
                            .')' :
                            'Item '.$kk).' ('.count($fC['fields']).' fields)';
                }
            }
            foreach($GLOBALS['TCA'] as $tablename => $tableTCA) {
                if(!$optValues[$tablename]) {
                    $optValues[$tablename] = $tablename.' ('.$GLOBALS['LANG']->sL($tableTCA['ctrl']['title']).')';
                }
            }
            asort($optValues);

            $subContent=$this->renderSelectBox($ffPrefix.'[table]',$piConf[table],$optValues);
            $lines[] = '<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

            $lines[] = '<tr><td><strong>Free name for table to operate on:</strong></td></tr>';
            $lines[] = '<tr><td>'.$this->renderStringBox($ffPrefix.'[freename]',$piConf[freename]).'</td></tr>';
        }

        $content = '<table border=0 cellpadding=2 cellspacing=2>'.implode("\n",$lines).'</table>';
        return $content;
    }

}


// Include ux_class extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter__mvc/sections/class.tx_kickstarter_section_mvc_model.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter__mvc/sections/class.tx_kickstarter_section_mvc_model.php']);
}

?>
