<?php

$owBaseURL = 'http://owpdb.mfo.de';
$owURL = $owBaseURL . '/vifa_search';
$owSearchParam = 'term=';

if (isset($_GET["person"])){
$params = $_GET["person"];
//echo "searchURL= " . $owURL. '?'. $owSearchParam . $params;
//echo "QUERY: " . $iportURL . '?'. $_SERVER["QUERY_STRING"];
$xml = simplexml_load_file($owURL. '?'. $owSearchParam . $params);


if (count($xml->result) > 2 ) {
$photo1 = $owBaseURL . (string)$xml->result[0]->thumbnail;
$photo2 = $owBaseURL . (string)$xml->result[1]->thumbnail;
$photo3 = $owBaseURL . $xml->result[2]->thumbnail;

echo $photo1;
//echo '<span id="ow">' . wrap_photo($photo1) . wrap_photo($photo2) . wrap_photo($photo3) . '</span>';
}
else {
	echo "No photos found";
}
}


/**
* wraps image-location in html-img-tag
*
* @param string $img-location
* @return string $tag
*/

function wrap_photo($img) {

return '<img alt="photo" src="' . $img . '"/>'; 

}

?>
