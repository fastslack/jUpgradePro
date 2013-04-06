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
	public $replace = '';
	public $xmlpath = '';
	public $element = null;
	public $conditions = null;

	public $tbl_key = '';
	public $source = '';
	public $destination = '';
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

	public $debug = '';
	public $error = '';
	
	/**
	 * @var      
	 * @since  3.0
	 */
	protected $_db = null;

	function __construct($name = null, $extensions = false)
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
		$this->_load($name);
	}

	/**
	 *
	 * @param   stdClass   $options  Parameters to be passed to the database driver.
	 *
	 * @return  jUpgrade  A jupgrade object.
	 *
	 * @since  3.0.0
	 */
	static function getInstance($name = null, $extensions = false)
	{
		// Create our new jUpgrade connector based on the options given.
		try
		{
			$instance = new JUpgradeStep($name, $extensions);
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

		// Getting the total
		if (isset($this->source)) {
			$this->total = jUpgradeProHelper::getTotal($this);
		}

		// We must to fragment the steps
		if ($this->total > $limit) {

			if ($this->cache == 0 && $this->status == 0) {

				$this->cache = round( ($this->total-1) / $limit, 0, PHP_ROUND_HALF_DOWN);
				$this->stop = $limit - 1;
				$this->first = true;

			} else if ($this->cache == 1 && $this->status == 1) {

				$this->start = $this->cid;
				$this->cache = 0;
				$this->stop = $this->total - 1;

			} else if ($this->cache > 0) { 

				$this->start = $this->cid;
				$this->stop = ($this->start - 1) + $limit;
				$this->cache = $this->cache - 1;

				if ($this->stop > $this->total) {
					$this->stop = $this->total - 1;
					$this->next = true;
				}else{
					$this->middle = true;
				}
			}

			// Status == 1
			$this->status = 1;

		}else if ($this->total == 0) {

			$this->stop = -1;
			$this->next = 1;
			$this->first = true;
			if ($this->name == $this->laststep) {
				$this->end = true;
			}
			$this->cache = 0;
			$this->status = 2;

		}else{

			//$this->next = true;
			$this->first = 1;
			$this->cache = 0;
			$this->stop = $this->total - 1;
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
	public function _load($name = null) {

		// Getting the data
		$query = $this->_db->getQuery(true);
		$query->select('e.*');
		$query->from($this->_table.' AS e');

		if ($this->_table == 'jupgrade_extensions_tables') {
			$query->leftJoin('`jupgrade_extensions` AS ext ON ext.name = e.element');
			$query->select('ext.xmlpath');
		}

		if (isset($name)) {
			$query->where("e.name = '{$name}'");
		}else{
			$query->where("e.status != 2");
		}

		$query->order('e.id ASC');
		$query->limit(1);

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

		// Reset the $query object
		$query->clear();

		// Select last step
		$query->select('name');
		$query->from($this->_table);
		$query->where("status = 0");
		if ($this->_table == 'jupgrade_extensions_tables') {
			$query->where("element = '{$step['element']}'");
		}
		$query->order('id DESC');
		$query->limit(1);

		$this->_db->setQuery($query);
		$step['laststep'] = $this->_db->loadResult();

		// Set the parameters
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

		$query = $this->_db->getQuery(true);
		$query->update($this->_table);

		$columns = array('status', 'cache', 'cid', 'total', 'start', 'stop', 'first');

		foreach ($columns as $column) {
			if (!empty($this->$column)) {
				$query->set("{$column} = {$this->$column}");
			}
		}

		$query->where("name = {$this->_db->quote($this->name)}");
		// Execute the query
		$this->_db->setQuery($query)->execute();

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

		$query = $this->_db->getQuery(true);
		$query->update($this->_table);
		$query->set("`cid` = '{$id}'");
		$query->where("name = {$this->_db->quote($name)}");
		// Execute the query
		return $this->_db->setQuery($query)->execute();
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
