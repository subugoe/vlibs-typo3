<?php

$owBaseURL = 'http://owpdb.mfo.de';
$owURL = $owBaseURL . '/vifa_search';
$owSearchParam = 'term=';

if (isset($_GET["person"])){
$params = $_GET["person"];
//echo "searchURL= " . $owURL. '?'. $owSearchParam . $params;
//echo "QUERY: " . $iportURL . '?'. $_SERVER["QUERY_STRING"];
$xml = simplexml_load_file($owURL. '?'. $owSearchParam . $params);

$count = count($xml->result);
$result = '';

if ($count > 0 ) {
$result = '<span id="ow">';
$photo1 = $owBaseURL . (string)$xml->result[0]->thumbnail;
$result = $result . wrap_photo($photo1);


if ($count > 1 ) {
$photo2 = $owBaseURL . (string)$xml->result[1]->thumbnail;
$result = $result . wrap_photo($photo2);
}

if ($count > 2 ) {
$photo3 = $owBaseURL . (string)$xml->result[2]->thumbnail;
$result = $result . wrap_photo($photo3);
}

$result = $result . '</span><a  class="external-link-new-window" target="_blank" href="http://owpdb.mfo.de/search?term=' . $params . '">  more in OW Collection...</a>';
}
else {
 	$result="No photos found";
}

echo $result;
}


/**
* wraps image-location in html-img-tag
*
* @param string $img-location
* @return string $tag
*/

function wrap_photo($img) {

return '<img class="ow" alt="photo: ' . $_GET["person"] . '" src="' . $img . '"/>'; 

}

?>
