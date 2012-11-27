<?php
/**
 * jUpgrade
 *
 * @version		  $Id$
 * @package		  MatWare
 * @subpackage	com_jupgrade
 * @author      Matias Aguirre <maguirre@matware.com.ar>
 * @link        http://www.matware.com.ar
 * @copyright		Copyright 2006 - 2011 Matias Aguirre. All rights reserved.
 * @license		  GNU General Public License version 2 or later; see LICENSE.txt
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
	protected $_step = array();

	/**
	 * @var    array  List of possible parameters.
	 * @since  12.1
	 */
	private $_reserved = array(
		'id',
		'cid',
		'lastid',
		'name',
		'title',
		'class',
		'category',
		'status',
		'type',
		'laststep',
		'state',
		'xml',
		'table',
		'conditions',
	);

	function __construct($step = null)
	{
		jimport('legacy.component.helper');

		// Set the step params	
		$this->setParameters((array) $step);

		// Getting the params and Joomla version web and cli
		if (!$this->isCli()) {
			// Getting the parameters
			$this->params	= JComponentHelper::getParams('com_jupgradepro');
		}else{
			// Getting the parameters
			$this->params = new JRegistry(new JConfig);
		}

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
	static function getInstance($step = null, $conditions = array())
	{
		// Getting the params and Joomla version web and cli
		if (!jUpgradeDriver::isCli()) {
			// Getting the parameters
			$params	= JComponentHelper::getParams('com_jupgradepro');
		}else{
			// Getting the parameters
			$params = new JRegistry(new JConfig);
		}

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

		// Create our new jUpgrade connector based on the options given.
		try
		{
			$instance = new $class($step, $conditions);
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException(sprintf('Unable to load jUpgrade object: %s', $e->getMessage()));
		}

		return $instance;
	}

	/**
	 * Check if the class is called from CLI
	 *
	 * @return  void	True if is running from cli
	 *
	 * @since   3.0.0
	 */
	static public function isCli()
	{
		return defined('SIGHUP') ? true : false;
	}

	/**
	 * Method to set the parameters. 
	 *
	 * @param   array  $parameters  The parameters to set.
	 *
	 * @return  void
	 *
	 * @since   3.0.0
	 */
	public function setParameters($data)
	{
		// Ensure that only valid OAuth parameters are set if they exist.
		if (!empty($data))
		{
			foreach ($data as $k => $v)
			{
				if (in_array($k, $this->_reserved))
				{
					// Perform url decoding so that any use of '+' as the encoding of the space character is correctly handled.
					$this->_step[$k] = urldecode((string) $v);
				}
			}
		}
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
		return $this->_step['cid'];
	}

	/**
	 * @return  string	The step name  
	 *
	 * @since   3.0
	 */
	public function _getStepName()
	{
		return $this->_step['name'];
	}
}
