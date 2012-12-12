<?php
/**
 * jUpgrade
 *
 * @version		  $Id$
 * @package		  MatWare
 * @subpackage	com_jupgrade
 * @author      Matias Aguirre <maguirre@matware.com.ar>
 * @link        http://www.matware.com.ar
 * @copyright		Copyright 2006 - 2013 Matias Aguirre. All rights reserved.
 * @license		  GNU General Public License version 2 or later; see LICENSE.txt
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * jUpgrade step class
 *
 * @package		MatWare
 * @subpackage	com_jupgrade
 */
class jUpgradeStep
{	
	public $id = null;
	public $lastid = null;
	public $name = null;
	public $title = null;
	public $class = null;
	//public $category = null;

	public $cid = 1;
	public $cache = 0;
	public $status = 0;
	public $total = 0;

	public $type = null;
	public $laststep = null;
	public $xml = null;
	public $table = null;
	public $conditions = null;
	public $next = null;

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

	function __construct($step = null)
	{
		jimport('legacy.component.helper');
		JLoader::import('helpers.jupgradepro', JPATH_COMPONENT_ADMINISTRATOR);

		// Set the step params	
		$this->setParameters((array) $step);

		//$this->params = jUpgradeProHelper::getParams();

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
	static function getInstance($key = null, $extension = null)
	{

		$step = jUpgradeStep::_getStep($key, $extension);

		// Create our new jUpgrade connector based on the options given.
		try
		{
			$instance = new JUpgradeStep($step);
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException(sprintf('Unable to load jUpgrade object: %s', $e->getMessage()));
		}

		return $instance;
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
				if (property_exists ( $this , $k ))
				{
					// Perform url decoding so that any use of '+' as the encoding of the space character is correctly handled.
					$this->$k = urldecode((string) $v);
				}
			}
		}
	}

	/**
	 * Method to get the parameters. 
	 *
	 * @return  array  $parameters  The parameters of this object.
	 *
	 * @since   3.0.0
	 */
	public function getParameters()
	{
		$return = array();

		foreach ($this as $k => $v)
		{
			if (property_exists ( $this , $k ))
			{
				if (!is_object($v)) {
					// Perform url decoding so that any use of '+' as the encoding of the space character is correctly handled.
					$return[$k] = urldecode((string) $v);
				}
			}
		}

		return $return;
	}

	/**
	 * Get the next step
	 *
	 * @return   step object
	 */
	public function getStep($name = false, $json = true, $extension = false) {

		JLoader::import('helpers.jupgradepro', JPATH_COMPONENT_ADMINISTRATOR);
		$this->params = jUpgradeProHelper::getParams();

		$limit = $this->params->get('cache_limit');

		// Getting total
		$object = jUpgrade::getInstance($this);
		$this->total = (int) $object->getTotal();

		// We must to fragment the steps
		if ($this->total > $limit) {

			if ($this->cache == 0 && $this->status == 0) {
				echo "{[[1]]}";

				$this->start = 1;
				$this->cache = round($this->total / $limit, 0, PHP_ROUND_HALF_DOWN);
				$this->stop = $limit;
				$this->first = true;
				$this->status = 1;

			} else if ($this->cache == 1 && $this->status == 1) { 
				echo "{[[2]]}";

				$this->start = $this->cid;
				$this->next = true;
				$this->status = 1;
				$this->cache = 0;

			} else if ($this->cache > 0) { 
				echo "{[[3]]}";

				$this->start = $this->cid;
				$this->stop = ($this->start - 1) + $limit;
				$this->status = 1;
				$this->cache = $this->cache - 1;

				if ($this->stop > $this->total) {
					$this->next = true;
				}else{
					$this->middle = true;
					//unset($this->cid);
				}
			}

		}else if ($this->total == 0) {
			echo "{[[4]]}";

			$this->start = 0;
			$this->stop = -1;
			$this->first = true;
			$this->status = 2;
			$this->cache = 0;

		}else{
			echo "{[[5]]}";
			$this->start = 1;
			$this->next = true;
			$this->first = true;
			//$this->status = 2;
			$this->cache = 0;
		}

		// updating the status flag
		$this->_updateStep($extension);

		// Go to the next step
		if ($this->next == true) {
			$this->stop = $this->total;
		}

		// Mark if is the end of the step
		if ($this->name == $this->laststep) {
			$this->end = true;
		}

		// Encoding
		if ($json == true) {
			$return = json_encode($this->getParameters());
		}else{
			$return = (object) $this->getParameters();
		}

		return($return);
	}

	/**
	 * Getting the next step
	 *
	 * @return   step object
	 */
	public function _refresh($key = null, $extension = false) {

		if ($extension == false) {
			$table = 'jupgrade_steps';
		}else if($extension == 'table') {
			$table = 'jupgrade_extensions_tables';
		}else if($extension == true) {
			$table = 'jupgrade_extensions';
		}

		// Select the steps
		if (isset($key)) {
			$query = "SELECT * FROM {$table} AS s WHERE s.name = '{$key}' ORDER BY s.id ASC LIMIT 1";
		}else{
			$query = "SELECT * FROM {$table} AS s WHERE s.status != 2 ORDER BY s.id ASC LIMIT 1";
		}

		$this->_db->setQuery($query);
		$step = $this->_db->loadAssoc();

		$this->setParameters($step);
	}

	/**
	 * Getting the next step
	 *
	 * @return   step object
	 */
	static public function _getStep($key = null, $extension = false) {

		$db = JFactory::getDBO();

		if ($extension == false) {
			$table = 'jupgrade_steps';
		}else if($extension == 'table') {
			$table = 'jupgrade_extensions_tables';
		}else if($extension == true) {
			$table = 'jupgrade_extensions';
		}

		// Select the steps
		if (isset($key)) {
			$query = "SELECT * FROM {$table} AS s WHERE s.name = '{$key}' ORDER BY s.id ASC LIMIT 1";
		}else{
			$query = "SELECT * FROM {$table} AS s WHERE s.status != 2 ORDER BY s.id ASC LIMIT 1";
		}

		$db->setQuery($query);
		$step = $db->loadObject();

		if ($step == '') {
			return false;
		}

		// Check for query error.
		$error = $db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		// Select last step
		$query = "SELECT name FROM {$table} WHERE status = 0 ORDER BY id DESC LIMIT 1";
		$db->setQuery($query);
		$step->laststep = $db->loadResult();

		// Next
		$step->next = false;

		// Check for query error.
		$error = $db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		if ($extension == false) {
			$step->type = 'steps';
		}else if($extension == 'table') {
			$step->type = 'extensions_tables';
		}else if($extension == true) {
			$step->type = 'extensions';
		}

		// Check if steps is an object
		if (is_object($step)) {
		  return $step;
		}else{
			return false;
		}
	}

	/**
	 * updateStep
	 *
	 * @return	none
	 * @since	2.5.2
	 */
	public function _updateStep($extension = false) {

		$cache = $cid = $total = '';

		$status = "status = {$this->status}";
		$cache = ", cache = {$this->cache}";

		if (!empty($this->cid)) {
			$cid = ", cid = {$this->cid}";
		}
		if (!empty($this->total)) {
			$total = ", total = {$this->total}";
		}

		$table = $extension == false ? 'jupgrade_steps' : 'jupgrade_extensions_tables';

		// updating the status flag
		$query = "UPDATE {$table} SET {$status} {$cache} {$cid} {$total}"
		." WHERE name = '{$this->name}'";
		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		return true;
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
