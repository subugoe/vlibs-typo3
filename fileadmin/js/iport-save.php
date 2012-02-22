<?php

$iportURL = 'http://134.76.176.48:5010/iport';
$params = $_GET["params"];
//echo "QUERY: " . $iportURL . '?'. $_SERVER["QUERY_STRING"];
$result = @file_get_contents($iportURL . '?' . $params);
//echo $result;
?>


