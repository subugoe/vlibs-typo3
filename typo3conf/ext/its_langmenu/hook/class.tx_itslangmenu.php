<?php
class tx_itslangmenu {

	function tx_itslangmenu  () {




	}
	function checklang(&$params, &$ref) {
		if (!isset($GLOBALS['TSFE']->language_uid_modified))
				$GLOBALS['TSFE']->language_uid_modified=0;

		if (! isset ($_GET['L']) || $GLOBALS['TSFE']->language_uid_modified==1) {
			$pos = strpos($params['LD']['totalURL'], '&L=');


			/*
			if ($pos>0) {
				$pos2 = strpos($params['LD']['totalURL'], '&',$pos+1);
				if ($pos2 > 0) {

				}	else {
					$params['LD']['totalURL'] = substr($params['LD']['totalURL'],0,$pos);

				}
			}
			*/
			$pos = strpos($params['LD']['totalURL'], '&L=');

			if ($pos === false) {
				$params['LD']['totalURL'] .='&L='.intval($GLOBALS['TSFE']->sys_language_uid);

			}
			$_GET['L']=$GLOBALS['TSFE']->sys_language_uid;
			$GLOBALS['TSFE']->language_uid_modified=1;

		}

		return ;

	}
}
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/its_langmenu/hook/class.tx_itslangmenu.php']) {
	include_once ($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/its_langmenu/hook/class.tx_itslangmenu.php']);
}

?>