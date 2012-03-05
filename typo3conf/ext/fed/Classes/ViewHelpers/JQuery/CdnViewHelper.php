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
 * Loads required resources for jQuery and jQueryUI usage. Gets the source
 * files from Google CDN to improve performance and chance of cache hits - plus
 * adds parallel loading to generally decrease page-ready waiting time. Good-
 *
 * Can be called multiple times but only the first instance encountered is
 * respected - this to avoid version collissions.
 *
 * You should NOT use this if you are using t3jquery to always load jQuery!
 *
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage ViewHelpers\JQuery
 * @uses jQuery
 */
class Tx_Fed_ViewHelpers_JQuery_CdnViewHelper extends Tx_Fed_ViewHelpers_JQueryViewHelper {


}

?>