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

class tx_libconnect_controllers_search extends tx_lib_controller {

    public function displayFormAction() {

        if ($this->configurations->get('searchPid') === null) {
            echo 'ERROR: Please provide parameter configurations.searchPid for search controller';
            return;
        }

        $cObject = $this->findCObject();
        $searchUrl = $cObject->getTypolink_URL($this->configurations->get('searchPid'));

        $vars = $this->makeInstance('tx_lib_object');
        $vars->set('search_url', $searchUrl);

        $view = $this->makeInstance('tx_libconnect_views_smarty', $vars);
        $view->setTemplatePath($this->configurations->get('templatePath'));
        $output = $view->render("searchform.tpl");
        return $output;
    }

    public function switchAction() {

        if ($this->configurations->get('externalSearch') === null) {
            echo 'ERROR: Please provide parameter configurations.externalSearch for search controller';
            return;
        }

        if ($this->parameters->get('search_switch') == 'beluga') {

            $sword = $_REQUEST['q'];

            header('location: ' . $this->configurations->get('externalSearch') . $sword);
        }
    }

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/libconnect/controllers/class.tx_libconnect_controllers_search.php']) {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/libconnect/controllers/class.tx_libconnect_controllers_search.php']);
}
?>