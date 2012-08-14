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

defined('_JEXEC') or die;

/**
 * REST Request Dispatcher class 
 *
 * @package     Joomla.Platform
 * @subpackage  REST
 * @since       3.0
 */
class JRESTDispatcher
{
	/**
	 * @var    array  Associative array of parameters for the REST message.
	 * @since  3.0
	 */
	private $_parameters = array();

	/**
	 * @var    JUpgradeTable  JUpgradeTable object
	 * @since  3.0
	 */
	private $_table = array();
	
	/**
	 * 
	 *
	 * @return  boolean
	 *
	 * @since   3.0
	 */
	public function execute($parameters)
	{
		// Getting the database instance
		$db = JFactory::getDbo();	
	
		// Loading params
		$this->_parameters = $parameters;

		// Loading table
		if (isset($this->_parameters['HTTP_TYPE'])) {
			JTable::addIncludePath(JPATH_PLUGINS.'/system/jupgrade/table');
			$this->_table = JUpgradeTable::getInstance($this->_parameters['HTTP_TYPE'], 'JUpgradeTable');
		}

		//
		if (array_key_exists('HTTP_TASK', $parameters)) {

			$task = $this->_parameters['HTTP_TASK'];

			// 
			$method = 'get'.ucfirst($task);

			// Does the method exist?
			if (method_exists($this, $method))
			{
				return $this->$method();
			}
			else
			{
				return false;	
			}

		}else{
			return false;
		}
	}
	

	/**
	 * 
	 *
	 * @return  boolean
	 *
	 * @since   3.0
	 */
	public function getTotal()
	{	
		return $this->_table->total();
	}
	
	/**
	 * 
	 *
	 * @return  boolean  
	 *
	 * @since   3.0
	 */
	public function getRow()
	{
		// Get the next id
		$id = $this->_table->getNextID();
		// Load the row
		$this->_table->load($id);
		// Check if the row is loaded
		$key = $this->_table->getKeyName();
		if ($this->_table->$key == 0) {
			return false;
		}
		// Migrate it
		$this->_table->migrate();
		// Return as JSON
		return $this->_table->toJSON();
	}

	/**
	 * 
	 *
	 * @return  boolean
	 *
	 * @since   3.0
	 */
	public function getLastid()
	{	
		return $this->_table->lastid();
	}

	/**
	 * 
	 *
	 * @return  boolean 
	 *
	 * @since   3.0
	 */
	public function getCleanup()
	{
		$type = isset($this->_parameters['HTTP_TYPE']) ? $this->_parameters['HTTP_TYPE'] : '';

		return $this->cleanup($type);
	}
	
	/**
	 * 
	 *
	 * @return  boolean  
	 *
	 * @since   3.0
	 */
	public function cleanup($type)
	{
		// Getting the database instance
		$db = JFactory::getDbo();	

		$query = "UPDATE jupgrade_steps SET cid = 0"; 
		if ($type != false) {
			$query .= " WHERE name = '{$type}'";
		}

		$db->setQuery( $query );
		$result = $db->query();

		return true;
	}	
}
