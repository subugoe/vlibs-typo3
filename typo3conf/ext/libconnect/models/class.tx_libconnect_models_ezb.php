<?php

/***************************************************************
* Copyright notice
*
* (c) 2009 by Avonis - New Media Agency
*
* All rights reserved
*
* This script is part of the EZB/DBIS-Extention project. The EZB/DBIS-Extention project
* is free software; you can redistribute it and/or modify
* it under the terms of the GNU General Public License as published by
* the Free Software Foundation; either version 2 of the License, or
* (at your option) any later version.
*
* The GNU General Public License can be found at
* http://www.gnu.org/copyleft/gpl.html.
*
* This script is distributed in the hope that it will be useful,
* but WITHOUT ANY WARRANTY; without even the implied warranty of
* MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
* GNU General Public License for more details.
*
* This copyright notice MUST APPEAR in all copies of the script!
*
* Project sponsored by:
*  Avonis - New Media Agency - http://www.avonis.com/
***************************************************************/


require_once(t3lib_extMgm::extPath('div') . 'class.tx_div.php');
require_once(t3lib_extMgm::extPath('libconnect') . 'lib/ezb_dbis/classes/class_EZB.php');
		
class tx_libconnect_models_ezb extends tx_lib_object {
	
	private $ezb_to_t3_subjects;
	private $t3_to_ezb_subjects;
	
	public function loadOverview() {	
		$this->loadSubjects();
		$cObject = $this->findCObject();
		
		$ezb = new EZB();
		$fb = $ezb->getFachbereiche();

		foreach($fb as $el) {
			$subject = $this->ezb_to_t3_subjects[$el['id']];
			$el['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, array(
				'libconnect[subject]' => $subject['uid']
			));
			$list[$el['id']] = $el;
		}
		$this->set('list', $list );
	}
	
	public function loadList($subject_id, $index=0, $sc='A', $lc ='') {
		$cObject = $this->findCObject();
		$this->loadSubjects();
		$subject = $this->t3_to_ezb_subjects[$subject_id];
		
		$ezb = new EZB();
		$journals = $ezb->getFachbereichJournals($subject['ezb_notation'], $index, $sc, $lc);
		
		foreach(array_keys($journals['navlist']['pages']) as $page) {
			if (is_array($journals['navlist']['pages'][$page])) {
				$journals['navlist']['pages'][$page]['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, array(
					'libconnect[subject]' => $subject['uid'],
					'libconnect[index]' => 0,
				    'libconnect[sc]' => $journals['navlist']['pages'][$page]['sc']? $journals['navlist']['pages'][$page]['sc'] : 'A',
					'libconnect[lc]' => $journals['navlist']['pages'][$page]['lc'],		
				));
			}
		}
		foreach(array_keys($journals['alphabetical_order']['first_fifty']) as $section) {
			$journals['alphabetical_order']['first_fifty'][$section]['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, array(
					'libconnect[subject]' => $subject['uid'],
					'libconnect[index]' => $journals['alphabetical_order']['first_fifty'][$section]['sindex'],
				    'libconnect[sc]' => $journals['alphabetical_order']['first_fifty'][$section]['sc']? $journals['alphabetical_order']['first_fifty'][$section]['sc'] : 'A',
					'libconnect[lc]' => $journals['alphabetical_order']['first_fifty'][$section]['lc'],
			));
		}
		foreach(array_keys($journals['alphabetical_order']['journals']) as $journal) {
			$journals['alphabetical_order']['journals'][$journal]['detail_link'] = $cObject->getTypolink_URL(
					intval($this->controller->configurations->get('detailPid')), 
					array(
						'libconnect[jourid]' => $journals['alphabetical_order']['journals'][$journal]['jourid'],
					)
			);
		}
		foreach(array_keys($journals['alphabetical_order']['next_fifty']) as $section) {
			$journals['alphabetical_order']['next_fifty'][$section]['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, array(
					'libconnect[subject]' => $subject['uid'],
					'libconnect[index]' => $journals['alphabetical_order']['next_fifty'][$section]['sindex'],
				    'libconnect[sc]' => $journals['alphabetical_order']['next_fifty'][$section]['sc']? $journals['alphabetical_order']['next_fifty'][$section]['sc'] : 'A',
					'libconnect[lc]' => $journals['alphabetical_order']['next_fifty'][$section]['lc'],
			));
		}
		
		$this->set('journals', $journals);
	}
	
	public function loadDetail($journal_id) {
		$cObject = $this->findCObject();
		$ezb = new EZB();
		$journal = $ezb->getJournalDetail($journal_id);
		
		if (! $journal)
			$this->set('error', 1);
		
		$this->set('journal', $journal);
		$this->set('bibid', $GLOBALS['TSFE']->tmpl->setup['plugin.']['tx_libconnect.']['ezbbibid']);
	}
	
	private function loadSubjects() {
		$this->ezb_to_t3_subjects = array();
		$this->t3_to_ezb_subjects = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_libconnect_subject', "1=1");
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$this->ezb_to_t3_subjects[$row['ezb_notation']] = $row;
			$this->t3_to_ezb_subjects[$row['uid']] = $row;
		}
	}

	public function loadSearch() {
		$cObject = $this->findCObject();
		$this->loadSubjects();
		
		$searchVars = $this->controller->parameters->get('search');

		$linkParams = array();
		foreach ($searchVars as $key => $value) {
			$linkParams["libconnect[search][$key]"] = $value;
		}
		
		$term = $searchVars['sword'];
		unset($searchVars['sword']);
	
		$ezb = new EZB();
		$journals = $ezb->search($term, $searchVars);
		
		if (! $journals)
			return false;
			
		if (is_array($journals['navlist']['pages'])) {
			
			foreach(array_keys($journals['navlist']['pages']) as $page) {
				if (is_array($journals['navlist']['pages'][$page])) {
					$journals['navlist']['pages'][$page]['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, 
						array_merge($linkParams, array(
							'libconnect[search][sc]' => $journals['navlist']['pages'][$page]['id']
						)));
				}
			}
		}
		
		if (is_array($journals['alphabetical_order']['first_fifty'])) {
			
			foreach(array_keys($journals['alphabetical_order']['first_fifty']) as $section) {
				$journals['alphabetical_order']['first_fifty'][$section]['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, 
					array_merge($linkParams, array(
						'libconnect[search][sindex]' => $journals['alphabetical_order']['first_fifty'][$section]['sindex'],
					    'libconnect[search][sc]' => $journals['alphabetical_order']['first_fifty'][$section]['sc'],
					)));
			}
		}
		
		if (is_array($journals['alphabetical_order']['journals'])) {
			
			foreach(array_keys($journals['alphabetical_order']['journals']) as $journal) {
				$journals['alphabetical_order']['journals'][$journal]['detail_link'] = $cObject->getTypolink_URL(
						intval($this->controller->configurations->get('detailPid')), 
						array(
							'libconnect[jourid]' => $journals['alphabetical_order']['journals'][$journal]['jourid'],
						)
				);
			}
		}
		
		if (is_array($journals['alphabetical_order']['next_fifty'])) {
			
			foreach(array_keys($journals['alphabetical_order']['next_fifty']) as $section) {
				$journals['alphabetical_order']['next_fifty'][$section]['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, 
					array_merge($linkParams, array(
						'libconnect[search][sindex]' => $journals['alphabetical_order']['next_fifty'][$section]['sindex'],
					    'libconnect[search][sc]' => $journals['alphabetical_order']['next_fifty'][$section]['sc'],
					)));
			}
		}
		$this->set('journals', $journals);
	}
	
	public function loadMiniForm() {
		$cObject = $this->findCObject();
			
		$ezb = new EZB();
		$form = $ezb->detailSearchFormFields();
		$searchVars = $this->controller->parameters->get('search');
		$this->set('vars', $searchVars);
		$this->set('form', $form);
		$this->set('siteUrl', $cObject->getTypolink_URL($GLOBALS['TSFE']->id));
		$this->set('searchUrl', $cObject->getTypolink_URL($this->controller->configurations->get('searchPid')));
		$this->set('listPid', $this->controller->configurations->get('searchPid'));
	}

	public function loadForm() {
		$cObject = $this->findCObject();
		
		
		$ezb = new EZB();
		$form = $ezb->detailSearchFormFields();
		$searchVars = $this->controller->parameters->get('search');
		$this->set('vars', $searchVars);
		$this->set('form', $form);
		$this->set('siteUrl', $cObject->getTypolink_URL($GLOBALS['TSFE']->id));
		$this->set('listUrl', $cObject->getTypolink_URL($this->controller->configurations->get('listPid')));
		$this->set('listPid', $this->controller->configurations->get('listPid'));
	}	
}
?>