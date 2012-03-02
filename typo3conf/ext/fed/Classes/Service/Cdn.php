<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
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
 * CDN implementation service. Provides easy methods for including header
 * resources from Google's CDN which hosts projects and their code. Using
 * Google's CDN drastically increases the chances of getting a cache hit from
 * your clients - limiting the number of requests your server has to process
 * as well as limiting the bandwidth usage. The client's download gets a little
 * larger but you also gain parallel loading which should overall speed up page
 * loads even in comparison with a customized, trimmed jQueryUI file hosted locally.
 *
 * Currently jQuery and jQueryUI plus hosted and custom themes are supported.
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage Service
 */
class Tx_Fed_Service_Cdn implements t3lib_Singleton {

	/**
	 * The pattern from which jQuery library URIs are generated
	 * @var string
	 */
	protected $patternUrlJQuery = "https://ajax.googleapis.com/ajax/libs/@package/@version/@file";

	/**
	 * @var t3lib_PageRenderer
	 */
	protected $pageRenderer;

	/**
	 * @var Tx_Fed_Utility_DocumentHead
	 */
	protected $documentHead;

	/**
	 * @param t3lib_PageRenderer $pageRenderer
	 */
	public function injectPageRenderer(t3lib_PageRenderer $pageRenderer) {
		$this->pageRenderer = $pageRenderer;
	}

	/**
	 * @param Tx_Fed_Utility_DocumentHead $documentHead
	 */
	public function injectDocumentHead(Tx_Fed_Utility_DocumentHead $documentHead) {
		$this->documentHead = $documentHead;
	}

	/**
	 * Inserts necessary header tags for loading jQuery, jQueryUI (optional) and
	 * a theme for jQueryUI (optional; can be URI of your own theme). Can also
	 * return a set or URLs for external processing.
	 *
	 * Version can be set to NULL to always get the latest version (NOT producion
	 * safe!)
	 * Version can be set to "1" to always get latest release of jQuery 1.x.x
	 * Version can be set to "1.8" to always get latest 1.8.x
	 * This is true for both the jQuery and jQueryUI versions.
	 *
	 * The default values of all arguments is NULL, which means "always include
	 * latest version". You -MUST- specify NULL if you intentionally want to
	 * load most recent jQueryUI.
	 *
	 * jQueryUITheme is excluded if either NULL or FALSE.
	 *
	 * Defining a jQueryUITheme but no jQueryUIVersion loads theme regardless.
	 *
	 * jQueryUITheme can be either a Google CDN url (in which case the version
	 * of jQueryUI, if specified, determines the version of the theme) or it
	 * can be a relative URL (from PATH_site) to your own template file
	 *
	 * @param mixed $jQueryVersion Specify as either "1", "1.8" or "1.8.2" - for the loose versions you always get the latest release
	 * @param mixed $jQueryUIVersion Specify as either "1", "1.8" or "1.8.2" - for the loose versions you always get the latest release
	 * @param mixed $jQueryUITheme If NULL/FALSE excludes theme
	 * @param boolean $compatibility If TRUE puts jQuery into compatibility mode. Ignored if $return === TRUE
	 * @return void
	 * @api
	 */
	public function includeJQuery($jQueryVersion='1', $jQueryUIVersion=FALSE, $jQueryUITheme=FALSE, $compatibility=FALSE) {
		$file = $this->buildPackageUri('jquery', $jQueryVersion, 'jquery.min.js');
		#$this->pageRenderer->addJsFile($file, 'text/javascript', FALSE, TRUE, FALSE, TRUE);
		$this->documentHead->includeFile($file);
		#die($file);
		if ($jQueryUIVersion) {
			$this->includeJQueryUI($jQueryUIVersion, $return);
		}
		if ($jQueryUITheme) {
			$this->includeJQueryUITheme($jQueryUITheme, $return);
		}
		if ($compatibility) {
			$this->includeJQueryNoConflict();
		}
	}

	/**
	 * Inserts a script tag loading the specified $jQueryUIVersion - or returns
	 * the URL if $return === TRUE
	 * @param string $jQueryUIVersion
	 * @param boolean $return If TRUE returns only the URL
	 * @api
	 */
	public function includeJQueryUI($jQueryUIVersion=NULL) {
		$file = $this->buildPackageUri('jqueryui', $jQueryUIVersion, 'jquery-ui.min.js');
		#$this->pageRenderer->addJsFile($file, 'text/javascript', FALSE, FALSE, FALSE, TRUE);
		$this->documentHead->includeFile($file);
	}

	/**
	 * Inserts a style tag pointing to theme or returns the computed URL/URI
	 * @param string $jQueryUITheme URI/URL/name of theme to load
	 * @api
	 */
	public function includeJQueryUITheme($jQueryUITheme=NULL) {
		#$this->pageRenderer->addCssFile($jQueryUITheme, 'stylesheet', 'all', 'JQuery Theme CSS', TRUE, TRUE, FALSE, FALSE);
		$this->documentHead->includeFile($jQueryUITheme);
	}

	/**
	 * Includes a header script setting jQuery.noConflict() for compatibility
	 * @return void
	 * @api
	 */
	public function includeJQueryNoConflict() {
		$script = 'jQuery.noConflict();';
		#$this->pageRenderer->addJsInlineCode('jquery-compat', $script, FALSE);
		$this->documentHead->includeHeader($script, 'js');
	}

	/**
	 * Generates a CDN URI for a package as defined by arguments
	 *
	 * @param string $package URI-name of the package
	 * @param string $version The version number to load
	 * @param string $file Filename to load from that package
	 * @api
	 */
	public function buildPackageUri($package='jquery', $version=NULL, $file='jquery.min.js') {
		$uri = $this->patternUrlJQuery;
		if ($version == NULL) {
			$uri = str_replace('@version/', '', $uri);
		} else {
			$uri = str_replace('@version', $version, $uri);
		}
		$uri = str_replace('@file', $file, $uri);
		$uri = str_replace('@package', $package, $uri);
		return $uri;
	}

}

?>
