<?php
namespace SJBR\StaticInfoTables\Cache;
/***************************************************************
 *  Copyright notice
 *  (c) 2012 Georg Ringer <typo3@ringerge.org>
 *  (c) 2013 Stanislas Rolland <typo3(arobas)sjbr.ca>
 *  All rights reserved
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Class Cache Manager
 *
 */
class ClassCacheManager {

	const CACHE_FILE_LOCATION = 'typo3temp/Cache/Code/cache_phpcode/StaticInfoTables/';

	/**
	 * Builds the proxy files
	 *
	 * @return array information for the autoloader
	 * @throws Exception
	 */
	public function build() {
		$cacheEntries = array();

		$extensibleExtensions = $this->getExtensibleExtensions();
		foreach ($extensibleExtensions as $key => $extensionsWithThisClass) {
			$extendingClassFound = FALSE;

			// Get the file from static_info_tables itself, this needs to be loaded as first
			$path = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('static_info_tables') . 'Classes/' . $key . '.php';
			if (!is_file($path)) {
				throw new Exception('given file "' . $path . '" does not exist');
			}
			$code = $this->parseSingleFile($path, FALSE);

			// Get the files from all other extensions
			foreach ($extensionsWithThisClass as $extension => $value) {
				$path = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extension) . 'Classes/' . $key . '.php';
				if (is_file($path)) {
					$extendingClassFound = TRUE;
					$code .= $this->parseSingleFile($path);
				}
			}

			// If an extending class is found, the file is written and added to the autoloader info
			if ($extendingClassFound) {
				$cacheIdentifier = 'SJBR\StaticInfoTables\\' . str_replace('/', '\\', $key);
				try {
					$cacheEntries[$cacheIdentifier] = $this->writeFile($code, 'staticInfoTables_' . $key);
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			}
		}
		return $cacheEntries;
	}

	/**
	 * Get all loaded extensions which try to extend EXT:static_info_tables
	 *
	 * @return array
	 */
	protected function getExtensibleExtensions() {
		$loadedExtensions = array_unique(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::getLoadedExtensionListArray());

		// Get the extensions which want to extend static_info_tables
		$extensibleExtensions = array();
		foreach ($loadedExtensions as $extensionKey) {
			$extensionInfoFile = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($extensionKey) . 'Configuration/DomainModelExtension/StaticInfoTables.txt';
			if (file_exists($extensionInfoFile)) {
				$info = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl($extensionInfoFile);
				$classes = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(LF, $info, TRUE);
				foreach ($classes as $class) {
					$extensibleExtensions[$class][$extensionKey] = 1;
				}
			}
		}
		return $extensibleExtensions;
	}

	/**
	 * Write the proxy file
	 *
	 * @param string $content
	 * @param string $identifier identifier of the file
	 * @return string path of the written file
	 */
	protected function writeFile($content, $identifier) {
		$path = PATH_site . self::CACHE_FILE_LOCATION;
		if (!is_dir($path)) {
			\TYPO3\CMS\Core\Utility\GeneralUtility::mkdir_deep(PATH_site, self::CACHE_FILE_LOCATION);
		}

		$content = '<?php ' . LF . $content . LF . '}' . LF . '?>';

		$path .= $this->generateFileNameFromIdentifier($identifier);

		$success = \TYPO3\CMS\Core\Utility\GeneralUtility::writeFile($path, $content);
		if (!$success) {
			throw new RuntimeException('File "' . $path . '" could not be written');
		}
		return $path;
	}

	/**
	 * Generate cache file name
	 *
	 * @param string $identifier identifier
	 * @return string
	 */
	protected function generateFileNameFromIdentifier($identifier) {
		if (!is_string($identifier) || empty($identifier)) {
			throw new InvalidArgumentException('Given identifier is either not a string or empty');
		}

		$result = str_replace('/', '_', $identifier) . '.php';
		$result = ucfirst($result);

		return $result;
	}

	/**
	 * Parse a single file and does some magic
	 * - Remove the <?php tags
	 * - Remove the class definition (if set)
	 *
	 * @param string $filePath path of the file
	 * @param boolean $removeClassDefinition If class definition should be removed
	 * @return string path of the saved file
	 * @throws Exception
	 * @throws InvalidArgumentException
	 */
	public function parseSingleFile($filePath, $removeClassDefinition = TRUE) {
		if (!is_file($filePath)) {
			throw new InvalidArgumentException(sprintf('File "%s" could not be found', $filePath));
		}
		$code = \TYPO3\CMS\Core\Utility\GeneralUtility::getUrl($filePath);
		return $this->changeCode($code, $filePath, $removeClassDefinition);
	}

	/**
	 * @param string $code
	 * @param string $filePath
	 * @param boolean $removeClassDefinition
	 * @param boolean $renderPartialInfo
	 * @return string
	 * @throws Exception
	 */
	protected function changeCode($code, $filePath, $removeClassDefinition = TRUE, $renderPartialInfo = TRUE) {
		if (empty($code)) {
			throw new InvalidArgumentException(sprintf('File "%s" could not be fetched or is empty', $filePath));
		}
		$code = trim($code);
		$code = str_replace(array('<?php', '?>'), '', $code);
		$code = trim($code);

		// Remove everything before 'class ', including namespaces,
		// comments and require-statements.
		if ($removeClassDefinition) {
			$pos = strpos($code, 'class ');
			$pos2 = strpos($code, '{', $pos);

			$code = substr($code, $pos2 + 1);
		}

		$code = trim($code);

		// Add some information for each partial
		if ($renderPartialInfo) {
			$code = $this->getPartialInfo($filePath) . $code;
		}

		// Remove last }
		$pos = strrpos($code, '}');
		$code = substr($code, 0, $pos);
		$code = trim($code);
		return $code . LF . LF;
	}

	protected function getPartialInfo($filePath) {
		return '/*' . str_repeat('*', 70) . LF .
			' * this is partial from: ' . $filePath . LF . str_repeat('*', 70) . '*/' . LF . TAB;
	}

	/**
	 * Clear the class cache
	 *
	 * @return void
	 */
	public function clear() {
		$path = PATH_site . self::CACHE_FILE_LOCATION;
		if (is_dir($path)) {
			\TYPO3\CMS\Core\Utility\GeneralUtility::rmdir($path, TRUE);
		}
		if (isset($GLOBALS['BE_USER'])) {
			$GLOBALS['BE_USER']->writelog(3, 1, 0, 0, '[StaticInfoTables]: User %s has cleared the class cache', array($GLOBALS['BE_USER']->user['username']));
		}
	}

	/**
	 * Rebuild the class cache
	 *
	 * @return void
	 */
	public function reBuild() {
		$this->clear();
		$this->build();
	}

	/**
	 * Load the cached classes
	 *
	 * @return void
	 */
	public function load() {
		$path = PATH_site . self::CACHE_FILE_LOCATION;
		if (!is_dir($path)) {
			$this->build();
		}
		$classFiles = \TYPO3\CMS\Core\Utility\GeneralUtility::getFilesInDir($path, 'php', TRUE);
		foreach ($classFiles as $classFile) {
			require_once($classFile);
		}		
	}
}
?>