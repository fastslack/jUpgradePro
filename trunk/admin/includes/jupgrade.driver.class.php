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
 * jUpgrade driver class
 *
 * @package		MatWare
 * @subpackage	com_jupgrade
 */
class jUpgradeDriver
{	
	/**
	 * @var      
	 * @since  3.0
	 */
	public $params = null;
	
	/**
	 * @var      
	 * @since  3.0
	 */
	public $_db = null;

	/**
	 * @var	array
	 * @since  3.0
	 */
	protected $_step = null;

	function __construct(jUpgradeStep $step = null)
	{
		jimport('legacy.component.helper');
		JLoader::import('helpers.jupgradepro', JPATH_COMPONENT_ADMINISTRATOR);

		// Set the step params	
		$this->_step = $step;

		$this->params = jUpgradeProHelper::getParams();

		// Creating dabatase instance for this installation
		$this->_db = JFactory::getDBO();
	}

	/**
	 *
	 * @param   stdClass   $options  Parameters to be passed to the database driver.
	 *
	 * @return  jUpgrade  A jupgrade object.
	 *
	 * @since  3.0.0
	 */
	static function getInstance(jUpgradeStep $step = null)
	{
		// Getting the params and Joomla version web and cli
		$params = jUpgradeProHelper::getParams();

		// Require the driver file
		if (JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.'/includes/driver/'.$params->get('method').'.php')) {
			require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/driver/'.$params->get('method').'.php';
		}

		// Derive the class name from the driver.
		$class = 'JUpgradeDriver' . ucfirst(strtolower($params->get('method')));

		// If the class still doesn't exist we have nothing left to do but throw an exception.  We did our best.
		if (!class_exists($class))
		{
			throw new RuntimeException(sprintf('Unable to load JUpgrade Driver: %s', $params->get('method')));
		}

		// Create our new jUpgradeDriver connector based on the options given.
		try
		{
			$instance = new $class($step);
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException(sprintf('Unable to load jUpgrade object: %s', $e->getMessage()));
		}

		return $instance;
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
		return $this->_step->cid;
	}

	/**
	 * @return  string	The step name  
	 *
	 * @since   3.0
	 */
	public function _getStepName()
	{
		return $this->_step->name;
	}
}
