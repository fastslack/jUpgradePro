<?php
/**
 * jUpgradePro
 *
 * @version		    $Id$
 * @package		    MatWare
 * @subpackage    com_jupgradepro
 * @author        Matias Aguirre <maguirre@matware.com.ar>
 * @link          http://www.matware.com.ar
 * @copyright		  Copyright 2004 - 2013 Matias Aguirre. All rights reserved.
 * @license		    GNU General Public License version 2 or later; see LICENSE.txt
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
	public $ready = true;
	
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
			$this->_step->table = $this->getSourceTable();
		}

		// Initialize the driver
		$this->_driver = JUpgradeDriver::getInstance($step);

		// Getting the total
		if (!empty($step->source)) {
			$this->_total = jUpgradeProHelper::getTotal($step);
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

		// Require the correct file
		jUpgradeProHelper::requireClass($step->name, $step->xmlpath);

		// Correct the 3rd party extensions class name
		if (isset($step->element)) {
			$step->class = empty($step->class) ? 'jUpgradeExtensions' : $step->class;
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
		$name = $this->_step->_getStepName();
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

/*
		// Calling the data modificator hook
		$structureHookFunc = 'structureHookFunc_'.$name;

		if (method_exists($this, $structureHookFunc)) { 
			$rows = $this->$structureHookFunc($rows);
		}else{
			$rows = $this->dataHook($rows);
		}
*/
		// Calling the data modificator hook
		$dataHookFunc = 'dataHook_'.$name;

		if (method_exists($this, $dataHookFunc)) { 
			$rows = $this->$dataHookFunc($rows);
		}else{
			$rows = $this->dataHook($rows);
		}

		if ($rows !== false) {
			$this->ready = $this->insertData($rows);
		}

		// Load the step object
		$this->_step->_load();

		if ($this->getTotal() == $this->_step->cid) {
			$this->ready = $this->afterHook($rows);
		}

		if ($this->_step->name == $this->_step->laststep && $this->_step->cache == 0 && $this->getTotal() == $this->_step->cid) {
			$this->ready = $this->afterAllStepsHook();
		}

		return $this->ready;
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
	 * Hook to do custom migration after all steps
	 *
	 * @return	boolean Ready
	 * @since	1.1.0
	 */
	protected function afterAllStepsHook()
	{
		// Restore the deleted menu's
		require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/core/menus.php';
		$return = jUpgradeMenu::insertDefaultMenus();

		return true;
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
				$name = ($name == null) ? $this->_step->_getStepName() : $name;
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
		$table = $this->getSourceTable();

		if ($method == 'database') {
			$result = $this->_driver->_db_old->getTableCreate($table);
			$structure = "{$result[$table]} ;\n\n";

			$structure = str_replace($this->_driver->_db_old->getPrefix(), "#__", $structure);

		}else if ($method == 'rest') {
			$structure = $this->_driver->requestRest("tablestructure", str_replace('#__', '', $table));
		}

		$structure = str_replace('CREATE TABLE', 'CREATE TABLE IF NOT EXISTS', $structure);

		// Replacing the table name from xml
		$replaced_table = $this->replaceTable($table);

		if ($replaced_table != $table) {
			$structure = str_replace($table, $replaced_table, $structure);
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
		$table = $this->getDestinationTable();

		// Replacing the table name if xml exists
		$table = $this->replaceTable($table);

		if (is_array($rows)) {

			$total = count($rows);

			foreach ($rows as $row)
			{
				// Convert the array into an object.
				$row = (object) $row;

				if (!$this->_db->insertObject($table, $row)) {
					$this->_step->error = $this->_db->getErrorMsg();
				}

				$this->_step->_nextID($total);
			}
		}else if (is_object($rows)) {

			if (!$this->_db->insertObject($table, $rows)) {
				$this->_step->error = $this->_db->getErrorMsg();
			}

		}
	
		return !empty($this->_step->error) ? false : true;
	}

	/**
	 * Replace table name
	 *
	 * @return	string The replaced table
	 * @since 3.0.3
	 * @throws	Exception
	 */
	protected function replaceTable($table, $structure = null) {

		$replaced_table = $table;

		// Replace table name from xml
		$replace = explode("|", $this->_step->replace);

		if (count($replace) > 1) {
			$replaced_table = str_replace($replace[0], $replace[1], $table);
		}

		return $replaced_table;
	}

	/**
	 * @return  string	The destination table key name  
	 *
	 * @since   3.0
	 */
	public function getDestKeyName()
	{
		$table = $this->getDestinationTable();

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
	public function getSourceTable()
	{
		return '#__'.$this->_step->source;
	}

	/**
	 * @return  string	The table name  
	 *
	 * @since   3.0
	 */
	public function getDestinationTable()
	{
		return '#__'.$this->_step->destination;
	}

	/**
	 * @return  bool	Check if the value exists in the table
	 *
	 * @since   3.0
	 */
	public function valueExists($row, $fields)
	{
		$table = $this->getSourceTable();
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
	public static function getConditionsHook()
	{
		$conditions = array();		
		$conditions['where'] = array();
		// Do customisation of the params field here for specific data.
		return $conditions;	
	}
}
