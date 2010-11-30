<?php
/**
 * Copyright notice
 * 
 * Copyright (c) 2007 Joerg Schoppet
 * All rights reserved
 * 
 * This script is part of the TYPO3 project. The TYPO3 project is 
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license 
 * from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */
/**
 * configuration file, which runs only if the page contains that plugin
 *
 * @package		TYPO3
 * @subpackage	jsmanager
 * @author		Joerg Schoppet <joerg@schoppet.de>
 * @version		SVN: $Id: ext_tables.php 20 2008-01-16 10:03:53Z tzf4vy $
 */

if (!defined('TYPO3_MODE')) {
	die ('Access denied.');
} // if (!defined('TYPO3_MODE'))

t3lib_extMgm::addStaticFile(
	$_EXTKEY,
	'static/', 'JS-Manager'
);
?>