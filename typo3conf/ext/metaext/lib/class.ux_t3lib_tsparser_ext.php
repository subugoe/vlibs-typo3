<?php
/***************************************************************
*  Copyright notice
*
*  (c) 1999-2008 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
 * Original TSParser extension class to t3lib_TStemplate
 *
 * $Id: class.t3lib_tsparser_ext.php 4137 2008-09-16 19:24:13Z benni $
 * Contains functions for the TS module in TYPO3 backend
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 */




/**
 * Extended TSParser extension class 
 *
 * @author	Michael Rudolph - sensomedia.de <info@sensomedia.de>
 * @package TYPO3
 * @subpackage metaext
*/
class ux_t3lib_tsparser_ext extends t3lib_tsparser_ext	{

	var $subCategories = array(
		// Standard categories:
		'enable' => Array('Enable features', 'a'),
		'dims' => Array('Dimensions, widths, heights, pixels', 'b'),
		'file' => Array('Files', 'c'),
		'typo'	=> Array('Typography', 'd'),
		'color' => Array('Colors', 'e'),
		'links' => Array('Links and targets', 'f'),
		'language' => Array('Language specific constants', 'g'),
		
		// EXTENDED Standard categories
		'site' => Array('Site specific features', 'la'),
		'sitemap' => Array('Default Sitemap settings', 'lb'),
		'page' => Array('Page specific features', '1c'),
		'urlmgm' => Array('Speaking URL Management', 'ld'),
		'title' => Array('Title settings', 'le'),
		'meta' => Array('Metatag settings', 'lf'),
		'admin' => Array('Administrative settings', 'lg'),
		// theres a subcat language, but this one sorts underneath the new categories if this is wanted. 
		'lang' => Array('Site language settings', 'lh'),  
		
		// subcategories based on the default content elements
		'cheader' => Array('Content: \'Header\'', 'ma'),
		'cheader_g' => Array('Content: \'Header\', Graphical', 'ma'),
		'ctext' => Array('Content: \'Text\'', 'mb'),
		'cimage' => Array('Content: \'Image\'', 'md'),
		'cbullets' => Array('Content: \'Bullet list\'', 'me'),
		'ctable' => Array('Content: \'Table\'', 'mf'),
		'cuploads' => Array('Content: \'Filelinks\'', 'mg'),
		'cmultimedia' => Array('Content: \'Multimedia\'', 'mh'),
		'cmailform' => Array('Content: \'Form\'', 'mi'),
		'csearch' => Array('Content: \'Search\'', 'mj'),
		'clogin' => Array('Content: \'Login\'', 'mk'),
		'csplash' => Array('Content: \'Textbox\'', 'ml'),
		'cmenu' => Array('Content: \'Menu/Sitemap\'', 'mm'),
		'cshortcut' => Array('Content: \'Insert records\'', 'mn'),
		'clist' => Array('Content: \'List of records\'', 'mo'),
		'cscript' => Array('Content: \'Script\'', 'mp'),
		'chtml' => Array('Content: \'HTML\'', 'mq')
	);

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/metaext/lib/class.ux_t3lib_tsparser_ext.php']) {
    include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/metaext/lib/class.ux_t3lib_tsparser_ext.php']);
}
?>
