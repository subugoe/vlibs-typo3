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
require_once(t3lib_extMgm::extPath('libconnect') . 'lib/ezb_dbis/classes/class_DBIS.php');

class tx_libconnect_models_dbis extends tx_lib_object {

	private $dbis_to_t3_subjects;
	private $t3_to_dbis_subjects;

	public function loadTop($subject_id) {
		$cObject = $this->findCObject();

		$this->loadSubjects();
		$subject = $this->t3_to_dbis_subjects[$subject_id];
		$dbis_id = $subject['dbis_id'];
		$dbis = new DBIS();
		$result = $dbis->getDbliste($dbis_id);

		foreach(array_keys($result['list']['top']) as $db) {
			$result['list']['top'][$db]['detail_link'] = $cObject->getTypolink_URL(
				intval($this->controller->configurations->get('detailPid')),
				array(
					'libconnect[titleid]' => $result['list']['top'][$db]['id'],
				)
			);
		}

		$this->set('top', $result['list']['top']);
	}

	public function loadOverview() {
		$this->loadSubjects();
		$cObject = $this->findCObject();

		$dbis = new DBIS();
		$list = $dbis->getFachliste();

		foreach($list as $el) {
			$subject = $this->dbis_to_t3_subjects[$el['id']];
			$el['link'] = $cObject->getTypolink_URL($GLOBALS['TSFE']->id, array(
				'libconnect[subject]' => $subject['uid']
			));
			$list[$el['id']] = $el;
		}
		$this->set('list', $list );
	}

	public function loadList($subject_id) {
		$cObject = $this->findCObject();
		$this->loadSubjects();

		$dbis = new DBIS();
		$subject = $this->t3_to_dbis_subjects[$subject_id];

		$dbis_id = $subject['dbis_id'];
		$sort = $this->controller->configurations->get('sortParameter');
		$result = $dbis->getDbliste($dbis_id, $sort);

		foreach(array_keys($result['list']['top']) as $db) {
			$result['list']['top'][$db]['detail_link'] = $cObject->getTypolink_URL(
				intval($this->controller->configurations->get('detailPid')),
				array(
					'libconnect[titleid]' => $result['list']['top'][$db]['id'],
				)
			);
		}
		foreach(array_keys($result['list']['groups']) as $group) {
			foreach(array_keys($result['list']['groups'][$group]['dbs']) as $db) {
				$result['list']['groups'][$group]['dbs'][$db]['detail_link'] = $cObject->getTypolink_URL(
					intval($this->controller->configurations->get('detailPid')),
					array(
						'libconnect[titleid]' => $result['list']['groups'][$group]['dbs'][$db]['id'],
					)
				);
			}
		}

		// sort groups by name
		$alph_sort_groups = array();
		foreach ($result['list']['groups'] as $group) {
			$alph_sort_groups[$group['title']] = $group;
		}
		ksort($alph_sort_groups);
		$result['list']['groups'] = $alph_sort_groups;
		$this->set('subject', $subject['title']);
		$this->set('list', $result['list']);
	}

	public function loadSearch() {
		$cObject = $this->findCObject();
		$this->loadSubjects();

		$searchVars = $this->controller->parameters->get('search');
		$term = $searchVars['sword'];
		unset($searchVars['sword']);

		$dbis = new DBIS();
		$result = $dbis->search($term, $searchVars);

		foreach(array_keys($result['list']['top']) as $db) {
			$result['list']['top'][$db]['detail_link'] = $cObject->getTypolink_URL(
				intval($this->controller->configurations->get('detailPid')),
				array(
					'libconnect[titleid]' => $result['list']['top'][$db]['id'],
				)
			);
		}
		foreach(array_keys($result['list']['values']) as $value) {
			//foreach(array_keys($result['list']['groups'][$group]['dbs']) as $db) {
				$result['list']['values'][$value]['detail_link'] = $cObject->getTypolink_URL(
					intval($this->controller->configurations->get('detailPid')),
					array(
						'libconnect[titleid]' => $result['list']['values'][$value]['id'],
					)
				);
			//}
		}

		// sort groups by name
		/*$alph_sort_groups = array();
		foreach ($result['list']['groups'] as $group) {
			$alph_sort_groups[$group['title']] = $group;
		}
		ksort($alph_sort_groups);
		$result['list']['groups'] = $alph_sort_groups;
		*/
		$this->set('list', $result['list']);
	}

	public function loadDetail($title_id) {
		$cObject = $this->findCObject();
		$dbis = new DBIS();
		$db = $dbis->getDbDetails($title_id);

		if (! $db )
			$this->set('error', 1);

		$this->set('db', $db);
	}

	private function loadSubjects() {
		$this->dbis_to_t3_subjects = array();
		$this->t3_to_dbis_subjects = array();
		$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', 'tx_libconnect_subject', "1=1");
		while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)) {
			$this->dbis_to_t3_subjects[$row['dbis_id']] = $row;
			$this->t3_to_dbis_subjects[$row['uid']] = $row;
		}
	}

	public function loadMiniForm() {
		$cObject = $this->findCObject();


		$dbis = new DBIS();
		$form = $dbis->detailSucheFormFelder();
		$searchVars = $this->controller->parameters->get('search');
		$this->set('vars', $searchVars);
		$this->set('form', $form);
		$this->set('siteUrl', $cObject->getTypolink_URL($GLOBALS['TSFE']->id));
		$this->set('searchUrl', $cObject->getTypolink_URL($this->controller->configurations->get('searchPid')));
		$this->set('listPid', $this->controller->configurations->get('searchPid'));
	}

	public function loadForm() {
		$cObject = $this->findCObject();


		$dbis = new DBIS();
		$form = $dbis->detailSucheFormFelder();
		$searchVars = $this->controller->parameters->get('search');
		$this->set('vars', $searchVars);
		$this->set('form', $form);
		$this->set('siteUrl', $cObject->getTypolink_URL($GLOBALS['TSFE']->id));
		$this->set('listUrl', $cObject->getTypolink_URL($this->controller->configurations->get('listPid')));
		$this->set('listPid', $this->controller->configurations->get('listPid'));
	}

}
?>