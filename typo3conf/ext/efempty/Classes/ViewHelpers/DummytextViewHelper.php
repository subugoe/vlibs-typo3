<?php

class Tx_Efempty_ViewHelpers_DummytextViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	
	/**
	 * Das ist der Dummytext-ViewHelper
	 *
	 * @param int $length LŠnge des Dummy-Textes
	 */
	public function render($length = 100) {
		//$dummytext = 'Lorem ipsum dolor sit amet. ';
		$dummytext = $this->renderChildren();
		$len = strlen($dummytext);
		$repeat = ceil($length / $len);
		$dummytext_neu = substr(str_repeat($dummytext,$repeat),0,$length);
		debug($this->arguments);
		return $dummytext_neu;
	}
	
}
?>