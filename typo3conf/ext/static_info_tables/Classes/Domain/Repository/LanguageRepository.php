<?php
namespace SJBR\StaticInfoTables\Domain\Repository;
/***************************************************************
*  Copyright notice
*
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
 * Repository for \SJBR\StaticInfoTables\Domain\Model\Language
 *
 */
class LanguageRepository extends AbstractEntityRepository {

	/**
	 * @var array ISO keys for this static table
	 */
	protected $isoKeys = array('lg_iso_2', 'lg_country_iso_2');

	/**
	 * Find all neither constructed nor sacred languages
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array all languages neither constructed nor sacred
	 */
	public function findAllNonConstructedNonSacred() {
		$query = $this->createQuery();
		$query->matching($query->logicalAnd(
			$query->equals('constructedLanguage', FALSE),
			$query->equals('sacredLanguage', FALSE)
		));
		return $query->execute();	 	 
	}

	/**
	 * Find the language object with the specified iso codes
	 *
	 * @param string $languageIsoCodeA2
	 * @param string $countryIsoCodeA2
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array all entries ordered by $propertyName
	 */
	public function findOneByIsoCodes($languageIsoCodeA2, $countryIsoCodeA2 = '') {
		$query = $this->createQuery();
		$query->matching($query->logicalAnd(
			$query->equals('isoCodeA2', $languageIsoCodeA2),
			$query->equals('countryIsoCodeA2', $countryIsoCodeA2)
		));
		return $query->execute()->getFirst();
	}
}
?>