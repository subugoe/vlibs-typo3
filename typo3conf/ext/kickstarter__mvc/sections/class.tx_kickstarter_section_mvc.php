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

require_once(t3lib_extMgm::extPath('kickstarter__mvc').'renderer/class.tx_kickstarter_switched_renderer.php');
require_once(t3lib_extMgm::extPath('kickstarter__mvc').'renderer/class.tx_kickstarter_simple_renderer.php');

class tx_kickstarter_section_mvc extends tx_kickstarter_section_mvc_base {
    var $sectionID = 'mvc';

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
            $lines = $this->catHeaderLines($lines, $this->sectionID, $this->wizard->options[$this->sectionID], '<strong>Edit Plugin #'.$action[1].'</strong>', $action[1]);
            $piConf   = $this->wizard->wizArray[$this->sectionID][$action[1]];
            $ffPrefix = '['.$this->sectionID.']['.$action[1].']';

                // Enter title of the plugin
            $subContent='<strong>Enter a title for the plugin:</strong><br />'.
                $this->renderStringBox_lang('title',$ffPrefix,$piConf);
            $lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

                // Position
            if (is_array($this->wizard->wizArray['fields']))	{
                $optValues = array(
                    '0' => '',
                );
                foreach($this->wizard->wizArray['fields'] as $kk => $fC)	{
                    if ($fC['which_table']=='tt_content')	{
                        $optValues[$kk]=($fC['title']?$fC['title']:'Item '.$kk).' ('.count($fC['fields']).' fields)';
                    }
                }
                if (count($optValues)>1)	{
                    $subContent='<strong>Apply a set of extended fields</strong><br />
                        If you have configured a set of extra fields (Extend existing Tables) for the tt_content table, you can have them assigned to this plugin.
                        <br />'.
                        $this->renderSelectBox($ffPrefix.'[apply_extended]',$piConf['apply_extended'],$optValues);
                    $lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';
                }
            }
/*
                // Insert Plugin
            if (is_array($this->wizard->wizArray['tables']))	{
                $optValues = array(
                    '0' => '',
                );
                foreach($this->wizard->wizArray['tables'] as $kk => $fC)	{
                    $optValues[$kk]=($fC['tablename']||$fC['title']?$fC['title'].' ('.$this->returnName($this->wizard->extKey,'tables').($fC['tablename']?'_'.$fC['tablename']:'').')':'Item '.$kk).' ('.count($fC['fields']).' fields)';
                }
                $subContent='<strong>Example Code Generation</strong><br />'.
                        'If you have configured custom tables you can select one of the tables to list by default as an example:<br />'.
                        $this->renderSelectBox($ffPrefix.'[list_default]',$piConf['list_default'],$optValues);
                $lines[] = '<tr><td><hr /></td></tr>';
                $lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';
            }
*/

            $subContent='<strong>New Content Element Wizard</strong><br />'.
                $this->renderCheckBox($ffPrefix.'[plus_wiz]',$piConf['plus_wiz']).
                'Add icon to \'New Content Element\' wizard<br />'.
                'Write a description for the entry (if any):<br />'.
                $this->renderStringBox_lang('plus_wiz_description',$ffPrefix,$piConf)
                ;
            $lines[] = '<tr><td><hr /></td></tr>';
            $lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

            $subContent='<strong>Code Layout Selection</strong><br />'.
                $this->renderSelectBox($ffPrefix.'[code_sel]',$piConf['code_sel'],
                $this->renderer_select);
            $lines[] = '<tr><td><hr /></td></tr>';
            $lines[]='<tr'.$this->bgCol(3).'><td>'.$this->fw($subContent).'</td></tr>';

                /* create default controller */
            if(!isset($piConf[done])) {
                $ctr_cnt = count($this->wizard->wizArray['mvccontroller'])+1;
                $this->regNewEntry('mvccontroller', $ctr_cnt);;
                $this->wizard->wizArray['mvccontroller'][$ctr_cnt] = array('plugin'=>$action[1],'title'=>'default'.$action[1]);
                $piConf[done] = 1;
            }
            $lines[] = '<input type="hidden" name="kickstarter[wizArray_upd]'.$ffPrefix.'[done]" value="'.$piConf[done].'" />';
        }

        $content = '<table border=0 cellpadding=2 cellspacing=2>'.implode("\n",$lines).'</table>';
        return $content;
    }

    /**
     * Renders the extension PHP code; this was
     *
     * @param	string		$k: module name key
     * @param	array		$config: module configuration
     * @param	string		$extKey: extension key
     * @return	void
     */
    function render_extPart($k,$config,$extKey) {
        $WOP='[mvc]['.$k.']';
        $cN = $this->returnName($extKey,'class','mvc'.$k);
        $ll = array();

        $ll = $this->addStdLocalLangConf($ll,$k);

        $this->wizard->ext_tables[]=$this->sPS('
            '.$this->WOPcomment('WOP:'.$WOP.'[addType]')."
            t3lib_div::loadTCA('tt_content');
            \$TCA['tt_content']['types']['list']['subtypes_excludelist'][\$_EXTKEY.'_mvc".$k."']='layout,select_key,pages,recursive';
            \$TCA['tt_content']['types']['list']['subtypes_addlist'][\$_EXTKEY.'_mvc".$k."']='pi_flexform".($config['apply_extended']?$this->wizard->_apply_extended_types[$config['apply_extended']]:"")."';
        ");

        $this->wizard->ext_tables[] = $this->sPS('t3lib_extMgm::addStaticFile(\''.$extKey.'\', \'./configurations/mvc'.$k.'\', \''.$config[title].'\');');

        $this->wizard->ext_tables[]=$this->sPS("t3lib_extMgm::addPiFlexFormValue(\$_EXTKEY.'_mvc".$k."', 'FILE:EXT:".$extKey."/configurations/mvc".$k."/flexform.xml');");

        $this->wizard->ext_tables[]=$this->sPS('
            '.$this->WOPcomment('WOP:'.$WOP.'[addType]')."
            t3lib_extMgm::addPlugin(array('".addslashes($this->getSplitLabels_reference($config,'title','tt_content.'.'list_type_pi'.$k))."', \$_EXTKEY.'_mvc".$k."'),'list_type');
        ");

        $renderer = t3lib_div::makeInstance('tx_kickstarter_'.($this->renderer[$config[code_sel]]).'_renderer');
        $renderer->setParent($this);

        $renderer->generateSetup($extKey, $k);
        $renderer->generateControllers($extKey, $k);
        $renderer->generateConfigClass($extKey, $k);
        $renderer->generateModels($extKey, $k);
        $renderer->generateViews($extKey, $k);
        $renderer->generateTemplates($extKey, $k);
        $renderer->generateFlexform($extKey, $k);

        $this->wizard->ext_localconf[] = 'require_once(t3lib_extMgm::extPath(\'div\') . \'class.tx_div.php\');';
        $this->wizard->ext_localconf[] = 'if(TYPO3_MODE == \'FE\') tx_div::autoLoadAll($_EXTKEY);';

            // Add wizard?

        if ($config['plus_wiz'])	{
            $this->addLocalConf($this->wizard->ext_locallang,$config,'title','mvc',$k);
            $this->addLocalConf($this->wizard->ext_locallang,$config,'plus_wiz_description','mvc',$k);

            $indexContent = $this->sPS(
                'class '.$cN.'_wizicon {

                    /**
                     * Processing the wizard items array
                     *
                     * @param	array		$wizardItems: The wizard items
                     * @return	Modified array with wizard items
                     */
                    function proc($wizardItems)	{
                        global $LANG;

                        $LL = $this->includeLocalLang();

                        $wizardItems[\'plugins_'.$cN.'\'] = array(
                            \'icon\'=>t3lib_extMgm::extRelPath(\''.$extKey.'\').\'ce_wiz.gif\',
                            \'title\'=>$LANG->getLLL(\'mvc'.$k.'_title\',$LL),
                            \'description\'=>$LANG->getLLL(\'mvc'.$k.'_plus_wiz_description\',$LL),
                            \'params\'=>\'&defVals[tt_content][CType]=list&defVals[tt_content][list_type]='.$extKey.'_mvc'.$k.'\'
                        );

                        return $wizardItems;
                    }

                    /**
                     * Reads the [extDir]/locallang.xml and returns the $LOCAL_LANG array found in that file.
                     *
                     * @return	The array with language labels
                     */
                    function includeLocalLang()	{
                        $llFile = t3lib_extMgm::extPath(\''.$extKey.'\').\'locallang.xml\';
                        $LOCAL_LANG = t3lib_div::readLLXMLfile($llFile, $GLOBALS[\'LANG\']->lang);

                        return $LOCAL_LANG;
                    }
                }
            ',
            0);

            $this->addFileToFileArray(
                'configurations/mvc'.$k.'/class.'.$cN.'_wizicon.php',
                $this->PHPclassFile(
                    $extKey,
                    'configurations/mvc'.$k.'/class.'.$cN.'_wizicon.php',
                    $indexContent,
                    'Class that adds the wizard icon.'
                )
            );

                // Add wizard icon
            $this->addFileToFileArray('ce_wiz.gif',t3lib_div::getUrl(t3lib_extMgm::extPath('kickstarter').'res/wiz.gif'));

            $this->wizard->ext_tables[]=$this->sPS('
                '.$this->WOPcomment('WOP:'.$WOP.'[plus_wiz]:').'
                if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["'.$cN.'_wizicon"] = t3lib_extMgm::extPath($_EXTKEY).\'configurations/mvc'.$k.'/class.'.$cN.'_wizicon.php\';
            ');

            $this->addLocalLangFile($ll,'mvc'.$k.'/locallang.xml','Language labels for plugin "'.$cN.'"');
        }
    }
}


// Include ux_class extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter__mvc/sections/class.tx_kickstarter_section_mvc.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter__mvc/sections/class.tx_kickstarter_section_mvc.php']);
}

?>
