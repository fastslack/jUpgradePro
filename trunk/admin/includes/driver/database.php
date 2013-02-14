<?php
/**
 * jUpgrade
 *
 * @version		  $Id$
 * @package		  MatWare
 * @subpackage	com_jupgrade
 * @author      Matias Aguirre <maguirre@matware.com.ar>
 * @link        http://www.matware.com.ar
 * @copyright		Copyright 2004 - 2013 Matias Aguirre. All rights reserved.
 * @license		  GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * jUpgrade database utility class
 *
 * @package		MatWare
 * @subpackage	com_jupgrade
 */
class jUpgradeDriverDatabase extends jUpgradeDriver
{
	/**
	 * @var      
	 * @since  3.0
	 */
	public $_db_old = null;
	/**
	 * @var	conditions  
	 * @since  3.0
	 */
	public $_conditions = null;

	/**
	 * @var    array  List of extensions steps
	 * @since  12.1
	 */
	private $extensions_steps = array('extensions', 'ext_components', 'ext_modules', 'ext_plugins');

	function __construct(jUpgradeStep $step = null, $conditions = array())
	{
		parent::__construct($step);

		$this->_conditions = $conditions;

		$db_config = array();
		$db_config['driver'] = $this->params->get('old_dbtype');
		$db_config['host'] = $this->params->get('old_hostname');
		$db_config['user'] = $this->params->get('old_username');
		$db_config['password'] = $this->params->get('old_password');
		$db_config['database'] = $this->params->get('old_db');
		$db_config['prefix'] = $this->params->get('old_prefix');

		$this->_db_old = JDatabase::getInstance($db_config);
	}

	/**
	 * Get total of the rows of the table
	 *
	 * @access	public
	 * @return	int	The total of rows
	 */
	public function getSourceDatabase( )
	{
		$cache_limit = $this->params->get('cache_limit');

		$key = $this->getKeyName();
		$name = $this->_getStepName();

		$where = '';
		$where_or = '';
		$join = '';
		$limit = '';
		$order = '';

		if ( !in_array($name, $this->extensions_steps) ) {
			$oid = $this->_getStepID();
			$limit = "LIMIT {$oid}, {$cache_limit}";
		}

		// Get the conditions
		$conditions = $this->getConditionsHook();

		if ( isset( $conditions['where'] ) ) {
			$where = count( $conditions['where'] ) ? 'WHERE ' . implode( ' AND ', $conditions['where'] ) : '';
		}
		if (isset($conditions['where_or'])) {
			$where_or = count( $conditions['where_or'] ) ? 'WHERE ' . implode( ' OR ', $conditions['where_or'] ) : '';
		}		
		$select = isset($conditions['select']) ? $conditions['select'] : '*';
		$as = isset($conditions['as']) ? 'AS '.$conditions['as'] : '';
		$group_by = isset($conditions['group_by']) ? 'GROUP BY '.$conditions['group_by'] : '';

		if (isset($conditions['join'])) {
			$join = count( $conditions['join'] ) ? implode( ' ', $conditions['join'] ) : '';
		}

		if ($key != '') {
			$order = isset($conditions['order']) ? "ORDER BY " . $conditions['order'] : "ORDER BY {$key} ASC";
		}

		// Get the row
		$query = "SELECT {$select} FROM {$this->getTableName()} {$as} {$join} {$where}{$where_or} {$group_by} {$order} {$limit}";
		$this->_db_old->setQuery( $query );
		//echo "\nQUERY: $query\n";
		$rows = $this->_db_old->loadAssocList();
//print_r($rows);
		if (is_array($rows)) {
			return $rows;
		}
		else
		{
			throw new Exception( $this->_db_old->getErrorMsg() );
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
		$table = $this->getTableName();

		$conditions = $this->getConditionsHook();

		$where = '';
		$where_or = '';

		if ( isset( $conditions['where'] ) ) {
			$where = count( $conditions['where'] ) ? 'WHERE ' . implode( ' AND ', $conditions['where'] ) : '';
		}
		if ( isset( $conditions['where_or'] ) ) {
			$where_or = count( $conditions['where_or'] ) ? 'WHERE ' . implode( ' OR ', $conditions['where_or'] ) : '';
		}

		$as = isset($conditions['as']) ? 'AS '.$conditions['as'] : '';

		$join = '';
		if (isset($conditions['join'])) {
			$join = count( $conditions['join'] ) ? implode( ' ', $conditions['join'] ) : '';
		}

		/// Get Total
		$query = "SELECT COUNT(*) FROM {$table} {$as} {$join} {$where}{$where_or}";
		$this->_db_old->setQuery( $query );
		$total = $this->_db_old->loadResult();

		return (int)$total;
	}

	/*
	 *
	 * @return	void
	 * @since	3.0.0
	 * @throws	Exception
	 */
	public function getConditionsHook()
	{
		//$conditions = array();		
		//$conditions['where'] = array();
		// Do customisation of the params field here for specific data.
		return $this->_conditions;	
	}

	/**
 	* 
	* @param string $table The table name
	*/
	function tableExists ($table) { 
		$tables = array();

		$tables = $this->_db_old->getTableList();
		return (in_array($table, $tables)) ? 'YES' : 'NO';
	}

	/**
	 * @return  string	The table name  
	 *
	 * @since   3.0
	 */
	public function getTableName()
	{
		return $this->_step->table;
	}

	/**
	 * @return  string	The table key name  
	 *
	 * @since   3.0
	 */
	public function getKeyName()
	{
		if (empty($this->_tbl_key)) {
			$table = $this->getTableName();

			$query = "SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'";
			$this->_db_old->setQuery( $query );
			$keys = $this->_db_old->loadObjectList();

			return !empty($keys) ? $keys[0]->Column_name : '';
		}else{
			return $this->_tbl_key;
		}
	}

	/**
	 * Cleanup the data in the destination database.
	 *
	 * @return	void
	 * @since	0.5.1
	 * @throws	Exception
	 */
	protected function cleanDestinationData($table = false)
	{
		// Get the table
		if ($table == false) {
			$table = $this->getTableName();
		}

		if ($this->canDrop) {
			$query = "TRUNCATE TABLE {$table}";
			$this->_db->setQuery($query);
			$this->_db->query();
		} else {
			$query = "DELETE FROM {$table}";
			$this->_db->setQuery($query);
			$this->_db->query();
		}

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}
	}
}
