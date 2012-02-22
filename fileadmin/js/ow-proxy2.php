<?php

// Array indexes are 0-based, jCarousel positions are 1-based.
$first = max(0, intval($_GET['first']) - 1);
$last  = max($first + 1, intval($_GET['last']) - 1);

$length = $last - $first + 1;

$owBaseURL = 'http://owpdb.mfo.de';
$owURL = $owBaseURL . '/vifa_search';
$owSearchParam = 'term=';

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
		$cnames = count($xml->result[$showcount]->person);
		$found = false;
		$pos = 0;
		while (($pos < $cnames) && (!$found)) { 

			$found = (strpos($xml->result[$showcount]->person, $params) !== false);	
			$pos++;
		}
		$title = (string)$xml->result[$showcount]->person;
		$link = $owBaseURL . (string)$xml->result[$showcount]->detail;
                
		$images[$showcount] = '<link><![CDATA[<a target="_blank" href="' . $link .'"><img  src="' . $img . '" alt="photo: ' . $title . '" title="' . $title . '" height="110"/></a>]]></link>';
		//$images[$showcount] = '<img  src="'. $img . '" />';
                $showcount++;
       }

  }
  else {
	$images[0] = "No photos found";
  }
}



// ---

/*$images = array(
    'http://static.flickr.com/66/199481236_dc98b5abb3_s.jpg',
    'http://static.flickr.com/75/199481072_b4a0d09597_s.jpg',
    'http://static.flickr.com/57/199481087_33ae73a8de_s.jpg',
    'http://static.flickr.com/77/199481108_4359e6b971_s.jpg',
    'http://static.flickr.com/58/199481143_3c148d9dd3_s.jpg',
    'http://static.flickr.com/72/199481203_ad4cdcf109_s.jpg',
    'http://static.flickr.com/58/199481218_264ce20da0_s.jpg',
    'http://static.flickr.com/69/199481255_fdfe885f87_s.jpg',
    'http://static.flickr.com/60/199480111_87d4cb3e38_s.jpg',
    'http://static.flickr.com/70/229228324_08223b70fa_s.jpg',
);
*/

$total    = count($images);
$selected = array_slice($images, $first, $length);

// ---

header('Content-Type: text/xml');

echo '<data>';

// Return total number of images so the callback
// can set the size of the carousel.
echo '  <total>' . $total . '</total>';

foreach ($selected as $img) {
//    echo '  <image>' . $img . '</image>';
//    echo ' <img src="' + $img + '" height="110" alt="" title=""/>';	
     echo $img;
}

echo '</data>';

?>
