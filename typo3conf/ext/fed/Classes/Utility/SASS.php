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
 * Compiles SASS styles. That's all.
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage Utility
 */
class Tx_Fed_Utility_SASS implements t3lib_Singleton {

	/**
	 * Location or command name for "compass"
	 * @var string
	 */
	static $COMPASS_COMMAND = 'compass';

	/**
	 * Default location of compile targets. This default suits ExtJS's SASS
	 * implementation; if you need to compile a different path see the compile
	 * function in this class.
	 * @var string
	 */
	static $DEFAULT_COMPILE_TARGET = 'resources/sass';

	/**
	 * Set the command name (or full path of command) of the "compass" command
	 * @param type $command
	 * @api
	 */
	public function setCompassCommand($command) {
		self::$COMPASS_COMMAND = $command;
	}

	/**
	 * Get the name/binary path of the "compass" command
	 * @return type
	 * @api
	 */
	public function getCompassCommand() {
		return self::$COMPASS_COMMAND;
	}

	/**
	 * Compiles SASS styles. Changes local path to $basePath before running the
	 * compile command - so specify $basePath if your compile configuration uses
	 * relative paths. Returns string output of "compass" command.
	 *
	 * @param string $basePath Path to the execution root for the "compass" command. Must be full path to SASS resource dir if you set $compileTargetPath to FALSE
	 * @param string $compileTargetPath if FALSE (type-strict comparison) assumes "." is the compileTargetPath, meaning all SASS configuration paths are resolved relative to $basePath
	 * @return string
	 * @api
	 */
	public function compile($basePath, $compileTargetPath=NULL) {
		if ($compileTargetPath === FALSE) {
			$compileTargetPath = '.';
		} else if ($compileTargetPath === NULL) {
			$compileTargetPath = self::$DEFAULT_COMPILE_TARGET;
		}
		$command = "cd " . escapeshellarg($basePath) . " && " . self::$COMPASS_COMMAND . " " . escapeshellarg($compileTargetPath);
		return shell_exec($command);
	}

}

?>