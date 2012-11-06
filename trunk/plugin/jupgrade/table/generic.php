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
 * Menu table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JUpgradeTableGeneric extends JUpgradeTable
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
		parent::__construct( 'jupgrade_steps', 'id', $db );
	}

	/**
	 * Change the generic table to new one
	 *
	 * @return	void
	 * @since	3.0.0
	 * @throws	Exception
	 */
	public function changeTable($table)
	{
		// Getting table
		$table = '#__'.$table;

		// Getting key
		$query = "SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'";
		$this->_db->setQuery( $query );
		$keys = $this->_db->loadObjectList();
		$key = !empty($keys) ? $keys[0]->Column_name : '';

		// Getting columns
		$this->_db->setQuery("SHOW COLUMNS FROM {$table}");
		$columns = $this->_db->loadObjectList();

		foreach ($columns as $column) {
			$colname = $column->Field;
			$this->$colname = '';
		}

		parent::__construct( $table, $key, $this->_db );
	}
}
