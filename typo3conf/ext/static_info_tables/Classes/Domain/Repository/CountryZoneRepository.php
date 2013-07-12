<?php
namespace SJBR\StaticInfoTables\Domain\Repository;
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
 * Repository for \SJBR\StaticInfoTables\Domain\Model\CountryZone
 */
class CountryZoneRepository extends AbstractEntityRepository {

	/**
	 * @var array ISO keys for this static table
	 */
	protected $isoKeys = array('zn_country_iso_2', 'zn_code');

	/**
	 * Finds country zones by country
	 *
	 * @param \SJBR\StaticInfoTables\Domain\Model\Country $country
	 *
	 * @return \TYPO3\CMS\Extbase\Persistence\QueryResultInterface|array
	 */
	public function findByCountry(\SJBR\StaticInfoTables\Domain\Model\Country $country) {
		$query = $this->createQuery();
		$query->matching(
			$query->equals('countryIsoCodeNumber', $country->getIsoCodeNumber())
		);
		return $query->execute();
	}

	/**
	 * Finds country zones by country ordered by localized name
	 *
	 * @param \SJBR\StaticInfoTables\Domain\Model\Country $country
	 *
	 * @return array Country zones of the country sorted by localized name
	 */
	public function findByCountryOrderedByLocalizedName(\SJBR\StaticInfoTables\Domain\Model\Country $country) {
		$entities = $this->findByCountry($country);
		return $this->localizedSort($entities);
	}
}
?>