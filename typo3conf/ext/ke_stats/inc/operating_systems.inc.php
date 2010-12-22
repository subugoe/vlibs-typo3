<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2007 Christian Bülter <buelter@kennziffer.com>
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
 * @author	Christian Bülter <buelter@kennziffer.com>
 * @package	TYPO3
 * @subpackage	tx_kestats
 */

// List of operating systems (HTTP_USER_AGENTS)
// First match counts, further matches are ignored, so keep the most specific
// strings at the beginning

$GLOBALS['operating_systems'] = array(/*{{{*/
	'Windows NT 6.1' => 'Windows 7',
	'Windows NT 6.0' => 'Windows Vista',
	'Windows NT 5.2' => 'Windows Server 2003; Windows XP x64 Edition',
	'Windows NT 5.1' => 'Windows XP',
	'Windows NT 5.0' => 'Windows 2000',
	'Win NT 5.0' => 'Windows 2000',
	'Windows NT 4.0' => 'Windows NT 4.0',
	'WinNT4.0' => 'Windows NT 4.0',
	'Windows NT' => 'Windows NT',
	'Win 9x 4.90' => 'Windows Me',
	'Windows 98' => 'Windows 98',
	'Win98' => 'Windows 98',
	'Windows 95' => 'Windows 95',
	'Win95' => 'Windows 95',
	'Windows CE' => 'Windows CE',
	'Mac OS X' => 'Mac OS X',
	'Mac_PowerPC' => 'Mac OS',
	'Linux' => 'Linux',
	'FreeBSD' => 'FreeBSD',
	'SunOS' => 'SunOS',
	'Bluecoat DRTR' => 'Bluecoat DRTR',
	'PLAYSTATION 3' => 'PLAYSTATION 3',
	'PlayStation Portable' => 'PlayStation Portable',
	'BlackBerry' => 'BlackBerry',
);/*}}}*/

?>
