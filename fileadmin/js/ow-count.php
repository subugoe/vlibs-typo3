<?php


$owBaseURL = 'http://owpdb.mfo.de';
$owURL = $owBaseURL . '/vifa_search';
$owSearchParam = 'term=';

if (isset($_GET["person"])){
$params = $_GET["person"];
$result = "";
//$params = "hilbert";
//echo "searchURL= " . $owURL. '?'. $owSearchParam . $params;
//echo "QUERY: " . $iportURL . '?'. $_SERVER["QUERY_STRING"];
$xml = simplexml_load_file($owURL. '?'. $owSearchParam . $params);

$count = count($xml->result);
if ($count > 7 ) {
$result = '<div id="owlink"><a href="src"' . $owBaseURL . '/search?term=' . $params . '">Weitere Photos >></a><div>' ;
}


echo $result;


?>
