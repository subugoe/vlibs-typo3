<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Claus Due <claus@wildside.dk>, Wildside A/S
*
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
 * Controller
 *
 * @package Fed
 * @subpackage Controller
 */
class Tx_Fed_Controller_HashController extends Tx_Fed_Core_AbstractController {

	/**
	 * @var Tx_Extbase_Security_Cryptography_HashService
	 */
	protected $hashService;

	/**
	 * @param Tx_Extbase_Security_Cryptography_HashService $hashService
	 */
	public function injectHashService(Tx_Extbase_Security_Cryptography_HashService $hashService) {
		$this->hashService = $hashService;
	}

	/**
	 * @param string $fieldNames
	 * @param string $fieldnamePrefix
	 * @return string
	 * @dontverifyrequesthash
	 */
	public function requestAction($fieldNames, $fieldNamePrefix='') {
		$hash = $this->hashService->generateHash($fieldNames, $fieldNamePrefix);
		echo $hash;
		exit();
	}

}

?>