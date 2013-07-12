<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2007 René Fritz (r.fritz@colorcube.de)
*  All rights reserved
*
*  This script is part of the Typo3 project. The Typo3 project is
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
 * Functions to convert the character encoding of the static info tables
 * * DEPRECATED * Used by language packs prior to Static Info Tables version 6
 *
 */
class tx_staticinfotables_encoding {
	/**
	 * Returns a selector box with charset encodings
	 *
	 * @deprecated since 6.0, will be removed two versions later - Language pack should be re-created
	 *
	 * @param	string		$elementName it the form elements name, probably something like "SET[...]"
	 * @param	string		$currentKey is the key to be selected currently.
	 * @param	string		$firstEntry is the key to be placed on top as first (default) entry.
	 * @param	string		$unsetEntries List of keys that should be removed (comma list).
	 * @return	string		HTML code for selector box
	 */
	function getEncodingSelect ($elementName, $currentKey, $firstEntry='', $unsetEntries='') {
		\TYPO3\CMS\Core\Utility\GeneralUtility::logDeprecatedFunction();
		$menuItems = array(
			'utf-8' => 'UTF-8',
		);

		if ($firstEntry && $menuItems[$firstEntry]) {
			$entry = array($firstEntry => $menuItems[$firstEntry]);
			unset($menuItems[$firstEntry]);
			$menuItems = array_merge($entry, $menuItems);
		}

		$unsetEntries = explode(',', $unsetEntries);
		foreach($unsetEntries as $entry) {
			unset($menuItems[$entry]);
		}

		$options = array();
		foreach($menuItems as $value => $label)	{
			$options[] = '<option value="'.htmlspecialchars($value).'"'.(!strcmp($currentKey,$value)?' selected="selected"':'').'>'.
							\TYPO3\CMS\Core\Utility\GeneralUtility::deHSCentities(htmlspecialchars($label)).
							'</option>';
		}
		if (count($options)) {
			return '

					<!-- charset encoding menu -->
					<select name="'.$elementName.'">
						'.implode('
						',$options).'
					</select>
						';
		}
	}
}
?>