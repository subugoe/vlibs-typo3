<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Georg Ringer <typo3@ringerge.org>
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
 * ViewHelper to add disqus thread
 * Details: http://www.disqus.com/
 *
 * Example
 * ==============
 * <div id="disqus_thread"></div>
 * <fed:social.disqus identifier="a-type-plus-{uid}" shortName="My Link" link="{url}" />
 *
 * @package Fed
 * @subpackage ViewHelpers
 */
class Tx_Fed_ViewHelpers_Social_DisqusViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	protected $tagName = 'div';

	protected $escapingInterceptorEnabled = FALSE;

	/**
	 * Initialize
	 */
	public function initializeArguments() {
		$this->registerArgument('identifier', 'string', 'Identifier for disqus link', FALSE, 'fed_disqus');
		$this->registerArgument('shortName', 'string', 'Short name of this disqus link', TRUE, NULL, TRUE);
		$this->registerArgument('link', 'string', 'Short name of this disqus link', TRUE, NULL, TRUE);
	}

	/**
	 * Render disqus thread
	 *
	 * @return void
	 */
	public function render() {
		//$this->tag->addAttribute('id', $this->arguments['identifier']);
		$code = '	var disqus_shortname = "' . htmlspecialchars($this->arguments['shortName']) . '";
					var disqus_identifier = "' . $this->arguments['identifier'] . '";
					var disqus_url = "' . htmlspecialchars($this->arguments['link']) . '";
					(function() {
						var dsq = document.createElement("script"); dsq.type = "text/javascript"; dsq.async = true;
						dsq.src = "http://" + disqus_shortname + ".disqus.com/embed.js";
						(document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(dsq);
					})();';
		$scriptTag = $this->documentHead->wrap($code, NULL, 'js');
		return $scriptTag;
		#$this->documentHead->includeHeader($code, 'js');
		#return $this->tag->render();
	}
}

?>