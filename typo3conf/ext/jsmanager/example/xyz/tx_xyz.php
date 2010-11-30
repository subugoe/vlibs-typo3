<?php

class tx_xyz implements tx_jsmanager_ManagerInterface {
	protected $isIncluded = FALSE;
	protected $configuration = array();
	
	public function checkConfiguration(array $configuration) {
		$this->configuration = $configuration;
		return true;
	} // public function checkConfiguration(array $configuration)

	public function getData() {
		return '<script type="text/javascript">alert(\'' . str_replace("\n", '', print_r($this->configuration, true)) . '\');</script>';
	} // public function getData()

	public function checkIsIncluded() {
		return $this->isIncluded;
	} // public function checkIsIncluded()

	public function setIsIncluded($isIncluded = TRUE) {
		$this->isIncluded = $isIncluded;
	} // public function setIsIncluded($isIncluded = TRUE)

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xyz/tx_xyz.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/xyz/tx_xyz.php']);
}

?>
