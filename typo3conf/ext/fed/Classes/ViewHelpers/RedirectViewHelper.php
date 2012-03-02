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
 * Switch ViewHelper
 *
 * Fluid implementation of PHP's switch($var) construct
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers
 */
class Tx_Fed_ViewHelpers_RedirectViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * Inititlize
	 */
	public function initializeArguments() {
		$this->registerArgument('timeout', 'integer', 'Number of seconds to pass before redirecting', FALSE, 10);
		$this->registerArgument('location', 'string', 'URI/URL to redirect to', TRUE);
	}

	/**
	 * @param mixed $value The value to output
	 * @return string
	 */
	public function render() {
		$script = <<< SCRIPT
var remaining = parseInt({$this->arguments['timeout']});
var onInterval = function() {
	document.getElementById('fed-timeout-counter').innerHTML = remaining.toString();
	remaining -= 1;
	if (remaining == 0) {
		window.location.href = '{$this->arguments['location']}';
		clearTimeout(interval);
	};
};
var interval = setInterval(onInterval, 1000);
onInterval();
SCRIPT;
		$this->documentHead->includeHeader($script, 'js');
		$counter = '<span id="fed-timeout-counter">' . $this->arguments['timeout'] . '</span>';
		$this->templateVariableContainer->add('timeoutCounter', $counter);
		$content = $this->renderChildren();
		$this->templateVariableContainer->remove('timeoutCounter');
		return $content;
	}

}
?>
