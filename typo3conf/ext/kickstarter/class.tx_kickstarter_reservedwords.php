<?php
/***************************************************************
*  Copyright notice
*
*  (c)  2006-2008 Ingo Renner (ingo@typo3.org)  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*  A copy is found in the textfile GPL.txt and important notices to the license
*  from the author is found in LICENSE.txt distributed with these scripts.
*
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

/**
 * Reserved TYPO3 and MySQL words
 *
 * @author Ingo Renner <ingo@typo3.org>
 * @author Peter Foerger
 */
class tx_kickstarter_reservedWords {

	var $TYPO3ReservedFields = array(
		'uid',
		'pid',
		'endtime',
		'starttime',
		'sorting',
		'fe_group',
		'hidden',
		'deleted',
		'cruser_id',
		'crdate',
		'tstamp'
	);

	var $mysqlReservedWords = array(
		'data',
		'table',
		'field',
		'key',
		'desc',
		'all',
		'and',
		'asensitive',
		'bigint',
		'both',
		'cascade',
		'char',
		'character',
		'collate',
		'column',
		'connection',
		'convert',
		'current_date',
		'current_user',
		'databases',
		'day_minute',
		'decimal',
		'default',
		'delayed',
		'describe',
		'distinctrow',
		'drop',
		'else',
		'escaped',
		'explain',
		'float',
		'for',
		'from',
		'group',
		'hour_microsecond',
		'if',
		'index',
		'inout',
		'int',
		'int3',
		'integer',
		'is',
		'key',
		'leading',
		'like',
		'load',
		'lock',
		'longtext',
		'match',
		'mediumtext',
		'minute_second',
		'natural',
		'null',
		'optimize',
		'or',
		'outer',
		'primary',
		'raid0',
		'real',
		'release',
		'replace',
		'return',
		'rlike',
		'second_microsecond',
		'separator',
		'smallint',
		'specific',
		'sqlstate',
		'sql_cal_found_rows',
		'starting',
		'terminated',
		'tinyint',
		'trailing',
		'undo',
		'unlock',
		'usage',
		'utc_date',
		'values',
		'varcharacter',
		'where',
		'write',
		'year_month',
		'asensitive',
		'call',
		'condition',
		'connection',
		'continue',
		'cursor',
		'declare',
		'deterministic',
		'each',
		'elseif',
		'exit',
		'fetch',
		'goto',
		'inout',
		'insensitive',
		'iterate',
		'label',
		'leave',
		'loop',
		'modifies',
		'out',
		'reads',
		'release',
		'repeat',
		'return',
		'schema',
		'schemas',
		'sensitive',
		'specific',
		'sql',
		'sqlexception',
		'sqlstate',
		'sqlwarning',
		'trigger',
		'undo',
		'upgrade',
		'while'
	);

	/**
	 * merges the lists of reserved words and returns them in an unique array
	 *
	 * @return array array of reserved words
	 */
	function getReservedWords() {
		$reservedWords = array_unique(
			array_merge (
				$this->TYPO3ReservedFields,
				$this->mysqlReservedWords
			)
		);

		return $reservedWords;
	}

}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/class.tx_kickstarter_reservedwords.php']) {
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/kickstarter/class.tx_kickstarter_reservedwords.php']);
}

?>
