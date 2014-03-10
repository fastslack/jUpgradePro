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
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Generic table
 *
 * @package 	Matware.jUpgradePro
 * @subpackage		JUpgradeTableGeneric
 * @since	3.0.1
 */
class JUpgradeTableGeneric extends JUpgradeTable
{
	/**
	 * Contructor
	 *
	 * @access protected
	 * @param database A database connector object
	 */
	function __construct( &$db ) {
		$this->_type = 'generic';
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
		// Get the database instance
		$db = JFactory::getDBO();

		// Getting table
		$name = $table;
		$table = '#__'.$table;

		// Getting key
		$keys = $this->_getTableKeys($table);
		$key = !empty($keys) ? $keys[0] : '';

		// Getting columns
		$db->setQuery("SHOW COLUMNS FROM {$table}");
		$columns = $db->loadObjectList();

		foreach ($columns as $column) {
			$colname = $column->Field;
			$this->$colname = '';
		}

		// Check if table exists on db
		$query = "SELECT name FROM jupgradepro_plugin_steps WHERE name = '{$name}'";
		$db->setQuery( $query );
		$exists = $db->loadResult();

		if ($exists == '') {
			$query = "INSERT INTO jupgradepro_plugin_steps (`name`) VALUES ( '{$name}' )  ";
			$db->setQuery( $query );
			$db->query();
		}

		parent::__construct( $table, $key, $db );
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
		$db = JFactory::getDBO();

		$query = "SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'";
		$db->setQuery( $query );
		$result = $db->loadObjectList();

		$return = array();
		foreach ($result as $key) {
			$return[] = $key->Column_name;
		}

		return $return;
	}
}
