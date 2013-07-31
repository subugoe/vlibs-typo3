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
 * The Currency model
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 */
class Currency extends AbstractEntity {

	/**
	 * The number of decimals to be shown when an amount is presented in this currency
	 * @var integer
	 */
	protected $decimalDigits = 0;

	/**
	 * The character to be shown in front of the decimals when an amount is presented in this currency
	 * @var string
	 */
	protected $decimalPoint = '';

	/**
	 * Deletion status of the object
	 * @var boolean
	 */
	protected $deleted = FALSE;

	/**
	 * The divisor used to obtain the subdivision of the currency
	 * @var integer
	 */
	protected $divisor = 0;

	/**
	 * Currency code as three digit string (i.e. EUR)
	 * @var string ISO 4217 alpha-3 currency code
	 */
	protected $isoCodeA3 = '';

	/**
	 * Currency code as number
	 * @var integer ISO 4217 numeric currency code
	 */
	protected $isoCodeNumber = 0;

	/**
	 * English name of the currency
	 * @var string
	 */
	protected $nameEn = '';

	/**
	 * English name of the currency subdivision unit
	 * @var string
	 */
	protected $subdivisionNameEn = '';

	/**
	 * The symbol to be shown to the left of an amount stated in units of the subdivision of the currency
	 * @var string
	 */
	protected $subdivisionSymbolLeft = '';

	/**
	 * The symbol to be shown to the right of an amount stated in units of the subdivision of the currency
	 * @var string
	 */
	protected $subdivisionSymbolRight = '';

	/**
	 * The symbol to be shown to the left of an amount stated in units of the currency
	 * @var string
	 */
	protected $symbolLeft = '';

	/**
	 * The symbol to be shown to the right of an amount stated in units of the currency
	 * @var string
	 */
	protected $symbolRight = '';

	/**
	 * Character to be used between every group of thousands of an amount stated in units of this currency
	 * @var string
	 */
	protected $thousandsPoint = '';

	/**
	 * Sets the number of decimal digits.
	 *
	 * @param integer $decimalDigits
	 *
	 * @return void
	 */
	public function setDecimalDigits($decimalDigits) {
		$this->decimalDigits = $decimalDigits;
	}

	/**
	 * Gets the number of decimal digits.
	 *
	 * @return integer
	 */
	public function getDecimalDigits() {
		return $this->decimalDigits;
	}

	/**
	 * Sets the decimal point character
	 *
	 * @param string $decimalPoint
	 *
	 * @return void
	 */
	public function setDecimalPoint($decimalPoint) {
		$this->decimalPoint = $decimalPoint;
	}

	/**
	 * Gets the decimal point character
	 *
	 * @return string
	 */
	public function getDecimalPoint() {
		return $this->decimalPoint;
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
	 * Sets the divisor.
	 *
	 * @param integer $divisor
	 *
	 * @return void
	 */
	public function setDivisor($divisor) {
		$this->divisor = $divisor;
	}

	/**
	 * Gets the divisor.
	 *
	 * @return integer
	 */
	public function getDivisor() {
		return $this->divisor;
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
	 * Sets the English name of the currency
	 *
	 * @param string $nameEn
	 *
	 * @return void
	 */
	public function setNameEn($nameEn) {
		$this->nameEn = $nameEn;
	}

	/**
	 * Gets the English name of the currency
	 *
	 * @return string
	 */
	public function getNameEn() {
		return $this->nameEn;
	}

	/**
	 * Sets the English name of the currency subdivision
	 *
	 * @param string $subdivisionNameEn
	 *
	 * @return void
	 */
	public function setSubdivisionNameEn($subdivisionNameEn) {
		$this->subdivisionNameEn = $subdivisionNameEn;
	}

	/**
	 * Gets the English name of the currency subdivision
	 *
	 * @return string
	 */
	public function getSubdivisionNameEn() {
		return $this->subdivisionNameEn;
	}

	/**
	 * Sets the left-hand side symbol for an amount stated in units of the subdivision of the currency
	 *
	 * @param string $subdivisionSymbolLeft
	 *
	 * @return void
	 */
	public function setSubdivisionSymbolLeft($subdivisionSymbolLeft) {
		$this->subdivisionSymbolLeft = $subdivisionSymbolLeft;
	}

	/**
	 * Gets the left-hand side symbol for an amount stated in units of the subdivision of the currency
	 *
	 * @return string
	 */
	public function getSubdivisionSymbolLeft() {
		return $this->subdivisionSymbolLeft;
	}

	/**
	 * Sets the right-hand side symbol for an amount stated in units of the subdivision of the currency
	 *
	 * @param string $subdivisionSymbolRight
	 *
	 * @return void
	 */
	public function setSubdivisionSymbolRight($subdivisionSymbolRight) {
		$this->subdivisionSymbolRight = $subdivisionSymbolRight;
	}

	/**
	 * Gets the right-hand side symbol for an amount stated in units of the subdivision of the currency
	 *
	 * @return string
	 */
	public function getSubdivisionSymbolRight() {
		return $this->subdivisionSymbolRight;
	}

	/**
	 * Sets the symbol to be shown to the left of an amount stated in units of the currency
	 *
	 * @param string $symbolLeft
	 *
	 * @return void
	 */
	public function setSymbolLeft($symbolLeft) {
		$this->symbolLeft = $symbolLeft;
	}

	/**
	 * Gets the symbol to be shown to the left of an amount stated in units of the currency
	 *
	 * @return string
	 */
	public function getSymbolLeft() {
		return $this->symbolLeft;
	}

	/**
	 * Sets the symbol to be shown to the right of an amount stated in units of the currency
	 *
	 * @param string $symbolRight
	 *
	 * @return void
	 */
	public function setSymbolRight($symbolRight) {
		$this->symbolRight = $symbolRight;
	}

	/**
	 * Gets the symbol to be shown to the right of an amount stated in units of the currency
	 *
	 * @return string
	 */
	public function getSymbolRight() {
		return $this->symbolRight;
	}

	/**
	 * Sets the thousands point/separator.
	 *
	 * @param string $thousandsPoint
	 *
	 * @return void
	 */
	public function setThousandsPoint($thousandsPoint) {
		$this->thousandsPoint = $thousandsPoint;
	}

	/**
	 * Gets the thousands point/separator.
	 *
	 * @return string
	 */
	public function getThousandsPoint() {
		return $this->thousandsPoint;
	}
}
?>