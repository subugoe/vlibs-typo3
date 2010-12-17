<?php
/********************************************************************
 *  Copyright notice
 *
 *  © 2010 Sven-S. Porst, SUB Göttingen <porst@sub.uni-goettingen.de>
 *  All rights reserved
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 *  This copyright notice MUST APPEAR in all copies of the script.
 ********************************************************************/


/**
 * Pazpar2neuerwerbungenController.php
 *
 * Main controller for pazpar2 Neuerwerbungen plug-in.
 *
 * @author Sven-S. Porst <porst@sub-uni-goettingen.de>
 */




/**
 * Controller for the pazpar2 Neuerwerbungen package.
 */
class Tx_Pazpar2neuerwerbungen_Controller_Pazpar2neuerwerbungenController extends Tx_Extbase_MVC_Controller_ActionController {

	/**
	 * Initialiser
	 *
	 * @return void
	 */
	public function initializeAction () {
		$defaults = array(
			'serviceID' => '',
			'CSSPath' => t3lib_extMgm::siteRelPath('pazpar2') . 'Resources/Public/pz2.css',
			'pz2JSPath' => t3lib_extMgm::siteRelPath('pazpar2') . 'Resources/Public/pz2.js',
			'pz2-clientJSPath' => t3lib_extMgm::siteRelPath('pazpar2') . 'Resources/Public/pz2-client.js',
			'pz2-neuerwerbungenJSPath' => t3lib_extMgm::siteRelPath('pazpar2neuerwerbungen') . 'Resources/Public/pz2-neuerwerbungen.js',
			'pz2-neuerwerbungenCSSPath' => t3lib_extMgm::siteRelPath('pazpar2neuerwerbungen') . 'Resources/Public/pz2-neuerwerbungen.css',
			'useGoogleBooks' => '1',
			'useZDB' => '1',
			'subjects' => '',
		);

		foreach ( $defaults as $key => $value ) {
			// If a setting is present and non-empty, use it. Otherwise use the default value.
			if( $this->settings[$key] !== null && $this->settings[$key] !== '' ) {
				$this->conf[$key] = $this->settings[$key];
			} else {
				$this->conf[$key] = $value;
			}
		}
	}



	/**
	 * Index: Insert pazpar2 CSS <link> and JavaScript <script>-tags into
	 * the page’s <head> which are required to make the search work.
	 *
	 * @return void
	 */
	public function indexAction () {
		$this->addResourcesToHead();
		$pz2Neuerwerbungen = new Tx_Pazpar2neuerwerbungen_Domain_Model_Pazpar2neuerwerbungen;
		$pz2Neuerwerbungen->setSubjects( $this->getSubjectsArray() );
		$this->view->assign('pazpar2neuerwerbungen', $pz2Neuerwerbungen);
	}


	// TODO: use localisation
	private function localise ($string) {
		return $string;
	}

	// TODO: deal with $key being an array
	private function prepareSearchKey ($searchKey) {
		return $searchKey;
	}

	private function checkboxWithTitleAndSearchKey ($title, $searchKey) {
		$checkbox = new Tx_Fluid_Core_ViewHelper_TagBuilder('input');
		$checkbox->addAttribute('type', 'checkbox');
		$checkbox->addAttribute('name', 'subject');
		$checkbox->addAttribute('value', $this->prepareSearchKey($searchKey));
		$checkbox->setContent($title);
		
		return $checkbox;
	}

	private function getSubjectsArray () {
		$subjectsFile = 'Configuration/Subjects/' . $this->conf['subjects'] . '.php';
		require_once(t3lib_extMgm::siteRelPath('pazpar2neuerwerbungen') . $subjectsFile);
		return $subjects;	
	}


	private function addCheckboxes () {
		$mySubjects = $this->getSubjectsArray();
		
		$fieldsetsString = '';
		foreach ( $mySubjects as $sectionName => $section) {
			$localisedSectionName = $this->localise($sectionName);
			$legend = new Tx_Fluid_Core_ViewHelper_TagBuilder('legend');
			if ( $section['searchKey'] ) {
				$checkbox = $this->checkboxWithTitleAndSearchKey($localisedSectionName, $section['searchKey']);
				$legend->setContent( $checkbox->render() );
			}
			else {
				$legend->setContent($localisedSectionName);
			}
		
			$checkboxesString = '';
			foreach ( $section['subjects'] as $subjectName => $subjectSearchKey ) {
				$checkbox = $this->checkboxWithTitleAndSearchKey($this->localise($subjectName), $subjectSearchKey);
				$checkboxesString .= $checkbox->render();
			}

			$fieldset = new Tx_Fluid_Core_ViewHelper_TagBuilder('fieldset');		
			$fieldset->setContent($legend->render() . $checkboxesString);
			$fieldsetsString .= $fieldset->render();
		}
		
		$form = new Tx_Fluid_Core_ViewHelper_TagBuilder('form');
		$form->setContent($fieldsetsString);

		echo($form->render());
	}


	/**
	 * Helper: Inserts pazpar2 headers into page.
	 *
	 * @return void
	 */
	private function addResourcesToHead () {
		// Add pazpar2.css to <head>.
		$cssTag = new Tx_Fluid_Core_ViewHelper_TagBuilder('link');
		$cssTag->addAttribute('rel', 'stylesheet');
		$cssTag->addAttribute('type', 'text/css');
		$cssTag->addAttribute('href', $this->conf['CSSPath']);
		$cssTag->addAttribute('media', 'all');
		$this->response->addAdditionalHeaderData( $cssTag->render() );

		// Add pz2-neuerwerbungen.css to <head>.
		$cssTag = new Tx_Fluid_Core_ViewHelper_TagBuilder('link');
		$cssTag->addAttribute('rel', 'stylesheet');
		$cssTag->addAttribute('type', 'text/css');
		$cssTag->addAttribute('href', $this->conf['pz2-neuerwerbungenCSSPath']);
		$cssTag->addAttribute('media', 'all');
		$this->response->addAdditionalHeaderData( $cssTag->render() );


		// Add pz2.js to <head>.
		// This is Indexdata’s JavaScript that ships with the pazpar2 software.
		$scriptTag = new Tx_Fluid_Core_ViewHelper_TagBuilder('script');
		$scriptTag->addAttribute('type', 'text/javascript');
		$scriptTag->addAttribute('src',  $this->conf['pz2JSPath']);
		$scriptTag->forceClosingTag(true);
		$this->response->addAdditionalHeaderData( $scriptTag->render() );

		// Set up pazpar2 service ID.
		$jsCommand = 'my_serviceID = "' . $this->conf['serviceID'] . '";';
		$scriptTag = new Tx_Fluid_Core_ViewHelper_TagBuilder('script');
		$scriptTag->addAttribute('type', 'text/javascript');
		$scriptTag->setContent($jsCommand);
		$this->response->addAdditionalHeaderData( $scriptTag->render() );

		// Add pz2-client.js to <head>.
		$scriptTag = new Tx_Fluid_Core_ViewHelper_TagBuilder('script');
		$scriptTag->addAttribute('type', 'text/javascript');
		$scriptTag->addAttribute('src', $this->conf['pz2-clientJSPath']) ;
		$scriptTag->forceClosingTag(true);
		$this->response->addAdditionalHeaderData( $scriptTag->render() );

		// Add pz2-neuerwerbungen.js to <head>. ***************
		$scriptTag = new Tx_Fluid_Core_ViewHelper_TagBuilder('script');
		$scriptTag->addAttribute('type', 'text/javascript');
		$scriptTag->addAttribute('src', $this->conf['pz2-neuerwerbungenJSPath']) ;
		$scriptTag->forceClosingTag(true);
		$this->response->addAdditionalHeaderData( $scriptTag->render() );



		// Add settings for pz2-client.js to <head>.
		$jsCommand = 'useGoogleBooks = ' . (($this->conf['useGoogleBooks']) ? 'true' : 'false') . '; ';
		$jsCommand .= 'useZDB = ' . (($this->conf['useZDB']) ? 'true' : 'false') . ';';
		$scriptTag = new Tx_Fluid_Core_ViewHelper_TagBuilder('script');
		$scriptTag->addAttribute('type', 'text/javascript');
		$scriptTag->setContent($jsCommand);
		$this->response->addAdditionalHeaderData( $scriptTag->render() );

		// Make jQuery initialise pazpar2 when the DOM is ready.
		$jsCommand = 'jQuery(document).ready(domReady);';
		$scriptTag = new Tx_Fluid_Core_ViewHelper_TagBuilder('script');
		$scriptTag->addAttribute('type', 'text/javascript');
		$scriptTag->setContent($jsCommand);
		$this->response->addAdditionalHeaderData( $scriptTag->render() );
		
		// Add Google Books support if asked to do so.
		if ( $this->conf['useGoogleBooks'] ) {
			// Structurally this might be better in a separate extension?
			$scriptTag = new Tx_Fluid_Core_ViewHelper_TagBuilder('script');
			$scriptTag->addAttribute('type', 'text/javascript');
			$scriptTag->addAttribute('src',  'https://www.google.com/jsapi');
			$scriptTag->forceClosingTag(true);
			$this->response->addAdditionalHeaderData( $scriptTag->render() );
			
			$jsCommand = 'google.load("books", "0");';
			$scriptTag = new Tx_Fluid_Core_ViewHelper_TagBuilder('script');
			$scriptTag->addAttribute('type', 'text/javascript');
			$scriptTag->setContent($jsCommand);
			$this->response->addAdditionalHeaderData( $scriptTag->render() );
		}

	}


}
?>
