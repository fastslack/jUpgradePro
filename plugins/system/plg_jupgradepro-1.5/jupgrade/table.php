<?php
/**
* @version $Id:
* @package Matware.jUpgradePro
* @copyright Copyright (C) 2005 - 2014 Matware. All rights reserved.
* @author Matias Aguirre
* @email maguirre@matware.com.ar
* @link http://www.matware.com.ar/
* @license GNU General Public License version 2 or later; see LICENSE
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Abstract Table class
 *
 * Parent classes to all tables.
 *
 * @abstract
 * @package 	Joomla.Framework
 * @subpackage	Table
 * @since		1.0
 * @tutorial	Joomla.Framework/jtable.cls
 */
class JUpgradeTable extends JTable
{
	/**
	 * Table type
	 */
	public $_type = '';

	/**
	 * Get the row
	 *
	 * @return  string/json	The json row
	 *
	 * @since   3.0
	 */
	public function getRow()
	{
		// Get the next id
		$id = $this->_getStepID();
		// Load the row
		$row = array();
		$row[] = $this->load($id);
		// Migrate it
		if ($row !== false) {

			$row = $this->migrate($row);

			$this->bind($row);
			// Return as JSON
			return $this->toJSON();
		}else{
			return false;
		}
	}

	/**
	 * Get the row
	 *
	 * @return  string/json	The json row
	 *
	 * @since   3.0
	 */
	public function getRows($chunk)
	{
		// Get the next id
		$id = $this->_getStepID();

		// Load the row
		$list = $this->loadList($id, $chunk);

		// Migrate it
		if (is_array($list)) {
			$list = $this->migrate($list);

			// Return as JSON
			echo json_encode($list);
		}else{
			return false;
		}
	}

	/**
	 * Cleanup
	 *
	 * @return  boolean
	 *
	 * @since   3.0
	 */
	public function getCleanup()
	{
		// Getting the database instance
		$db = JFactory::getDbo();

		$query = "UPDATE jupgrade_plugin_steps SET cid = 0";
		//if ($this->table != false) {
		//	$query .= " WHERE name = '{$this->table}'";
		//}

		$db->setQuery( $query );
		$result = $db->query();

		return true;
	}

	/**
	 * Get the row
	 *
	 * @access	public
	 * @return	int	The total of rows
	 */
	public function load( $oid = null )
	{
		$key = $this->getKeyName();
		$table = $this->getTableName();

		if ($oid === null) {
			return false;
		}

		if ($oid !== null AND $key != '') {
			$this->$key = $oid;
		}

		$this->reset();

		// Getting the database instance
		$db = JFactory::getDbo();

		// Get the conditions
		$conditions = $this->_processConditions();

		$limit = "LIMIT {$oid}, 1";

		// Get the row
		$query = "SELECT {$conditions['select']} FROM {$table} {$conditions['as']} {$conditions['join']} {$conditions['where']}{$conditions['where_or']} {$conditions['order']} {$limit}";
		$db->setQuery( $query );
		$row = $db->loadAssoc();

		if (is_array($row)) {
			$this->_updateID($oid+1);
			return $row;
		}
		else
		{
			$this->_updateID(0);
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Get the row
	 *
	 * @access	public
	 * @return	int	The total of rows
	 */
	public function loadList( $oid = null, $chunk = null )
	{
		$key = $this->getKeyName();
		$table = $this->getTableName();

		if ($oid === null) {
			return false;
		}

		$this->reset();

		// Getting the database instance
		$db = JFactory::getDbo();

		// Get the conditions
		$conditions = $this->_processConditions();

		$limit = "LIMIT {$oid}, {$chunk}";

		// Get the row
		$query = "SELECT {$conditions['select']} FROM {$table} {$conditions['as']} {$conditions['join']} {$conditions['where']}{$conditions['where_or']} {$conditions['order']} {$limit}";
		$db->setQuery( $query );
		$rows = $db->loadAssocList();

		if (is_array($rows)) {

			$update_id = $oid + count($rows);

			$this->_updateID($update_id);

			return $rows;
		}
		else
		{
			$this->_updateID(0);
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Get total of the rows of the table
	 *
	 * @access	public
	 * @return	int	The total of rows
	 */
	public function getTotal()
	{
		// Get the database instance
		$db = JFactory::getDbo();
		// Get the table
		$table = $this->getTableName();
		// Get the conditions
		$conditions = $this->_processConditions();

		// Get Total
		$query = "SELECT COUNT(*) FROM {$table} {$conditions['as']} {$conditions['join']} {$conditions['where']}{$conditions['where_or']}";
		$db->setQuery( $query );
		$total = $db->loadResult();

		if ($total != '') {
			return $total;
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Update the step id
	 *
	 * @return  boolean  True if the update is ok
	 *
	 * @since   3.0.0
	 */
	public function _processConditions()
	{
		// Get the conditions
		$conditions = $this->getConditionsHook();

		$key = $this->getKeyName();

		$return = array();

		//
		$return['where'] = '';
		if (isset($conditions['where'])) {
			$return['where'] = count( $conditions['where'] ) ? 'WHERE ' . implode( ' AND ', $conditions['where'] ) : '';
		}

		$return['where_or'] = '';
		if (isset($conditions['where_or'])) {
			$return['where_or'] = count( $conditions['where_or'] ) ? 'WHERE ' . implode( ' OR ', $conditions['where_or'] ) : '';
		}

		$return['select'] = isset($conditions['select']) ? $conditions['select'] : '*';
		$return['as'] = isset($conditions['as']) ? 'AS '.$conditions['as'] : '';

		//
		$return['join'] = '';
		if (isset($conditions['join'])) {
			$return['join'] = count( $conditions['join'] ) ? implode( ' ', $conditions['join'] ) : '';
		}

		$return['order'] = '';
		if ($key != '') {
			$return['order'] = isset($conditions['order']) ? "ORDER BY " . $conditions['order'] : "ORDER BY {$key} ASC";
		}

		return $return;
	}

	/**
	 * Update the step id
	 *
	 * @return  boolean  True if the update is ok
	 *
	 * @since   3.0.0
	 */
	public function _updateID($id)
	{
		// Getting the database instance
		$db = JFactory::getDbo();

		$name = $this->_getStepName();

		$query = "SELECT `name` FROM `jupgrade_plugin_steps` WHERE `name` = ".$db->quote($name);
		$db->setQuery( $query );
		$exists = $db->loadResult();

		if ($exists == "")
		{
			$query = "INSERT INTO `jupgrade_plugin_steps` (`id`, `name`, `cid`) VALUES (NULL, {$db->quote($name)}, '0')";
			$db->setQuery( $query );
			$db->query();
		}

		$query = "UPDATE `jupgrade_plugin_steps` SET `cid` = '{$id}' WHERE name = ".$db->quote($name);
		$db->setQuery( $query );
		return $db->query();
	}

	/**
	 * Update the step id
	 *
	 * @return  int  The next id
	 *
	 * @since   3.0.0
	 */
	public function _getStepID()
	{
		// Getting the database instance
		$db = JFactory::getDbo();

		$name = $this->_getStepName();

		$query = 'SELECT `cid` FROM jupgrade_plugin_steps'
		. ' WHERE name = '.$db->quote($name);
		$db->setQuery( $query );
		$stepid = (int) $db->loadResult();

		return $stepid;
	}

	/**
	 * Update the step id
	 *
	 * @return  int  The next id
	 *
	 * @since   3.0.0
	 */
	public function _getStepName()
	{
		if ($this->_type == 'generic') {
			return str_replace('#__', '', $this->_tbl);
		}else{
			return $this->_type;
		}
	}

	/**
	 * Get the mysql conditions hook
	 *
	 * @return  array  The basic conditions
	 *
	 * @since   3.0.0
	 */
	public function getConditionsHook()
	{
		$conditions = array();
		$conditions['where'] = array();
		// Do customisation of the params field here for specific data.
		return $conditions;
	}

	/**
	 * Migrate hook
	 *
	 * @return  nothing
	 *
	 * @since   3.0.0
	 */
	public function migrate($rows)
	{
		// Do custom migration
		return $rows;
	}

	/**
 	* Writes to file all the selected database tables structure with SHOW CREATE TABLE
	* @param string $table The table name
	*/
	public function getTableStructure() {
		// Getting the database instance
		$db = JFactory::getDbo();

		$tables = $this->_tbl;

		// Header
		$structure  = "-- \n";
		$structure .= "-- Table structure for table `{$tables}`\n";
		$structure .= "-- \n\n";

		// Initialise variables.
		$result = array();

		// Sanitize input to an array and iterate over the list.
		settype($tables, 'array');
		foreach ($tables as $table)
		{
			// Set the query to get the table CREATE statement.

			$query = "SHOW CREATE table {$table}";
			$db->setQuery($query);
			$row = $db->loadRow();

			// Populate the result array based on the create statements.
			$result[$table] = $row[1];
		}

		$structure .= "{$result[$table]} ;\n\n";

		$structure = str_replace('TYPE', 'ENGINE', $structure);
		$structure = str_replace($db->getPrefix(), '#__', $structure);
		//$structure = str_replace('MyISAM', 'InnoDB', $structure);

		return $structure;
	}

	/**
	 * Method to get bool if table exists
	 *
	 * @return  string  YES is table is found, NO if not.
	 *
	 * @since   3.0.0
	 * @throws  JDatabaseException
	 */
	public function getTableexists()
	{
		// Getting the database instance
		$db = JFactory::getDbo();

		$table = $this->_tbl;
		$prefix = $db->getPrefix();

		$table = str_replace ('#__', $prefix, $table);

		// Set the query to get the tables statement.
		$db->setQuery('SHOW TABLES');
		$tables = $db->loadResultArray();

		if (in_array($table, $tables)) {
			return 'YES';
		}else{
			return 'NO';
		}
	}

	/**
	 * Method to get the tables list
	 *
	 * @return  array  An array of all the tables in the database.
	 *
	 * @since   3.2.0
	 * @throws  JDatabaseException
	 */
	public function getTableslist()
	{
		// Getting the database instance
		$db = JFactory::getDbo();

		// Set the query to get the tables statement.
		$prefix = $db->getPrefix();
		$tables = $db->getTableList();

		$new_tables = array();
		$count_prefix = strlen($prefix);

		foreach ($tables as $table)
		{
			if ($prefix == substr($table, 0, $count_prefix))
			{
				$new_tables[] = $table;
			}
		}

		return json_encode($new_tables);
	}

	/**
	 * Method to get the table prefix
	 *
	 * @return  string  The table prefix
	 *
	 * @since   3.2.0
	 * @throws  JDatabaseException
	 */
	public function getTablesprefix()
	{
		// Getting the database instance
		$db = JFactory::getDbo();

		return json_encode($db->getPrefix());
	}

	/**
	 * Method to get the columns
	 *
	 * @since   3.8.0
	 * @throws  JDatabaseException
	 */
	public function getTablescolumns($table)
	{
		// Getting the database instance
		$db = JFactory::getDbo();
		return json_encode($db->getTableColumns($table));
	}

	/**
	 * Method to get the parameters of one table
	 *
	 * @return  string  JSON parameters
	 *
	 * @since   3.0.0
	 * @throws  JDatabaseException
	 */
	public function getTableParams()
	{
		// Getting the database instance
		$db = JFactory::getDbo();

		$table = $this->_tbl;
		$prefix = $db->getPrefix();

		$table = str_replace ('#__', $prefix, $table);

		// Set the query to get the tables statement.
		$query = "SELECT params FROM {$table} WHERE `option` = 'com_content' LIMIT 1";
		$db->setQuery($query);
		$params = $db->loadResult();

		$params = $this->convertParams($params);

		return $params;
	}

	/**
	 * Export item list to json
	 *
	 * @access public
	 */
	public function toJSON ()
	{
		$array = array();

		foreach (get_object_vars( $this ) as $k => $v)
		{
			if (is_array($v) or is_object($v) or $v === NULL)
			{
				continue;
			}
			if ($k[0] == '_')
			{ // internal field
				continue;
			}

			$array[$k] = $v;
		}

		$json = json_encode($array);

		return $json;
	}

	/**
	 * Converts the params fields into a JSON string.
	 *
	 * @param	string	$params	The source text definition for the parameter field.
	 *
	 * @return	string	A JSON encoded string representation of the parameters.
	 * @since	0.4.
	 * @throws	Exception from the convertParamsHook.
	 */
	protected function convertParams($params)
	{
		$temp	= new JParameter($params);
		$object	= $temp->toObject();

		// Fire the hook in case this parameter field needs modification.
		$this->convertParamsHook($object);

		return json_encode($object);
	}

	/**
	 * A hook to be able to modify params prior as they are converted to JSON.
	 *
	 * @param	object	$object	A reference to the parameters as an object.
	 *
	 * @return	void
	 * @since	0.4.
	 * @throws	Exception
	 */
	protected function convertParamsHook(&$object)
	{
		// Do customisation of the params field here for specific data.
	}
}
