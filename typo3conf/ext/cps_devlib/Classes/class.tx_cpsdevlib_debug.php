<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Nicole Cordes <cordes@cps-it.de>
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
 *  A copy is found in the textfile GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

class tx_cpsdevlib_debug {

	/**
	 * Returns human readable variable information output by print_r function
	 * Depending on TYPO3_CONF_VARS['SYS']['displayErrors'] and checks TYPO3_CONF_VARS['SYS']['devIPmask'] if needed to
	 *
	 * @param mixed $theData: Variable to dump (if allowed)
	 * @param string $codeClass: Class to use for pre-tag with SyntaxHighlighter
	 * @param string $blockTitle: Display a title above block
	 * @param boolean $useSyntaxHighlighter: Add some JavaScript to turn on SyntaxHighlighter
	 * @param array $shAdditionalConfig: Manual configuration of SyntaxHighlighter e.g. to add custom brushes
	 * @param string $additionalWrap:	Wrap output if not using SyntaxHighlighter
	 * @return string The dumped variable
	 *
	 */
	public static function debugOutput($theData, $codeClass = 'plain', $blockTitle = '', $useSyntaxHighlighter = true, $shAdditionalConfig = array(), $additionalWrap = '<pre>|</pre>') {
		global $TYPO3_CONF_VARS;

		$result = '';

		// If displayErrors is turned on
		if (($displayErrors = intval($TYPO3_CONF_VARS['SYS']['displayErrors'])) != '-1') {

			// Check for development IP mask if configured
			if ($displayErrors == 2) {
				if (t3lib_div::cmpIP(t3lib_div::getIndpEnv('REMOTE_ADDR'), $TYPO3_CONF_VARS['SYS']['devIPmask'])) {
					$displayErrors = 1;
				} else {
					$displayErrors = 0;
				}
			}

			if ($displayErrors == 1) {

				// Turn off caching if output in frontend
				if (TYPO3_MODE == 'FE') {
					$GLOBALS['TSFE']->set_no_cache();
				}

				// Start output buffering
				ob_start();

				print_r($theData);

				// Store output buffer in variable
				$result = ob_get_contents();

				// Clean output buffer
				ob_end_clean();

				// Style output with SyntaxHighlighter
				if ($useSyntaxHighlighter) {

					$shBasicConfig = array(
						'baseUrl' => '/' . t3lib_extMgm::siteRelPath('cps_devlib') . 'Resources/',
						'scripts' => 'scripts/',
						'styles' => 'styles/',
						'theme' => 'Default',
						'brushes' => array(),
					);

					$shBasicConfig = array_merge($shBasicConfig, $shAdditionalConfig);

					// Try to get brush to load
					if (!count($shBasicConfig['brushes'])) {

						$codeClass = strtolower($codeClass);
						switch ($codeClass) {

							case 'applescript':
								$shBasicConfig['brushes'] = array('AppleScript');
								break;

							case 'as3':
							case 'actionscript3':
								$shBasicConfig['brushes'] = array('AS3');
								break;

							case 'bash':
							case 'shell':
								$shBasicConfig['brushes'] = array('Bash');
								break;

							case 'cf':
							case 'coldfusion':
								$shBasicConfig['brushes'] = array('ColdFusion');
								break;

							case 'c#':
							case 'c-sharp':
							case 'csharp':
								$shBasicConfig['brushes'] = array('CSharp');
								break;

							case 'c':
							case 'cpp':
								$shBasicConfig['brushes'] = array('Cpp');
								break;

							case 'css':
								$shBasicConfig['brushes'] = array('Css');
								break;

							case 'delphi':
							case 'pas':
							case 'pascal':
								$shBasicConfig['brushes'] = array('Delphi');
								break;

							case 'diff':
							case 'patch':
								$shBasicConfig['brushes'] = array('Diff');
								break;

							case 'erl':
							case 'erlang':
								$shBasicConfig['brushes'] = array('Erlang');
								break;

							case 'groovy':
								$shBasicConfig['brushes'] = array('Groovy');
								break;

							case 'js':
							case 'jscript':
							case 'javascript':
								$shBasicConfig['brushes'] = array('JScript');
								break;

							case 'java':
								$shBasicConfig['brushes'] = array('Java');
								break;

							case 'jfx':
							case 'javafx':
								$shBasicConfig['brushes'] = array('JavaFX');
								break;

							case 'perl':
							case 'pl':
								$shBasicConfig['brushes'] = array('Perl');
								break;

							case 'php':
								$shBasicConfig['brushes'] = array('Php');
								break;

							case 'ps':
							case 'powershell':
								$shBasicConfig['brushes'] = array('PowerShell');
								break;

							case 'py':
							case 'python':
								$shBasicConfig['brushes'] = array('Python');
								break;

							case 'rails':
							case 'rb':
							case 'ror':
							case 'ruby':
								$shBasicConfig['brushes'] = array('Ruby');
								break;

							case 'sass':
							case 'scss':
								$shBasicConfig['brushes'] = array('Sass');
								break;

							case 'scala':
								$shBasicConfig['brushes'] = array('Scala');
								break;

							case 'sql':
								$shBasicConfig['brushes'] = array('Sql');
								break;

							case 'ts':
							case 'typoscript':
								$shBasicConfig['brushes'] = array('Typoscript');
								break;

							case 'vb':
							case 'vbnet':
								$shBasicConfig['brushes'] = array('Vb');
								break;

							case 'xml':
							case 'xhtml':
							case 'xslt':
							case 'html':
							case 'xhtml':
								$shBasicConfig['brushes'] = array('Xml');
								break;

							default:
								$shBasicConfig['brushes'] = array('Plain');
								break;
						}
					}

					// Add SyntaxHighlighter core style
					tx_cpsdevlib_extmgm::addCssFile($shBasicConfig['baseUrl'] . $shBasicConfig['styles'] . 'shCore . css', 'tx_cpsdevlib_debug_shcorecss');

					// Add SyntaxHighlighter theme
					tx_cpsdevlib_extmgm::addCssFile($shBasicConfig['baseUrl'] . $shBasicConfig['styles'] . 'shTheme' . $shBasicConfig['theme'] . '.css', 'tx_cpsdevlib_debug_shtheme' . $shBasicConfig['theme'] . 'css');

					// Add SyntaxHighlighter core javascript
					tx_cpsdevlib_extmgm::addJavascriptFile($shBasicConfig['baseUrl'] . $shBasicConfig['scripts'] . 'shCore.js', 'tx_cpsdevlib_debug_shcorejs');

					// Add brushes
					foreach ($shBasicConfig['brushes'] as $brush) {
						tx_cpsdevlib_extmgm::addJavascriptFile($shBasicConfig['baseUrl'] . $shBasicConfig['scripts'] . 'shBrush' . $brush . '.js', 'tx_cpsdevlib_debug_shbrush' . strtolower($brush) . 'js');
					}

					// Run SyntaxHighlighter
					tx_cpsdevlib_extmgm::addJavascriptInline('SyntaxHighlighter.all();', 'tx_cpsdevlib_debug_shrun');

					$result = LF . '<pre class="brush: ' . htmlspecialchars($codeClass) . '"' . (($blockTitle) ? ' title="' . htmlspecialchars($blockTitle) . '"' : '') . '>' . LF . htmlspecialchars($result) . LF . '</pre>';

				} else { // Alternative wrapping method without SyntaxHighlighter
					if ($additionalWrap) $result = str_replace('|', LF . $result . LF, $additionalWrap);
				}

			}
		}
		return $result;
	}
}

?>