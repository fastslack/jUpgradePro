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
	 * 
	 *
	 * @return  boolean  True if the user and pass are authorized
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function execute($parameters)
	{
		$this->_parameters = $parameters;
	
		//print_r($parameters);
	
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
		JTable::addIncludePath(JPATH_PLUGINS.'/system/jupgrade/table');
	
		$table = JUpgradeTable::getInstance($this->_parameters['HTTP_TYPE'], 'JUpgradeTable');
	
		return $table->total();
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
		JTable::addIncludePath(JPATH_PLUGINS.'/system/jupgrade/table');
	
		$table = JUpgradeTable::getInstance($this->_parameters['HTTP_TYPE'], 'JUpgradeTable');
	
		$table->load($this->_parameters['HTTP_ID']);
		
		return $table->toJSON();
	}
}
