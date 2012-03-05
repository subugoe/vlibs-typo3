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
*  the Free Software Foundation; either version 3 of the License, or
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
 * Controller for the DataSource object
 *
 * @package Fed
 * @subpackage Controller
 */
 class Tx_Fed_Controller_DataSourceController extends Tx_Fed_Core_AbstractController {

	/**
	 * dataSourceRepository
	 *
	 * @var Tx_Fed_Domain_Repository_DataSourceRepository
	 */
	protected $dataSourceRepository;

	/**
	 *
	 * @var Tx_Fed_Utility_DataSourceParser
	 */
	protected $dataSourceParser;

	/**
	 * injectDataSourceRepository
	 *
	 * @param Tx_Fed_Domain_Repository_DataSourceRepository $dataSourceRepository
	 * @return void
	 */
	public function injectDataSourceRepository(Tx_Fed_Domain_Repository_DataSourceRepository $dataSourceRepository) {
		$this->dataSourceRepository = $dataSourceRepository;
	}

	/**
	 *
	 * @param Tx_Fed_Utility_DataSourceParser $parser
	 */
	public function injectDataSourceParser(Tx_Fed_Utility_DataSourceParser $parser) {
		$this->dataSourceParser = $parser;
	}

	/**
	 * Displays all DataSources
	 *
	 * @return void
	 */
	public function listAction() {
		$parser = $this->objectManager->get('Tx_Fed_Utility_DataSourceParser');
		$this->injectDataSourceParser($parser);
		$flexform = $this->getFlexForm();
		$sources = trim($flexform['sources'], ',');
		$sources = explode(',', $sources);
		$dataSources = $this->dataSourceRepository->findByUids($sources)->toArray();
		$dataSources = $this->dataSourceParser->parseDataSources($dataSources);

		$view =& $this->view;

		if ($flexform['templateFile']) {
			$view = $this->objectManager->get('Tx_Fluid_View_StandaloneView');
			$view->setTemplateSource(file_get_contents(PATH_site . $flexform['templateFile']));
		} else if ($flexform['templateSource']) {
			$view = $this->objectManager->get('Tx_Fluid_View_StandaloneView');
			$view->setTemplateSource($flexform['templateSource']);
		}

		$view->assign('dataSources', $dataSources);

		return $view->render();
	}

	/**
	 * Displays a single DataSource or a list of DataSources from Flexform definitions
	 *
	 * @param Tx_Fed_Domain_Model_DataSource $dataSource the DataSource to display
	 * @return string The rendered view
	 */
	public function showAction(Tx_Fed_Domain_Model_DataSource $dataSource) {
		$this->view->assign('dataSource', $dataSource);
	}

	/**
	 * Displays a form for creating a new  DataSource
	 *
	 * @param Tx_Fed_Domain_Model_DataSource $newDataSource a fresh DataSource object which has not yet been added to the repository
	 * @return void
	 * @dontvalidate $newDataSource
	 */
	public function newAction(Tx_Fed_Domain_Model_DataSource $newDataSource = null) {
		$this->view->assign('newDataSource', $newDataSource);
	}

	/**
	 * Creates a new DataSource and forwards to the list action.
	 *
	 * @param Tx_Fed_Domain_Model_DataSource $newDataSource a fresh DataSource object which has not yet been added to the repository
	 * @return void
	 */
	public function createAction(Tx_Fed_Domain_Model_DataSource $newDataSource) {
		$this->dataSourceRepository->add($newDataSource);
		$this->flashMessageContainer->add('Your new DataSource was created.');


			if(!empty($_FILES)){
				$this->flashMessageContainer->add('File upload is not yet supported by the Persistence Manager. You have to implement it yourself.');
			}

		$this->redirect('list');
	}

	/**
	 * Displays a form for editing an existing DataSource
	 *
	 * @param Tx_Fed_Domain_Model_DataSource $dataSource the DataSource to display
	 * @return string A form to edit a DataSource
	 */
	public function editAction(Tx_Fed_Domain_Model_DataSource $dataSource) {
		$this->view->assign('dataSource', $dataSource);
	}

	/**
	 * Updates an existing DataSource and forwards to the list action afterwards.
	 *
	 * @param Tx_Fed_Domain_Model_DataSource $dataSource the DataSource to display
	 * @return
	 */
	public function updateAction(Tx_Fed_Domain_Model_DataSource $dataSource) {
		$this->dataSourceRepository->update($dataSource);
		$this->flashMessageContainer->add('Your DataSource was updated.');
		$this->redirect('list');
	}

	/**
	 * Deletes an existing DataSource
	 *
	 * @param Tx_Fed_Domain_Model_DataSource $dataSource the DataSource to be deleted
	 * @return void
	 */
	public function deleteAction(Tx_Fed_Domain_Model_DataSource $dataSource) {
		$this->dataSourceRepository->remove($dataSource);
		$this->flashMessageContainer->add('Your DataSource was removed.');
		$this->redirect('list');
	}

	/**
	 * Initializes the current action
	 *
	 * @return void
	 */
	public function initializeAction() {
		$this->dataSourceRepository = t3lib_div::makeInstance(Tx_Fed_Domain_Repository_DataSourceRepository);
	}

}
?>