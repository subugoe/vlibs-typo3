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
 * Before you continue you should read about SASS CSS compilation. SASS is a
 * format for your CSS files which allows variables, inclusions, minification,
 * functions etc. in your "CSS" files. This ViewHelper compiles those definitions
 * on-the-fly and caches as necessary.
 *
 * ViewHelper integration with Compass SASS compiled CSS resources -
 * for production apps, copy the resulting file as the application stylesheet
 * (or increase TTL to a -ver- high value). Beware that this can cause serious
 * errors on your server if the target path is not writable by the server
 * process, so be careful when choosing your path.
 *
 * Rendering requires the "compass" command ("gem install compass" shell command
 * installs the package). Depends on Ruby GEM.
 *
 * You can set ttl=0 for intensive CSS development but you should of course NOT
 * allow low values for production environments.
 *
 * Usage:
 *
 * <fed:sass mode="config" ttl="300">
 *     // contents of your content.rb file
 *	   // configuration includes target paths
 *     // ttl attribute is only respected for this tag due to section=config
 *     // don't worry about indentation; lines are atuomatically trimmed.
 *     # path is relative to the specified target path
 *     $ext_path = "../../ExtJS/"
 *     sass_path = File.dirname(__FILE__)
 *     css_path = File.join(sass_path, "..", "css")
 *     output_style = :compressed
 *     load File.join(File.dirname(__FILE__), $ext_path, 'resources', 'themes')
 * </fed:sass>
 *
 * <fed:sass mode="style">
 *     // SASS CSS rules go here. You can repeat this as many times as you like.
 * </fed:sass>
 * <fed:sass>
 *     // another style definition; mode is not necessary since "style" is default
 * </fed:sass>
 *
 * // after all your rules run this to issue the compass command and create files
 * // based on configuration.
 * <fed:sass mode="compile" />
 *
 * @package TYPO3
 * @subpackage Fluid
 * @version
 */
class Tx_Fed_ViewHelpers_SassViewHelper extends Tx_Fed_Core_ViewHelper_AbstractViewHelper {

	/**
	 * @var Tx_Fed_Utility_SASS
	 */
	protected $sass;

	/**
	 * @param Tx_Fed_Utility_SASS $sass
	 */
	public function injectSass(Tx_Fed_Utility_SASS $sass) {
		$this->sass = $sass;
	}

	/**
	 * Initialize arguments
	 */
	public function initializeArguments() {
		$this->registerArgument('mode', 'string', 'Type of section: config, style, compile', FALSE, 'style');
		$this->registerArgument('path', 'string', 'Base path - location from which to run the "compass" compile command. For ExtJS itself, this path is the ExtJS main folder', TRUE);
		$this->registerArgument('target', 'string', 'WARNING: overwrites target path! For ExtJS itself: this path is "resources/sass/" below the ExtJS main folder AND MUST BE SET, otherwise default becomes "." and compile will break!', FALSE, NULL);
		$this->registerArgument('ttl', 'int', 'TTL of generated files. You can set this to zero for development, something like 300 for staging and a very high value for production (altough you should copy the result file instead). Default is 60 to avoid hammering', FALSE, 60);
	}

	/**
	 * Render
	 *
	 * @return string
	 */
	public function render() {
		$content = $this->renderChildren();
		if ($this->arguments['path']) {
			$GLOBALS['fedSASS']['path'] = $this->arguments['path'];
		}
		if ($this->arguments['ttl']) {
			$GLOBALS['fedSASS']['ttl'] = $this->arguments['ttl'];
		}
		if ($this->arguments['target']) {
			$GLOBALS['fedSASS']['target'] = $this->arguments['target'];
		}
		if ($this->arguments['mode'] === 'config') {
			$GLOBALS['fedSASS']['config'] = $content;
		} else if ($this->arguments['mode'] === 'style') {
			if (is_array($GLOBALS['fedSASS']['styles']) === FALSE) {
				$GLOBALS['fedSASS']['styles'] = array();
			}
			array_push($GLOBALS['fedSASS']['styles'], $content);
		} else if ($this->arguments['mode'] === 'compile') {
			if (strlen(trim($GLOBALS['fedSASS']['target'])) === 0) {
				$GLOBALS['fedSASS']['target'] = '.';
			}
			// if there was a configuration specified we overwrite the one that exists, so be careful
			if (strlen(trim($GLOBALS['fedSASS']['config'])) > 0) {
				$this->writeConfig();
			}
			// if styles where collected, write them out
			if (count($GLOBALS['fedSASS']['styles']) > 0) {
				$this->writeStyleRuleFiles();
			}
			if (filemtime($GLOBALS['fedSASS']['path']) < (time()-$GLOBALS['fedSASS']['ttl'])) {
				touch($GLOBALS['fedSASS']['path']);
				return $this->compileStylesheet();
			}
		}
		return NULL;
	}

	protected function getPath() {
		if (is_dir($GLOBALS['fedSASS']['path'])) {
			return $GLOBALS['fedSASS']['path'];
		} else {
			$name = md5(implode($GLOBALS['fedSASS']));
			mkdir($name, 0775);
			return PATH_site . "typo3temp/{$name}";
		}
	}

	/**
	 * Write SASS configuration file to target path
	 * @return void
	 */
	protected function writeConfig() {
		$config = $GLOBALS['fedSASS']['config'];
		$path = $this->getPath();
		$configPath = "{$path}/config.rb";
		file_put_contents($configPath, $config);
	}

	/**
	 * Write SASS style rules based on definitions
	 * @return void
	 */
	protected function writeStyleRuleFiles() {
		$string = implode($GLOBALS['fedSASS']['styles'], LF);
		$name = md5($string);
		$path = $this->getPath();
		$targetFile = "{$path}/{$name}.scss";
		if (file_exists($targetFile) === FALSE || (filemtime($targetFile) < (time()-$GLOBALS['fedSASS']['ttl']))) {
			file_put_contents($targetFile, $string);
		}
	}

	/**
	 * Compiles the registered styles into target path. Returns output of
	 * command for debugging purposes
	 * @return string
	 */
	protected function compileStylesheet() {
		return $this->sass->compile($this->getPath(), $GLOBALS['fedSASS']['target']);
	}
}



?>
