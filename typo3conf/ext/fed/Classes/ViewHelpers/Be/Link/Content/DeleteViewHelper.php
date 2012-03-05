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
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers\Be\Link\Content
 */
class Tx_Fed_ViewHelpers_Be_Link_Content_DeleteViewHelper extends Tx_Fed_Core_ViewHelper_AbstractBackendViewHelper {

	/**
	 * Render uri
	 *
	 * @return string
	 */
	public function render() {
		$uid = $this->arguments['row']['uid'];
		$pid = $this->arguments['row']['pid'];

		$charCode = '65,114,101,32,121,111,117,32,115,117,114,101,32,121,111,117,32,119,97,110,116,32,116,111,32,100,101,108,101,116,101,32,116,104,105,115,32,114,101,99,111,114,100,63';
		$icon = $this->getIcon('actions-edit-delete', 'Delete content element: tt_content:' . $this->arguments['row']['uid']);
		$action = 'return confirm(String.fromCharCode(' . $charCode . '));';
		$token = $this->getFormToken();
		$vC = $this->getLinkChecksum();
		$returnUri = $this->getReturnUri($pid);
		$uri = '../../../tce_db.php?&cmd[tt_content][' . $uid . '][delete]=1&redirect='
			. $returnUri . '&vC=' . $vC . '&formToken=' . $token . '&prErr=1&uPT=1';
		return $this->wrapLink($icon, $uri);
	}
}

?>