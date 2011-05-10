<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

t3lib_extMgm::addStaticFile('${extensionKey}', './configurations', '${extensionTitle}');     // ($extKey, $path, $title)

?>
