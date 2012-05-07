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
 * Data Source for Fluid Display
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage Domain\Model
 * @ExtJS
 */
 class Tx_Fed_Domain_Model_DataSource extends Tx_Extbase_DomainObject_AbstractEntity {

	/**
	 * Name of this data source
	 *
	 * @var string $name
	 * @validate NotEmpty
	 * @ExtJS
	 */
	protected $name;

	/**
	 * Description of this data source
	 *
	 * @var string $description
	 * @ExtJS
	 */
	protected $description;

	/**
	 * If specified, SQL Query is used to collect data
	 *
	 * @var string $query
	 * @ExtJS
	 */
	protected $query;

	/**
	 * If specified, func is called to collect data
	 *
	 * @var string $func
	 * @ExtJS
	 */
	protected $func;

	/**
	 * If specified, data is read from this url - remember to specify method
	 *
	 * @var string $url
	 * @ExtJS
	 */
	protected $url;

	/**
	 * Method used to read url data
	 *
	 * @var string $urlMethod
	 * @ExtJS
	 */
	protected $urlMethod;

	/**
	 * Fluid template file to use when rendering this DataSource
	 *
	 * @var string $templateFile
	 * @ExtJS
	 */
	protected $templateFile;

	/**
	 * Optional Fluid template source code to use for rendering
	 *
	 * @var string $templateSource
	 * @ExtJS
	 */
	protected $templateSource;

	/**
	 * Enter description here ...
	 *
	 * @var array
	 * @ExtJS
	 */
	protected $data;

	/**
	 * Sets the name
	 *
	 * @param string $name
	 * @return void
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Returns the name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Sets the description
	 *
	 * @param string $description
	 * @return void
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Returns the description
	 *
	 * @return string
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Sets the query
	 *
	 * @param string $query
	 * @return void
	 */
	public function setQuery($query) {
		$this->query = $query;
	}

	/**
	 * Returns the query
	 *
	 * @return string
	 */
	public function getQuery() {
		return $this->query;
	}

	/**
	 * Sets the func
	 *
	 * @param string $func
	 * @return void
	 */
	public function setFunc($func) {
		$this->func = $func;
	}

	/**
	 * Returns the func
	 *
	 * @return string
	 */
	public function getFunc() {
		return $this->func;
	}

	/**
	 * Sets the url
	 *
	 * @param string $url
	 * @return void
	 */
	public function setUrl($url) {
		$this->url = $url;
	}

	/**
	 * Returns the url
	 *
	 * @return string
	 */
	public function getUrl() {
		return $this->url;
	}

	/**
	 * Sets the urlMethod
	 *
	 * @param string $urlMethod
	 * @return void
	 */
	public function setUrlMethod($urlMethod) {
		$this->urlMethod = $urlMethod;
	}

	/**
	 * Returns the urlMethod
	 *
	 * @return string
	 */
	public function getUrlMethod() {
		return $this->urlMethod;
	}

	/**
	 * Sets the templateFile
	 *
	 * @param string $templateFile
	 * @return void
	 */
	public function setTemplateFile($templateFile) {
		$this->templateFile = $templateFile;
	}

	/**
	 * Returns the templateFile
	 *
	 * @return string
	 */
	public function getTemplateFile() {
		return $this->templateFile;
	}

	/**
	 * Sets the templateSource
	 *
	 * @param string $templateSource
	 * @return void
	 */
	public function setTemplateSource($templateSource) {
		$this->templateSource = $templateSource;
	}

	/**
	 * Returns the templateSource
	 *
	 * @return string
	 */
	public function getTemplateSource() {
		return $this->templateSource;
	}

	/**
	 *
	 * @param array $data
	 */
	public function setData($data) {
		$this->data = $data;
	}

	/**
	 * @return array
	 */
	public function getData() {
		return $this->data;
	}

	/**
	 * __construct
	 *
	 * @return void
	 */
	public function __construct() {

	}

}
?>