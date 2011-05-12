<?php 

class tx_lib_resultBrowserSpl extends tx_lib_object {

	function buildAs($browserKey = 'browserKey', $totalResultCountKey = 'totalResultCountKey') {
		$resultbrowser = tx_div::makeInstance('tx_lib_resultBrowser_controller');
		$resultbrowser->setDefaultDesignator($this->getDefaultDesignator());
		$this->controller->configurations->set('totalResultCount', $this->controller->get($totalResultCountKey));
		$this->controller->set($browserKey, $resultbrowser->main(NULL, $this->controller->configurations));
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_resultBrowserSpl.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_resultBrowserSpl.php']);
}
?>
