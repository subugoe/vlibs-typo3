<?php

//for get parameter on command line
//usage: php browsing.php id=NNNNN
/*if ($argv)
   for ($i=1;$i<count($argv);$i++)
   {
       $it = split("=",$argv[$i]);
       $_GET[$it[0]] = $it[1];
   }
*/



$id = $_GET["id"];
$path = $_GET["path"];
//echo "id: " . $id;
	//if (file_exists('browsing/testnodes.xml')) {
    
    $xml = simplexml_load_file('js-nodes.txt');
	//$newnode = $xml->nodes->ul[0]->span; 	

$nodes = $xml->xpath('//li[@id="'.$id.'"]');
//echo "node: " . $nodes[0]['id'];

$newxml = new SimpleXMLElement('<li></li>');
$ul = $newxml->addChild('ul');
foreach ($nodes[0]->ul as $sub){


  foreach ($sub->li as $node){

	//echo "I am: " . $node->span;
	$li = $ul->addChild('li');
	$li->addAttribute('id', $node['id']);
	$msc = substr((String) $node['id'],1);
	//$a_first->addAttribute('id', (String) $node->a);

	//$dd = $newxml->addChild('dd');


	$a_first = $li->addChild('a', $msc . ' '.(String) $node);
	$a_first->addAttribute('href', '#');
	//$a_first->addAttribute('class','plus');
	
	
	//$span = $a_first->addChild('span', (String) $node->span);
	$a_last = $li->addChild('a', ' ('. (String) $node->span.'Docs->)');
	$a_last->addAttribute('class','search');
	$a_last->addAttribute('href', $path .'&amp;request=wait&amp;domain=Dome&amp;dbgroup=MSC&amp;searchField=cd&amp;numrec=10&amp;frame=VifaXML&mscquery='.$msc);


  }
echo $newxml->asXML();

}


?>
