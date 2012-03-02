<?php
if ($_GET['fields']) {
	$mod = array(
		'qf' => implode('+', $_GET['fields'])
	);
}
$queryString = $_SERVER['QUERY_STRING'];
$new = "http://localhost:8080/solr/select/?" . modifyUrl($queryString, $mod);


if (isset($_GET['facets']) && is_array($_GET['facets'])) {
	foreach ($_GET['facets'] as $facet) {
		$new .= "&facet.field=" . $facet;
	}
}

readfile($new);
exit();

function modifyUrl($url, $mod) {
    $query = explode("&", $url);
    foreach ($query as $q) {
        list($key, $value) = explode("=", $q);
        if (array_key_exists($key, $mod)) {
            if ($mod[$key]) {
                $url = preg_replace('/'.$key.'='.$value.'/', $key.'='.$mod[$key], $url);
            } else {
                $url = preg_replace('/&?'.$key.'='.$value.'/', '', $url);
            }
        }
    }
    // add new data
    foreach ($mod as $key => $value) {
        if ($value && !preg_match('/'.$key.'=/', $url)) {
            $url .= "&" . $key.'='.$value;
        }
    }
    return $url;
}

?>