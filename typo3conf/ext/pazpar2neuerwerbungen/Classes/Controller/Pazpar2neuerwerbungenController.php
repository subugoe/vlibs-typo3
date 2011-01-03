<?php
/*************************************************************************
 *  Copyright notice
 *
 *  © 2010-2011 Sven-S. Porst, SUB Göttingen <porst@sub.uni-goettingen.de>
 *  All rigs reserved
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  This copyright notice MUST APPEAR in all copies of the script.
 *************************************************************************/


/**
 * Pazpar2neuerwerbungenController.php
 *
 * Main controller for pazpar2 Neuerwerbungen plug-in,
 * a subclass of the pazpar2 plug-in.
 *
 * @author Sven-S. Porst <porst@sub-uni-goettingen.de>
 */

require_once(t3lib_extMgm::extPath('pazpar2') . 'Classes/Controller/Pazpar2Controller.php');



/**
 * Controller for the pazpar2 Neuerwerbungen package.
 */
class Tx_Pazpar2neuerwerbungen_Controller_Pazpar2neuerwerbungenController extends Tx_Pazpar2_Controller_Pazpar2Controller {

	/**
	 * defaultSettings: Return array with default settings.
	 * Add own default settings to those set by the superclass.
	 *
	 * @return array
	 */
	protected function defaultSettings () {
		$defaults = parent::defaultSettings();

		$defaults['pz2-neuerwerbungenJSPath'] = t3lib_extMgm::siteRelPath('pazpar2neuerwerbungen') . 'Resources/Public/pz2-neuerwerbungen.js';
		$defaults['pz2-neuerwerbungenCSSPath'] = t3lib_extMgm::siteRelPath('pazpar2neuerwerbungen') . 'Resources/Public/pz2-neuerwerbungen.css';
		$defaults['subjects'] = '';

		return $defaults;
	}


	

	/**
	 * Index: Insert pazpar2 CSS <link> and JavaScript <script>-tags into
	 * the page’s <head> which are required to make the search work.
	 *
	 * @return void
	 */
	public function indexAction () {
		parent::indexAction();

		$pz2Neuerwerbungen = new Tx_Pazpar2neuerwerbungen_Domain_Model_Pazpar2neuerwerbungen;
		$pz2Neuerwerbungen->setSubjects( $this->getSubjectsArray() );
		$pz2Neuerwerbungen->setMonths( $this->monthsArray() );
		$this->view->assign('pazpar2neuerwerbungen', $pz2Neuerwerbungen);
	}

	private function reduceMonth (&$month, &$year) {
		if ($month == 1) {
			$month = 12;
			$year--;
		}
		else {
			$month--;
		}
	}
	
	private function picaSearchStringForMonth ($month, $year) {
		$leadingZero = '';
		
		if ($month < 10) {
			$leadingZero = '0';
		}
		
		return $year . $leadingZero . $month;
	}


	private function monthsArray ($numberOfMonths = 13) {
		$months = array();
		$year = date('Y');
		$month = date('n');
		
		$currentMonthSearchString = $this->picaSearchStringForMonth($month, $year);
		
		for ($i = 1; $i <= $numberOfMonths; $i++) {
			$searchString = $this->picaSearchStringForMonth($month, $year);
			
			/* make sure the text encoding in the locale_all setting matches the encoding 
					of the page, otherwise umlauts in month names may appear broken */  
			$monthName = strftime('%B', mktime(0, 0, 0, $month, 1, 2010));
			$displayString = $monthName . ' ' . $year;

			if ($i == 1) {
				$displayString .= ' (' . Tx_Extbase_Utility_Localization::translate('unvollständig', 'pazpar2neuerwerbungen') . ')';
			}

			$months[$searchString] = $displayString;

			$this->reduceMonth($month, $year);
		}
		
		return $months;
	}


	private function getSubjectsArray () {
		$subjectsFile = 'Configuration/Subjects/' . $this->conf['subjects'] . '.php';
		require_once(t3lib_extMgm::siteRelPath('pazpar2neuerwerbungen') . $subjectsFile);
		return $subjects;	
	}



	/**
	 * Helper: Inserts pazpar2 headers into page.
	 *
	 * @return void
	 */
	protected function addResourcesToHead () {
		parent::addResourcesToHead();

		// Add pz2-neuerwerbungen.css to <head>.
		$cssTag = new Tx_Fluid_Core_ViewHelper_TagBuilder('link');
		$cssTag->addAttribute('rel', 'stylesheet');
		$cssTag->addAttribute('type', 'text/css');
		$cssTag->addAttribute('href', $this->conf['pz2-neuerwerbungenCSSPath']);
		$cssTag->addAttribute('media', 'all');
		$this->response->addAdditionalHeaderData( $cssTag->render() );

		// Add pz2-neuerwerbungen.js to <head>. ***************
		$scriptTag = new Tx_Fluid_Core_ViewHelper_TagBuilder('script');
		$scriptTag->addAttribute('type', 'text/javascript');
		$scriptTag->addAttribute('src', $this->conf['pz2-neuerwerbungenJSPath']) ;
		$scriptTag->forceClosingTag(true);
		$this->response->addAdditionalHeaderData( $scriptTag->render() );

		// Make jQuery initialise pazpar2neuerwerbungen when the DOM is ready.
		$jsCommand = 'jQuery(document).ready(pz2neuerwerbungenDOMReady);';
		$scriptTag = new Tx_Fluid_Core_ViewHelper_TagBuilder('script');
		$scriptTag->addAttribute('type', 'text/javascript');
		$scriptTag->setContent($jsCommand);
		$this->response->addAdditionalHeaderData( $scriptTag->render() );
	}


}
?>
