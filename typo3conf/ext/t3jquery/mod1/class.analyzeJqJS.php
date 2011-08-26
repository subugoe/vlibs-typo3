<?php
/***************************************************************
*  Copyright notice
*
*  Based on t3mootools from Peter Klein <peter@umloud.dk>
*  (c) 2007-2009 Juergen Furrer (juergen.furrer@gmail.com)
*  All rights reserved
*
*  This script is free software; you can redistribute it and/or modify
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
 * Module 'jQuery Analyze' for the 't3jquery' extension.
 *
 * @author     Juergen Furrer (juergen.furrer@gmail.com)
 * @package    TYPO3
 * @subpackage tx_t3jquery
 */
class analyzeJqJS
{
	var $version = '0.2';
	var $dependencies = array();
	var $jQuery = array(
		'Core' => array(
			'jQuery' => array(
				'Deps' => array(),
				'Source' => array('jQuery(', '$('),
			),
			'Core' => array(
				'Deps' => array('jQuery' => 'Core'),
				'Source' => array(),
			),
		),
		'Interactions' => array(
			'Draggable' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('.draggable('),
			),
			'Droppable' => array(
				'Deps' => array('Core' => 'Core', 'Draggable' => 'Interactions'),
				'Source' => array('.droppable('),
			),
			'Resizable' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('.resizable('),
			),
			'Selectable' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('.selectable('),
			),
			'Sortable' => array(
				'Deps' => array('Core' => 'Core', 'Draggable' => 'Interactions'),
				'Source' => array('.sortable('),
			),
		),
		'Widgets' => array(
			'Accordion' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('.accordion('),
			),
			'Dialog' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('.dialog('),
			),
			'Slider' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('.slider('),
			),
			'Tabs' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('.tabs('),
			),
			'Datepicker' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('.datepicker('),
			),
			'Progressbar' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('.progressbar('),
			),
		),
		'Effects' => array(
			'EffectsCore' => array(
				'Deps' => array('Core' => 'Core'),
				'Source' => array('.effect('),
			),
			'EffectsBlind' => array(
				'Deps' => array('Core' => 'Core', 'EffectsCore' => 'Effects'),
				'Source' => array('.effect("blind"', '.show("blind"', '.hide("blind"'),
			),
			'EffectsBounce' => array(
				'Deps' => array('Core' => 'Core', 'EffectsCore' => 'Effects'),
				'Source' => array('.effect("bounce"', '.show("bounce"', '.hide("bounce"'),
			),
			'EffectsClip' => array(
				'Deps' => array('Core' => 'Core', 'EffectsCore' => 'Effects'),
				'Source' => array('.effect("clip"', '.show("clip"', '.hide("clip"'),
			),
			'EffectsDrop' => array(
				'Deps' => array('Core' => 'Core', 'EffectsCore' => 'Effects'),
				'Source' => array('.effect("drop"', '.show("drop"', '.hide("drop"'),
			),
			'EffectsExplode' => array(
				'Deps' => array('Core' => 'Core', 'EffectsCore' => 'Effects'),
				'Source' => array('.effect("explode"', '.show("explode"', '.hide("explode"'),
			),
			'EffectsFold' => array(
				'Deps' => array('Core' => 'Core', 'EffectsCore' => 'Effects'),
				'Source' => array('.effect("fold"', '.show("fold"', '.hide("fold"'),
			),
			'EffectsHighlight' => array(
				'Deps' => array('Core' => 'Core', 'EffectsCore' => 'Effects'),
				'Source' => array('.effect("highlight"', '.show("highlight"', '.hide("highlight"'),
			),
			'EffectsPulsate' => array(
				'Deps' => array('Core' => 'Core', 'EffectsCore' => 'Effects'),
				'Source' => array('.effect("pulsate"', '.show("pulsate"', '.hide("pulsate"'),
			),
			'EffectsScale' => array(
				'Deps' => array('Core' => 'Core', 'EffectsCore' => 'Effects'),
				'Source' => array('.effect("scale"', '.show("scale"', '.hide("scale"'),
			),
			'EffectsShake' => array(
				'Deps' => array('Core' => 'Core', 'EffectsCore' => 'Effects'),
				'Source' => array('.effect("shake"', '.show("shake"', '.hide("shake"'),
			),
			'EffectsSlide' => array(
				'Deps' => array('Core' => 'Core', 'EffectsCore' => 'Effects'),
				'Source' => array('.effect("slide"', '.show("slide"', '.hide("slide"'),
			),
			'EffectsTransfer' => array(
				'Deps' => array('Core' => 'Core', 'EffectsCore' => 'Effects'),
				'Source' => array('.effect("transfer"', '.show("transfer"', '.hide("transfer"'),
			),
			'Easing' => array(
				'Deps' => array('Core' => 'Core', 'EffectsCore' => 'Effects'),
				'Source' => array('easing:'),
			),
		),
	);

	/**
	 * Analyze a given JS script
	 * @param $file
	 * @param $string
	 * @return void
	 */
	function analyzeJqJS($file='', $string=FALSE)
	{
		if ($string || $string = t3lib_div::getURL($file)) {
			// we just look for double quote
			$string = str_replace("'", '"', $string);
			$result = array();
			foreach ($this->jQuery as $dir => $files) {
				foreach ($files as $file => $info) {
					if ($this->contains($string, $info['Source']) === TRUE) {
						$result = array_merge($result, $info['Deps']);
						$result = array_merge($result, array($file => $dir));
					}
				}
			}
			$this->dependencies = $result;
		}
	}

	/**
	 * 
	 * 
	 */
	function contains($fileData, $array=array())
	{
		if (!is_array($array)) {
			return FALSE;
		}
		foreach($array as $item) {
			if (strpos($fileData, $item) !== FALSE) {
				return TRUE;
			}
		}
		return FALSE;
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3jquery/mod1/class.analyzeJqJS.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/t3jquery/mod1/class.analyzeJqJS.php']);
}
?>