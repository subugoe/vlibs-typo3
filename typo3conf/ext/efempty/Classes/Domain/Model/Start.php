<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Patrick Lobacher <patrick.lobacher@typovision.de> 
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
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Just a model called Start with one attribute 
 *
 * @version 	$Id:$
 * @author		Patrick Lobacher <patrick.lobacher@typovision.de>
 * @copyright 	Copyright belongs to the respective authors
 * @license 	http://opensource.org/licenses/gpl-license.php GNU Public License, version 2
 * @scope 		prototype
 * @entity
 */
class Tx_Efempty_Domain_Model_Start extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * Some title.
	 *
	 * @var string
	 * @identity
	 */
	protected $title = '';
	
    /**
	 * An empty constructor - fill it as you like
	 *
	 */
	public function __construct() {
		
	}
	
	
	/**
	 * Sets the title
	 * 
	 * @param string $title
	 * return void
	 */
	public function setTitle($title) {
		$this->title = $title;
	}
	
	/**
	 * Gets the title
	 * 
	 * @return string The title of the album
	 */
	public function getTitle() {
		return $this->title;
	}
	
}

?>