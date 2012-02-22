<?php

$baseURL = 'http://www-history.mcs.st-and.ac.uk/';
$pictureURL = 'Thumbnails/';
$bioURL = 'Biographies/';


if (isset($_GET["person"])){
$param = $_GET["person"];
	if (strpos($param, ',') > 0 )
	{
		$params = split(',', $param);
		foreach ($params as &$value) {
    			$value = ucfirst(trim($value));

		}
	$term=implode('_',$params);
	}
	elseif (strpos($param, ' ') > 0 ) {
			$params = split(' ', $param);
			foreach ($params as &$value) {
		  		  $value = ucfirst(trim($value));

			}

	$term=implode('_',$params);
	}
	else {
		$term = ucfirst($param);
	}

$biographie = @file_get_contents($baseURL . $bioURL. $term . '.html');


//$result = '<result><![CDATA[<div id="macTutResult">';
$result = '<div id="macTutResult">';


  if ($biographie != null) {
	$result .= '<img alt="photo" src="' . $baseURL . $pictureURL . $term . '.jpg"></img>';
	$result .= '<a href="' . $baseURL . $bioURL. $term . '.html">zur Biogrphie</a>'; 
  }

  else {
 	$result='No results found. <br />Try it with "surname firstname or surname, firstname" again!';
  }
//  $result .= '</div>]]></result>';
$result .= '</div>';
  echo $result;
}




?>
