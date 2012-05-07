<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Claus Due <claus@wildside.dk>, Wildside A/S
*
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Accordion integration for jQuery UI - remember to load jQueryUI yourself
 * For example through <fed:script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8/jquery-ui.min.js" />
 *
 * Usage example:
 *
 * <fed:jQuery.accordion animated="bounceslide" collapsed="TRUE" collapsible="TRUE">
 *     <fed:jQuery.accordion title="Tab no. 1">
 *         <p>Tab 1 content. If no other tabs are declared active and collapsed=FALSE,
 *		   then this tab is initially active.</p>
 *     </fed:jQuery.accordion>
 *     <fed:jQuery.accordion title="Tab no. 2">
 *         <p>Tab 2 content</p>
 *     </fed:jQuery.accordion>
 *     <fed:jQuery.accordion active="TRUE" title="Tab no. 3">
 *         <p>This tab is active due to active=TRUE and this overrides collapsed=TRUE</p>
 *     </fed:jQuery.accordion>
 * </fed:jQuery.accordion>
 *
 * Title is required for each tab but is not a required property since it is
 * not needed for the parent element - you must add the title manually for tabs.
 *
 * Note that the same ViewHelpers acts as accordion group and tab renderer. The
 * top-level tag is considered group and the following tabs are considered
 * inidividual tabs. At this time nested accordions are not supported.
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers\JQuery
 * @uses jQuery
 */

class Tx_Fed_ViewHelpers_JQuery_AccordionViewHelper extends Tx_Fed_Core_ViewHelper_AbstractJQueryViewHelper {

	protected $tagName = 'div';

	protected $uniqId;

	/**
	 * Initialization
	 *
	 * @return void
	 */
	public function initializeArguments() {
		$this->registerUniversalTagAttributes();
		$this->registerArgument('tagName', 'string', 'Tag name to use, default "div"');
		$this->registerArgument('animated', 'string', 'String name of optional jQuery animation to use.', FALSE, 'slide');
		$this->registerArgument('active', 'boolean', 'Set this to TRUE to indicate which tab should be active - use only on a single tab');
		$this->registerArgument('disabled', 'boolean', 'Set this to true to deactivate entire tab sets or individual tabs');
		$this->registerArgument('autoHeight', 'boolean', 'Automatically adjust height of tabs');
		$this->registerArgument('fillSpace', 'boolean', 'Fill space to match max tab height');
		$this->registerArgument('clearStyle', 'boolean', 'Clear styles of touched elements');
		$this->registerArgument('collapsible', 'boolean', 'Tabs are collapsible');
		$this->registerArgument('collapsed', 'boolean', 'Tabs are collapsed by default (if no active tab is set)');
		parent::initializeArguments();
	}

	/**
	 * Render method
	 */
	public function render() {
		$this->uniqId = uniqid('fedjqueryaccordion');
		if ($this->templateVariableContainer->exists('tabs') === TRUE) {
			// render one tab
			$index = $this->getCurrentIndex();
			$this->tag->addAttribute('class', 'fed-accordion');
			if ($this->arguments['active'] === TRUE) {
				$this->setSelectedIndex($index);
			}
			if ($this->arguments['disabled'] === TRUE) {
				$this->addDisabledIndex($index);
			}
			$this->addTab($this->arguments['title'], $this->renderChildren());
			$this->setCurrentIndex($index + 1);
			return;
		}

		// render tab group
		$this->templateVariableContainer->add('tabs', array());
		$this->templateVariableContainer->add('selectedIndex', 0);
		$this->templateVariableContainer->add('disabledIndices', array());
		$this->templateVariableContainer->add('currentIndex', 0);
		$content = $this->renderChildren();

		// uniq DOM id for this accordion
		$tabs = $this->renderTabs();
		$html = ($tabs . LF . $content . LF);
		$this->addScript();
		$this->tag->setContent($html);
		$this->tag->addAttribute('class', 'fed-accordion-group');
		$this->tag->addAttribute('id', $this->uniqId);
		$this->templateVariableContainer->remove('tabs');
		$this->templateVariableContainer->remove('selectedIndex');
		$this->templateVariableContainer->remove('disabledIndices');
		$this->templateVariableContainer->remove('currentIndex');
		return $this->tag->render();
	}

	/**
	 * Render the tab group HTML
	 *
	 * @return string
	 */
	protected function renderTabs() {
		$html = "";
		foreach ($this->templateVariableContainer->get('tabs') as $tab) {
			$html .= '<h3><a href="#">' . $tab['title'] . '</a></h3>' . LF;
			$html .= '<div>' . $tab['content'] . '</div>' . LF;
		}
		return $html;
	}

	/**
	 * Add one tab to the internal storage
	 *
	 * @param string $title
	 * @param string $content
	 */
	protected function addTab($title, $content) {
		$tab = array(
			'title' => $title,
			'content' => $content
		);
		$tabs = $this->templateVariableContainer->get('tabs');
		array_push($tabs, $tab);
		$this->templateVariableContainer->remove('tabs');
		$this->templateVariableContainer->add('tabs', $tabs);
	}

	/**
	 * Set the currently selected index
	 *
	 * @param integer $index
	 */
	protected function setSelectedIndex($index) {
		$this->templateVariableContainer->remove('selectedIndex');
		$this->templateVariableContainer->add('selectedIndex', $index);
	}

	/**
	 * Add an index to list of disabled indices
	 *
	 * @param integer $index
	 */
	protected function addDisabledIndex($index) {
		$disabled = $this->templateVariableContainer->get('disabledIndices');
		array_push($disabled, $index);
		$this->templateVariableContainer->remove('disabledIndices');
		$this->templateVariableContainer->add('disabledIndices', $disabled);
	}

	/**
	 * Get the currently set index
	 *
	 * @return integer
	 */
	protected function getCurrentIndex() {
		return $this->templateVariableContainer->get('currentIndex');
	}

	/**
	 * Set the currently set index
	 *
	 * @param integer $index
	 */
	protected function setCurrentIndex($index) {
		$this->templateVariableContainer->remove('currentIndex');
		$this->templateVariableContainer->add('currentIndex', $index);
	}

	/**
	 * Attach necessary scripting
	 */
	protected function addScript() {
		$selectedIndex = $this->templateVariableContainer->get('selectedIndex');
		if ($selectedIndex === 0 && $this->arguments['collapsed'] === TRUE && $this->arguments['collapsible'] === TRUE) {
			$this->setSelectedIndex(FALSE);
		}
		if ($this->arguments['animated'] === 'FALSE') {
			$animation = '"animated" : false,';
		} else {
			$animation = '"animated" : "'. $this->arguments['animated'] . '",';
		}
		$cookie = $this->getBooleanForJavascript('cookie');
		$collapsible = $this->getBooleanForJavascript('collapsible');
		$disabled = $this->getBooleanForJavascript('disabled');
		$autoHeight = $this->getBooleanForJavascript('autoHeight');
		$clearStyle = $this->getBooleanForJavascript('clearStyle');
		$fillSpace = $this->getBooleanForJavascript('fillSpace');
		$csvOfDisabledTabIndices = implode(',' ,$this->templateVariableContainer->get('disabledIndices'));
		$init = <<< INITSCRIPT
jQuery(document).ready(function() {
	jQuery('#{$this->uniqId}').accordion({
		disabled : {$disabled},
		animated : '{$this->arguments['animated']}',
		autoHeight : {$autoHeight},
		clearStyle : {$clearStyle},
		collapsible : {$collapsible},
		fillSpace : {$fillSpace},
		active : ''
	});
});
INITSCRIPT;
		$this->documentHead->includeHeader($init, 'js');
	}


}

?>