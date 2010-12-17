<?php
/********************************************************************
 *  Copyright notice
 *
 *  © 2010 Sven-S. Porst, SUB Göttingen <porst@sub.uni-goettingen.de>
 *  All rights reserved
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  This copyright notice MUST APPEAR in all copies of the script.
 ********************************************************************/


/**
 * Pazpar2neuerwerbungen.php
 *
 * Pazpar2neuerwerbungen model class.
 *
 * @author Sven-S. Porst <porst@sub-uni-goettingen.de>
 */




/**
 * pazpar2 Neuerwerbungen model object.
 */
class Tx_Pazpar2neuerwerbungen_Domain_Model_Pazpar2neuerwerbungen extends Tx_Extbase_DomainObject_AbstractEntity {


	/**
	 * Array with subject tree
	 *
	 * @var Array
	 */
	protected $subjects;
	
	/**
	 * Getter for subject tree Array
	 *
	 * return Array
	 */
	public function getSubjects () {
		return $this->subjects;
	}
	
	/**
	 * Setter for subject tree Array
	 *
	 * @param Array $newSubjects
	 * return void
	 */
	public function setSubjects ($newSubjects) {
		$this->subjects = $newSubjects;
	}


}

?>
