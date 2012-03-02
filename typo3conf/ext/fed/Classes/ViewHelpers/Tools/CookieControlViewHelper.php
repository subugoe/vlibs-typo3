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

class Tx_Fed_ViewHelpers_Tools_CookieControlViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {


	/**
	 * Renders AJAX controls over caching. Perform cache-clearing operations
	 * then reload the exact URL. Should NATURALLY ONLY BE USED DURING DEVELOPMENT!
	 * @return string
	 */
	public function render() {
		$this->addScript();
		return $this->renderControls();
	}

	/**
	 * Render links for cache control
	 * @return string
	 */
	private function renderControls() {
		$cookieOptions = $this->getCookieOptions();
		$html = <<< HTML
<div class="fedCookieController">
<select>
<option value="-1">(select cookie to manage)</option>
{$cookieOptions}
</select>
<span>
<br />
<input type="text" value="" size="60" /><br />
<button name="remove">remove</button><br />
<button name="set">set value</button><br />
<button name="cancel">cancel</button>
</span>
</div>
HTML;
		return $html;
	}

	private function getCookieOptions() {
		$html = '';
		foreach ($_COOKIE as $name=>$value) {
			$html .= "<option value='{$name}'>{$name}</option>" . LF;
			unset($value);
		}
		return $html;
	}

	/**
	 * Add script for communication
	 * @return void
	 */
	private function addScript() {
		$script = <<< SCRIPT

jQuery(document).ready(function() {
	var cid;
	var controls = jQuery('.fedCookieController span').hide();
	controls.find('button[name="remove"]').click(function() {
		var response = jQuery.ajax('?type=4815162342', {method: 'post', async: false,
			data: {tx_fed_api: {controller: 'Tool', action: 'removeCookie', target: cid}}});
		if (parseInt(jQuery.parseJSON(response.responseText).payload) == 1) {
			jQuery('.fedCookieController select').val(-1);
			controls.hide();
			document.location.reload();
		};
	});
	controls.find('button[name="set"]').click(function() {
		var response = jQuery.ajax('?type=4815162342', {method: 'post', async: false,
			data: {tx_fed_api: {controller: 'Tool', action: 'setCookie', target: cid, value: controls.find('input').val()}}});
	});
	controls.find('button[name="cancel"]').click(function() {
		controls.hide();
		jQuery('.fedCookieController select').val(-1);
	});
	jQuery('.fedCookieController select').each(function() {
		jQuery(this).change(function() {
			cid = jQuery(this).val();
			if (parseInt(cid) < 0) {
				return;
			};
			var response = jQuery.ajax('?type=4815162342', {method: 'post', async: false,
				data: {tx_fed_api: {controller: 'Tool', action: 'inspectCookie', target: cid}}});
			var json = jQuery.parseJSON(response.responseText);
			controls.show();
			controls.find('input').val(json);
		})
	})
});
SCRIPT;
		$this->includeHeader($script, 'js', 'fedCookieControl');
	}


}

?>