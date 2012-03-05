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
 * @subpackage Utility
 */
class Tx_Fed_Utility_Path implements t3lib_Singleton {

	/**
	 * Translates an array of paths or single path into absolute paths/path
	 *
	 * @param mixed $path
	 * @return mixed
	 */
	public static function translatePath($path) {
		if (is_array($path) == FALSE) {
			return t3lib_div::getFileAbsFileName($path);
		} else {
			foreach ($path as $key=>$subPath) {
				$path[$key] = self::translatePath($subPath);
			}
		}
		return $path;
	}


	/**
	 * Get a list of files (recursively) located in and below $basePath
	 *
	 * @param string $basePath
	 * @param boolean $recursive
	 * @param string $appendBasePath
	 * @return array
	 */
	public static function getFiles($basePath, $recursive=FALSE, $appendBasePath=NULL) {
		$files = scandir($basePath . $appendBasePath);
		$addFiles = array();
		foreach ($files as $file) {
			if (substr($file, 0, 1) === '.') {
				continue;
			} else if (is_dir($basePath . $appendBasePath . $file) && $recursive) {
				foreach (self::getFiles($basePath, $recursive, $appendBasePath . $file . DIRECTORY_SEPARATOR) as $addFile) {
					$addFiles[] = $appendBasePath . $addFile;
				}
			} else if (is_file($basePath . $appendBasePath . $file)) {
				$addFiles[] = $appendBasePath . $file;
			}
		}
		sort($addFiles);
		return (array) $addFiles;
	}
}

?>