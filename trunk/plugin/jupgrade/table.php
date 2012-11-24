<?php
/**
* @version $Id:
* @package Matware.jUpgradePro
* @copyright Copyright (C) 2005 - 2012 Matware. All rights reserved.
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
		$load = $this->load($id);

		if ($load !== false) {
			// Migrate it
			$this->migrate();
			// Return as JSON
			return $this->toJSON();
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
		$table = isset($this->_parameters['HTTP_TABLE']) ? $this->_parameters['HTTP_TABLE'] : '';

		// Getting the database instance
		$db = JFactory::getDbo();	

		$query = "UPDATE jupgrade_plugin_steps SET cid = 0"; 
		if ($table != false) {
			$query .= " WHERE name = '{$table}'";
		}

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
		$conditions = $this->getConditionsHook();
		
		//
		$where = '';
		if (isset($conditions['where'])) {
			$where = count( $conditions['where'] ) ? 'WHERE ' . implode( ' AND ', $conditions['where'] ) : '';
		}

		$where_or = '';
		if (isset($conditions['where_or'])) {
			$where_or = count( $conditions['where_or'] ) ? 'WHERE ' . implode( ' OR ', $conditions['where_or'] ) : '';
		}
	
		$select = isset($conditions['select']) ? $conditions['select'] : '*';
		$as = isset($conditions['as']) ? 'AS '.$conditions['as'] : '';

		//
		$join = '';
		if (isset($conditions['join'])) {
			$join = count( $conditions['join'] ) ? implode( ' ', $conditions['join'] ) : '';
		}

		$order = '';
		if ($key != '') {
			$order = isset($conditions['order']) ? "ORDER BY " . $conditions['order'] : "ORDER BY {$key} ASC";
		}

		$limit = "LIMIT {$oid}, 1";

		// Get the row
		$query = "SELECT {$select} FROM {$table} {$as} {$join} {$where}{$where_or} {$order} {$limit}";
		$db->setQuery( $query );
		$row = $db->loadAssoc();

		if (is_array($row)) {
			$this->_updateID($oid+1);
			return $this->bind($row);
		}
		else
		{
			$this->_updateID(0);
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
	public function _updateID($id)
	{
		// Getting the database instance
		$db = JFactory::getDbo();	

		$name = $this->_getStepName();

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

		if ($stepid == 0) {
			$query = "INSERT INTO `jupgrade_plugin_steps` (`id` , `name` , `cid` , `tmp`) VALUES (NULL , '{$name}', '0', '');";
			$db->setQuery( $query );
			$db->query();
		}

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
	public function migrate()
	{
		// Do custom migration
	}	

	/**
	 * Get total of the rows of the table
	 *
	 * @access	public
	 * @return	int	The total of rows
	 */
	public function getTotal()
	{
		// Getting the database instance
		$db = JFactory::getDbo();

		$conditions = $this->getConditionsHook();

		$where = '';
		if (isset($conditions['where'])) {
			$where = count( $conditions['where'] ) ? 'WHERE ' . implode( ' AND ', $conditions['where'] ) : '';
		}

		$where_or = '';
		if (isset($conditions['where_or'])) {
			$where_or = count( $conditions['where_or'] ) ? 'WHERE ' . implode( ' OR ', $conditions['where_or'] ) : '';
		}
		$as = isset($conditions['as']) ? 'AS '.$conditions['as'] : '';

		$join = '';
		if (isset($conditions['join'])) {
			$join = count( $conditions['join'] ) ? implode( ' ', $conditions['join'] ) : '';
		}

		/// Get Total
		$query = "SELECT COUNT(*) FROM {$this->_tbl} {$as} {$join} {$where}{$where_or}";
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
	 * Method to get an array of all tables in the database.
	 *
	 * @return  array  An array of all the tables in the database.
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
