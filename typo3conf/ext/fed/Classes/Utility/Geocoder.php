<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2010 Claus Due <claus@wildside.dk>, Wildside A/S
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
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage Utility
 */
class Tx_Fed_Utility_Geocoder implements t3lib_Singleton {

	/**
	 * Geocodes an address
	 *
	 * @param string $address The address to geocode
	 * @return array
	 * @api
	 */
	public function geocode($address) {
		$address = urlencode($address);
		$url = 'http://maps.googleapis.com/maps/api/geocode/json?address='.$address.'&region=DK&sensor=true';
		$json = file_get_contents($url);
		$response = json_decode($json);
		if ($response->status == 'ZERO_RESULTS') {
			return 'Address geocoding of "' . $address . '" yielded zero results';
		} else if ($response->status != 'OK') {
			return FALSE;
		} else {
			$location = $response->results[0]->geometry->location;
			return array('lat' => $location->lat, 'lng' => $location->lng);
		}
	}

}

?>