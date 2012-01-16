<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Nicole Cordes <cordes@cps-it.de>
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
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class tx_cpsdevlib_extmgm {

	/**
	 * Include a CSS file in frontend or backend
	 *
	 * @param string $cssFile: Path to CSS file
	 * @param string $cssName: File depending name to avoid duplicate insert
	 * @param string $rel: Parameter "rel" for generated link-tag
	 * @param string $media: Parameter "media" for generated link-tag
	 * @param string $title: Parameter "title" for generated link-tag
	 * @return boolean Returns true when successfully inserted
	 *
	 */
	public static function addCssFile($cssFile, $cssName = '', $rel = 'stylesheet', $media = 'all', $title = '') {

		if (!$cssName) $cssName = $cssFile;
		$cssLink = '<link rel="' . htmlspecialchars($rel) . '" type="text/css" href="' . htmlspecialchars($cssFile) . '" media="' . htmlspecialchars($media) . '"' . ($title ? ' title="' . htmlspecialchars($title) . '"' : '') . self::getEndingSlash() . '>';

		// If CSS should be added to backend output
		if (TYPO3_MODE == 'BE') {
			if (is_object($GLOBALS['SOBE'])) {
				// If content already rendered
				if ((isset($GLOBALS['SOBE']->content)) && ($GLOBALS['SOBE']->content)) {
					if (strpos($GLOBALS['SOBE']->content, $cssLink) === false) {
						$GLOBALS['SOBE']->content = str_replace('</head>', LF . $cssLink . LF . '</head>', $GLOBALS['SOBE']->content);
					}

					return true;
				} else {
					if (method_exists($GLOBALS['SOBE'], 'getPageRenderer')) {
						// Support for TYPO3 >= 4.3
						$GLOBALS['SOBE']->getPageRenderer()->addCssFile($cssFile, $rel, $media, $title);

						return true;
					} elseif (isset($GLOBALS['SOBE']->additionalHeaderData)) {
						// Support for TYPO3 <= 4.2
						$GLOBALS['SOBE']->additionalHeaderData[$cssName] = $cssLink;

						return true;
					}
				}
			}
		} elseif (TYPO3_MODE == 'FE') { // If CSS should be added to frontend output
			$GLOBALS['TSFE']->additionalHeaderData[$cssName] = $cssLink;

			return true;
		}

		return false;
	}

	/**
	 * Include inline CSS in frontend or backend
	 *
	 * @param string $cssCode: Code to insert
	 * @param string $cssName: Code depending name to avoid duplicate insert
	 * @return boolean Returns true when successfully inserted
	 *
	 */
	public static function addCssInline($cssCode, $cssName = '') {

		if (!$cssName) $cssName = substr(md5($cssCode), 0, 30);
		$cssStyle = '<style type="text/css">' . LF . '/*<![CDATA[*/' . LF . '<!-- ' . LF . $cssCode . LF . '-->' . LF . '/*]]>*/' . LF . '</style>';

		// If CSS should be added to backend output
		if (TYPO3_MODE == 'BE') {
			if (is_object($GLOBALS['SOBE'])) {
				// If content already rendered
				if ((isset($GLOBALS['SOBE']->content)) && ($GLOBALS['SOBE']->content)) {
					if (strpos($GLOBALS['SOBE']->content, $cssStyle) === false) {
						$GLOBALS['SOBE']->content = str_replace('</head>', LF . $cssStyle . LF . '</head>', $GLOBALS['SOBE']->content);
					}

					return true;
				} else {
					if (method_exists($GLOBALS['SOBE'], 'getPageRenderer')) {
						// Support for TYPO3 >= 4.3
						$GLOBALS['SOBE']->getPageRenderer()->addCssInlineBlock($cssName, $cssCode);

						return true;
					} elseif (isset($GLOBALS['SOBE']->additionalHeaderData)) {
						// Support for TYPO3 <= 4.2
						$GLOBALS['SOBE']->additionalHeaderData[$cssName] = $cssStyle;

						return true;
					}
				}
			}
		} elseif (TYPO3_MODE == 'FE') { // If CSS should be added to frontend output
			$GLOBALS['TSFE']->additionalHeaderData[$cssName] = $cssStyle;

			return true;
		}

		return false;
	}

	/**
	 * Include a JavaScript file in frontend or backend
	 *
	 * @param string $jsFile: Path to JavaScript file
	 * @param string $jsName: File depending name to avoid duplicate insert
	 * @param string $type: Parameter "type" for generated script-tag
	 * @return boolean Returns true when successfully inserted
	 *
	 */
	public static function addJavascriptFile($jsFile, $jsName = '', $type = 'text/javascript') {

		if (!$jsName) $jsName = $jsFile;
		$jsScript = '<script src="' . htmlspecialchars($jsFile) . '" type="' . htmlspecialchars($type) . '"></script>';

		// If JS should be added to backend output
		if (TYPO3_MODE == 'BE') {
			if (is_object($GLOBALS['SOBE'])) {
				// If content already rendered
				if ((isset($GLOBALS['SOBE']->content)) && ($GLOBALS['SOBE']->content)) {
					if (strpos($GLOBALS['SOBE']->content, $jsScript) === false) {
						$GLOBALS['SOBE']->content = str_replace('</head>', LF . $jsScript . LF . '</head>', $GLOBALS['SOBE']->content);
					}

					return true;
				} else {
					if (method_exists($GLOBALS['SOBE'], 'getPageRenderer')) {
						// Support for TYPO3 >= 4.3
						$GLOBALS['SOBE']->getPageRenderer()->addJsFile($jsFile, $type);

						return true;
					} elseif (isset($GLOBALS['SOBE']->additionalHeaderData)) {
						// Support for TYPO3 <= 4.2
						$GLOBALS['SOBE']->additionalHeaderData[$jsName] = $jsScript;

						return true;
					}
				}
			}
		} elseif (TYPO3_MODE == 'FE') { // If JS should be added to frontend output
			$GLOBALS['TSFE']->additionalHeaderData[$jsName] = $jsScript;

			return true;
		}

		return false;
	}

	/**
	 * Include inline JavaScript in frontend or backend
	 *
	 * @param string $jsCode: Code to insert
	 * @param string $jsName: Code depending name to avoid duplicate insert
	 * @param string $type: Parameter "type" for generated script-tag
	 * @return boolean Returns true when successfully inserted
	 *
	 */
	public static function addJavascriptInline($jsCode, $jsName = '', $type = 'text/javascript') {

		if (!$jsName) $jsName = substr(md5($jsCode), 0, 30);
		$jsScript = '<script type="' . htmlspecialchars($type) . '">' . LF . $jsCode . LF . '</script>';

		// If JS should be added to backend output
		if (TYPO3_MODE == 'BE') {
			if (is_object($GLOBALS['SOBE'])) {
				// If content already rendered
				if ((isset($GLOBALS['SOBE']->content)) && ($GLOBALS['SOBE']->content)) {
					if (strpos($GLOBALS['SOBE']->content, $jsScript) === false) {
						$GLOBALS['SOBE']->content = str_replace('</head>', LF . $jsScript . LF . '</head>', $GLOBALS['SOBE']->content);
					}

					return true;
				} else {
					if (method_exists($GLOBALS['SOBE'], 'getPageRenderer')) {
						// Support for TYPO3 >= 4.3
						$GLOBALS['SOBE']->getPageRenderer()->addJsInlineCode($jsName, $jsCode);

						return true;
					} elseif (isset($GLOBALS['SOBE']->additionalHeaderData)) {
						// Support for TYPO3 <= 4.2
						$GLOBALS['SOBE']->additionalHeaderData[$jsName] = $jsScript;

						return true;
					}
				}
			}
		} elseif (TYPO3_MODE == 'FE') { // If JS should be added to frontend output
			$GLOBALS['TSFE']->additionalHeaderData[$jsName] = $jsScript;

			return true;
		}

		return false;
	}

	/**
	 * Implementation of enableFields for FE and BE. Parameter isNull can handle fields to be NULL needed for tables joins.
	 *
	 * @param string $table
	 * @param int $showHidden
	 * @param int $isNull
	 * @return string
	 */
	public static function enableFields($table, $showHidden = 0, $isNull = 0) {
		$ctrl = $GLOBALS['TCA'][$table]['ctrl'];
		$sql = '';

		if (is_array($ctrl)) {
			// Add delete statement
			if ($ctrl['delete']) {
				$field = $table . '.' . $ctrl['delete'];
				$clause = $field . '=0';
				if ($isNull) {
					$clause = '(' . $clause . ' OR ' . $field . ' IS NULL)';
				}
				$sql .= ' AND ' . $clause;
			}

			// Add parts only available in frontend
			if (TYPO3_MODE == 'FE') {
				// Filter out new place-holder records in case we are NOT in a versioning preview (that means we are online!)
				if ($ctrl['versioningWS'] && !$GLOBALS['TSFE']->sys_page->versioningPreview)	{
					$clause = $table . '.t3ver_state<=0 AND ' . $table . '.pid!=-1';	// Shadow state for new items MUST be ignored!
					if ($isNull) {
						$clause = '((' . $clause . ') OR (' . $table . '.t3ver_state IS NULL AND ' . $table . '.pid IS NULL))';
					}
					$sql .= ' AND ' . $clause;
				}

				// In case of versioning-preview, enableFields are ignored
				if ($GLOBALS['TSFE']->sys_page->versioningPreview && $ctrl['versioningWS']) {
					unset($ctrl['enablecolumns']);
				}
			}

			// Add enablecolumns
			if (is_array($ctrl['enablecolumns'])) {
				// Add disable statement
				if ($ctrl['enablecolumns']['disabled'] && !$showHidden) {
					$field = $table . '.' . $ctrl['enablecolumns']['disabled'];
					$clause = $field . '=0';
					if ($isNull) {
						$clause = '(' . $clause . ' OR ' . $field . ' IS NULL)';
					}
					$sql .= ' AND ' . $clause;
				}

				// Add starttime statement
				if ($ctrl['enablecolumns']['starttime']) {
					$field = $table . '.' . $ctrl['enablecolumns']['starttime'];
					$clause = $field . '<=' . $GLOBALS['SIM_ACCESS_TIME'];
					if ($isNull) {
						$clause = '(' . $clause . ' OR ' . $field . ' IS NULL)';
					}
					$sql .= ' AND ' . $clause;
				}

				// Add endtime statement
				if ($ctrl['enablecolumns']['endtime']) {
					$field = $table . '.' . $ctrl['enablecolumns']['endtime'];
					$clause = $field . '=0 OR ' . $field . '>' . $GLOBALS['SIM_ACCESS_TIME'];
					if ($isNull) {
						$clause = $clause . ' OR ' . $field . ' IS NULL';
					}
					$sql .= ' AND (' . $clause . ')';
				}

				// Add fe_group statement in frontend
				if (TYPO3_MODE == 'FE') {
					if ($ctrl['enablecolumns']['fe_group']) {
						$field = $table . '.' . $ctrl['enablecolumns']['fe_group'];
						$sql .= $GLOBALS['TSFE']->sys_page->getMultipleGroupsWhereClause($field, $table); // isNull already integrated in statement
					}
				}

				// Call hook functions for additional enableColumns
				// It is used by the extension ingmar_accessctrl which enables assigning more than one usergroup to content and page records
				if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_page.php']['addEnableColumns']))    {
					$_params = array(
						'table' => $table,
						'show_hidden' => $showHidden,
						'ignore_array' => array(),
						'ctrl' => $ctrl
					);
					foreach($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_page.php']['addEnableColumns'] as $_funcRef)    {
						$sql .= t3lib_div::callUserFunction($_funcRef, $_params, $GLOBALS['TSFE']->sys_page);
					}
				}
			}
		}

		return $sql;
	}

	/**
	 * Return ending slash for xhtml document doctypes (and html5)
	 *
	 * @return string Either ending slash or none
	 *
	 */
	public static function getEndingSlash() {
		// Array with all xhtml doctypes
		$xhtmlArray = array(
			'html_5',
			'html5',
			'xhtml+rdfa_10',
			'xhtml_11',
			'xhtml_2',
			'xhtml_basic',
			'xhtml_frames',
			'xhtml_strict',
			'xhtml_trans'
		);

		$result = '';

		// Only check for frontend
		if (TYPO3_MODE == 'FE') {

			// If current doctype is a xhtml one
			if (in_array($GLOBALS['TSFE']->config['config']['doctype'], $xhtmlArray)) {
				$result = ' /';
			}
		} else {
			$result = ' /';
		}

		return $result;
	}

	/**
	 * Get proper language for frontend and backend
	 *
	 * @param string $default: Default language key to return if nothing else was found
	 * @static
	 * @return string
	 */
	public static function getLanguage($default='default') {
		switch (TYPO3_MODE) {
			case 'BE':

				// Return language or default
				if ($GLOBALS['LANG'] instanceof language) {
					$lang = strtolower($GLOBALS['LANG']->lang);
					return ($lang == 'default') ? $default : $lang;
				}
				break;

			case 'FE':

				// Return language or default
				if ($GLOBALS['TSFE'] instanceof tslib_fe) {
					return $GLOBALS['TSFE']->lang;
				}
				break;
		}

		return $default;
	}
}

?>