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
 * @subpackage ViewHelpers\Be\Uri\Content
 */
class Tx_Fed_ViewHelpers_Be_Link_Content_VisibilityViewHelper extends Tx_Fed_Core_ViewHelper_AbstractBackendViewHelper {

	/**
	 * Render uri
	 *
	 * @return string
	 */
	public function render() {
		$pid = $this->arguments['row']['pid'];
		$uid = $this->arguments['row']['uid'];
		$sysLang = $this->arguments['row']['sys_language_uid'];
		$colPos = 255;

		if ($row['hidden'] == 1) {
			$iconFile = 'actions-edit-unhide';
			$label = 'Unhide content element';
			$newHidden = 0;
		} else {
			$iconFile = 'actions-edit-hide';
			$label = 'Hide content element';
			$newHidden = 1;
		}

		$vC = $this->getLinkChecksum();
		$token = $this->getFormToken();

		$uri = '/typo3/tce_db.php?&data[tt_content][' . $uid . '][hidden]=' . $newHidden
			. '&redirect=%2Ftypo3%2Fsysext%2Fcms%2Flayout%2Fdb_layout.php%3Fid%3D' . $pid
			. '&vC=' . $vC . '&formToken=' . $token . '&prErr=1&uPT=1'
			;
		$icon = $this->getIcon($iconFile, $label);
		return $this->wrapLink($icon, $uri);
	}
}

?>