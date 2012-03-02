<?php
/***************************************************************
 *  Copyright notice
 *
 *  (c) 2011 Claus Due <claus@wildside.dk>, Wildside A/S
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Record Selector Widget Controller
 *
 * @package Fed
 * @subpackage ViewHelpers/Widget
 */
class Tx_Fed_ViewHelpers_Widget_Controller_RecordSelectorController extends Tx_Fluid_Core_Widget_AbstractWidgetController {

	/**
	 * @var Tx_Fed_Utility_DomainObjectInfo
	 */
	protected $infoService;

	/**
	 * @var boolean
	 */
	protected $multiple;

	/**
	 * @var string
	 */
	protected $relationType;

	/**
	 * @var string
	 */
	protected $property;

	/**
	 * @var string
	 */
	protected $searchProperty;

	/**
	 * @var string
	 */
	protected $prefix;

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $name;

	/**
	 * @var string
	 */
	protected $listLabel;

	/**
	 * @var string
	 */
	protected $listHelp;

	/**
	 * @var string
	 */
	protected $buttonLabel;

	/**
	 * @var string
	 */
	protected $listButtonLabel;

	/**
	 * @var boolean
	 */
	protected $allowAdd;

	/**
	 * @var Tx_Extbase_DomainObject_DomainObjectInterface
	 */
	protected $object;

	/**
	 * @var string
	 */
	protected $allUrl;

	/**
	 * @param Tx_Fed_Utility_DomainObjectInfo $infoService
	 */
	public function injectInfoService(Tx_Fed_Utility_DomainObjectInfo $infoService) {
		$this->infoService = $infoService;
	}

	/**
	 * Initialize action
	 */
	public function initializeAction() {
		$this->sections = $this->widgetConfiguration['sections'];
		$this->object = $this->widgetConfiguration['object'];
		$this->property = $this->widgetConfiguration['property'];
		$this->searchProperty = $this->widgetConfiguration['searchProperty'];
		$this->id = $this->widgetConfiguration['id'] ? $this->widgetConfiguration['id'] : uniqid('recordselector');
		$this->name = $this->widgetConfiguration['name'];
		$this->listLabel = $this->widgetConfiguration['listLabel'];
		$this->listHelp = $this->widgetConfiguration['listHelp'];
		$this->buttonLabel = $this->widgetConfiguration['buttonLabel'];
		$this->listButtonLabel = $this->widgetConfiguration['listButtonLabel'];
		$this->allowAdd = $this->widgetConfiguration['allowAdd'];
		$this->allUrl = $this->widgetConfiguration['allUrl'];
		if (class_exists($this->widgetConfiguration['objectType'])) {
			$this->relationType = $this->widgetConfiguration['objectType'];
		} else {
			$typeArray = $this->infoService->getAnnotationValuesByProperty($this->object, $this->property, 'var');
			$this->relationType = array_shift($typeArray);
		}
		$this->prefix = $this->infoService->getPluginNamespace($this->object);
		if (strpos($this->relationType, '<') !== FALSE) {
			$this->multiple = TRUE;
			$this->relationType = $this->infoService->parseObjectStorageAnnotation($this->relationType);
		} else if ($this->widgetConfiguration['multiple']) {
			$this->multiple = (bool) $this->widgetConfiguration['multiple'];
		} else {
			$this->multiple = FALSE;
		}
	}

	/**
	 * Default action
	 *
	 * @return string
	 */
	public function indexAction() {
		if ($this->object && $this->property) {
			$values = Tx_Extbase_Reflection_ObjectAccess::getProperty($this->object, $this->property);
			$this->view->assign('values', $values);
		}
		$this->view->assign('multiple', $this->multiple);
		$this->view->assign('property', $this->property);
		$this->view->assign('searchProperty', $this->searchProperty);
		$this->view->assign('object', $this->object);
		$this->view->assign('id', $this->id);
		$this->view->assign('prefix', $this->prefix);
		$this->view->assign('name', $this->name);
		$this->view->assign('listLabel', $this->listLabel);
		$this->view->assign('listHelp', $this->listHelp);
		$this->view->assign('buttonLabel', $this->buttonLabel);
		$this->view->assign('listButtonLabel', $this->listButtonLabel);
		$this->view->assign('allowAdd', $this->allowAdd);
		$this->view->assign('allUrl', $this->allUrl);
		$this->view->assign('sections', $this->sections);
	}

	/**
	 * Search for a list of Objects by term
	 *
	 * @param string $term
	 * @return string
	 */
	public function searchAction($term) {
		$relationType = $this->relationType;
		$repository = $this->infoService->getRepositoryInstance($relationType);
		$query = $repository->createQuery();
		$results = $query->execute();
		$query->getQuerySettings()->setRespectStoragePage(FALSE);
		$query->matching($query->like($this->searchProperty, '%' . $term . '%', FALSE));
		$results = $query->execute();
		$output = array();
		foreach ($results as $singleResult) {
			$val = Tx_Extbase_Reflection_ObjectAccess::getProperty($singleResult, $this->searchProperty);
			$output[] = array(
				'id' => $singleResult->getUid(),
				'label' => $val,
				'value' => $val
			);
		}
		return json_encode($output);
	}

}

?>