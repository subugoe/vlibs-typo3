<?php

// Array indexes are 0-based, jCarousel positions are 1-based.
$first = max(0, intval($_GET['first']) - 1);
$last  = max($first + 1, intval($_GET['last']) - 1);

$length = $last - $first + 1;

$owBaseURL = 'http://owpdb.mfo.de';
$owURL = $owBaseURL . '/vifa_search';
$owSearchParam = 'term=';
$owSearchURL = 'http://owpdb.mfo.de/search?'. $owSearchParam;

if (isset($_GET["person"])){
$params = $_GET["person"];
//$params = "hilbert";
//echo "searchURL= " . $owURL. '?'. $owSearchParam . $params;
//echo "QUERY: " . $iportURL . '?'. $_SERVER["QUERY_STRING"];
$xml = simplexml_load_file($owURL. '?'. $owSearchParam . $params);

$count = count($xml->result);
$result = '<ul id="mycarousel" class="jcarousel-skin-tango">';
$showcount = 0;

$images = array();
$text = "No photos found.";
  if ($count > 0) {
	
        while (($showcount < $count) and ($showcount < 7)) {
		$img = $owBaseURL . (string)$xml->result[$showcount]->thumbnail;

		
		//extract person names
                $names = "";
                foreach ($xml->result[$showcount]->person as $name) {

                                $names .= ($names == "")?$name:'&' . $name;

                }
                $link = $owBaseURL . (string)$xml->result[$showcount]->detail;

                
		$images[$showcount] = '<link><![CDATA[<a target="_blank" href="' . $link .'"><img  src="' . $img . '" alt="photo: ' . $names . '" title="' . $names . '" height="110"/></a>]]></link>';
		//$images[$showcount] = '<img  src="'. $img . '" />';
                $showcount++;
       }
	if ($count > 7) {

                $images[$showcount] = '<link><![CDATA[<a target="_blank" href="'. $owSearchURL . $params .'">More...</a>]]></link>';
        }


  }
  else {
	$images[0] = "No photos found";
  }
}




$total    = count($images);
$selected = array_slice($images, $first, $length);


header('Content-Type: text/xml');

echo '<data>';

// Return total number of images so the callback
// can set the size of the carousel.
echo '  <total>' . $total . '</total>';

foreach ($selected as $img) {
     echo $img;
}

echo '</data>';

?>
