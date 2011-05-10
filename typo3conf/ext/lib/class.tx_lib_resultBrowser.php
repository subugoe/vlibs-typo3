<?php 

/**
	* This is a simple straight forward resultbroser class.
	*	The API offers all functions needed to generate a result browser.
	* This class can be used independently from a lib MVC setup. 
	* It doesn't depend on the presence of a lib controller object.
	* It can directly be used by external extensions. 
	*/

class tx_lib_resultBrowser {

	var $count = 0;
	var $designator = '';
	var $labels= array();
	var $parameters = array();

	/**
	 * Build the result browser
	 * 
	 * Call this function after all settings have been done.
	 *
	 * @return void 
	 */
	function build() {
	}	

	/** 
	 * Get the pager
	 *
	 * Depending on the implementation, this can be a select or a list of links
	 *
	 * @return string   The HTML string of the control.
	 */
	function controls() {
		$out = '<p>Resultbrowser ' . $this->count() .'</p>';
		return $out;
	}

	/**
	 * Set or get total result count
	 *
	 * @param integer The total result count.
	 * @return integer The total result count.
	 */
	function count($count = NULL) {
		if(is_numeric($count)) $this->count = $count;
		return $this->count;
	}

	/**
	 * Set or get the designator 
	 *
	 * @param	string		The designator.
	 * @return string   The designator. 
	 * @see tx_lib_link->designator();
	 */
	function designator($string = NULL) {
		if(strlen($string)) $this->designatorString = $string;
		return $this->designatorString;
	}

	/**
	 * Set or get the result lables 
	 *
	 * Takes key value pairs of labels.
	 * Key is the offset, value a text string.
	 *
	 * @param array Key-value pairs of labels.
	 * @return array Key-value pairs of labels.
	 */
	function labels($array=NULL) {
		if(is_array($array)) $this->labelsArray = $array;
		return $this->labelsArray;
	}

	/**
	 * Set or get the array of parameters to send
	 *
	 * @param	array     The parameters.
	 * @return array   The parameters. 
	 */
	function parameters($Array = NULL) {
		if(is_array($rray)) $this->parametersArray = $array; 
		return $this->parametersArray; 
	}


}

?>
