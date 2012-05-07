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
 * @author Claus Due, Wildside A/S
 * @package Fed
 * @subpackage Utility
 */
class Tx_Fed_Utility_Debug implements t3lib_Singleton {

	const DEFAULT_NAME = 'debug';

	const STORAGE = 'fed-debug-storage';

	public static $SKELETON = array(
		'name' => self::DEFAULT_NAME,
		'begin' => 0,
		'end' => 0,
		'data' => NULL,
		'memory' => 0,
		'results' => array(
			'exectime' => 0,
			'comparison' => ''
		),
		'laps' => array()
	);

	/**
	 * Stores the name of the currently active subsection
	 * @var string
	 */
	protected $currentSubsessionName = self::DEFAULT_NAME;

	/**
	 * @var Tx_Fed_Utility_DataComparison
	 */
	protected $dataComparisonService;

	/**
	 * @param Tx_Fed_Utility_DataComparison $dataComparisonService
	 */
	public function injectDataComparisonService(Tx_Fed_Utility_DataComparison $dataComparisonService) {
		$this->dataComparisonService = $dataComparisonService;
	}

	/**
	 * CONSTRUCTOR
	 */
	public function __construct() {
		if (is_array($GLOBALS[self::STORAGE]) === FALSE) {
			$GLOBALS[self::STORAGE] = array();
		}
	}

	/**
	 * Begin a debuggin sub-session with $name and optional attached $data.
	 * Subsequent calls with the same name simply re-use the storage.
	 *
	 * @param string $name
	 * @param mixed $data
	 * @return void
	 * @api
	 */
	public function begin($name=self::DEFAULT_NAME, &$data=NULL) {
		$session =& $this->getDebugSession($name);
		$session['begin'] = microtime(TRUE);
		$session['name'] = $name;
		$session['data'] =& $data;
		$session['memory'] = memory_get_usage();
		$GLOBALS[self::STORAGE][$name] = $session;
		$this->currentSubsessionName = $name;
	}

	/**
	 * Register a completed "lap" - for example fire this in every loop iteration
	 * to register how long each iteration takes but keep the association to the
	 * current debugging sub-session
	 *
	 * @param type $name
	 * @param type $data
	 */
	public function lap(&$data=NULL) {
		$updatedSession = $session = $this->getDebugSession($this->currentSubsessionName);
		$last = array_pop($session['laps']);
		if (!$last) {
			$last = $session;
		}
		$lap = array();
		$lap['end'] = microtime(TRUE);
		$lap['memory'] = memory_get_usage() - $last['memory'];
		$lap['data'] =& $data;
		$lap['exectime'] = microtime(TRUE) - ($last['end'] ? $last['end'] : $last['begin']);
		$updatedSession['laps'][] = $lap;
		$this->setDebugSession($this->currentSubsessionName, $updatedSession);
	}

	/**
	 * Stop a debuggin sub-session (and return the debug information).
	 * Use same $name as in begin() but specify optional data again for a
	 * quick comparison of the two - a bit of smart logic has been built in
	 * to allow human-readable comparisons of most data types, including some
	 * Extbase object types such as DomainObjects.
	 *
	 * @param mixed $data
	 * @return array
	 * @api
	 */
	public function end(&$data=NULL) {
		$session = $this->getDebugSession($this->currentSubsessionName);
		$data1 = $this->fetchData($this->currentSubsessionName);
		$session['end'] = microtime(TRUE);
		$session['data'] =& $data;
		$session['results'] = array(
			'exectime' => $this->getElapsedTime($this->currentSubsessionName),
			'memory' => $this->getConsumedMemory($this->currentSubsessionName),
			'comparison' => $this->dataComparisonService->compare($data1, $data2)
		);
		$this->setDebugSession($this->currentSubsessionName, $session);
		$this->currentSubsessionName = self::DEFAULT_NAME;
		return $session;
	}

	/**
	 * Get the microtime stamp on which $name sub-session began
	 *
	 * @param string $name
	 * @return float
	 * @api
	 */
	public function getBeginTime($name=self::DEFAULT_NAME) {
		$session = $this->getDebugSession($name);
		return $session['begin'];
	}

	/**
	 * Get the microtime stamp on which $name sub-session ended. If no end request
	 * had been registered use current time - and automatically call an end to
	 * the debug sub-session
	 *
	 * @param string $name
	 * @return float
	 * @api
	 */
	public function getEndTime($name=self::DEFAULT_NAME) {
		$session = $this->getDebugSession($name);
		if ($session['end'] > 0) {
			return $session['end'];
		} else {
			return microtime(TRUE);
		}
	}

	/**
	 * Returns the time elapsed while debugging sub-session $name. If end is
	 * not yet met we simply return the current microtime stamp. This allows
	 * you to continually call $debugService->getElapsedTime('someNameOrNotSpecified');
	 * every time a debugged loop's iteration completes, for example, to measure
	 * not only the complete loop itself but also inspect how much each loop adds
	 * to the total execution time (you can graph this curve, too - the
	 * DebugViewHelper actually does this...)
	 *
	 * @param string $name
	 * @return float
	 * @api
	 */
	public function getElapsedTime($name=self::DEFAULT_NAME) {
		return ($this->getEndTime($name) - $this->getBeginTime($name));
	}

	/**
	 * Fetch an array of all executed debug sessions and their associated
	 * data, for rendering or dumping.
	 *
	 * @return array
	 */
	public function getAllDebugSessions() {
		return $GLOBALS[self::STORAGE];
	}

	/**
	 * Returns a reference to the requested debugging sub-session
	 *
	 * @param string $name
	 * @return array
	 */
	public function getDebugSession($name=self::DEFAULT_NAME) {
		if (is_array($GLOBALS[self::STORAGE][$name]) === FALSE) {
			$GLOBALS[self::STORAGE][$name] = self::$SKELETON;
		}
		return $GLOBALS[self::STORAGE][$name];
	}

	/**
	 * PURELY INTERNAL
	 *
	 * @param string $name
	 * @param array $session
	 * @return void
	 */
	private function setDebugSession($name, $session) {
		$GLOBALS[self::STORAGE][$name] = $session;
	}

	/**
	 * Returns the data associated with subsession $name
	 *
	 * @param string $name
	 * @return mixed
	 */
	public function fetchData($name=self::DEFAULT_NAME) {
		$session = $this->getDebugSession($name);
		return $session['data'];
	}

	/**
	 * Get the amount of memory consumed as measured by subsession $name
	 *
	 * @param string $name
	 * @return int
	 */
	public function getConsumedMemory($name) {

	}

}

?>
