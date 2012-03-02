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

class Tx_Fed_ViewHelpers_Tools_SessionControlViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {
	
	
	/**
	 * Renders AJAX controls over caching. Perform cache-clearing operations
	 * then reload the exact URL. Should NATURALLY ONLY BE USED DURING DEVELOPMENT!
	 * @return string
	 */
	public function render() {
		session_start();
		$this->addScript();
		return $this->renderControls();
	}

	/**
	 * Render links for cache control
	 * @return string
	 */
	private function renderControls() {
		$sessionOptions = '';
		$this->getSessionOptions($_SESSION, $sessionOptions);
		$html = <<< HTML
<select class="fedSessionController">
<option value="-1">(select session variable to manage)</option>
{$sessionOptions}
</select>
HTML;
		return $html;
	}
	
	/**
	 * Get HTML options for all currnetly stored session variables
	 */
	private function getSessionOptions($var, &$html, $path='') {
		$indent = substr_count($path, '.');
		$pad = str_repeat('  ', $indent);
		foreach ($var as $name=>$value) {
			if (is_array($value)) {
				$html .= $this->getSessionOptions($value, $html, $path != '' ? $path.'.'.$name : $name);
			} else {
				$html .= "<option value='{$path}'>{$pad}{$name}</option>" . LF;
			}
		}
	}
	
	
	
	/**
	 * Add script for communication
	 * @return void
	 */
	private function addScript() {
		$script = <<< SCRIPT

jQuery(document).ready(function() { 
	jQuery('.fedSessionController').each(function() { 
		jQuery(this).change(function() {
			var cid = jQuery(this).val();
			if (parseInt(cid) < 0) {
				return;
			};
			var response = jQuery.ajax('?type=4815162342', {method: 'post', async: false, 
				data: {tx_fed_api: {controller: 'Tool', action: 'inspectSession', target: cid}}});
			var json = jQuery.parseJSON(response.responseText);
			if (typeof json == 'object' && parseInt(json.payload) > 0) {
				
				
				
				
				
			} else {
				alert('There was an error clearing the cache. The response was: ' + response);
			};
		})
	})
});
SCRIPT;
		$this->includeHeader($script, 'js', 'fedSessionControl');
	}
	
	
}

?>