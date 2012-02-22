<?php

$iportURL = 'http://134.76.176.48:5010/iport';


if (isset($_GET["record"])){
$params = $_GET["record"].'&frame=VifaXML';
//echo "QUERY: " . $iportURL . '?'. $_SERVER["QUERY_STRING"];
$result = @file_get_contents($iportURL . '?' . $params);

//parse record

$start = strpos($result, '<table id="recordlisttable"');
$stop = strpos($result, '<!-- goto form -->');
$offset = $stop - $start;
$record = substr($result, $start, $offset);
$record = str_replace('id="recordlisttable"', 'class="fullRecord"', $record);


//parse menu
$menu = substr($result, 0, $start);
$saveStart = strpos($menu, '<!-- save -->');
$saveStop = strpos($menu, '<!-- end order-copy -->');
if (!$saveStop ) { 
	$saveStop = strpos($menu, '<!-- end order-doc -->');
}
if (!$saveStop ) {
	$saveStop = strpos($menu, '<!-- end save -->');
}
$saveOffs = $saveStop - $saveStart;
$menu = substr($menu, $saveStart, $saveOffs);


$close = '<a class="close" href="#">X</a>';
$menu = $close . $menu;

//clear record
//$record = str_replace('<!-- end save -->', '', $record);
$record = str_replace('<br>', '<br />', $record);

echo  '<div class="record">' . $menu . "<br />\n" .$record;


//echo '<div class="recordWrap">' . $menu . "<br />\n" .$record . '</div>';
}
else if (isset($_GET["save"])){
$params = $_GET["save"];
$result = @file_get_contents($iportURL . '?' . $params);
//$result = str_replace('bookshelf_plus.png', 'bookshelf_ok.png', $result);
// error check
}
else if (isset($_GET["download"])) {
$params = $_GET["download"];
$result = @file_get_contents($iportURL . '?' . $params);
echo $result;


}
?>


