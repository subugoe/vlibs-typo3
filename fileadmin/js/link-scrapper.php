<?php
//Fetches the mactutorial index and extracts the links 

$base_url='http://www-history.mcs.st-and.ac.uk';
$mactut_index = $base_url . '/Indexes/';
$userAgent='Mozilla/5.0 (X11; U; Linux i686; de; rv:1.9.0.4) Gecko/2008103100 SUSE/3.0.4-4.6 Firefox/3.0.4';


$index = array('A','B','C','D','E','F','G','H','IJ','K','L','M','N','O','PQ','R','S','T','UV','W','XYZ');


foreach ($index as $site) {

$target_url=$mactut_index . $site . '.html';


$ch = curl_init();
curl_setopt($ch, CURLOPT_USERAGENT, $userAgent);
curl_setopt($ch, CURLOPT_URL,$target_url);
curl_setopt($ch, CURLOPT_FAILONERROR, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);
curl_setopt($ch, CURLOPT_AUTOREFERER, false);
curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);



$html = curl_exec($ch);
if (!$html) {
	echo "cURL error number:" .curl_errno($ch) ;
	echo "cURL error:" . curl_error($ch);
	exit;
}

// parse the html into a DOMDocument
$dom = new DOMDocument();
@$dom->loadHTML($html);

$xpath = new DOMXPath($dom);
$hrefs = $xpath->evaluate("/html/body//a");
//$hrefs = $xpath->evaluate("/html/body//td");

$chars = array('..', ',', ' ');
$replace = array('', '', '_');

for ($i = 0; $i < $hrefs->length; $i++) {
	$href = $hrefs->item($i);
	$url = $href->getAttribute('href');
	$text = $href->nodeValue;
	if (strpos($url, 'Mathematicians')){
	//echo $url . ": " .  $text . "\n";
	echo '<a href="' . $base_url . str_replace($chars, $replace, $url) . '" target="_blank">' . $text . '</a>' . "\n";
	}
}
}
?>
