<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

t3lib_extMgm::addPItoST43($_EXTKEY,"pi1/class.tx_bekodebugster_pi1.php","_pi1","",1);

include_once (t3lib_extMgm::extPath($_EXTKEY) . 'pi1/class.tx_bekodebugster_pi1.php');
?>