<?php
/**
* jUpgradePro
*
* @version $Id:
* @package jUpgradePro
* @copyright Copyright (C) 2004 - 2013 Matware. All rights reserved.
* @author Matias Aguirre
* @email maguirre@matware.com.ar
* @link http://www.matware.com.ar/
* @license GNU General Public License version 2 or later; see LICENSE
*/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * jUpgradePro database utility class
 *
 * @package		jUpgradePro
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

	function __construct(jUpgradeStep $step = null)
	{
		parent::__construct($step);

		$class = (!empty($step->class)) ? $step->class : 'jUpgrade';
		$name = (!empty($step->name)) ? $step->name : '';
		$xmlpath = (!empty($step->xmlpath)) ? $step->xmlpath : '';

		JLoader::import('helpers.jupgradepro', JPATH_COMPONENT_ADMINISTRATOR);

		jUpgradeProHelper::requireClass($name, $xmlpath, $class);

		// @@ Fix bug using PHP < 5.2.3 version
		$this->_conditions = call_user_func($class .'::getConditionsHook');

		$db_config = array();
		$db_config['driver'] = $this->params->old_dbtype;
		$db_config['host'] = $this->params->old_hostname;
		$db_config['user'] = $this->params->old_username;
		$db_config['password'] = $this->params->old_password;
		$db_config['database'] = $this->params->old_db;
		$db_config['prefix'] = $this->params->old_dbprefix;

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
		$cache_limit = $this->params->cache_limit;

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
		$query = "SELECT {$select} FROM {$this->getSourceTable()} {$as} {$join} {$where}{$where_or} {$group_by} {$order} {$limit}";
		$this->_db_old->setQuery( $query );
		//echo "\nQUERY: $query\n";
		$rows = $this->_db_old->loadAssocList();

		if (is_array($rows)) {
			return $rows;
		}
		else
		{
			throw new Exception( $this->_db_old->getErrorMsg() );
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
		$table = $this->getSourceTable();
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

		$error = $this->_db_old->getErrorMsg();

		if ($error) {
			throw new Exception( $error );
		}

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
		return $this->_conditions;	
	}

	/**
 	* 
	* @param string $table The table name
	*/
	function tableExists ($table) { 
		$tables = array();
		$tables = $this->_db_old->getTableList();

		$table = $this->_db_old->getPrefix().$table;

		return (in_array($table, $tables)) ? 'YES' : 'NO';
	}

	/**
	 * @return  string	The table name  
	 *
	 * @since   3.0
	 */
	public function getSourceTable()
	{
		return '#__'.$this->_step->source;
	}

	/**
	 * @return  string	The table name  
	 *
	 * @since   3.0
	 */
	public function getDestinationTable()
	{
		return '#__'.$this->_step->destination;
	}

	/**
	 * @return  string	The table key name  
	 *
	 * @since   3.0
	 */
	public function getKeyName()
	{
		if (empty($this->_tbl_key)) {
			$table = $this->getSourceTable();

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
			$table = $this->getDestinationTable();
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
