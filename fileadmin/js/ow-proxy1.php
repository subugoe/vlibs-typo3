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
$result = '<ul id="mycarousel" class="jcarousel-skin-tango">';
$showcount = 0;


  if ($count > 0) {
	while (($showcount < $count) and ($showcount < 7)) {
		$photo = $owBaseURL . (string)$xml->result[$showcount]->thumbnail;
		$result = $result . wrap_photo($photo);
		$showcount++;
	} 
  }

  else {
 	$result="No photos found";
  }
  $result .= '</ul>';
  echo $result;
}



/**
* wraps image-location in html-img-tag
*
* @param string $img-location
* @return string $tag
*/

function wrap_photo($img) {

return '<li><img alt="photo"  src="' . $img . '"></img></li>'; 

}

?>
