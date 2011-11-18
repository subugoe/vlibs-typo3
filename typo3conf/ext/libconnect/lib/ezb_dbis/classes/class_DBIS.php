<?php
/**
 * Doku:
 * @author niklas guenther
 *
 */
class DBIS{

	//such meta
	private $search_term;
	private $fachgebiet;
	private $colors = 255;
	private $ocolors = 40;
	private $lett = 'f';


	//private $fachliste_url = "http://rzblx10.uni-regensburg.de/dbinfo/fachliste.php?xmloutput=1&bib_id=sub_hh&";
	//private $dbliste_url = "http://rzblx10.uni-regensburg.de/dbinfo/dbliste.php?xmloutput=1&bib_id=sub_hh&";
	//private $db_detail_url = "http://rzblx10.uni-regensburg.de/dbinfo/detail.php?xmloutput=1&bib_id=sub_hh&colors=&ocolors=&";
	//private $db_detail_suche_url = "http://rzblx10.uni-regensburg.de/dbinfo/suche.php?xmloutput=1&bib_id=sub_hh&";

	public $all;
	public $top_five_dbs;

	function __construct(){

	}

	/**
	 * Alle Fachbereiche auslesen
	 * @return array()
	 */
	public function getFachliste(){
		$xml_categories = $this->getRequestFachliste('');
		$categories = array();

		foreach ($xml_categories->list_subjects_collections->list_subjects_collections_item AS $key => $value){
			$categories[(string)$value['notation']] = array('title' => (string)$value, 'id' => (string)$value['notation'], 'count' => (int)(string)$value['number'], 'lett' => (string)$value['lett'] );
		}
		return $categories;
	}

	/**
	 * Return the list Fachgebiet
	 * @param int $fachgebiet
	 * @return array
	 */
	public function getDbliste( $fachgebiet, $sort = "type" ){
		$sortlist = array();
		$bibid = $this->getBibid();
		$url = 'http://rzblx10.uni-regensburg.de/dbinfo/dbliste.php?xmloutput=1&bib_id='. $bibid .'&' . "colors={$this->colors}&ocolors={$this->ocolors}&sort=".$sort."&";

		if (is_numeric($fachgebiet)) {
			// notation ist eine id => dbis sammlung
			$url .= 'lett=f&gebiete=' . $fachgebiet;
		} else {
			// notation ist ein Zeichen => SUB sammlung
			$url .= 'lett=c&collid=' . $fachgebiet;
		}


		$xml_fachgebiet_db = simplexml_load_file( $url );

		$list = array(
			'top' => array(),
			'groups' => array(),
			'access_infos' => array()
		);
		foreach ($xml_fachgebiet_db->list_dbs->db_access_infos->db_access_info as $value){
			$id = (string) $value->attributes()->access_id;
			$list['access_infos'][$id] = array(
				'id' => $id,
				'title' => (string)$value->db_access,
				'description' => (string)$value->db_access_short_text,
				'dbs' => array()
			);
		}
		if($sort=='access'){
			$list['groups']=&$list['access_infos'];
		}else{

			foreach ($xml_fachgebiet_db->list_dbs->db_type_infos->db_type_info as $value){
				$id = (string) $value->attributes()->db_type_id;
				$title = (string) $value->db_type;
				$list['groups'][$id] = array(
					'id' => $id,
					'title' => $title,
					'dbs' => array()
				);
			}
		}

		foreach ($xml_fachgebiet_db->list_dbs->dbs as $dbs){

			foreach($dbs->db as $value) {

				$db = array (
					'id' => (int)$value['title_id'],
					'title' => (string)$value,
					'access_ref' => (string)$value['access_ref'],
					'access' => $list['access_infos'][(string)$value['access_ref']]['title'],
					'db_type_refs' => (string)$value['db_type_refs'],
					'top_db' => (int)$value['top_db'],
					'link' => 'http://rzblx10.uni-regensburg.de/dbinfo/detail.php?xmloutput=1&bib_id='. $bibid .'&colors=&ocolors=&'.  "lett={$this->lett}&titel_id={$value['title_id']}",
				);

				if ($db['top_db']) {
					$list['top'][]  = $db;
				} else {
					if($sort=="alph"){
						$list['groups']['Treffer']['dbs'][] = $db;
						$sortlist['Treffer']=$db['Treffer'];
					}elseif($sort=='access'){
						$list['access_infos'][$db['access_ref']]['dbs'][] = $db;
						$sortlist[$db['access']]=$db['access_ref'];
					}else{
						foreach(explode(' ', $db['db_type_refs']) as $ref) {
							$list['groups'][$ref]['dbs'][] = $db;
							$sortlist[$db['access']]=$db['access_ref'];
						}
					}
				}

			}

		}

		if(!empty($sortlist)&&($sort=='access')){
			natsort($sortlist);
			foreach($sortlist as $value =>$key){
				$list['alphasort'][$value]=$key;
			}
		}

		$list['alphasort']=$sortlist;

		return array( 'groups' => $access_infos, 'list' => $list);
	}

	/**
	 * get DB Detail Information
	 * @param int db id
	 */
	public function getDbDetails( $db_id){
		$bibid = $this->getBibid();
	
		$xml_db_details = simplexml_load_file( 'http://rzblx10.uni-regensburg.de/dbinfo/detail.php?xmloutput=1&bib_id='. $bibid ."&colors=&ocolors=&". "lett={$this->lett}&colors={$this->colors}&ocolors={$this->ocolors}&titel_id=" . $db_id );
		$details = array();

		if (!is_object($xml_db_details->details))
			return false;

		foreach ($xml_db_details->details->children() as $key => $value) {

			if( $key == 'titles' ){
				$details['else_titles'] = array();
				foreach ($value->children() as $key2 => $value2) {
					if (((string)$value2->attributes()->main) == 'Y') {
						$details['title'] = (string)$value2;
					} else {
						$details['else_titles'][] = (string)$value2;
					}
				}
			} else if( $key == 'db_access_info' ){
				$details['access_id'] = (string) $value->attributes()->access_id;
				$details['access_icon'] = (string) $value->attributes()->access_icon;
				$details['db_access'] = (string)$value->db_access;
				$details['db_access_short_text'] = (string)$value->db_access_short_text;

			} else if( $key == 'accesses' ){

				foreach($value->access as $access) {

					$main = (string)$access->attributes()->main;
					$type = (string)$access->attributes()->type;
					$href = (string)$access->attributes()->href;
					if ($main == 'Y') {
						$details['access'] = array (
							'main' => $main,
							'type' => $type,
							'href' => $href
						);
					} else {
						$details['access_lic'][] = array(
							'name' => (string)$access,
							'main' => $main,
							'type' => $type,
							'href' =>  $href
						);
					}
				}
			} else if( $key == 'subjects' ){
				foreach ($value->children() as $key2 => $value2) {
					$details['subjects'][] = (string) $value2;
				}
			} else if( $key == 'keywords' ){
				foreach ($value->children() as $key2 => $value2) {
					$details['keywords'][] = (string) $value2;
				}
				$details['keywords_join'] = join(', ', $details['keywords']);
			} else if( $key == 'db_type_infos' ){
//				$i = 0;
				foreach ($value->children() as $value2) {
//					$details['db_type_infos'][$i]['db_type_id'] = (string) $value2->attributes()->db_type_id;
//					$details['db_type_infos'][$i]['db_type'] = (string) $value2->db_type;
//					$details['db_type_infos'][$i]['db_type_long_text'] = (string) $value2->db_type_long_text;
//					$i++;
					$details['db_type_infos'][] = (string) $value2->db_type;
				}
				$details['db_type_infos_join'] = join(', ', $details['db_type_infos']);
			}

			// copy all left values into array
				else {


				$details[$key] = (string) $value;
			}

		}

		return $details;
	}

	/**
	 * Detailsuche Formular ausgeben
	 */
	public function detailSucheFormFelder(){
		$bibid = $this->getBibid();
		$url = 'http://rzblx10.uni-regensburg.de/dbinfo/suche.php?xmloutput=1&bib_id='. $bibid . '&' . "colors={$this->colors}&ocolors={$this->ocolors}";
		$xml_such_form = simplexml_load_file( $url );

		foreach ($xml_such_form->dbis_search->option_list AS $key => $value){
			foreach ( $value->option AS $key2 => $value2 ){
				$form[ (string) $value->attributes()->name ][ (string) $value2->attributes()->value ] = (string)$value2;
			}
		}

		$zugaenge = array (
			1000 => $form[zugaenge][1000],
			0 => $form[zugaenge][0],
			1 => $form[zugaenge][1],
			7 => $form[zugaenge][7],
			5 => $form[zugaenge][5],
			6 => $form[zugaenge][6],
			2 => $form[zugaenge][2],
			4 => $form[zugaenge][4],
			500 => $form[zugaenge][500],
			300 => $form[zugaenge][300],
		);

		$form[zugaenge] = $zugaenge;

		return $form;
	}


	private function createSearchUrl($searchVars, $lett = 'k') {
		$bibid = $this->getBibid();
	
		$searchUrl = 'http://rzblx10.uni-regensburg.de/dbinfo/dbliste.php?xmloutput=1&bib_id='. $bibid .'&' . "colors={$this->colors}&ocolors={$this->ocolors}&lett={$lett}";

		foreach($searchVars as $var => $values) {

			if (! is_array($values)) {
				$searchUrl .= "&$var=".utf8_decode($values);
			} else {
				foreach($values as $value) {
					$searchUrl .= '&'.$var.'[]='.utf8_decode($value);
				}
			}
		}

		return $searchUrl;
	}


	/**
	 * Search
	 * @param such String
	 */
	public function search($term, $searchVars = false, $lett = 'fs'){
		$bibid = $this->getBibid();
		// encode term
		$term = rawurlencode(utf8_decode($term));

		$searchUrl = '';
		if (!$searchVars){
			$searchUrl = 'http://rzblx10.uni-regensburg.de/dbinfo/dbliste.php?xmloutput=1&bib_id='. $bibid .'&' . "colors={$this->colors}&ocolors={$this->ocolors}&lett={$lett}&Suchwort={$term}";
		} else {
			$searchUrl = $this->createSearchUrl($searchVars);
		}

		$request = simplexml_load_file($searchUrl);

		$list = array(
			'top' => array(),
			//'groups' => array(),
			//'access_infos' => array(),
			'page_vars' => array(),
			'values' => array()
		);
		$dbsid=array();

		foreach ($request->page_vars->children() AS $key => $value){
			$page_vars[$key] = (string) $value;
		}

		if( isset( $request->list_dbs->db_access_infos->db_access_info) ){
			foreach ($request->list_dbs->db_access_infos->db_access_info as $value){
				$id = (string) $value->attributes()->access_id;
				$list['access_infos'][$id] = array(
					'id' => $id,
					'title' => (string)$value->db_access,
					'description' => (string)$value->db_access_short_text,
				);
			}
		}

		if( isset($request->list_dbs->db_type_infos->db_type_info) ){
			foreach ($request->list_dbs->db_type_infos->db_type_info as $value){
				$id = (string) $value->attributes()->db_type_id;
				$list['groups'][$id] = array(
					'id' => $id,
					'title' => (string)$value->db_type,
					'dbs' => array()
				);
			}
		}

		if( isset($request->list_dbs->dbs) ){

			foreach ($request->list_dbs->dbs as $dbs){

				foreach($dbs->db as $value) {

					$db = array (
						'id' => (int)$value['title_id'],
						'title' => (string)$value,
						'access_ref' => (string)$value['access_ref'],
						'access' => $list['access_infos'][(string)$value['access_ref']]['title'],
						'db_type_refs' => (string)$value['db_type_refs'],
						'top_db' => (int)$value['top_db'],
						'link' => 'http://rzblx10.uni-regensburg.de/dbinfo/detail.php?xmloutput=1&bib_id='. $bibid .'&colors=&ocolors=&'. "lett={$this->lett}&titel_id={$value['title_id']}",
					);

					if ($db['top_db']) {
						$list['top'][]  = $db;
					}
					$list['values'][$db['title'].'_'.$db['id']]=$db;
					$sort[$db['title'].'_'.$db['id']]=(string)$db['title'];

					/*foreach(explode(' ', $db['db_type_refs']) as $ref) {
						$list['groups'][$ref]['dbs'][] = $db;
					}*/
				}

			}
		}

		$list['searchDescription'] = array();
		foreach ($request->search_desc->search_desc_item as $searchDesc) {
			$list['searchDescription'][] = $searchDesc;
		}

		if (isset($request->error)) {
			$list['error'] = (string) $request->error;
		}

		//setlocale(LC_COLLATE, "de_DE.UTF-8");
		
		//asort($sort, SORT_LOCALE_STRING);
		$list['alphasort']=$sort;

		return array( 'page_vars' => $page_vars, /*'groups' => $access_infos,*/ 'list' => $list);
	}


	/**
	 * Set id Fachgebiet
	 * @param int $fachgebiet
	 */
	public function setGebiet($fachgebiet){
		$this->fachgebiet = (int)$fachgebiet;
	}

	/**
	 * Set the letter string
	 * @param string $lett
	 */
	public function setLett($lett){
		$this->lett = $lett;
	}

	/**
	 * Set the int value colors
	 * @param int $colors
	 */
	public function setColors($colors){
		$this->colors = $colors;
	}

	/**
	 * Set the int value ocolors
	 * @param int $ocolors
	 */
	public function setOcolors($ocolors){
		$this->ocolors = $ocolors;
	}

	/**
	 *
	 * helper function get fachliste
	 * @param string
	 * @return xml array
	 */
	public function getRequestFachliste( $request ){
		$bibid = $this->getBibid();
		$url = 'http://rzblx10.uni-regensburg.de/dbinfo/fachliste.php?xmloutput=1&bib_id='. $bibid .'&' . $request;
		$xml_request = simplexml_load_file( $url );
		return $xml_request;
	}

	/**
	 *
	 * helper function get db liste
	 * @param string
	 * @return xml array
	 */
	public function getRequestDbliste( $request ){
		$bibid = $this->getBibid();
		$url = 'http://rzblx10.uni-regensburg.de/dbinfo/dbliste.php?xmloutput=1&bib_id='. $bibid .'&'. $request;
		$xml_request = simplexml_load_file( $url );
		return $xml_request;
	}
	
	private function getBibid(){
		$bibid = $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['dbisbibid'];
		
		if(empty($bibid)){
			$bibid = 'sub_hh';
		}
		return $bibid;
	}
}