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
	public $_driver = null;

	/**
	 * @var      
	 * @since  3.0
	 */
	public $_version = null;

	/**
	 * @var      
	 * @since  3.0
	 */
	public $_total = null;

	/**
	 * @var	array
	 * @since  3.0
	 */
	protected $_step = null;

	/**
	 * @var    array  List of extensions steps
	 * @since  12.1
	 */
	private $extensions_steps = array('extensions', 'ext_components', 'ext_modules', 'ext_plugins');

	/**
	 * @var bool Can drop
	 * @since	0.4.
	 */
	public $canDrop = false;

	function __construct(jUpgradeStep $step = null)
	{
		// Set the current step
		$this->_step = $step;

		jimport('legacy.component.helper');
		jimport('cms.version.version');
		JLoader::import('helpers.jupgradepro', JPATH_COMPONENT_ADMINISTRATOR);

		$this->params = jUpgradeProHelper::getParams();

		// Getting the params and Joomla version web and cli
		if (!jUpgradeProHelper::isCli()) {
			// Getting the J! version
			$version = new JVersion;
			$this->_version = $version->RELEASE;
		}else{
			$this->_version = $this->params->get('RELEASE');
		}

		// Creating dabatase instance for this installation
		$this->_db = JFactory::getDBO();

		// Getting the driver
		require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.driver.class.php';

		if ($this->_step instanceof jUpgradeStep) {
			$this->_step->table = $this->getTableName();
		}

		$conditions = array();
		if ($this->params->get('method') == 'database') {
			$conditions = $this->getConditionsHook();
		}

		$this->_driver = JUpgradeDriver::getInstance($step, $conditions);

		if (isset($this->_step->total) && !in_array($this->_step->name, $this->extensions_steps) ) {
			$this->_total = $step->total == false ? $this->_driver->getTotal() : $step->total;
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
	 *
	 * @param   stdClass   $options  Parameters to be passed to the database driver.
	 *
	 * @return  jUpgrade  A jupgrade object.
	 *
	 * @since  3.0.0
	 */
	static function getInstance(jUpgradeStep $step = null)
	{
		$class = '';

		if ($step == null) {
			return false;
		}

		// Require the file
		if (JFile::exists(JPATH_COMPONENT_ADMINISTRATOR.'/includes/core/'.$step->name.'.php')) {
			require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/core/'.$step->name.'.php';
		}else if (JFile::exists(JPATH_PLUGINS."/jupgradepro/jupgradepro_{$step->name}/extensions/{$step->name}.php")) {
			require_once JPATH_PLUGINS."/jupgradepro/jupgradepro_{$step->name}/extensions/{$step->name}.php";
		// Checks
		}else if (JFile::exists(JPATH_COMPONENT_ADMINISTRATOR."/includes/extensions/{$step->name}.php")) {
			require_once JPATH_COMPONENT_ADMINISTRATOR."/includes/extensions/{$step->name}.php";
		// Cli
		}else if (JFile::exists(JPATH_ROOT."/extensions/{$step->name}.php")) {
			require_once JPATH_ROOT."/extensions/{$step->name}.php";
		}else if (isset($step->element)) {
			if (JFile::exists(JPATH_PLUGINS."/jupgradepro/jupgradepro_{$step->name}/extensions/{$step->element}.php")) {
				require_once JPATH_PLUGINS."/jupgradepro/jupgradepro_{$step->name}/extensions/{$step->element}.php";
			}
		}

		// Getting the class name
		if (isset($step->class)) {
			$class = $step->class;
		}

		// If the class still doesn't exist we have nothing left to do but throw an exception.  We did our best.
		if (!class_exists($class))
		{
			$class = 'jUpgrade';
		}

		// Create our new jUpgrade connector based on the options given.
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
			throw new Exception($e->getMessage());
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
	protected function setDestinationData($rows = false)
	{
		$name = $this->_getStepName();
		$method = $this->params->get('method');

		// Get the source data.
		if ($rows === false) {
			$rows = $this->dataSwitch();
		}

		if ( $method == 'database' OR $method == 'database_all') {
			if (method_exists($this, 'databaseHook')) { 
				$rows = $this->databaseHook($rows);
			}
		}

		$dataHookFunc = 'dataHook_'.$name;
		if (method_exists($this, $dataHookFunc)) { 
			$rows = $this->$dataHookFunc($rows);
		}else{
			$rows = $this->dataHook($rows);
		}

		if ($rows !== false) {
			$this->insertData($rows);
		}

		// Update the step object
		$this->_step->_update();

		if ($this->getTotal() == $this->_step->cid) {
			$this->afterHook($rows);
		}
	}

	/*
	 * Fake method of dataHook if it not exists
	 *
	 * @return	void
	 * @since	3.0.0
	 * @throws	Exception
	 */
	public function dataHook($rows)
	{
		// Do customisation of the params field here for specific data.
		return $rows;	
	}

	/*
	 * Fake method after hooks
	 *
	 * @return	void
	 * @since	3.0.0
	 * @throws	Exception
	 */
	public function afterHook()
	{
		// Do customisation data here
	}

	/**
	 * dataSwitch
	 *
	 * @return	array	The requested data
	 * @since	3.0.0
	 * @throws	Exception
	 */
	protected function dataSwitch($name = null)
	{
		$method = $this->params->get('method');

		$rows = array();

		switch ($method) {
			case 'rest':
				$name = ($name == null) ? $this->_getStepName() : $name;
				if ( in_array($name, $this->extensions_steps) ) {
					$rows = $this->_driver->getSourceDataRest($name);
				}else{
					$rows = $this->_driver->getSourceDataRestIndividual($name);
				}
		    break;
			case 'database':
		    $rows = $this->_driver->getSourceDatabase();
		    break;
		}
//print_r($rows);
		return $rows;
	}

	/**
	 * Get total of the rows of the table
	 *
	 * @access	public
	 * @return	int	The total of rows
	 */
	public function getTotal()
	{
		return $this->_total;
	}

	/**
 	* Get the table structure
	*/
	public function getTableStructure() {

		$method = $this->params->get('method');
		$table = $this->getTableName();
		
		if ($method == 'database') {
			$result = $this->_driver->_db_old->getTableCreate($table);
			$result[$table] = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $result[$table]);
			$structure = "{$result[$table]} ;\n\n";

		}else if ($method == 'rest') {
			$table = str_replace('#__', '', $table);
			$structure = $this->_driver->requestRest("tablestructure", $table);
		}

		// Inserting the structure to new site
		$this->_db->setQuery($structure);
		$this->_db->query();

		return true;
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
		$table = $this->getDestinationTableName();

		if (is_array($rows)) {

			$total = count($rows);

			foreach ($rows as $row)
			{
				// Convert the array into an object.
				$row = (object) $row;

				if (!$this->_db->insertObject($table, $row)) {
					$this->_step->error = $this->_db->getErrorMsg();
				}

				$this->_nextID($total);
			}
		}else if (is_object($rows)) {

			if (!$this->_db->insertObject($table, $rows)) {
				$this->_step->error = $this->_db->getErrorMsg();
			}

		}
	
		return true;
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
		$cid = $this->_getStepID();
		$update_cid = $cid + 1;
		$this->_updateID($update_cid);
		echo jUpgradeProHelper::isCli() ? "•" : "";
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
		$table = "jupgrade_{$this->_step->type}";

		$query = "UPDATE `{$table}` SET `cid` = '{$id}' WHERE name = ".$this->_db->quote($name);
		$this->_db->setQuery( $query );

		return $this->_db->query();
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
		$this->_step->_update();
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

	/**
	 * @return  string	The destination table key name  
	 *
	 * @since   3.0
	 */
	public function getDestKeyName()
	{
		$table = $this->getDestinationTableName();

		$query = "SHOW KEYS FROM {$table} WHERE Key_name = 'PRIMARY'";
		$this->_db->setQuery( $query );
		$keys = $this->_db->loadObjectList();

		return !empty($keys) ? $keys[0]->Column_name : '';
	}

	/**
	 * @return  string	The table name  
	 *
	 * @since   3.0
	 */
	public function getTableName()
	{
		if (isset($this->source)) {
			return $this->source;
		}else if (isset($this->destination)) {
			return $this->destination;
		}else if (isset($this->_step->name)) {
			return '#__'.$this->_step->name;
		}
	}

	/**
	 * @return  string	The table name  
	 *
	 * @since   3.0
	 */
	public function getDestinationTableName()
	{
		if (isset($this->destination)) {
			return $this->destination;
		}else if (isset($this->source)) {
			return $this->source;
		}else{
			return '#__'.$this->_step->name;
		}
	}

	/**
	 * @return  bool	Check if the value exists in the table
	 *
	 * @since   3.0
	 */
	public function valueExists($row, $fields)
	{
		$table = $this->getTableName();
		$key = $this->getDestKeyName();	
		$value = $row->$key;

		$conditions = array();
		foreach ($fields as $field) {
			$conditions[] = "{$field} = {$row->$field}";
		}

		$where = count( $conditions ) ? 'WHERE ' . implode( ' AND ', $conditions ) : '';

		$query = "SELECT `{$key}` FROM {$table} {$where} LIMIT 1";
		$this->_db->setQuery( $query );
		$exists = $this->_db->loadResult();

		return empty($exists) ? false : true;
	}

	/**
	 * TODO: Replace this function: get the new id directly
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
	 * Internal function to get original database prefix
	 *
	 * @return	an original database prefix
	 * @since	0.5.3
	 * @throws	Exception
	 */
	public function getMapListValue($table = 'categories', $section = false, $custom = false)
	{
		// Getting the categories id's
		$query = "SELECT new"
		." FROM jupgrade_{$table}";

		if ($section !== false) {
			$query .= " WHERE section = '{$section}'";
		}

		if ($custom !== false) {
			if ($section !== false) {
				$query .= " AND {$custom}";
			}else{
				$query .= " WHERE {$custom}";
			}
		}

		$this->_db->setQuery($query);
		$data = $this->_db->loadResult();

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
			return false;
		}

		return $data;
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
		$temp	= new JRegistry($params);
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
	 */
	public function getParams()
	{
		return $this->params->toObject();
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
}
