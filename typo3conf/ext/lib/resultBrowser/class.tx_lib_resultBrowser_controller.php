<?php

/**
 * Controller class of the resultbrowser
 *
 * PHP versions 4 and 5
 *
 * Copyright (c) 2006-2007 Elmar Hinz
 *
 * LICENSE:
 *
 * This library is free software; you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation; either
 * version 2.1 of the License, or (at your option) any later version.
 *
 * This library is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
 * Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public
 * License along with this library; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307 USA
 *
 * @package    TYPO3
 * @subpackage lib
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @copyright  2006-2007 Elmar Hinz
 * @license    http://www.opensource.org/licenses/lgpl-license.php LGPL
 * @version    SVN: $Id: class.tx_lib_captcha.php 5733 2007-06-21 15:27:25Z sir_gawain $
 * @since      0.1
 */


/**
 * Controller class of the resultbrowser
 *
 * Create a resultbrowser:
 * <code>
 *  $resultbrowser = tx_div::makeInstance('tx_lib_resultBrowser_controller');
 *  $resultbrowser->main(NULL, $configuration, $parameters, $context);
 * </code>
 *
 * @author     Elmar Hinz <elmar.hinz@team-red.net>
 * @package    TYPO3
 * @subpackage lib
 */
class tx_lib_resultBrowser_controller extends tx_lib_controller {

	function defaultAction() {
		$model = $this->makeInstance('tx_lib_resultBrowser_model');
		$view = $this->makeInstance('tx_lib_resultBrowser_view', $model);
		$view->setPathToTemplateDirectory('EXT:lib/resultBrowser/');
		return $view->render('template.php');
	}

}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_resultBrowser_controller.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/lib/class.tx_lib_resultBrowser_controller.php']);
}
?>
