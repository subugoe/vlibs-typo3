<?php
/**
 * Doku: http://www.bibliothek.uni-regensburg.de/ezeit/vascoda/vifa/doku_xml_ezb.html
 * Doku: http://rzblx1.uni-regensburg.de/ezeit/vascoda/vifa/doku_xml_ezb.html
 * @author niklas guenther
 *
 */

require_once(PATH_tslib.'class.tslib_pibase.php');

class EZB extends tslib_pibase {
	
	//document search meta infos
	private $title;
	private $author_firstname;
	private $author_lastname;
	private $genre; // journal / article
	private $isbn;
	private $issn;
	private $eissn;
	private $date; // YYYY-MM-DD YYYY-MM YYYY 
	
	// general config
	private $overview_requst_url = "http://rzblx1.uni-regensburg.de/ezeit/fl.phtml?xmloutput=1&";
	private $detailview_request_url = "http://rzblx1.uni-regensburg.de/ezeit/detail.phtml?xmloutput=1&";
	private $search_url = "http://rzblx1.uni-regensburg.de/ezeit/search.phtml?xmloutput=1&";
//	private $journal_link_url = "http://rzblx1.uni-regensburg.de/ezeit/warpto.phtml?bibid=SUBHH&colors=7&lang=de&jour_id=";
//	private $search_result_page = "http://rzblx1.uni-regensburg.de/ezeit/searchres.phtml?&xmloutput=1&bibid=SUBHH&colors=7&lang=de&";
//	private $search_result_page = "http://ezb.uni-regensburg.de/searchres.phtml?xmloutput=1&bibid=SUBHH&colors=7&lang=de";
	
	private $lang = "de";
	private $colors = 7;
	
	//Fachbereich Journals
	public $notation;
	public $sc;
	public $lc;
	public $sindex;
	
	/**
	 * Fachbereiche laden
	 * @return array()
	 */
	public function getFachbereiche(){
		$xml_request = simplexml_load_file( "{$this->overview_requst_url}bibid={$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_ezbdbis.']['ezbbibid']}&colors={$this->colors}&lang={$this->lang}&" );
		$fachbereiche = array();
		foreach ($xml_request->ezb_subject_list->subject AS $key => $value){
			$fachbereiche[(string) $value['notation'][0]] = array('title' => (string) $value[0], 'journalcount' => (int) $value['journalcount'], 'id' => (string) $value['notation'][0], 'notation' => (string) $value['notation'][0] );
		}
		
		return $fachbereiche;
		
	}
	
	/**
	 * Alle Journals eines Fachbereichs laden
	 * 
	 * @param string $jounal
	 * @param string $letter
	 * @param string $lc
	 * @param int $sindex
	 * 
	 * @return array()
	 */
	public function getFachbereichJournals($jounal, $sindex = 0, $sc = 'A', $lc = 'B', $lc = ''){
		$xml_request = simplexml_load_file( "{$this->overview_requst_url}bibid={$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_ezbdbis.']['ezbbibid']}&colors={$this->colors}&lang={$this->lang}&notation={$jounal}&sc={$sc}&lc={$lc}&sindex={$sindex}&");
		$journals = array();

		if( $xml_request->page_vars ){
			$this->notation = (string) $xml_request->page_vars->notation->attributes()->value;
			$this->sc = (string)$xml_request->page_vars->sc->attributes()->value;
			$this->lc = (string)$xml_request->page_vars->lc->attributes()->value;
			$this->sindex = (string)$xml_request->page_vars->sindex->attributes()->value;
		}
		
		if( $xml_request->ezb_alphabetical_list ){
			
			$journals['subject'] = (string)$xml_request->ezb_alphabetical_list->subject;
			$journals['navlist']['current_page'] = (string)$xml_request->ezb_alphabetical_list->navlist->current_page;
			$journals['navlist']['current_title'] = (string) $xml_request->ezb_alphabetical_list->current_title;

			foreach ($xml_request->ezb_alphabetical_list->navlist->other_pages AS $key2 => $value2){
				foreach ( $value2->attributes() AS $key3 => $value3){
					$journals['navlist']['pages'][(string)$value2[0]][(string) $key3] = (string) $value3;
				}
				// set title
				$journals['navlist']['pages'][(string)$value2[0]]['title'] = (string)$value2[0];
			}
		}
		$journals['navlist']['pages'][$journals['navlist']['current_page']] = $journals['navlist']['current_page'];
		ksort($journals['navlist']['pages']);

		foreach ($xml_request->ezb_alphabetical_list->alphabetical_order->journals->journal AS $key => $value){
			$journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['title'] = (string) $value->title;
			$journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['jourid'] = (int) $value->attributes()->jourid;
			$journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['color_code'] = (int) $value->journal_color->attributes()->color_code;
			$journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['color'] = (string) $value->journal_color->attributes()->color;
			$journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['detail_link'] = '';
			$journals['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['warpto_link'] = $this->journal_link_url . $value->attributes()->jourid;
			
		}
		$i = 0;
		
		foreach ( $xml_request->ezb_alphabetical_list->next_fifty AS $key => $value){
			$journals['alphabetical_order']['next_fifty'][$i]['sc'] = (string) $value->attributes()->sc;
			$journals['alphabetical_order']['next_fifty'][$i]['lc'] = (string) $value->attributes()->lc;
			$journals['alphabetical_order']['next_fifty'][$i]['sindex'] = (string) $value->attributes()->sindex;
			$journals['alphabetical_order']['next_fifty'][$i]['next_fifty_titles'] = (string) $value->next_fifty_titles;
			$i++;
		}
		
		$i = 0;
		
		foreach ( $xml_request->ezb_alphabetical_list->first_fifty AS $key => $value){
			$journals['alphabetical_order']['first_fifty'][$i]['sc'] = (string) $value->attributes()->sc;
			$journals['alphabetical_order']['first_fifty'][$i]['lc'] = (string) $value->attributes()->lc;
			$journals['alphabetical_order']['first_fifty'][$i]['sindex'] = (string) $value->attributes()->sindex;
			$journals['alphabetical_order']['first_fifty'][$i]['first_fifty_titles'] = (string) $value->first_fifty_titles;
			$i++;
		}
		
		return $journals;
	}
	
	/**
	 * Details zu einem Journal laden
	 * @param int Journal ID
	 */
	public function getJournalDetail($journalId){
		$xml_request = simplexml_load_file( "{$this->detailview_request_url}bibid={$GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_ezbdbis.']['ezbbibid']}&colors={$this->colors}&lang={$this->lang}&jour_id={$journalId}");
		$journal = array();
		
		if (! is_object($xml_request->ezb_detail_about_journal->journal))
			return false;
		
		$journal['id'] = (int) $xml_request->ezb_detail_about_journal->journal->attributes()->jourid;
		$journal['title'] = (string) $xml_request->ezb_detail_about_journal->journal->title;
		$journal['color'] = (string) $xml_request->ezb_detail_about_journal->journal->journal_color->attributes()->color;
		$journal['color_code'] = (int) $xml_request->ezb_detail_about_journal->journal->journal_color->attributes()->color_code;
		$journal['publisher'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->publisher;
		$journal['ZDB_number'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->ZDB_number;
		$journal['ZDB_number_link'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->ZDB_number->attributes()->url;
		$journal['subjects'] = array();
		foreach($xml_request->ezb_detail_about_journal->journal->detail->subjects->subject as $subject) {
			$journal['subjects'][] = (string) $subject;
		}
		$journal['subjects_join'] = join(', ', $journal['subjects']);
		$journal['pissns'] = array();
		foreach($xml_request->ezb_detail_about_journal->journal->detail->P_ISSNs->P_ISSN as $pissn) {
			$journal['pissns'][] = (string) $pissn;
		}
		$journal['pissns_join'] = join(', ', $journal['pissns']);
		$journal['eissns'] = array();
		foreach($xml_request->ezb_detail_about_journal->journal->detail->E_ISSNs->E_ISSN as $eissn) {
			$journal['eissns'][] = (string) $eissn;
		}
		$journal['eissns_join'] = join(', ', $journal['eissns']);
		$journal['keywords'] = array();
		foreach($xml_request->ezb_detail_about_journal->journal->detail->keywords->keyword as $keyword) {
			$journal['keywords'][] = (string) $keyword;
		}
		$journal['keywords_join'] = join(', ', $journal['keywords']);
		$journal['fulltext'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->fulltext;
		$journal['fulltext_link'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->fulltext->attributes()->url;
		$journal['homepages'] = array();
		foreach($xml_request->ezb_detail_about_journal->journal->detail->homepages->homepage as $homepage) {
			$journal['homepages'][] = (string) $homepage;
		}
		$journal['first_fulltext'] = array(
			'volume' => (int) $xml_request->ezb_detail_about_journal->journal->detail->first_fulltext_issue->first_volume,
			'issue' => (int) $xml_request->ezb_detail_about_journal->journal->detail->first_fulltext_issue->first_issue,
			'date' => (int) $xml_request->ezb_detail_about_journal->journal->detail->first_fulltext_issue->first_date
		);
		if ($xml_request->ezb_detail_about_journal->journal->detail->last_fulltext_issue) {
			$journal['last_fulltext'] = array(
				'volume' => (int) $xml_request->ezb_detail_about_journal->journal->detail->last_fulltext_issue->last_volume,
				'issue' => (int) $xml_request->ezb_detail_about_journal->journal->detail->last_fulltext_issue->last_issue,
				'date' => (int) $xml_request->ezb_detail_about_journal->journal->detail->last_fulltext_issue->last_date
			);
		}
		$journal['appearence'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->appearence;
		$journal['costs'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->costs;
		$journal['remarks'] = (string) $xml_request->ezb_detail_about_journal->journal->detail->remarks;

		// periods
		
		$color_map = array (
			'green' => 1,
			'yellow' => 2,
			'red' => 4,
			'yellow_red' => 6
		);
		$journal['periods'] = array();		
		foreach($xml_request->ezb_detail_about_journal->journal->periods->period as $period) {
			$journal['periods'][] = array (
				'label' => (string) $period->label,
				'color' => (string) $period->journal_color->attributes()->color,
				'color_code' => $color_map[(string) $period->journal_color->attributes()->color],
				'link' => (string) $period->warpto_link->attributes()->url
			);
		}

		
//print_r($journal);	
			
		return $journal;

	}
	
	/**
	 * Suchformular anzeigen
	 */
	public function detailSearchFormFields(){
		$xml_such_form = simplexml_load_file( $this->search_url . "" );
		
		foreach ($xml_such_form->ezb_search->option_list AS $key => $value){
			foreach ( $value->option AS $key2 => $value2 ){
				$form[ (string) $value->attributes()->name ][ (string) $value2->attributes()->value ] = (string)$value2; 
			}
		}
		
		
		// fehlenden Eintrag ergaenzen
		$form['selected_colors'][2] = 'im Campus-Netz';
		
		// schlagwort und issn tauschen...
		$form['jq_type'] = array (
            'KT' => 'Titelwort(e)',
            'KS' => 'Titelanfang',
            'IS' => 'ISSN',
            'PU' => 'Verlag',
            'KW' => 'Schlagwort(e)',
            'ID' => 'Eingabedatum',
            'LC' => 'Letzte Änderung',
            'ZD' => 'ZDB-Nummer',
		);

//print_r($form);		
		
		return $form;
	}
	
	private function createSearchUrl($term, $searchVars/*, $lett = 'k'*/) {

/* Bei der EZB gibts den Parameter "lett" gar nicht?!		
		// Achtung!!! Wichtig: lett=k !!!
		// Bist du Dir da sicher? ich glaube das muss fs sein.
		// mit fs kommen immer leere ergebnisse...zumin
		$searchUrl = $this->search_result_page . "colors={$this->colors}&ocolors={$this->ocolors}&lett={$lett}";
*/
		$searchUrl = $this->search_result_page;

		// urlencode termi
		$term = rawurlencode(utf8_decode($term));
		
		if (strlen($term)) {
			$searchUrl .= "&jq_type1=KT&jq_term1={$term}";
		}
		
		if (!$searchVars['sc']) 
			$searchVars['sc'] = 'A';
			
		foreach($searchVars as $var => $values) {
			
			if (! is_array($values)) {
				$searchUrl .= "&$var=$values";
			} else {
				foreach($values as $value) {
					$searchUrl .= '&'.$var.'[]='.$value;
				}
			}
		}
		
		return $searchUrl;
	}
	
	/**
	 * Suche durchführen
	 * @param string Such string
	 */
	public function search( $term, $searchVars = array() ){
		
		$searchUrl = $this->createSearchUrl($term, $searchVars);

//echo $searchUrl;

		$xml_request = simplexml_load_file( $searchUrl );
		if (! $xml_request) 
			return false;
		
		foreach ($xml_request->page_vars->children() AS $key => $value){
			$result['page_vars'][$key] = (string) $value->attributes()->value;
		}
		
		foreach ($xml_request->page_vars->children() AS $key => $value){
			$result['page_vars'][$key] = (string) $value->attributes()->value;
		}
		
		$result['page_vars']['search_count'] = (int) $xml_request->ezb_alphabetical_list_searchresult->search_count;
		

		foreach ($xml_request->ezb_alphabetical_list_searchresult->navlist->other_pages AS $key2 => $value2){
			foreach ( $value2->attributes() AS $key3 => $value3){
				$result['navlist']['pages'][(string)$value3] = array (
				'id' => (string) $value3,
				'title' => (string) $value2
				);
			}
		}
		$current_page = (string) $xml_request->ezb_alphabetical_list_searchresult->navlist->current_page;
	
		if ($current_page) {
			$result['navlist']['pages'][$current_page] = $current_page;
		}
		if (is_array($result['navlist']['pages'])) {
			ksort($result['navlist']['pages']);
		}
		
		if($xml_request->ezb_alphabetical_list_searchresult->current_title)
			$result['alphabetical_order']['current_title'] = (string) $xml_request->ezb_alphabetical_list_searchresult->current_title;
		
		foreach ( $xml_request->ezb_alphabetical_list_searchresult->alphabetical_order->journals->journal AS $key => $value){
			$result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['title'] = (string) $value->title;
			$result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['jourid'] = (int) $value->attributes()->jourid;
			$result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['color_code'] = (int) $value->journal_color->attributes()->color_code;
			$result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['color'] = (string) $value->journal_color->attributes()->color;
			$result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['detail_link'] = '';
			$result['alphabetical_order']['journals'][(int) $value->attributes()->jourid]['warpto_link'] = $this->journal_link_url . $value->attributes()->jourid;
			
		}
		
		$i = 0;
		foreach ($xml_request->ezb_alphabetical_list_searchresult->next_fifty AS $key => $value){
			$result['alphabetical_order']['next_fifty'][$i]['sc'] = (string) $value->attributes()->sc;
			$result['alphabetical_order']['next_fifty'][$i]['sindex'] = (string) $value->attributes()->sindex;
			$result['alphabetical_order']['next_fifty'][$i]['next_fifty_titles'] = (string) $value->next_fifty_titles;
			$i++;
		}

		$i = 0;
		foreach ($xml_request->ezb_alphabetical_list_searchresult->first_fifty AS $key => $value){
			$result['alphabetical_order']['first_fifty'][$i]['sc'] = (string) $value->attributes()->sc;
			$result['alphabetical_order']['first_fifty'][$i]['sindex'] = (string) $value->attributes()->sindex;
			$result['alphabetical_order']['first_fifty'][$i]['first_fifty_titles'] = (string) $value->first_fifty_titles;
			$i++;
		}
		
		return $result;
	}
	
	
}

?>