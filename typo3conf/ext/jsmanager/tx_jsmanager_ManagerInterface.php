<?php
/**
 * Copyright notice
 * 
 * Copyright (c) 2007 Joerg Schoppet
 * All rights reserved
 * 
 * This script is part of the TYPO3 project. The TYPO3 project is 
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 * A copy is found in the textfile GPL.txt and important notices to the license 
 * from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 */
/**
 * Interface, for all js-lib-extensions
 * 
 * This interface have to be included by the processing class of
 * each js-lib-extension. Only classes, which implement this interface
 * can be registered by tx_jsmanager_Manager::register().
 * 
 * @package	TYPO3
 * @subpackage	jsmanager
 * @author	Joerg Schoppet <joerg@schoppet.de>
 * @version	SVN: $Id: tx_jsmanager_ManagerInterface.php 7932 2008-01-17 07:45:34Z derjoerg $
 */
interface tx_jsmanager_ManagerInterface {

	/**
	 * Checks if the TS-Config has the right syntax
	 * 
	 * @param	array	$configuration
	 * @return	bool|tx_jsmanager_Exception
	 */
	public function checkConfiguration(array $configuration);

	/**
	 * Processes the configuration and return the relevant output
	 * 
	 * @return	string
	 */
	public function getData();

	/**
	 * Returns the status, if the output is already included in page
	 * 
	 * @return	bool
	 */
	public function checkIsIncluded();

	/**
	 * Sets the status, if the output is already included in page
	 * 
	 * @param	bool	$isIncluded
	 * @return	void
	 */
	public function setIsIncluded($isIncluded = TRUE);

} // interface tx_jsmanager_ManagerInterface

?>