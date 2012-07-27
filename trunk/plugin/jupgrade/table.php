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
	 * 
	 *
	 * @return  boolean  True if the user and pass are authorized
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function getNextID()
	{
		// Getting the database instance
		$db = JFactory::getDbo();	

		$id = $this->_requestID();
		$this->_updateID($id);

		return $id;
	}

	/**
	 * Get next id
	 *
	 * @access	public
	 * @return	int	The total of rows
	 */
	public function _requestID( $moreconditions = null)
	{
		$db =& $this->getDBO();

		$query = 'SELECT `cid` FROM jupgrade_steps'
		. ' WHERE name = '.$db->quote($this->_type);
		$db->setQuery( $query );
		$stepid = (int) $db->loadResult();

		$conditions = $this->getConditionsHook();
		
		if ($moreconditions != null && is_array($conditions)) {
			array_push($conditions['where'], $moreconditions);
		}

		$where = count( $conditions['where'] ) ? 'WHERE ' . implode( ' AND ', $conditions['where'] ) : '';
		$order = isset($conditions['order']) ? $conditions['order'] : 'ASC';

		if ($order == 'ASC') {
			$query = "SELECT MIN({$this->getKeyName()}) FROM {$this->getTableName()} WHERE {$this->getKeyName()} > {$stepid} LIMIT 1";
		}else if ($order == 'DESC') {
			if ($stepid == 0) {
				$query = "SELECT MAX({$this->getKeyName()}) FROM {$this->getTableName()} LIMIT 1";
			}else{
				$query = "SELECT MAX({$this->getKeyName()}) FROM {$this->getTableName()} WHERE {$this->getKeyName()} < {$stepid} LIMIT 1";
			}
		}
		
		$db->setQuery( $query );
		$id = $db->loadResult();

		if ($id) {
			return (int)$id;
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * 
	 *
	 * @return  boolean  True if the user and pass are authorized
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function _updateID($id)
	{
		// Getting the database instance
		$db = JFactory::getDbo();	
	
		$query = "UPDATE `jupgrade_steps` SET `cid` = '{$id}' WHERE name = ".$db->quote($this->_type);
		$db->setQuery( $query );
		return $db->query();
	}

	/**
	 * 
	 *
	 * 
	 *
	 * @return	void
	 * @since	3.0.0
	 * @throws	Exception
	 */
	public function getConditionsHook()
	{
		$conditions = array();		
		$conditions['where'] = array();
		// Do customisation of the params field here for specific data.
		return $conditions;	
	}


	/**
	 * Get total of the rows of the table
	 *
	 * @access	public
	 * @return	int	The total of rows
	 */
	public function load( $oid = null )
	{
		$k = $this->_tbl_key;

		if ($oid !== null) {
			$this->$k = $oid;
		}

		$oid = $this->$k;

		if ($oid === null) {
			return false;
		}
		$this->reset();	

		// Get the database instance	
		$db =& $this->getDBO();

		// Get the conditions
		$conditions = $this->getConditionsHook();
		
		// Add oid condition		
		$oid_condition = "`{$this->getKeyName()}` = {$oid}";
		array_push($conditions['where'], $oid_condition);

		$where = count( $conditions['where'] ) ? 'WHERE ' . implode( ' AND ', $conditions['where'] ) : '';
		$select = isset($conditions['select']) ? $conditions['select'] : '*';
		
		$join = '';
		if (isset($conditions['join'])) {
			$join = count( $conditions['join'] ) ? implode( ' ', $conditions['join'] ) : '';
		}
		
		$order = isset($conditions['order']) ? $conditions['order'] : "{$this->getKeyName()} ASC";

		// Get the row
		$query = "SELECT {$select} FROM {$this->getTableName()} {$join} {$where} ORDER BY {$order} LIMIT 1";
		$db->setQuery( $query );
		//echo $query;
		
		if ($result = $db->loadAssoc( )) {
			return $this->bind($result);
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * 
	 *
	 * @access	public
	 * @param		Array	Result to migrate
	 * @return	Array	Migrated result
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
	public function total()
	{
		$db =& $this->getDBO();

		$conditions = $this->getConditionsHook();

		$where = count( $conditions['where'] ) ? 'WHERE ' . implode( ' AND ', $conditions['where'] ) : '';

		/// Get Total
		$query = "SELECT COUNT(*) FROM {$this->_tbl} {$where}";
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
