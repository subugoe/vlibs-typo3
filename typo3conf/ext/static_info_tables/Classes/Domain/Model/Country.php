<?php
namespace SJBR\StaticInfoTables\Domain\Model;
/***************************************************************
*  Copyright notice
*
*  (c) 2011-2012 Armin Rüdiger Vieweg <info@professorweb.de>
*  (c) 2013 Stanislas Rolland <typo3(arobas)sjbr.ca>
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
 * The Country model
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Country extends AbstractEntity {
	/**
	 * @var string
	 */
	protected $addressFormat = '';

	/**
	 * @var string
	 */
	protected $capitalCity = '';

	/**
	 * Currency code as number (i.e. 978)
	 * @var integer ISO 4217 Nr Currency code
	 */
	protected $currencyIsoCodeNumber = 0;

	/**
	 * Currency code as three digit string (i.e. EUR)
	 * @var string ISO 4217 A3 Currency code
	 */
	protected $currencyIsoCodeA3 = '';

	/**
	 * Deletion status of the object
	 * @var boolean
	 */
	protected $deleted = FALSE;

	/**
	 * Whether or not the country is a member of the European Union
	 * @var boolean
	 */
	protected $euMember = FALSE;

	/**
	 * Country code as two digit string (i.e. AT)
	 * @var string ISO 3166-1 A2 Country code
	 */
	protected $isoCodeA2 = '';

	/**
	 * Country code as three digit string (i.e. AUT)
	 * @var string ISO 3166-1 A3 Country code
	 */
	protected $isoCodeA3 = '';

	/**
	 * Country code as number (i.e. 40)
	 * @var integer ISO 3166-1 Nr Country code
	 */
	protected $isoCodeNumber = 0;

	/**
	 * The official name of the country in English
	 * @var string
	 */
	protected $officialNameEn = '';

	/**
	 * The official name of the country in local language and local script
	 * @var string
	 */
	protected $officialNameLocal = '';

	/**
	 * UN number of territory in which the country is located
	 * @var integer
	 */
	protected $parentTerritoryUnCodeNumber = 0;

	/**
	 * The international phone prefix for the country
	 * @var integer
	 */
	protected $phonePrefix = 0;

	/**
	 * @var string
	 */
	protected $shortNameEn = '';

	/**
	 * @var string
	 */
	protected $shortNameLocal = '';

	/**
	 * Whether the country is a member of the UNO or not
	 * @var boolean
	 */
	protected $unMember = FALSE;

	/**
	 * The Internet top level domain of the country
	 * @var string
	 */
	protected $topLevelDomain = '';

	/**
	 * @var boolean
	 */
	protected $zoneFlag = FALSE;

	/**
	 * Sets the address format.
	 *
	 * @param string $addressFormat
	 *
	 * @return void
	 */
	public function setAddressFormat($addressFormat) {
		$this->addressFormat = $addressFormat;
	}

	/**
	 * Gets the address format.
	 *
	 * @return string
	 */
	public function getAddressFormat() {
		return $this->addressFormat;
	}

	/**
	 * Sets the name of the capital city
	 *
	 * @param string $capitalCity
	 *
	 * @return void
	 */
	public function setCapitalCity($capitalCity) {
		$this->capitalCity = $capitalCity;
	}

	/**
	 * Gets the name of the capital city
	 *
	 * @return string
	 */
	public function getCapitalCity() {
		return $this->capitalCity;
	}

	/**
	 * Sets the ISO A3 currency code.
	 *
	 * @param string $currencyIsoCodeA3
	 *
	 * @return void
	 */
	public function setCurrencyIsoCodeA3($currencyIsoCodeA3) {
		$this->currencyIsoCodeA3 = $currencyIsoCodeA3;
	}

	/**
	 * Gets the ISO A3 currency code.
	 *
	 * @return string
	 */
	public function getCurrencyIsoCodeA3() {
		return $this->currencyIsoCodeA3;
	}

	/**
	 * Sets the ISO numeric currency code
	 *
	 * @param integer $currencyIsoCodeNumber
	 *
	 * @return void
	 */
	public function setCurrencyIsoCodeNumber($currencyIsoCodeNumber) {
		$this->currencyIsoCodeNumber = $currencyIsoCodeNumber;
	}

	/**
	 * Gets the ISO numeric currency code
	 *
	 * @return integer
	 */
	public function getCurrencyIsoCodeNumber() {
		return $this->currencyIsoCodeNumber;
	}

	/**
	 * Sets whether this country is a member of the European Union.
	 *
	 * @param boolean $euMember
	 *
	 * @return void
	 */
	public function setEuMember($euMember) {
		$this->euMember = $euMember;
	}

	/**
	 * Gets the deletion status of the entity
	 *
	 * @return boolean
	 */
	public function getDeleted() {
		return $this->deleted;
	}

	/**
	 * Sets the deletion status of the entity
	 *
	 * @param boolean $deleted
	 * @return void
	 */
	public function setDeleted($deleted) {
		return $this->deleted = $deleted;
	}

	/**
	 * Gets whether this country is a member of the European Union.
	 *
	 * @return boolean
	 */
	public function getEuMember() {
		return $this->euMember;
	}

	/**
	 * Gets whether this country is a member of the European Union.
	 *
	 * This method is a synonym for the getEuMember method.
	 *
	 * @return boolean
	 */
	public function isEuMember() {
		return $this->getEuMember();
	}

	/**
	 * Sets the ISO alpha-2 code.
	 *
	 * @param string $isoCodeA2
	 *
	 * @return void
	 */
	public function setIsoCodeA2($isoCodeA2) {
		$this->isoCodeA2 = $isoCodeA2;
	}

	/**
	 * Gets the ISO alpha-2 code.
	 *
	 * @return string
	 */
	public function getIsoCodeA2() {
		return $this->isoCodeA2;
	}

	/**
	 * Sets the ISO alpha-3 code.
	 *
	 * @param string $isoCodeA3
	 *
	 * @return void
	 */
	public function setIsoCodeA3($isoCodeA3) {
		$this->isoCodeA3 = $isoCodeA3;
	}

	/**
	 * Gets the ISO alpha-3 code.
	 *
	 * @return string
	 */
	public function getIsoCodeA3() {
		return $this->isoCodeA3;
	}

	/**
	 * Sets the ISO code number.
	 *
	 * @param integer $isoCodeNumber
	 *
	 * @return void
	 */
	public function setIsoCodeNumber($isoCodeNumber) {
		$this->isoCodeNumber = $isoCodeNumber;
	}

	/**
	 * Gets the ISO code number.
	 *
	 * @return integer
	 */
	public function getIsoCodeNumber() {
		return $this->isoCodeNumber;
	}

	/**
	 * Sets the official name of the country in English
	 *
	 * @param string $officialNameEn
	 *
	 * @return void
	 */
	public function setOfficialNameEn($officialNameEn) {
		$this->officialNameEn = $officialNameEn;
	}

	/**
	 * Gets the official name of the country in English
	 *
	 * @return string
	 */
	public function getOfficialNameEn() {
		return $this->officialNameEn;
	}

	/**
	 * Sets the official name of the country in local language and script
	 *
	 * @param string $officialNameLocal
	 *
	 * @return void
	 */
	public function setOfficialNameLocal($officialNameLocal) {
		$this->officialNameLocal = $officialNameLocal;
	}

	/**
	 * Gets the official name of the country in local language and script
	 *
	 * @return string
	 */
	public function getOfficialNameLocal() {
		return $this->officialNameLocal;
	}

	/**
	 * Sets the parent territory UN numeric code.
	 *
	 * @param integer $parentTerritoryUnCodeNumber
	 *
	 * @return void
	 */
	public function setParentTerritoryUnCodeNumber($parentTerritoryUnCodeNumber) {
		$this->parentTerritoryUnCodeNumber = $parentTerritoryUnCodeNumber;
	}

	/**
	 * Gets the parent territory UN numeric code.
	 *
	 * @return integer
	 */
	public function getParentTerritoryUnCodeNumber() {
		return $this->parentTerritoryUnCodeNumber;
	}

	/**
	 * Sets the phone prefix.
	 *
	 * @param integer $phonePrefix
	 *
	 * @return void
	 */
	public function setPhonePrefix($phonePrefix) {
		$this->phonePrefix = $phonePrefix;
	}

	/**
	 * Gets the phone prefix.
	 *
	 * @return integer
	 */
	public function getPhonePrefix() {
		return $this->phonePrefix;
	}

	/**
	 * Sets the English short name.
	 *
	 * @param string $shortNameEn
	 *
	 * @return void
	 */
	public function setShortNameEn($shortNameEn) {
		$this->shortNameEn = $shortNameEn;
	}

	/**
	 * Gets the English short name.
	 *
	 * @return string
	 */
	public function getShortNameEn() {
		return $this->shortNameEn;
	}

	/**
	 * Sets the short local name.
	 *
	 * @param string $shortNameLocal
	 *
	 * @return void
	 */
	public function setShortNameLocal($shortNameLocal) {
		$this->shortNameLocal = $shortNameLocal;
	}

	/**
	 * Gets the short local name.
	 *
	 * @return string
	 */
	public function getShortNameLocal() {
		return $this->shortNameLocal;
	}

	/**
	 * Sets the top-level domain.
	 *
	 * @param string $topLevelDomain
	 *
	 * @return void
	 */
	public function setTopLevelDomain($topLevelDomain) {
		$this->topLevelDomain = $topLevelDomain;
	}

	/**
	 * Gets the top-level domain.
	 *
	 * @return string
	 */
	public function getTopLevelDomain() {
		return $this->topLevelDomain;
	}

	/**
	 * Sets whether this country is a member of the United Nations.
	 *
	 * @param boolean $unMember
	 *
	 * @return void
	 */
	public function setUnMember($unMember) {
		$this->unMember = $unMember;
	}

	/**
	 * Gets whether this country is a member of the United Nations.
	 *
	 * @return boolean
	 */
	public function getUnMember() {
		return $this->unMember;
	}

	/**
	 * Sets whether this country is a member of the United Nations.
	 *
	 * This method is a synonym for the getUnMember method.
	 *
	 * @return boolean
	 */
	public function isUnMember() {
		return $this->getUnMember();
	}

	/**
	 * Sets the zone flag.
	 *
	 * @param boolean $zoneFlag
	 *
	 * @return void
	 */
	public function setZoneFlag($zoneFlag) {
		$this->zoneFlag = $zoneFlag;
	}

	/**
	 * Gets the zone flag.
	 *
	 * @return boolean
	 */
	public function getZoneFlag() {
		return $this->zoneFlag;
	}
}
?>