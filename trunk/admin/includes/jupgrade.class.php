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
 * jUpgrade utility class for migrations
 *
 * @package		MatWare
 * @subpackage	com_jupgrade
 */
class jUpgrade
{	
	protected $ready = true;
	protected $output = '';
	protected $rest_type = null;

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
	 * @var      
	 * @since  3.0
	 */
	public $_db_old = null;

	/**
	 * @var	array
	 * @since  3.0
	 */
	private $_step = array();

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
		'extension',
		'laststep',
		'state',
		'xml'
	);

	/**
	 * @var bool Can drop
	 * @since	0.4.
	 */
	public $canDrop = false;

	function __construct($step = null)
	{
		// Set the step params	
		$this->setParameters((array) $step);
			
		$this->checkTimeout();

		// Getting the parameters
		$this->params	= JComponentHelper::getParams('com_jupgradepro');

		// Creating dabatase instance for this installation
		$this->_db = JFactory::getDBO();

		// Creating old dabatase instance
		if ($this->params->get('method') == 'database') {

			$db_config['driver'] = $this->params->get('driver');
			$db_config['host'] = $this->params->get('hostname');
			$db_config['user'] = $this->params->get('username');
			$db_config['password'] = $this->params->get('password');
			$db_config['database'] = $this->params->get('database');
			$db_config['prefix'] = $this->params->get('prefix');

			$this->_db_old = JDatabase::getInstance($db_config);
		}

		// Set timelimit to 0
		if(!@ini_get('safe_mode')) {
			if (!empty($this->params->timelimit)) {
				set_time_limit(0);
			}
		}

		// Make sure we can see all errors.
		if (!empty($this->params->error_reporting)) {
			error_reporting(E_ALL);
			@ini_set('display_errors', 1);
		}

		// MySQL grants check
		$query = "SHOW GRANTS FOR CURRENT_USER";
		$this->_db->setQuery( $query );
		$list = $this->_db->loadRowList();
		$grant = isset($list[1][0]) ? $list[1][0] : $list[0][0];
		$grant = empty($list[1][0]) ? $list[0][0] : $list[1][0];

		if (strpos($grant, 'DROP') == true || strpos($grant, 'ALL') == true) {
			$this->canDrop = true;
		}
	}

	/**
	 * Method to set the OAuth message parameters.  This will only set valid OAuth message parameters.  If non-valid
	 * parameters are in the input array they will be ignored.
	 *
	 * @param   array  $parameters  The OAuth message parameters to set.
	 *
	 * @return  void
	 *
	 * @since   12.1
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
	 * The public entry point for the class.
	 *
	 * @return	boolean
	 * @since	0.4.
	 */
	public function upgrade()
	{
		try
		{
			$this->setDestinationData();
		}
		catch (Exception $e)
		{
			echo JError::raiseError(500, $e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Sets the data in the destination database.
	 *
	 * @return	void
	 * @since	0.4.
	 * @throws	Exception
	 */
	protected function setDestinationData($rows = null)
	{	
		// Get the source data.
		if ($rows === null) {
			$rows = $this->loadData();
		}

		$this->insertData($rows);
	}

	/**
	 * loadData
	 *
	 * @return	void
	 * @since	3.0.0
	 * @throws	Exception
	 */
	protected function loadData($type = null)
	{
		$method = $this->params->get('method');

		$rows = array();

		// Get the source data.
		if ($method == 'rest') {
			$rows = $this->getSourceDataRest($type);
		} else if ($method == 'rest_individual') {
			$rows[] = (object) $this->getSourceDataRestIndividual($type);
		} else if ($method == 'database') {
			$rows = $this->getSourceDatabase();
		}

		return $rows;
	}

	/**
	 * insertData
	 *
	 * @return	void
	 * @since	3.0.0
	 * @throws	Exception
	 */
	protected function insertData($rows)
	{	
		$table = empty($this->destination) ? $this->source : $this->destination;
	
		if (is_array($rows)) {
			foreach ($rows as $row)
			{
				// Convert the array into an object.
				$row = (object) $row;

				if (!$this->_db->insertObject($table, $row)) {
					throw new Exception($this->_db->getErrorMsg());
				}
			}
		}else if (is_object($rows)) {
		
			if (!$this->_db->insertObject($table, $rows)) {
				throw new Exception($this->_db->getErrorMsg());
			}		
	
		}
	
		return true;
	}

	/**
	 * Get total of the rows of the table
	 *
	 * @access	public
	 * @return	int	The total of rows
	 */
	public function getSourceDatabase( )
	{
		$k = $this->_tbl_key;

		$oid = $this->_requestID();

		if ($oid === null) {
			return false;
		}

		// Get the conditions
		$conditions = $this->getConditionsHook();

		// Get `AS` mysql statement
		$where_as = isset($conditions['as']) ? $conditions['as'].'.' : '';
		
		// Add oid condition		
		$oid_condition = "{$where_as}{$this->getKeyName()} = {$oid}";
		array_push($conditions['where'], $oid_condition);

		$where = count( $conditions['where'] ) ? 'WHERE ' . implode( ' AND ', $conditions['where'] ) : '';		
		$select = isset($conditions['select']) ? $conditions['select'] : '*';
		$as = isset($conditions['as']) ? 'AS '.$conditions['as'] : '';

		$join = '';
		if (isset($conditions['join'])) {
			$join = count( $conditions['join'] ) ? implode( ' ', $conditions['join'] ) : '';
		}

		$order = isset($conditions['order']) ? $conditions['order'] : "{$this->getKeyName()} ASC";

		// Get the row
		$query = "SELECT {$select} FROM {$this->getTableName()} {$as} {$join} {$where} LIMIT 1";
		$this->_db_old->setQuery( $query );
		//echo $query;

		$this->_db_old->loadAssocList();

		if ($result = $this->_db_old->loadAssocList( )) {
			$this->_updateID($oid);
			return $result;
		}
		else
		{
			throw new Exception( $this->_db_old->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Get next id
	 *
	 * @access	public
	 * @return	int	The total of rows
	 */
	public function _requestID( $moreconditions = null)
	{

		function processWhere($conditions) {
			$where = count( $conditions['where'] ) ? 'WHERE ' . implode( ' AND ', $conditions['where'] ) : '';
			return $where;
		}

		$query = 'SELECT `cid` FROM jupgrade_steps'
		. ' WHERE name = '.$this->_db->quote($this->_step['name']);
		$this->_db->setQuery( $query );
		$stepid = (int) $this->_db->loadResult();

		$conditions = $this->getConditionsHook();
		
		if ($moreconditions != null && is_array($conditions)) {
			array_push($conditions['where'], $moreconditions);
		}

		$as = isset($conditions['as']) ? 'AS '.$conditions['as'] : '';
		$order = isset($conditions['order']) ? $conditions['order'] : 'ASC';

		if (strpos($order,'ASC') !== false) {
			$conditions['where'][] = "{$this->getKeyName()} > {$stepid}";
			$query = "SELECT MIN({$this->getKeyName()}) FROM {$this->getTableName()} {$as} ".processWhere($conditions)." LIMIT 1";
		}else if (strpos($order,'DESC') !== false) {
			if ($stepid == 0) {
				$query = "SELECT MAX({$this->getKeyName()}) FROM {$this->getTableName()} {$as} ".processWhere($conditions)." LIMIT 1";
			}else{
				$conditions['where'][] = "{$this->getKeyName()} < {$stepid}";
				$query = "SELECT MAX({$this->getKeyName()}) FROM {$this->getTableName()} {$as} ".processWhere($conditions)." LIMIT 1";
			}
		}

		$this->_db_old->setQuery( $query );
		$id = $this->_db_old->loadResult();

		if ($id) {
			return (int)$id;
		}
		else
		{
			throw new Exception( $this->_db_old->getErrorMsg() );
			return false;
		}
	}

	/**
	 * 
	 * @return  boolean  
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function getKeyName()
	{
		return $this->_tbl_key;
	}

	/**
	 * 
	 * @return  boolean  
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function getTableName()
	{
		return $this->source;
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
		$query = "UPDATE `jupgrade_steps` SET `cid` = '{$id}' WHERE name = ".$this->_db->quote($this->_step['name']);
		$this->_db->setQuery( $query );

		return $this->_db->query();
	}

	/**
	 * Get total of the rows of the table
	 *
	 * @access	public
	 * @return	int	The total of rows
	 */
	public function getSourceDatabaseTotal()
	{
		$conditions = $this->getConditionsHook();

		$where = count( $conditions['where'] ) ? 'WHERE ' . implode( ' AND ', $conditions['where'] ) : '';

		$as = isset($conditions['as']) ? 'AS '.$conditions['as'] : '';

		/// Get Total
		$query = "SELECT COUNT(*) FROM {$this->source} {$as} {$where}";
		$this->_db_old->setQuery( $query );
		$total = $this->_db_old->loadResult();

		return (int)$total;
	}


	/*
	 *
	 * @return	void
	 * @since	3.0.0
	 * @throws	Exception
	 */
	public function getConditionsHook()
	{
		$conditions = array();		
		$conditions['where'] = array();
		// Do customisation of the params field here for specific data.
		return $conditions;	
	}

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 * @since	0.4.4
	 * @throws	Exception
	 */
	public function &getRestData()
	{
		$data = array();
	
		// Setting the headers for REST
		$str = $this->params->get('rest_username').":".$this->params->get('rest_password');
		$data['Authorization'] = base64_encode($str);
		$data['AUTH_USER'] = $this->params->get('rest_username');
    $data['AUTH_PW'] = $this->params->get('rest_password');

		return $data;
	}

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 * @since	0.4.4
	 * @throws	Exception
	 */
	protected function &getSourceDataRest($type = null)
	{
		jimport('joomla.http.http');

		$rows = array();
		$data = array();
	
		// JHttp instance
		$http = new JHttp();
		
		$data = $this->getRestData();

		// Cleanup
		$data['task'] = "cleanup";
		$data['type'] = ($type == null) ? $this->_step['name'] : $type;
		$cleanup = $http->get($this->params->get('rest_hostname'), $data);
		
		// Getting the total
		$data['task'] = "total";
		$total = $http->get($this->params->get('rest_hostname'), $data);
		$total = (int) $total->body;

		// Getting the rows
		$data['task'] = "row";

		for ($i=1;$i<=$total;$i++) {		
			$response = $http->get($this->params->get('rest_hostname'), $data);
			if ($response->body != '') {
				$rows[$i] = json_decode($response->body, true);
			}
		}

		return $rows;
	}

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 * @since	0.4.4
	 * @throws	Exception
	 */
	protected function &getSourceDataRestIndividual($type = null)
	{
		jimport('joomla.http.http');

		//$row = array();
		$data = array();
	
		// JHttp instance
		$http = new JHttp();
		
		$data = $this->getRestData();
	
		// Getting the rows
		$data['type'] = ($type == null) ? $this->_step['name'] : $type;
		$data['task'] = "row";

		$response = $http->get($this->params->get('rest_hostname'), $data);
		if ($response->body != '') {
			$row = json_decode($response->body, true);
		}

		return $row;
	}

	protected function getLastId($type)
	{
		$method = $this->params->get('method');
	
		// Get the source data.
		if ($method == 'rest' || $method == 'rest_individual') {

			jimport('joomla.http.http');
	
			// JHttp instance
			$http = new JHttp();		
			$data = $this->getRestData();

			// Getting the total
			$data['task'] = "lastid";
			$data['type'] = $type;
			$lastid = $http->get($this->params->get('rest_hostname'), $data);
			$lastid = (int) $lastid->body;

		} else if ($method == 'database') {
			//$rows = $this->getSourceData();
		}

		return $lastid;
	}

	/**
	 * populateDatabase
	 */
	function populateDatabase(& $db, $sqlfile, & $errors, $nexttask='mainconfig')
	{
		if( !($buffer = file_get_contents($sqlfile)) )
		{
			return -1;
		}

		$queries = $db->splitSql($buffer);

		foreach ($queries as $query)
		{
			$query = trim($query);
			if ($query != '' && $query {0} != '#')
			{
				$db->setQuery($query);
				$db->query() or die($db->getErrorMsg());
			}
		}

		return true;
	}

	/**
	 * Cleanup the data in the destination database.
	 *
	 * @return	void
	 * @since	0.5.1
	 * @throws	Exception
	 */
	protected function cleanDestinationData($table = false)
	{
		// Get the table
		if ($table == false) {
			$table	= empty($this->destination) ? $this->source : $this->destination;
		}

		if ($this->canDrop) {
			$query = "TRUNCATE TABLE {$table}";
			$this->_db->setQuery($query);
			$this->_db->query();
		} else {
			$query = "DELETE FROM {$table}";
			$this->_db->setQuery($query);
			$this->_db->query();
		}

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

	}

	/**
	 * Save internal state.
	 *
	 * @return	boolean
	 * @since	1.1.0
	 */
	public function saveState()
	{
		// Cannot save state if step is not defined
		if (!$this->name) return false;

		$state = json_encode($this->state);
		$query = "UPDATE jupgrade_steps SET state = {$this->_db->quote($state)} WHERE name = {$this->_db->quote($this->name)}";
		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		return !$error;
	}

	/**
	 * Check if this migration has been completed.
	 *
	 * @return	boolean
	 * @since	1.1.0
	 */
	public function isReady()
	{
		return $this->ready;
	}

	/**
	 * Function to output text back to user
	 *
	 * @return	string Previous output
	 * @since	1.1.0
	 */
	public function output($text='')
	{
		$output = empty($this->output) ? $this->name : $this->output;
		$this->output = $text;
		return $output;
	}

	/**
	 * Internal function to get original database prefix
	 *
	 * @return	an original database prefix
	 * @since	0.5.3
	 * @throws	Exception
	 */
	public function getMapList($table = 'categories', $section = false, $custom = false)
	{
		// Getting the categories id's
		$query = "SELECT *"
		." FROM jupgrade_{$table}";

		if ($section !== false) {
			$query .= " WHERE section = '{$section}'";
		}

		if ($custom !== false) {
			$query .= " WHERE {$custom}";
		}

		$this->_db->setQuery($query);
		$data = $this->_db->loadObjectList('old');

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
			return false;
		}

		return $data;
	}

	/**
	 * Get the mapping of the old usergroups to the new usergroup id's.
	 *
	 * @return	array	An array with keys of the old id's and values being the new id's.
	 * @since	1.1.0
	 */
	protected function getUsergroupIdMap()
	{
		return $this->usergroup_map;
	}

	/**
	 * Map old user group from Joomla 1.5 to new installation.
	 *
	 * @return	int	New user group
	 * @since	1.2.2
	 */
	protected function mapUserGroup($id) {
		return isset($this->usergroup_map[$id]) ? $this->usergroup_map[$id] : $id;
	}

	/**
	 * Converts the params fields into a JSON string.
	 *
	 * @param	string	$params	The source text definition for the parameter field.
	 *
	 * @return	string	A JSON encoded string representation of the parameters.
	 * @since	0.4.
	 * @throws	Exception from the convertParamsHook.
	 */
	protected function convertParams($params)
	{
		$temp	= new JParameter($params);
		$object	= $temp->toObject();

		// Fire the hook in case this parameter field needs modification.
		$this->convertParamsHook($object);

		return json_encode($object);
	}

	/**
	 * A hook to be able to modify params prior as they are converted to JSON.
	 *
	 * @param	object	$object	A reference to the parameters as an object.
	 *
	 * @return	void
	 * @since	0.4.
	 * @throws	Exception
	 */
	protected function convertParamsHook(&$object)
	{
		// Do customisation of the params field here for specific data.
	}

	/**
	 * Internal function to get the component settings
	 *
	 * @return	an object with global settings
	 * @since	0.5.7
	 * @throws	Exception
	 */
	public function getParams()
	{
		return $this->params->toObject();
	}

	/**
	 * Internal function to check if 5 seconds has been passed
	 *
	 * @return	bool	true / false
	 * @since	1.1.0
	 */
	protected function checkTimeout($stop = false) {
		static $start = null;
		if ($stop) $start = 0;
		$time = microtime (true);
		if ($start === null) {
			$start = $time;
			return false;
		}
		if ($time - $start < 5)
			return false;

		return true;
	}
}
