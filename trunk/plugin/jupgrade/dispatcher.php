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
 * @since       1.0
 */
class JRESTDispatcher
{
	/**
	 * @var    array  Associative array of parameters for the REST message.
	 * @since  1.0
	 */
	private $_parameters = array();

	/**
	 * @var    JUpgradeTable  JUpgradeTable object
	 * @since  1.0
	 */
	private $_table = array();
	
	/**
	 * 
	 *
	 * @return  boolean  True if the user and pass are authorized
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
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
				return $return = $this->$method();
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
	 * @return  boolean  True if the user and pass are authorized
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function getTotal()
	{
		return $this->_table->total();
	}
	
	/**
	 * 
	 *
	 * @return  boolean  True if the user and pass are authorized
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function getRow()
	{			
		$id = $this->_table->_getRequestID();

		$this->_table->load($id);
		$this->_table->migrate();
		
		return $this->_table->toJSON();
	}

	/**
	 * 
	 *
	 * @return  boolean  True if the user and pass are authorized
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function getCleanup()
	{
		// Getting the database instance
		$db = JFactory::getDbo();	

		$query = "UPDATE jupgrade_steps SET cid = 0, status = 0 WHERE name = '{$this->_parameters['HTTP_TYPE']}'";
		$db->setQuery( $query );
		$result = $db->query();

		return true;
	}
}
