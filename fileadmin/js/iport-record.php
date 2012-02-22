<?php

$iportURL = "http://134.76.176.48:5010/iport";
//$params = $_GET["record"];
//echo "QUERY: " . $iportURL . '?'. $_SERVER["QUERY_STRING"];
$params = "previous=162&sessionid=012462796080&request=full_record&amp;position=1&domain=Dome";


$result = @file_get_contents($iportURL . '?' . $params);
print $result;

//parse record

$start = strpos($result, '<table id="recordlisttable"');
$stop = strpos($result, '<!-- goto form -->');
$offset = $stop - $start;
$record = substr($result, $start, $offset);
$record = str_replace("recordlisttable", "fullRecord", $record);

//parse menu
$menu = substr($result, 0, $start);
$saveStart = strpos($menu, '!-- download');
$saveStop = strpos($menu, '<!-- end save -->');
$saveOffs = $saveStop - $saveStart;
$menu = substr($menu, $saveStart, $saveOffs);

print "MENUOFFS: " . $saveOffs . "\n\n";


print "menuStart: " . $saveStart . " menuStop: " . $saveStop . " offset: " . $saveOffs . "\n\n";

//print "recordStart: " . $start . " recordStop: " . $stop . " offset: " . $offset. "\n\n";

print $menu;
//print "----------------------\n\n".$result;
?>


