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

$form = <<<XML
<span id="cat">Suche</span>
 <form action="http://134.76.176.48/thematische-suche/msc/" method="get" name="main" id="searchMsc">

<input name="dbgroup" value="MSC" type="hidden">
   <input name="request" value="wait" type="hidden">
   <input name="domain" value="Dome" type="hidden">

   <input name="searchField" value="any" type="hidden">
   <input name="query" maxlength="256" id="prefix" size="28" value="" type="text">

   <input name="boolean" id="boolean" value="and" type="hidden">
   <input name="searchField" id="cd" value="cd" type="hidden">
   <input name="query" id="msc" value="" type="hidden">
   <input name="numrec" value="10" type="hidden">

   <input value="Start" id="iport_search_button" type="submit">

</form>
XML;

;
if (!isset($_GET["id"])){
$top = <<<XML
  
  <div id="tree">
      <li id="level0">
         <ul>
              <li id="nGeneral"><a href="#?id=10?nGeneral">General</a></li>
              <li id="nHistory"><a href="#?id=10?nHisotry"> History and Foundations</a></li>
              <li id="nAlgebra"><a href="#?nAlgebra"> Algebra</a></li>
     
              <li class="active" id="nAnalysis"><a href="index.php?id=10#"> Analysis</a></li>
              <li id="nGeometry"><a href="index.php?id=10#">Geometry and Topology</a></li>
              <li id="nStatistics"><a href="index.php?id=10#"> Probability and Statistics</a></li>
              <li id="nNumerical"><a href="index.php?id=10#"> Numerical Analysis</a></li>
              <li id="nComputer"><a href="index.php?id=10#">Computer Science</a></li>
     
              <li id="nApplications"><a href="index.php?id=10#">Applications</a></li>
              <li id="nEducation"><a href="index.php?id=10#">Education</a></li>
         </ul>
      </li>
      </div>
XML;

$newxml = new SimpleXMLElement($top);
      

} else {
$id = $_GET["id"];
//echo "id: " . $id;
	//if (file_exists('browsing/testnodes.xml')) {
    
    $xml = simplexml_load_file('js-nodes.txt');
	//$newnode = $xml->nodes->ul[0]->span; 	

$nodes = $xml->xpath('//li[@id="'.$id.'"]');
//echo "node: " . $nodes[0]['id'];


$parent = $xml->xpath('//li[@id="'.$id.'"]/../..');

$newxml = new SimpleXMLElement('<div></div>');
$newxml->addAttribute('id', 'tree');
$current = $newxml->addChild('li', substr($id,1).' ' . (String)$nodes[0]);

$ul = $current->addChild('ul');
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
	$a_last = $li->addChild('a', ' ('. (String) $node->span.' Docs->)');
	$a_last->addAttribute('class','search');
	$a_last->addAttribute('href', 'recherche/thematischer-katalog/msc/?amp;request=wait&amp;domain=Dome&amp;dbgroup=MSC&amp;searchField=cd&amp;numrec=10&amp;frame=VifaXML&query='.$msc);


  }
}
if ($parent == null){
echo '<a href="http://134.76.160.80/math/fileadmin/browsing/php_browsing.php">Top</a><br />';
} else {echo '<a href="http://134.76.160.80/math/fileadmin/browsing/php_browsing.php">Top</a>'. '  '.'<a href="http://134.76.160.80/math/fileadmin/browsing/php_browsing.php?id='.$parent[0]['id'].'">1 h&ouml;her</a><br />';
 }

}
echo $form;
echo $newxml->asXML();

#echo $parent[0]['id'];



?>
