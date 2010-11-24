<?php

class Tx_Efempty_ViewHelpers_LoopViewHelper extends Tx_Fluid_Core_ViewHelper_AbstractViewHelper {

	
	/**
     * Renders a loop
     *
     * @param array $each Array to iterate over1
     * @param string $as Iteration variable
     */
    public function render(array $each, $as) {
    $out = '';
    foreach ($each as $singleElement) {
      $this->templateVariableContainer->add($as, $singleElement);
      $out .= $this->renderChildren();
      $this->templateVariableContainer->remove($as);
    }
    return $out;
  }

	
}
?>