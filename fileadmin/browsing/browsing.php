<?php

//for get parameter on command line
//usage: php browsing.php id=NNNNN
if ($argv)
   for ($i=1;$i<count($argv);$i++)
   {
       $it = split("=",$argv[$i]);
       $_GET[$it[0]] = $it[1];
   }




$id = $_GET["id"];

	//if (file_exists('browsing/testnodes.xml')) {
    
    $xml = simplexml_load_file('math-nodes.txt');
	//$newnode = $xml->nodes->ul[0]->span; 	

foreach ($xml->xpath('//li') as $node) {
         
//$out = var_dump($xml);

//foreach ($xml->li->ul->li as $node) {
//echo $_GET["node"];

if ($node["id"] == $id) {
//$datei = fopen($node["id"],"w+");
//fwrite($datei, $node);
//fclose($datei);
//$out = new SimpleXMLElement($node);
//$out->asXML();
//echo $node->asXML();
echo $node->asXML();

}



}
    
/*} else {
    exit('Failed to open testnodex.xml.');
 }
  */  

?>