<?php



/*
** The function:
*/
 
function PostRequest($url, $referer, $_data) {
 
    // convert variables array to string:
    $data = array();    
    while(list($n,$v) = each($_data)){
        $data[] = "$n=$v";
    }    
    $data = implode('&', $data);
    // format --> test1=a&test2=b etc.
 
    // parse the given URL
    $url = parse_url($url);
    if ($url['scheme'] != 'http') { 
        die('Only HTTP request are supported !');
    }
 
    // extract host and path:
    $host = $url['host'];
    $path = $url['path'];
 
    // open a socket connection on port 80
    $fp = fsockopen($host, 80);
 
    // send the request headers:
    fputs($fp, "POST $path HTTP/1.1\r\n");
    fputs($fp, "Host: $host\r\n");
    fputs($fp, "Referer: $referer\r\n");
    fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
    fputs($fp, "Content-length: ". strlen($data) ."\r\n");
    fputs($fp, "Connection: close\r\n\r\n");
    fputs($fp, $data);
 
    $result = ''; 
    while(!feof($fp)) {
        // receive the results of the request
        $result .= fgets($fp, 128);
    }
 
    // close the socket connection:
    fclose($fp);
 
    // split the result header from the content
    $result = explode("\r\n\r\n", $result, 2);
 
    $header = isset($result[0]) ? $result[0] : '';
    $content = isset($result[1]) ? $result[1] : '';
 
    // return as array:
    return array($header, $content);
}
 
 
 
/*
** The example:
*/
 
// submit these variables to the server:
$name = $_GET["name"];

$data = array(
    'searchTerms' => $name,
//    'okay' => 'yes',
 //   'number' => 2
);
 
// send a request to example.com (referer = jonasjohn.de)
list($header, $content) = PostRequest(
    "http://genealogy.math.uni-bielefeld.de/genealogy/quickSearch.php",
    "http:vifamath.de",
    $data
);
 
// print the result of the whole request:
     
    $start = strpos($content, '<table');
    if (strpos($content, 'Your search has found') < 0){
	$content = 'No item found';
    } else {
    	$end = strpos($content, '</table>');
    	$offset = $end - $start + 8;
   	$content = substr($content, $start, $offset);
    	$content = str_replace('<a href="id.php?id=', '<a class="external-link" target="_blank" href="http://genealogy.math.uni-bielefeld.de/genealogy/id.php?id=', $content);
#	$content0 = '<div class="genMark"><a href="http://genealogy.mathematik.uni-bielefeld.de">';
#    	$content0 .= '<img src="fileadmin/images/tree-small.gif" alt="The Mathematics Genealogy Project Logo" title="The Mathematics Genealogy Project">';
#    	$content0 .= '</a></div>';
    	//$content0 = str_replace('id.php?id=', 'http://genealogy.math.uni-bielefeld.de/genealogy/id.php?id=', $content0);
#   	 $content = $content0 .'<div class="genText">' . $content . '</div>'; 	
	$content = '<div class="genText">' . $content . '</div>';
    }
 
// print $header; --> prints the headers

echo $content;


?>

