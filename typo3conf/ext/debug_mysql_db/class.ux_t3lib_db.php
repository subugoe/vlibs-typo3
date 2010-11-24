<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2004-2010 Kasper Skaarhoj (kasperYYYY@typo3.com)
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
 * Contains a debug extension for mysql-db calls
 *
 * $Id: class.ux_t3lib_db.php 2090 2010-10-08 14:07:54Z fholzinger $
 *
 * @author	Kasper Skaarhoj <kasperYYYY@typo3.com>
 * @author	Stefan Geith <typo3dev2010@geithware.de>
 */
/**
 * [CLASS/FUNCTION INDEX of SCRIPT]
 *

 *
 */



/**
 * extension of TYPO3 mysql database debug
 *
 * @author	Kasper Skaarhoj <kasper@typo3.com>
 * @author	Karsten Dambekalns <k.dambekalns@fishfarm.de>
 * @package TYPO3
 * @subpackage tx_dbal
 */
class ux_t3lib_DB extends t3lib_DB {
	public		$debugOutput = FALSE;
	protected	$dbgConf = array();
	protected	$dbgQuery = array();
	protected	$dbgTable = array();
	protected	$dbgExcludeTable = array();
	protected	$dbgId = array();
	protected	$dbgFeUser = array();
	protected	$dbgOutput = '';
	protected	$dbgTextformat = FALSE;
	protected	$feUid = 0;
	protected	$ticker = '';
	private static $phpVersionGt50205;


	function ux_t3lib_DB () {

		$phpVersion = phpversion();
		self::$phpVersionGt50205 = version_compare($phpVersion, '5.2.5', '>=');

		$this->dbgConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['debug_mysql_db']);
		$this->dbgOutput = $this->dbgConf['OUTPUT'] ? $this->dbgConf['OUTPUT'] : 't3lib_div::debug';
		$this->dbgTextformat = $this->dbgConf['TEXTFORMAT'] ? $this->dbgConf['TEXTFORMAT'] : FALSE;
		$this->dbgTca = $this->dbgConf['TCA'] ? $this->dbgConf['TCA'] : FALSE;
		$this->debugOutput = (intval($this->dbgConf['DISABLE_ERRORS'])) ? FALSE : TRUE;
		$this->ticker = $this->dbgConf['TICKER'] ? floatval($this->dbgConf['TICKER'])/1000 : '';

		if (strtoupper($this->dbgConf['QUERIES'])=='ALL' || !trim($this->dbgConf['QUERIES'])) {
			$this->dbgQuery = Array('ALL'=>1, 'SQL'=>1,
				'SELECT'=>1, 'INSERT'=>1, 'UPDATE'=>1, 'DELETE'=>1, 'FETCH'=>1, 'FIRSTROW'=>1);
		} else {
			$tmp = t3lib_div::trimExplode(',',$this->dbgConf['QUERIES']);
			for ($i=0;$i<count($tmp);$i++) {
				$this->dbgQuery[strtoupper($tmp[$i])] = 1;
			}
		}

		if (strtoupper($this->dbgConf['TABLES'])=='ALL' || !trim($this->dbgConf['TABLES'])) {
			$this->dbgTable = Array('all'=>1);

			if ($this->dbgConf['EXCLUDETABLES'] != '') {
				$tmp = t3lib_div::trimExplode(',',$this->dbgConf['EXCLUDETABLES']);
				$count = count($tmp);
				for ($i=0;$i<$count;$i++) {
					$this->dbgExcludeTable[strtolower($tmp[$i])] = 1;
				}
			}
		} else {
			$tmp = t3lib_div::trimExplode(',',$this->dbgConf['TABLES']);
			$count = count($tmp);
			for ($i=0;$i<$count;$i++) {
				$this->dbgTable[strtolower($tmp[$i])] = 1;
			}
		}
		$tmp = t3lib_div::trimExplode(',',$this->dbgConf['PAGES']);
		$count = count($tmp);
		for ($i=0;$i<$count;$i++) {
			$this->dbgId[intval($tmp[$i]).'.'] = 1;
		}
		$tmp = t3lib_div::trimExplode(',',$this->dbgConf['FEUSERS']);
		$count = count($tmp);
		for ($i=0;$i<$count;$i++) if (intval($tmp[$i])) {
			$this->dbgFeUser[intval($tmp[$i]).'.'] = 1;
		}
	}

	/* determines if the PHP function debug_backtrace() may be called with the parameter to not populate the object index */
	public static function hasBacktraceParam ()	{
		return self::$phpVersionGt50205;
	}


	/************************************
	 *
	 * Query execution
	 *
	 * These functions are the RECOMMENDED DBAL functions for use in your applications
	 * Using these functions will allow the DBAL to use alternative ways of accessing data (contrary to if a query is returned!)
	 * They compile a query AND execute it immediately and then return the result
	 * This principle heightens our ability to create various forms of DBAL of the functions.
	 * Generally: We want to return a result pointer/object, never queries.
	 * Also, having the table name together with the actual query execution allows us to direct the request to other databases.
	 *
	 **************************************/

	/**
	 * Creates and executes an INSERT SQL-statement for $table from the array with field/value pairs $fields_values.
	 * Using this function specifically allows us to handle BLOB and CLOB fields depending on DB
	 * Usage count/core: 47
	 *
	 * @param	string		Table name
	 * @param	array		Field values as key=>value pairs. Values will be escaped internally. Typically you would fill an array like "$insertFields" with 'fieldname'=>'value' and pass it to this function as argument.
	 * @param	string/array		See fullQuoteArray()
	 * @return	pointer		MySQL result pointer / DBAL object
	 */
	function exec_INSERTquery ($table,$fields_values,$no_quote_fields=FALSE,$dbgModes=0)	{
		$myName = is_array($dbgModes) ? ($dbgModes['name'] ? $dbgModes['name'] : __FILE__.':'.__LINE__ ) : 'exec_INSERTquery';
		$starttime = microtime(true);
		$query = $this->INSERTquery($table,$fields_values,$no_quote_fields,1);
		$res = mysql_query($query, $this->link);
		$endtime = microtime(true);
		$error = $this->sql_error();
		if ($this->bDisplayOutput($error,$starttime,$endtime))	{
			$this->myDebug($myName,$error,'INSERT',$table,$query,$res,$endtime-$starttime);
		}
		return $res;
	}


	/**
	 * Creates and executes an UPDATE SQL-statement for $table where $where-clause (typ. 'uid=...') from the array with field/value pairs $fields_values.
	 * Using this function specifically allow us to handle BLOB and CLOB fields depending on DB
	 * Usage count/core: 50
	 *
	 * @param	string		Database tablename
	 * @param	string		WHERE clause, eg. "uid=1". NOTICE: You must escape values in this argument with $this->fullQuoteStr() yourself!
	 * @param	array		Field values as key=>value pairs. Values will be escaped internally. Typically you would fill an array like "$updateFields" with 'fieldname'=>'value' and pass it to this function as argument.
	 * @param	string/array		See fullQuoteArray()
	 * @return	pointer		MySQL result pointer / DBAL object
	 */
	function exec_UPDATEquery ($table,$where,$fields_values,$no_quote_fields=FALSE,$dbgModes=0)	{
		$myName = is_array($dbgModes) ? ($dbgModes['name'] ? $dbgModes['name'] : __FILE__.':'.__LINE__ ) : 'exec_UPDATEquery';
		$query = $this->UPDATEquery($table,$where,$fields_values,$no_quote_fields,1);
		$starttime = microtime(true);
		$res = mysql_query($query, $this->link);
		$endtime = microtime(true);
		$error = $this->sql_error();
		if ($this->bDisplayOutput($error,$starttime,$endtime))	{
			$this->myDebug($myName,$error,'UPDATE',$table,$query,$res,$endtime-$starttime);
		}
		return $res;
	}


	/**
	 * Creates and executes a DELETE SQL-statement for $table where $where-clause
	 * Usage count/core: 40
	 *
	 * @param	string		Database tablename
	 * @param	string		WHERE clause, eg. "uid=1". NOTICE: You must escape values in this argument with $this->fullQuoteStr() yourself!
	 * @return	pointer		MySQL result pointer / DBAL object
	 */
	function exec_DELETEquery ($table,$where,$dbgModes=0)	{
		$myName = is_array($dbgModes) ? ($dbgModes['name'] ? $dbgModes['name'] : __FILE__.':'.__LINE__ ) : 'exec_DELETEquery';
		$query = $this->DELETEquery($table,$where,1);
		$starttime = microtime(true);
		$res = mysql_query($query, $this->link);
		$endtime = microtime(true);
		$error = $this->sql_error();
		if ($this->bDisplayOutput($error,$starttime,$endtime))	{
			$this->myDebug($myName,$error,'DELETE',$table,$query,$res,$endtime-$starttime);
		}
		return $res;
	}


	/**
	 * Creates and executes a SELECT SQL-statement
	 * Using this function specifically allow us to handle the LIMIT feature independently of DB.
	 * Usage count/core: 340
	 *
	 * @param	string		List of fields to select from the table. This is what comes right after "SELECT ...". Required value.
	 * @param	string		Table(s) from which to select. This is what comes right after "FROM ...". Required value.
	 * @param	string		Optional additional WHERE clauses put in the end of the query. NOTICE: You must escape values in this argument with $this->fullQuoteStr() yourself! DO NOT PUT IN GROUP BY, ORDER BY or LIMIT!
	 * @param	string		Optional GROUP BY field(s), if none, supply blank string.
	 * @param	string		Optional ORDER BY field(s), if none, supply blank string.
	 * @param	string		Optional LIMIT value ([begin,]max), if none, supply blank string.
	 * @return	pointer		MySQL result pointer / DBAL object
	 */
	function exec_SELECTquery ($select_fields,$from_table,$where_clause,$groupBy='',$orderBy='',$limit='',$dbgModes=0)	{
		$myName = is_array($dbgModes) ? ($dbgModes['name'] ? $dbgModes['name'] : __FILE__.':'.__LINE__ ) : 'exec_SELECTquery';
		$query = $this->SELECTquery($select_fields,$from_table,$where_clause,$groupBy,$orderBy,$limit,1);
		$starttime = microtime(true);
		$level = error_reporting();
		error_reporting($level & (E_ALL ^ E_WARNING));
		$res = mysql_query($query, $this->link);
		error_reporting($level);
		$endtime = microtime(true);
		$error = $this->sql_error();
		if ($this->bDisplayOutput($error,$starttime,$endtime))	{
			$this->myDebug($myName,$error,'SELECT',$from_table,$query,$res,$endtime-$starttime);
		}
		return $res;
	}


	/**
	 * Creates and executes a SELECT query, selecting fields ($select) from two/three tables joined
	 * Use $mm_table together with $local_table or $foreign_table to select over two tables. Or use all three tables to select the full MM-relation.
	 * The JOIN is done with [$local_table].uid <--> [$mm_table].uid_local  / [$mm_table].uid_foreign <--> [$foreign_table].uid
	 * The function is very useful for selecting MM-relations between tables adhering to the MM-format used by TCE (TYPO3 Core Engine). See the section on $TCA in Inside TYPO3 for more details.
	 *
	 * Usage: 12 (spec. ext. sys_action, sys_messages, sys_todos)
	 *
	 * @param	string		Field list for SELECT
	 * @param	string		Tablename, local table
	 * @param	string		Tablename, relation table
	 * @param	string		Tablename, foreign table
	 * @param	string		Optional additional WHERE clauses put in the end of the query. NOTICE: You must escape values in this argument with $this->fullQuoteStr() yourself! DO NOT PUT IN GROUP BY, ORDER BY or LIMIT! You have to prepend 'AND ' to this parameter yourself!
	 * @param	string		Optional GROUP BY field(s), if none, supply blank string.
	 * @param	string		Optional ORDER BY field(s), if none, supply blank string.
	 * @param	string		Optional LIMIT value ([begin,]max), if none, supply blank string.
	 * @return	pointer		MySQL result pointer / DBAL object
	 * @see exec_SELECTquery()
	 */
	function exec_SELECT_mm_query ($select,$local_table,$mm_table,$foreign_table,$whereClause='',$groupBy='',$orderBy='',$limit='')	{
		if($foreign_table == $local_table) {
			$foreign_table_as = $foreign_table.uniqid('_join');
		}

		$mmWhere = $local_table ? $local_table.'.uid='.$mm_table.'.uid_local' : '';
		$mmWhere.= ($local_table AND $foreign_table) ? ' AND ' : '';
		$mmWhere.= $foreign_table ? ($foreign_table_as ? $foreign_table_as : $foreign_table).'.uid='.$mm_table.'.uid_foreign' : '';

		return $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					$select,
					($local_table ? $local_table.',' : '').$mm_table.($foreign_table ? ','. $foreign_table.($foreign_table_as ? ' AS '.$foreign_table_as : '') : ''),
					$mmWhere.' '.$whereClause,		// whereClauseMightContainGroupOrderBy
					$groupBy,
					$orderBy,
					$limit,
					Array('name'=>'exec_SELECT_mm_query')
				);
	}


	/**
	 * Executes a select based on input query parts array
	 *
	 * Usage: 9
	 *
	 * @param	array		Query parts array
	 * @return	pointer		MySQL select result pointer / DBAL object
	 * @see exec_SELECTquery()
	 */
	function exec_SELECT_queryArray ($queryParts)	{
		return $this->exec_SELECTquery(
					$queryParts['SELECT'],
					$queryParts['FROM'],
					$queryParts['WHERE'],
					$queryParts['GROUPBY'],
					$queryParts['ORDERBY'],
					$queryParts['LIMIT'],
					Array('name'=>'exec_SELECT_queryArray')
				);
	}


	/**
	 * Creates and executes a SELECT SQL-statement AND traverse result set and returns array with records in.
	 *
	 * @param	string		See exec_SELECTquery()
	 * @param	string		See exec_SELECTquery()
	 * @param	string		See exec_SELECTquery()
	 * @param	string		See exec_SELECTquery()
	 * @param	string		See exec_SELECTquery()
	 * @param	string		See exec_SELECTquery()
	 * @param	string		If set, the result array will carry this field names value as index. Requires that field to be selected of course!
	 * @return	array		Array of rows.
	 */
	function exec_SELECTgetRows ($select_fields,$from_table,$where_clause,$groupBy='',$orderBy='',$limit='',$uidIndexField='')	{
		$res = $this->exec_SELECTquery($select_fields,$from_table,$where_clause,$groupBy,$orderBy,$limit,
			Array('name'=>'exec_SELECTgetRows'));

		if (!$this->sql_error())	{
			$output = array();

			if ($uidIndexField)	{
				while($tempRow = $this->sql_fetch_assoc($res))	{
					$output[$tempRow[$uidIndexField]] = $tempRow;
				}
			} else {
				while($output[] = $this->sql_fetch_assoc($res));
				array_pop($output);
			}
			$this->sql_free_result($res);
		}
		return $output;
	}


	/**
	 * Counts the number of rows in a table.
	 *
	 * @param	string		$field: Name of the field to use in the COUNT() expression (e.g. '*')
	 * @param	string		$table: Name of the table to count rows for
	 * @param	string		$where: (optional) WHERE statement of the query
	 * @return	mixed		Number of rows counter (integer) or false if something went wrong (boolean)
	 */
	public function exec_SELECTcountRows ($field, $table, $where = '') {
		$count = false;
		$resultSet = $this->exec_SELECTquery('COUNT(' . $field . ')', $table, $where);
		if ($resultSet !== false) {
			list($count) = $this->sql_fetch_row($resultSet);
			$this->sql_free_result($resultSet);
		}
		return $count;
	}










	/**************************************
	 *
	 * Query building
	 *
	 **************************************/

	/**
	 * Creates an INSERT SQL-statement for $table from the array with field/value pairs $fields_values.
	 * Usage count/core: 4
	 *
	 * @param	string		See exec_INSERTquery()
	 * @param	array		See exec_INSERTquery()
	 * @param	string/array		See fullQuoteArray()
	 * @return	string		Full SQL query for INSERT (unless $fields_values does not contain any elements in which case it will be false)
	 * @deprecated			use exec_INSERTquery() instead if possible!
	 */
	function INSERTquery ($table,$fields_values,$no_quote_fields=FALSE,$noDbg=0)	{

			// Table and fieldnames should be "SQL-injection-safe" when supplied to this function (contrary to values in the arrays which may be insecure).
		if (is_array($fields_values) && count($fields_values))	{

				// quote and escape values
			$fields_values = $this->fullQuoteArray($fields_values,$table,$no_quote_fields);

				// Build query:
			$query = 'INSERT INTO '.$table.'
				(
					'.implode(',
					',array_keys($fields_values)).'
				) VALUES (
					'.implode(',
					',$fields_values).'
				)';

				// Return query:
			if ($this->debugOutput || $this->store_lastBuiltQuery) $this->debug_lastBuiltQuery = $query;
			return $query;
		}
	}


	/**
	 * Creates an UPDATE SQL-statement for $table where $where-clause (typ. 'uid=...') from the array with field/value pairs $fields_values.
	 * Usage count/core: 6
	 *
	 * @param	string		See exec_UPDATEquery()
	 * @param	string		See exec_UPDATEquery()
	 * @param	array		See exec_UPDATEquery()
	 * @param	array		See fullQuoteArray()
	 * @return	string		Full SQL query for UPDATE (unless $fields_values does not contain any elements in which case it will be false)
	 * @deprecated			use exec_UPDATEquery() instead if possible!
	 */
	function UPDATEquery ($table,$where,$fields_values,$no_quote_fields=FALSE,$noDbg=0)	{

			// Table and fieldnames should be "SQL-injection-safe" when supplied to this function (contrary to values in the arrays which may be insecure).
		if (is_string($where))	{
			if (is_array($fields_values) && count($fields_values))	{

					// quote and escape values
				$nArr = $this->fullQuoteArray($fields_values,$table,$no_quote_fields);

				$fields = array();
				foreach ($nArr as $k => $v) {
					$fields[] = $k.'='.$v;
				}

					// Build query:
				$query = 'UPDATE '.$table.'
					SET
						'.implode(',
						',$fields).
					(strlen($where)>0 ? '
					WHERE
						'.$where : '');

					// Return query:
				if ($this->debugOutput || $this->store_lastBuiltQuery) $this->debug_lastBuiltQuery = $query;
				return $query;
			}
		} else {
			die('<strong>TYPO3 Fatal Error:</strong> "Where" clause argument for UPDATE query was not a string in $this->UPDATEquery() !');
		}
	}


	/**
	 * Creates a DELETE SQL-statement for $table where $where-clause
	 * Usage count/core: 3
	 *
	 * @param	string		See exec_DELETEquery()
	 * @param	string		See exec_DELETEquery()
	 * @return	string		Full SQL query for DELETE
	 * @deprecated			use exec_DELETEquery() instead if possible!
	 */
	function DELETEquery ($table,$where,$noDbg=0)	{
		if (is_string($where))	{

				// Table and fieldnames should be "SQL-injection-safe" when supplied to this function
			$query = 'DELETE FROM '.$table.
				(strlen($where)>0 ? '
				WHERE
					'.$where : '');

			if ($this->debugOutput || $this->store_lastBuiltQuery) $this->debug_lastBuiltQuery = $query;
			return $query;
		} else {
			die('<strong>TYPO3 Fatal Error:</strong> "Where" clause argument for DELETE query was not a string in $this->DELETEquery() !');
		}
	}


	/**
	 * Creates a SELECT SQL-statement
	 * Usage count/core: 11
	 *
	 * @param	string		See exec_SELECTquery()
	 * @param	string		See exec_SELECTquery()
	 * @param	string		See exec_SELECTquery()
	 * @param	string		See exec_SELECTquery()
	 * @param	string		See exec_SELECTquery()
	 * @param	string		See exec_SELECTquery()
	 * @return	string		Full SQL query for SELECT
	 * @deprecated			use exec_SELECTquery() instead if possible!
	 */
	function SELECTquery ($select_fields,$from_table,$where_clause,$groupBy='',$orderBy='',$limit='',$noDbg=0)	{

			// Table and fieldnames should be "SQL-injection-safe" when supplied to this function
			// Build basic query:
		$query = 'SELECT '.$select_fields.'
			FROM '.$from_table.
			(strlen($where_clause)>0 ? '
			WHERE
				'.$where_clause : '');

			// Group by:
		if (strlen($groupBy)>0)	{
			$query.= '
			GROUP BY '.$groupBy;
		}
			// Order by:
		if (strlen($orderBy)>0)	{
			$query.= '
			ORDER BY '.$orderBy;
		}
			// Group by:
		if (strlen($limit)>0)	{
			$query.= '
			LIMIT '.$limit;
		}

			// Return query:
		if ($this->debugOutput || $this->store_lastBuiltQuery) $this->debug_lastBuiltQuery = $query;
		return $query;
	}





	/**************************************
	 *
	 * MySQL wrapper functions
	 * (For use in your applications)
	 *
	 **************************************/

	/**
	 * Executes query
	 * mysql() wrapper function
	 * DEPRECATED - use exec_* functions from this class instead!
	 * Usage count/core: 9
	 *
	 * @param	string		Database name
	 * @param	string		Query to execute
	 * @return	pointer		Result pointer / DBAL object
	 */
	function sql ($db,$query,$dbgModes=0)	{
		$myName = is_array($dbgModes) ? ($dbgModes['name'] ? $dbgModes['name'] : __FILE__.':'.__LINE__ ) : 'TYPO3_DB->sql';
		$starttime = microtime(true);
		$res = mysql_query($query, $this->link);
		$endtime = microtime(true);
		$error = $this->sql_error();
		if ($this->bDisplayOutput($error,$starttime,$endtime))	{
			$this->myDebug($myName,$error,'SQL','',$query,$res,$endtime-$starttime);
		}
		return $res;
	}


	/**
	 * Executes query
	 * mysql_query() wrapper function
	 * Usage count/core: 1
	 *
	 * @param	string		Query to execute
	 * @return	pointer		Result pointer / DBAL object
	 */
	function sql_query ($query,$dbgModes=0)	{
		$myName = is_array($dbgModes) ? ($dbgModes['name'] ? $dbgModes['name'] : __FILE__.':'.__LINE__ ) : 'TYPO3_DB->sql_query';
		$starttime = microtime(true);
		$res = mysql_query($query, $this->link);
		$endtime = microtime(true);
		$error = $this->sql_error();
		if ($this->bDisplayOutput($error,$starttime,$endtime))	{
			$this->myDebug($myName,$error,'SQL','',$query,$res,$endtime-$starttime);
		}
		return $res;
	}


	/**
	 * Returns the error number on the last sql() execution
	 * mysql_errno() wrapper function
	 *
	 * @return	int		MySQL error number.
	 */
	function sql_errno () {
		return mysql_errno($this->link);
	}


	/**
	 * Returns the number of selected rows.
	 * mysql_num_rows() wrapper function
	 * Usage count/core: 85
	 *
	 * @param	pointer		MySQL result pointer (of SELECT query) / DBAL object
	 * @return	integer		Number of resulting rows
	 */
	function sql_num_rows ($res)	{
		if ($this->debug_check_recordset($res))	{
			$rc = mysql_num_rows($res);
		} else {
			$rc = FALSE;
		}
		return $rc;
	}


	/**
	 * Returns an associative array that corresponds to the fetched row, or FALSE if there are no more rows.
	 * mysql_fetch_assoc() wrapper function
	 * Usage count/core: 307
	 *
	 * @param	pointer		MySQL result pointer (of SELECT query) / DBAL object
	 * @return	array		Associative array of result row, FALSE in case of error
	 */
	function sql_fetch_assoc ($res)	{
		if ($this->debug_check_recordset($res) && is_resource($res))	{
			$rc = mysql_fetch_assoc($res);
		} else {
			$rc = FALSE;
		}
		return $rc;
	}


	/**
	 * Returns an array that corresponds to the fetched row, or FALSE if there are no more rows.
	 * The array contains the values in numerical indices.
	 * mysql_fetch_row() wrapper function
	 * Usage count/core: 56
	 *
	 * @param	pointer		MySQL result pointer (of SELECT query) / DBAL object
	 * @return	array		Array with result rows.
	 */
	function sql_fetch_row ($res,$dbgModes=0)	{
		return mysql_fetch_row($res);
	}


	/**
	 * Free result memory
	 * mysql_free_result() wrapper function
	 * Usage count/core: 3
	 *
	 * @param	pointer		MySQL result pointer to free / DBAL object
	 * @return	boolean		Returns TRUE on success or FALSE on failure.
	 */
	function sql_free_result ($res)	{
		if ($this->debug_check_recordset($res) && is_resource($res))	{
			$rc = mysql_free_result($res);
		} else {
			$rc = FALSE;
		}
		return $rc;
	}


	/**
	 * Determines if the debug output should be displayed. An error message or a time comsuming SQL query shall be displayed.
	 *
	 * @param	string		error text
	 * @param	float		startime of mysql-command
	 * @param	float		endime of mysql-command
	 * @return	boolean		TRUE if output should be displayed
	 */
	function bDisplayOutput ($error, $starttime, $endtime)	{

		if ($error!='' || $this->ticker=='' || $this->ticker <= $endtime - $starttime)	{
			$rc = TRUE;
		} else {
			$rc = FALSE;
		}
		return $rc;
	}


	function enableByTable ($table, &$bEnable, &$bDisable)	{

		if ($table != '')	{
			$partArray = explode('.', $table);
			$lowerTable = strtolower($partArray[0]);

			if (isset($GLOBALS['TCA'][$lowerTable]) || !$this->dbgTca)	{ // is this a table name inside of TYPO3?
				if ($this->dbgTable[$lowerTable]) {
					$bEnable = TRUE;
				}
			} else if ($this->dbgTca) {
				$bDisable = TRUE;
			}
			if (
				$this->dbgExcludeTable[$lowerTable] ||
				strpos($lowerTable, 'transaction') !== FALSE ||
				strpos($lowerTable, 'commit') !== FALSE
			) {
				$bDisable = TRUE;
			}
		}
	}


	/**
	 * getEnableDisable function: determines if a table is enabled or disabled
	 *
	 * @param	string		table name
	 * @param	string		SQL part which should contain a table name
	 * @param	boolean		output: table is enabled
	 * @param	boolean		output: table is disabled
	 * @return	void
	 */
	function getEnableDisable($sqlpart, &$bEnable, &$bDisable) {
		$bEnable = FALSE;
		$bDisable = FALSE;
		$x = strtok($sqlpart, ', =');

		while ($x !== FALSE)	{
			self::enableByTable($x, $bEnable, $bDisable);
			$x = strtok(', =');
		}
		if ($bEnable)	{	// an explicitely set table overrides the excluded tables
			$bDisable = FALSE;
		}
	}


	/**
	 * generates a debug backtrace line
	 *
	 * @return	string	file name and line numbers ob the backtrace
	 */
	function getTraceLine ()	{

		if (self::hasBacktraceParam())	{
			$trail = debug_backtrace(FALSE);
		} else {
			$trail = debug_backtrace();
		}

		$debugTrail1 = $trail[2];
		$debugTrail2 = $trail[3];
		$debugTrail3 = $trail[4];

		$rc =
			basename($debugTrail3['file']) . '#' . $debugTrail3['line'] . '->' . $debugTrail3['function'] . ' // ' .
			basename($debugTrail2['file']) . '#' . $debugTrail2['line'] . '->' . $debugTrail2['function'] . ' // ' .
			basename($debugTrail1['file']) . '#' . $debugTrail1['line'] . '->' . $debugTrail1['function'];

		return $rc;
	}


	/**
	 * Debug function: Outputs error if any
	 *
	 * @param	string		Function calling debug()
	 * @param	string		error text
	 * @param	string		mode
	 * @param	string		table name
	 * @param	string		SQL query
	 * @param	resource	SQL resource
	 * @param	string		location with file and line number of the sql query
	 * @return	void
	 */
	function myDebug ($func, $error, $mode, $table, $query, $res, $seconds) {

		$debugArray = Array('function/mode'=>'Pg'.$GLOBALS['TSFE']->id.' '.$func.'('.$table.') - ', '$query'=>$query);
		if (is_object($GLOBALS['TSFE']->fe_user)) {
			if (is_array($GLOBALS['TSFE']->fe_user->user)) {
				$this->feUid = intval($GLOBALS['TSFE']->fe_user->user['uid']);
			}
		}

		if ($error)		{
			if (!intval($this->dbgConf['DISABLE_ERRORS']))	{
				$this->getEnableDisable($table, $bEnable, $bDisable);

				if (!$bDisable && $this->dbgTable['all'] || $bEnable)	{
					$debugArray['function/mode'] .= $this->getTraceLine();
					$debugArray['SQL ERROR ='] = $error;
					$debugArray['lastBuiltQuery'] = $this->debug_lastBuiltQuery;
					$debugArray['debug_backtrace'] = t3lib_div::debug_trail();
					$debugArray['miliseconds'] = round($seconds * 1000,3);
					if ($this->dbgTextformat)	{
						ob_start();
						print_r($debugArray);
						$debugOut = ob_get_contents();
						ob_end_clean();
					} else {
						$debugOut = $debugArray;
					}
					$this->callDebugger($this->dbgOutput, $debugOut);
				}
			}
		} else {
			if ($table != '')	{
				$sqlPart = $table;
			} else {
				$sqlPart = $query;
			}
			$this->getEnableDisable($sqlPart, $bEnable, $bDisable);

			if ($this->dbgQuery[$mode] &&
				!$bDisable &&
				($this->dbgTable['all'] || $bEnable || !$table) &&
				(count($this->dbgFeUser) == 0 || $this->dbgFeUser[$this->feUid . '.']) &&
				($this->dbgId[$GLOBALS['TSFE']->id . '.'] || $this->dbgId['0.'])
			) {
				$debugArray['function/mode'] .= $this->getTraceLine();
				if ($mode=='SELECT') {
					$debugArray['num_rows()'] = $this->sql_num_rows($res);
				}
				if ($mode=='UPDATE' || $mode=='DELETE' || $mode=='INSERT') {
					$debugArray['affected_rows()'] = $this->sql_affected_rows();
				}
				if ($mode=='INSERT') {
					$debugArray['insert_id()'] = $this->sql_insert_id();
				}
				if ($mode=='SQL') {
					if (is_resource($res)) $debugArray['num_rows()'] = $this->sql_num_rows($res);
					$debugArray['affected_rows()'] = $this->sql_affected_rows();
					$debugArray['insert_id()'] = $this->sql_insert_id();
				}
				if ($this->dbgConf['BTRACE_SQL']) {
					$debugArray['debug_backtrace'] = t3lib_div::debug_trail();
				}
				$debugArray['miliseconds'] = round($seconds * 1000,3);
				$debugArray['------------'] = '';

				if ($this->dbgTextformat)	{
					ob_start();
					print_r($debugArray);
					$debugOut = ob_get_contents();
					ob_end_clean();
				} else {
					$debugOut = $debugArray;
				}
				$this->callDebugger($this->dbgOutput, $debugOut);
			}
		}
	}


	function callDebugger ($debugFunc, $debugOut)	{

		try	{
			if (function_exists($debugFunc)) {
				call_user_func($debugFunc,$debugOut,'SQL debug');
			} else {
				t3lib_div::debug($debugOut);
			}
		}
		catch(Exception $e)	{
			t3lib_div::debug($debugOut);
		}
	}


	/**
	 * Will fullquote all values in the one-dimensional array so they are ready to "implode" for an sql query.
	 *
	 * @param	array		Array with values (either associative or non-associative array)
	 * @param	string		Table name for which to quote
	 * @param	string/array		List/array of keys NOT to quote (eg. SQL functions) - ONLY for associative arrays
	 * @return	array		The input array with the values quoted
	 * @see cleanIntArray()
	 */
	function fullQuoteArray ($arr, $table, $noQuote=FALSE)	{
		if (is_string($noQuote))	{
			$noQuote = explode(',',$noQuote);
		} elseif (!is_array($noQuote))	{	// sanity check
			$noQuote = FALSE;
		}

		foreach($arr as $k => $v)	{
			if ($noQuote===FALSE || !in_array($k,$noQuote))     {
				$arr[$k] = $this->fullQuoteStr($v, $table);
			}
		}
		return $arr;
	}
}


if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/debug_mysql_db/class.ux_t3lib_db.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/debug_mysql_db/class.ux_t3lib_db.php']);
}
?>
