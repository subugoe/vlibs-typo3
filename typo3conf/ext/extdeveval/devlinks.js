/***************************************************************
*  Copyright notice
*
*  (c) 2008 Steffen Kamper <info@sk-typo3.de>
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
 * class to handle the dev links menu
 *
 * $Id$
 */
var DevLinks = Class.create({

	/**
	 * registers for resize event listener and executes on DOM ready
	 */
	initialize: function() {
		Event.observe(window, 'resize', this.positionMenu);

		Event.observe(window, 'load', function() {
			this.positionMenu();
			this.toolbarItemIcon = $$('#dev-links-actions-menu .toolbar-item img')[0].src;

			Event.observe('dev-links-actions-menu', 'click', this.toggleMenu);
		}.bindAsEventListener(this));
	},

	/**
	 * Positions a toolbar item (has to have .toolbar-item-menu).
	 * This method was taken and adjusted from TYPO3 4.5.0 typo3/js/toolbarmanager.js
	 */
	positionMenu: function() {
		var elementId = 'dev-links-actions-menu';

		var calculatedOffset = 0;
		var parentWidth = $(elementId).getWidth();
		var currentToolbarItemLayer = $$('#' + elementId + ' .toolbar-item-menu')[0];
		var ownWidth = currentToolbarItemLayer.getWidth();
		var parentSiblings = $(elementId).previousSiblings();

		parentSiblings.each(function(toolbarItem) {
			calculatedOffset += toolbarItem.getWidth() - 1;
			// -1 to compensate for the margin-right -1px of the list items,
			// which itself is necessary for overlaying the separator with the active state background

			if (toolbarItem.down().hasClassName('no-separator')) {
				calculatedOffset -= 1;
			}
		});
		calculatedOffset = calculatedOffset - ownWidth + parentWidth;

		// border correction
		if (currentToolbarItemLayer.getStyle('display') !== 'none') {
			calculatedOffset += 2;
		}

		$$('#' + elementId + ' .toolbar-item-menu')[0].setStyle({
			left: calculatedOffset + 'px'
		});
	},

	/**
	 * toggles the visibility of the menu and places it under the toolbar icon
	 */
	toggleMenu: function() {
		var toolbarItem = $$('#dev-links-actions-menu > a')[0];
		var menu        = $$('#dev-links-actions-menu .toolbar-item-menu')[0];
		toolbarItem.blur();

		if(!toolbarItem.hasClassName('toolbar-item-active')) {
			toolbarItem.addClassName('toolbar-item-active');
			Effect.Appear(menu, {duration: 0.2});
			TYPO3BackendToolbarManager.hideOthers(toolbarItem);
		} else {
			toolbarItem.removeClassName('toolbar-item-active');
			Effect.Fade(menu, {duration: 0.1});
		}
	}

	

});

var TYPO3BackendDevLinks = new DevLinks();