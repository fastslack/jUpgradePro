<?php
/**
 * jUpgrade
 *
 * @version		  $Id$
 * @package		  MatWare
 * @subpackage	com_jupgrade
 * @author      Matias Aguirre <maguirre@matware.com.ar>
 * @link        http://www.matware.com.ar
 * @copyright		Copyright 2004 - 2013 Matias Aguirre. All rights reserved.
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
	public $name = null;
	public $title = null;
	public $class = null;
	public $table = null;
	public $element = null;
	public $conditions = null;

	public $debug = '';

	public $cid = 0;
	public $cache = 0;
	public $status = 0;
	public $total = 0;
	public $start = 0;
	public $stop = 0;
	public $laststep = '';

	public $first = false;
	public $next = false;
	public $middle = false;
	public $end = false;

	public $extensions = false;

	public $_table = false;

	public $error = '';
	
	/**
	 * @var      
	 * @since  3.0
	 */
	public $_db = null;

	function __construct($key = null, $extensions = false)
	{
		jimport('legacy.component.helper');
		JLoader::import('helpers.jupgradepro', JPATH_COMPONENT_ADMINISTRATOR);

		// Creating dabatase instance for this installation
		$this->_db = JFactory::getDBO();

		// Set step table
		if ($extensions == false) {
			$this->_table = 'jupgrade_steps';
		}else if($extensions === 'tables') {
			$this->_table = 'jupgrade_extensions_tables';
		}else if($extensions == true) {
			$this->_table = 'jupgrade_extensions';
		}

		// Load the last step from database
		$this->_load($key, $this->extensions);
	}

	/**
	 *
	 * @param   stdClass   $options  Parameters to be passed to the database driver.
	 *
	 * @return  jUpgrade  A jupgrade object.
	 *
	 * @since  3.0.0
	 */
	static function getInstance($key = null, $extensions = false)
	{
		// Create our new jUpgrade connector based on the options given.
		try
		{
			$instance = new JUpgradeStep($key, $extensions);
		}
		catch (RuntimeException $e)
		{
			throw new RuntimeException(sprintf('Unable to load jUpgradeStep object: %s', $e->getMessage()));
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
	public function getParameters($json = true)
	{
		$return = array();

		foreach ($this as $k => $v)
		{
			if (property_exists ( $this , $k ))
			{
				if (!is_object($v)) {
					if ($v != "" || $k == 'total' || $k == 'start' || $k == 'stop') {
						// Perform url decoding so that any use of '+' as the encoding of the space character is correctly handled.
						$return[$k] = urldecode((string) $v);
					}
				}
			}
		}

		// Encoding to JSON
		if ($json == true) {
			$return = json_encode($return);
		}

		return $return;
	}

	/**
	 * Get the next step
	 *
	 * @return   step object
	 */
	public function getStep($name = false, $json = true) {

		// Check if step is loaded
		if (empty($this->name)) {
			return false;
		}

		JLoader::import('helpers.jupgradepro', JPATH_COMPONENT_ADMINISTRATOR);
		$this->params = jUpgradeProHelper::getParams();

		$limit = $this->params->get('cache_limit');

		// Getting total
		$object = jUpgrade::getInstance($this);
		$this->total = (int) $object->getTotal();

		// We must to fragment the steps
		if ($this->total > $limit) {

			if ($this->cache == 0 && $this->status == 0) {

				$this->cache = round( ($this->total-1) / $limit, 0, PHP_ROUND_HALF_DOWN);
				$this->stop = $limit - 1;
				$this->first = true;

			} else if ($this->cache == 1 && $this->status == 1) {

				$this->start = $this->cid;
				//$this->next = true;
				$this->cache = 0;
				$this->stop = $this->total - 1;

			} else if ($this->cache > 0) { 

				$this->start = $this->cid;
				$this->stop = ($this->start - 1) + $limit;
				$this->cache = $this->cache - 1;

				if ($this->stop > $this->total) {
					$this->next = true;
				}else{
					$this->middle = true;
				}
			}

			// Status == 1
			$this->status = 1;

		}else if ($this->total == 0) {

			$this->stop = -1;
			$this->first = true;
			$this->cache = 0;
			$this->status = 2;

		}else{

			//$this->next = true;
			$this->first = true;
			$this->cache = 0;
			$this->stop = $this->total - 1;
		}

		// If first step start = 1
		if ($this->first == true) {
			$this->start = 0;
		}

		// Mark if is the end of the step
		if ($this->name == $this->laststep && $this->cache == 1) {
			$this->end = true;
		}

		// updating the status flag
		$this->_updateStep();

		return $this->getParameters($json);
	}

	/**
	 * Getting the current step from database and put it into object properties
	 *
	 * @return   step object
	 */
	public function _load($key = null) {

		// Select the steps
		if (isset($key)) {
			$query = "SELECT * FROM {$this->_table} AS s WHERE s.name = '{$key}' ORDER BY s.id ASC LIMIT 1";
		}else{
			$query = "SELECT * FROM {$this->_table} AS s WHERE s.status != 2 ORDER BY s.id ASC LIMIT 1";
		}

		$this->_db->setQuery($query);
		$step = $this->_db->loadAssoc();

		// Check for query error.
		$error = $this->_db->getErrorMsg();
		if ($error) {
			return false;
		}

		// Check if step is an array
		if (!is_array($step)) {
			return false;
		}

		// Select last step
		$query = "SELECT name FROM {$this->_table} WHERE status = 0 ORDER BY id DESC LIMIT 1";
		$this->_db->setQuery($query);
		$step['laststep'] = $this->_db->loadResult();

		$this->setParameters($step);

		return true;
	}

	/**
	 * updateStep
	 *
	 * @return	none
	 * @since	2.5.2
	 */
	public function _updateStep() {

		// Initialize
		$cache = $cid = $total = $start = $stop = '';

		// Setting the statements
		$status = "status = {$this->status}";
		$cache = ", cache = {$this->cache}";

		if (!empty($this->cid)) {
			$cid = ", cid = {$this->cid}";
		}
		if (!empty($this->total)) {
			$total = ", total = {$this->total}";
		}
		if (!empty($this->start)) {
			$start = ", start = {$this->start}";
		}
		if (!empty($this->stop)) {
			$stop = ", stop = {$this->stop}";
		}

		// Updating the status flag
		$query = "UPDATE {$this->_table} SET {$status} {$cache} {$cid} {$total} {$start} {$stop}"
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
	 * 
	 *
	 * @return  boolean  True if the user and pass are authorized
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function _updateID($id)
	{
		$name = $this->_getStepName();

		$query = "UPDATE `{$this->_table}` SET `cid` = '{$id}' WHERE name = ".$this->_db->quote($name);
		$this->_db->setQuery( $query );

		return $this->_db->query();
	}

	/**
	 * Updating the steps table
	 *
	 * @return  boolean  True if the user and pass are authorized
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function _nextID($total = false)
	{
		$update_cid = $this->_getStepID() + 1;
		$this->_updateID($update_cid);
		echo jUpgradeProHelper::isCli() ? "•" : "";
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
		$this->_load();
		return $this->cid;
	}

	/**
	 * @return  string	The step name  
	 *
	 * @since   3.0
	 */
	public function _getStepName()
	{
		return $this->name;
	}
}
