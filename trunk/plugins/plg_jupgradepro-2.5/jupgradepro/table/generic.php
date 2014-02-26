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
 * Generic table
 *
 * @package 	Matware.jUpgradePro
 * @subpackage		JUpgradeTableGeneric
 * @since	3.0.1
 */
class JUpgradeproTableGeneric extends JUpgradeproTable
{
	/**
	 * Table type
	 *
	 * @var string
	 */	
	var $_type = 'generic';	

	/**
	 * Contructor
	 *
	 * @access protected
	 * @param database A database connector object
	 */
	function __construct( &$db ) {
		parent::__construct( 'jupgradepro_plugin_steps', 'id', $db );
	}

	/**
	 * Setting the conditions hook
	 *
	 * @return	void
	 * @since	3.0.1
	 * @throws	Exception
	 */
	public function getConditionsHook()
	{
		$conditions = array();
		$table = $this->getTableName();

		$keys = $this->_getTableKeys($table);

		$conditions['order'] = count( $keys ) ? implode( ', ', $keys ) : '';
		
		return $conditions;
	}

	/**
	 * Change the generic table to new one
	 *
	 * @return	void
	 * @since	3.0.1
	 * @throws	Exception
	 */
	public function changeTable($table)
	{
		// Getting table
		$name = $table;
		$table = '#__'.$table;

		// Getting key
		$keys = $this->_getTableKeys($table);
		$key = !empty($keys) ? $keys[0] : '';

		// Getting columns
		$this->_db->setQuery("SHOW COLUMNS FROM {$table}");
		$columns = $this->_db->loadObjectList();

		foreach ($columns as $column) {
			$colname = $column->Field;
			$this->$colname = '';
		}

		// Check if table exists on db
		$query = "SELECT name FROM jupgradepro_plugin_steps WHERE name = '{$name}'";
		$this->_db->setQuery( $query );
		$exists = $this->_db->loadResult();

		if ($exists == '') {
			$query = "INSERT INTO jupgradepro_plugin_steps (`name`) VALUES ( '{$name}' )  ";
			$this->_db->setQuery( $query );
			$this->_db->query();
		}

		parent::__construct( $table, $key, $this->_db );
	}

	/**
	 * Get the keys of the generic table
	 *
	 * @return	void
	 * @since	3.0.1
	 * @throws	Exception
	 */
	private function _getTableKeys($table)
	{
		$query = "SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'";
		$this->_db->setQuery( $query );
		$result = $this->_db->loadObjectList();

		$return = array();
		foreach ($result as $key) {
			$return[] = $key->Column_name;
		}

		return $return;
	}

}
