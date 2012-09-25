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

		if ($load != false) {
			// Check if the row is loaded
			$key = $this->getKeyName();
			if ($this->$key == 0) {
				return false;
			}
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

		if ($oid !== null) {
			$this->$key = $oid;
		}

		$this->reset();	

		// Get the database instance	
		$db =& $this->getDBO();

		// Get the conditions
		$conditions = $this->getConditionsHook();
		
		//
		$where = count( $conditions['where'] ) ? 'WHERE ' . implode( ' AND ', $conditions['where'] ) : '';		
		$select = isset($conditions['select']) ? $conditions['select'] : '*';
		$as = isset($conditions['as']) ? 'AS '.$conditions['as'] : '';

		//
		$join = '';
		if (isset($conditions['join'])) {
			$join = count( $conditions['join'] ) ? implode( ' ', $conditions['join'] ) : '';
		}
		
		$order = isset($conditions['order']) ? "ORDER BY " . $conditions['order'] : "ORDER BY {$key} ASC";

		// Get the row
		$query = "SELECT {$select} FROM {$table} {$as} {$join} {$where} {$order}";
		$db->setQuery( $query );
		$rows = $db->loadAssocList();

		if (is_array($rows[$oid])) {
			$this->_updateID($oid+1);
			return $this->bind($rows[$oid]);
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

		$query = "UPDATE `jupgrade_plugin_steps` SET `cid` = '{$id}' WHERE name = ".$db->quote($this->_type);

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
		$db =& $this->getDBO();

		$query = 'SELECT `cid` FROM jupgrade_plugin_steps'
		. ' WHERE name = '.$db->quote($this->_type);
		$db->setQuery( $query );
		$stepid = (int) $db->loadResult();

		return $stepid;
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
		$db =& $this->getDBO();

		$conditions = $this->getConditionsHook();

		$where = count( $conditions['where'] ) ? 'WHERE ' . implode( ' AND ', $conditions['where'] ) : '';
		$as = isset($conditions['as']) ? 'AS '.$conditions['as'] : '';

		$join = '';
		if (isset($conditions['join'])) {
			$join = count( $conditions['join'] ) ? implode( ' ', $conditions['join'] ) : '';
		}

		/// Get Total
		$query = "SELECT COUNT(*) FROM {$this->_tbl} {$as} {$join} {$where}";
		$db->setQuery( $query );
		$total = $db->loadResult();

		if ($total) {
			return (int)$total;
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Get the last id
	 *
	 * @access	public
	 * @return	int	The last id
	 */
	public function getLastid()
	{
		$db =& $this->getDBO();

		$conditions = $this->getConditionsHook();

		$where = count( $conditions['where'] ) ? 'WHERE ' . implode( ' AND ', $conditions['where'] ) : '';
		$as = isset($conditions['as']) ? 'AS '.$conditions['as'] : '';

		$join = '';
		if (isset($conditions['join'])) {
			$join = count( $conditions['join'] ) ? implode( ' ', $conditions['join'] ) : '';
		}

		$order = isset($conditions['order']) ? "ORDER BY {$conditions['order']}" : "ORDER BY {$this->getKeyName()} DESC";

		// Get Total
		$query = "SELECT id FROM {$this->_tbl} {$as} {$where} {$join} {$order} LIMIT 1";
		$db->setQuery( $query );
		$lastid = $db->loadResult();

		if ($lastid) {
			return (int)$lastid;
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
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
