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
	 * @return Array
	 */
	protected function defaultSettings () {
		$defaults = parent::defaultSettings();

		$defaults['pz2-neuerwerbungenJSPath'] = t3lib_extMgm::siteRelPath('pazpar2neuerwerbungen') . 'Resources/Public/pz2-neuerwerbungen.js';
		$defaults['pz2-neuerwerbungenCSSPath'] = t3lib_extMgm::siteRelPath('pazpar2neuerwerbungen') . 'Resources/Public/pz2-neuerwerbungen.css';
		$defaults['subjects'] = '';

		return $defaults;
	}


	
	/**
	 * Index: Make superclass insert <script> and <link> tags into <head>.
	 * Create pazpar2neuerwerbungen model object, load subjects, assign subjects
	 *	and current months list to the model object and assign it to view.
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



	/*
	 * reduceMonth: Assume the passed references to month and year numbers
	 *	indicate a month; reduce them to indicate the previous month.
	 *
	 * @param $month reference to month number
	 * @param $year reference to year number
	 * @return void
	 */
	private function reduceMonth (&$month, &$year) {
		if ($month == 1) {
			$month = 12;
			$year--;
		}
		else {
			$month--;
		}
	}



	/*
	 * Return search string for Pica format of the given month: YYYYMM
	 *
	 * @param $month month number
	 * @param $year year number
	 * @return string the given month in YYYYMM format
	 */
	private function picaSearchStringForMonth ($month, $year) {
		$leadingZero = '';
		
		if ($month < 10) {
			$leadingZero = '0';
		}
		
		return $year . $leadingZero . $month;
	}



	/*
	 * Returns array of months preceding the current one.
	 *	* Keys are of the form YYYYMM.
	 *	* Values are localised names of the months followed by the year.
	 *		Localised '(incomplete)' is appended to the name of the current month.
	 *
	 * @param $numberOfMonths (default = 13)
	 * @return Array
	 */
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



	/*
	 * Return array of subjects.
	 *	conf['subjects'] determines the name of the data file in Configuration/Subjects.
	 *  Running the file sets the variable $subjectGroups to the array of subjects.	 *
	 *  Its form should be
	 *		* Array [subject groups]
	 *			* Array [subject group, associative]
	 *				* name => string - name of subject group [required]
	 *				* GOKs => Array [optional]
	 *					* string that is a truncated GOK notation
	 *				* subjects => Array [required, subjects]
	 *					* Array [subject, associative]
	 *						* name => string - name of subject group [required]
	 *						* GOKs => Array [required]
	 *							* string that is a truncated GOK notation
	 *						* inline => boolean - displayed in one line with other items?
	 *							[optional, defaults to false]
	 *						* break => boolean - insert <br> before current element?
	 *							[optional, should only be used with inline => true, defaults to false]
	 *
	 * If the GOKs field of a subject group is not specified, create it by taking
	 *	the union of the GOKs arrays of all its subjects.
	 *
	 * @return Array subjects to be displayed
	 */
	private function getSubjectsArray () {
		$subjectsFile = 'Configuration/Subjects/' . $this->conf['subjects'] . '.php';
		require_once(t3lib_extMgm::siteRelPath('pazpar2neuerwerbungen') . $subjectsFile);

		foreach ($subjectGroups as &$subjectGroup) {
			if (!$subjectGroup['GOKs']) {
				$GOKs = array();
				foreach ($subjectGroup['subjects'] as $subject) {
					foreach ($subject['GOKs'] as $GOK) {
						$GOKs[] = $GOK;
					}
				}
				$subjectGroup['GOKs'] = $GOKs;
			}
		}

		return $subjectGroups;
	}



	/**
	 * Inserts headers into page: first general ones by the superclass,
	 *	then our own.
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
