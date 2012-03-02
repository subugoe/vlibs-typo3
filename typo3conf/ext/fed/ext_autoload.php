<?php
$classPrefix = 'Tx_Fed_';
$classPath = t3lib_extMgm::extPath('fed', 'Classes/');
if ($GLOBALS['autoload_cache'][$classPath]) {
	return $GLOBALS['autoload_cache'][$classPath];
}
$files = t3lib_div::getAllFilesAndFoldersInPath(array(), $classPath);
$autoloadRegistry = array();
foreach ($files as $filename) {
	$relativeName = substr($filename, strlen($classPath));
	$relativeName = substr($relativeName, 0, -4);
	$className = $classPrefix . str_replace('/', '_', $relativeName);
	$key = strtolower($className);
	$autoloadRegistry[$key] = $filename;
}
$GLOBALS['autoload_cache'][$classPath] = $autoloadRegistry;
return $autoloadRegistry;
?>