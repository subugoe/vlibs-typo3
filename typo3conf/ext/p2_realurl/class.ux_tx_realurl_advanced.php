<?php
class ux_tx_realurl_advanced extends tx_realurl_advanced {
	/*
	 * Sets a default language UID if the current language is one of
	 * the ones specified as "problematic" (like Arabic or Hangul)
	 *
	 * @author 					Wolfgang Klinger <wk@plan2.net>
	 *
	 * @param 		integer 	The current language UID
	 * @return 		integer 	The modified or original language UID (depends on the settings)
	 *
	 */
	function get_right_language_uid($lang_id) {

		$ext_conf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['p2_realurl']);

		if (isset($ext_conf['language_uids']) && strlen(trim($ext_conf['language_uids']))) {
			if (t3lib_div::inList($ext_conf['language_uids'], $lang_id) || strtolower(trim($ext_conf['language_uids'])) == 'all') {
				// set language to default
				$lang_id = isset($ext_conf['default_language_uid']) ? intval($ext_conf['default_language_uid']) : 0;
			}
		}

		return $lang_id;

	} // end: function get_right_language_uid


	function IDtoPagePathSegments($id,$mpvar,$langID) {

		// info@plan2.net
		// --begin

		$langID = $this->get_right_language_uid($langID);

		// --end

		// Check to see if we already built this one in this session
		$cacheKey = $id.'.'.$mpvar.'.'.$langID;
		if (!isset($this->IDtoPagePathCache[$cacheKey]))    {

			// Get rootLine for current site (overlaid with any language overlay records).
			if (!is_object($this->sys_page))    {    // Create object if not found before:
				// Initialize the page-select functions.
				$this->sys_page = t3lib_div::makeInstance('t3lib_pageSelect');
				$this->sys_page->init($GLOBALS['TSFE']->showHiddenPage);
			}
			$this->sys_page->sys_language_uid = $langID;
			$rootLine = $this->sys_page->getRootLine($id,$mpvar);
			$cc = count($rootLine);
			$newRootLine = array();
			$rootFound = FALSE;
			for($a=0;$a<$cc;$a++)    {
				if ($GLOBALS['TSFE']->tmpl->rootLine[0]['uid'] == $rootLine[$a]['uid'])    {
					$rootFound = TRUE;
				}
				if ($rootFound)    {
					$newRootLine[] = $rootLine[$a];
				}
			}

			if ($rootFound)    {
				// Translate the rootline to a valid path (rootline contains localized titles at this point!):
				$pagepath = $this->rootLineToPath($newRootLine,$langID);
				$this->IDtoPagePathCache[$cacheKey] = array(
						'pagepath' => $pagepath,
						'langID' => $langID,
						'pagepathhash' => substr(md5($pagepath),0,10),
						);
			} else {    // Outside of root line:
				$this->IDtoPagePathCache[$cacheKey] = FALSE;
			}
		}

		return $this->IDtoPagePathCache[$cacheKey];
	}
}
?>
