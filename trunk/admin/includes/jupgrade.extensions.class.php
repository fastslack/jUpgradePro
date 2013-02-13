<?php
/**
 * jUpgrade
 *
 * @version		$Id$
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @copyright	Copyright 2004 - 2013 Matias Aguir3e. All rights reserved.
 * @license		GNU General Public License version 2 or later.
 * @author		Matias Aguirre <maguirre@matware.com.ar>
 * @link		http://www.matware.com.ar
 */

/**
 * Upgrade class for 3rd party extensions
 *
 * This class search for extensions to be migrated
 *
 * @since	0.4.5
 */
class jUpgradeExtensions extends jUpgrade
{

	function __construct($step = null)
	{
		parent::__construct($step);

		$name = $this->_getStepName();

		// Find xml file from jUpgrade
		$default_xmlfile = JPATH_PLUGINS."/jupgradepro/jupgradepro_{$name}/extensions/{$name}.xml";

		if (file_exists($default_xmlfile)) {
			$this->xml = simplexml_load_file($default_xmlfile);
		}
	}

	/**
	 * The public entry point for the class.
	 *
	 * @return	boolean
	 * @since	1.1.0
	 */
	public function upgrade()
	{
		try
		{
			// Detect
			if (!$this->detectExtension())
			{
				return false;
			}

			// Migrate
			//$this->ready = $this->migrateExtensionFolders();

			if ($this->ready)
			{
				//$this->ready = $this->migrateExtensionTables();
			}
			if ($this->ready)
			{
				$this->ready = $this->fixExtensionMenus();
			}
			if ($this->ready)
			{
				$this->ready = $this->migrateExtensionCustom();
			}

			// Store state
			//$this->saveState();
		}
		catch (Exception $e)
		{
			echo JError::raiseError(500, $e->getMessage());

			return false;
		}

		return true;
	}

	/**
	 * Check if extension migration is supported.
	 *
	 * @return	boolean
	 * @since	1.1.0
	 */
	protected function detectExtension()
	{
		return true;
	}
	
	/**
	 * Get extension version from the Joomla! 1.5 site
	 *
	 * @param	string Relative path to manifest file from Joomla! 1.5 JPATH_ROOT
	 * @return	string Version string
	 * @since	2.5.0
	 */
	protected function getExtensionVersion($manifest)
	{
		if (!file_exists(JPATH_ROOT.'/'.$manifest)) return null;

		$xml = simplexml_load_file(JPATH_ROOT.'/'.$manifest);
		return (string) $xml->version[0];
	}

	/**
	 * Migrate the database tables.
	 *
	 * @return	boolean
	 * @since	1.1.0
	 *
	protected function migrateExtensionTables()
	{
		if (!isset($this->state->tables))
		{
			$this->state->tables = $this->getCopyTables();
		}

		while(($value = array_shift($this->state->tables)) !== null) {

			$copyTableFunc = 'copyTable_'.$value;
			if (method_exists($this, $copyTableFunc)) {

				// Use function called like copyTable_kunena_categories
				$ready = $this->$copyTableFunc($value);

			} else if (strpos($value, '%') !== false) {

				$table = $this->db_old->getPrefix().$value;

				$query = "SHOW TABLES LIKE '{$table}'";
				$this->db_old->setQuery($query);
				$tables = $this->db_old->loadRowList();

				for ($i=0;$i<count($tables);$i++) {
					// Use default migration function
					$table = $tables[$i][0];
					$from = preg_replace ('/jos_/', '#__', $table);
					$this->copyTable($from);
					$ready = true;
				}

			} else {
				// Use default migration function
				$table = "#__$value";
				$this->copyTable($table);
				$ready = true;
			}
			// If table hasn't been fully copied, we need to push it back to stack
			if (!$ready) {
				array_unshift($this->state->tables, $value);
			}

			//	BUG: This break the loop, maybe is unused with new migrate table method
			//if ($this->checkTimeout()) {
			//	break;
			//}			
		}
		return empty($this->state->tables);
	}*/


	/**
	 * Copy table to old site to new site
	 *
	 * @return	boolean
	 * @since 1.1.0
	 * @throws	Exception
	 */
	protected function copyTable($from, $to=null) {
		// Check if table exists
		if (!$to) $to = $from;
		$from = preg_replace ('/#__/', $this->_db_old->getPrefix(), $from);
		$to = preg_replace ('/#__/', $this->_db->getPrefix(), $to);

		// Folder to save the sql files
		$folder = JPATH_COMPONENT_ADMINISTRATOR."/sql/updates";

		// Running the export table
		$export = $this->getExportTable($this->_db_old, $from, $folder);
	}

 	/**
	 * Return the exportable sql structure and data
	 *
	 * @param array or string $tables The tables name
	 * @return	string Return the structure as string
	 * @since 2.5.2
	 *
	protected function getExportTable(&$db, $tables, $folder, $addData = true) {

		$params = $this->getParams();

		// Sanitize input to an array and iterate over the list.
		settype($tables, 'array');
		foreach ($tables as $table)
		{
			$exists = $this->tableExists($db, $table); 

			if ($exists) {
				// Adding table structure
				$sql  = $this->getTableStructure($db, $table);

				// Getting the values
				$values = $this->getValues($db, $table);

				// Count the values
				$valCount = count($values);

				// Check if addData is enabled
				if ($addData && $valCount > 0) {

					// If valCount is > limit, split the sql files
					if ($valCount > $params->limit) {

						$offset = 0;

						while ($valCount >= 0) {

							if ($valCount == 0) {
								break;
							}

							if ($offset == 0) {
								$sql .= $this->getTableData($db, $table, $offset, $params->limit);
							}else{
								$sql = $this->getTableData($db, $table, $offset, $params->limit);
							}

							$filename = "{$table}-{$offset}";

							$this->writeSqlStatementToFile($table, $folder, $sql, $filename);
							$this->migrateTable($this->_db, $folder, $filename);

							$valCount = $valCount - $params->limit;
							$valCount = $valCount <= 0 ? $valCount = 0 : $valCount;

							$offset = $offset + $params->limit;
						}

					}else{
						$sql .= $this->getTableData($db, $table);

						$this->writeSqlStatementToFile($table, $folder, $sql);
						$this->migrateTable($this->_db, $folder, $table);

					}
				}else{

					$this->writeSqlStatementToFile($table, $folder, $sql);
					$this->migrateTable($this->_db, $folder, $table);
				}

			}
		}

		return true;
	}

 	/**
	 * Writes to file the $table's data
	 *
	 * @param string $table The table name
	 * @return	string Return the structure as string
	 * @since 2.5.2
	 *
	protected function getTableData(&$db, $table, $offset = 0, $limit = 0) {
		// Header
		$data  = "-- \n";
		$data .= "-- Dumping data for table `$table`\n";
		$data .= "-- \n\n";

		// Getting the values
		$values = $this->getValues($db, $table, $offset, $limit);

		// Count the values
		$valCount = count($values);

		if ($valCount == 0) {
			return null;
		}

		$insertStatement = $this->getInsertStatement($db, $table);
		$valuesStatement = $this->getValuesStatement($db, $table, $offset, $limit);

		$data .= $insertStatement.$valuesStatement;

		return $data;
	}

 	/**
	 * Generating the values statement
	 *
	 * @param JDatabase $db The database instance
	 * @param string $table The table name
	 * @return	string Return the structure as string
	 * @since 2.5.2
	 *
	protected function getValuesStatement(&$db, $table, $offset = 0, $limit = 0) {

		// Init variable
		$valuesStatement = "";

		// Getting the values
		$values = $this->getValues($db, $table, $offset, $limit);

		// Count the values
		$valCount = count($values);

		// Getting the columns
		$columns = $db->getTableColumns($table);
		// Getting the last key
		end($columns);        
		$endValue = key($columns);

		// Writting the values statement
		for ($i=0;$i<$valCount;$i++) {
			$value = $values[$i];

			reset($columns);

			$valuesStatement .= "( ";

			while ($val = current($columns)) {
			  $key = key($columns);

				if (is_numeric($value[$key])) {
					$valuesStatement .= "{$value[$key]}";
				}else if ($value[$key] == '') {
					$valuesStatement .= "''";
				}else{
					$valuesStatement .= "'".$db->getEscaped($value[$key])."'";
				}

				if ($key != $endValue) {
					$valuesStatement .= ", ";
				}else{
					$valuesStatement .= ")";
					if ($i != $valCount-1) {
						$valuesStatement .= ",";
					}
					$valuesStatement .= "\n";
				}

				next($columns);
			}
		}

		return $valuesStatement;
	}

 	/**
	 * Generating the insert statement
	 *
	 * @param JDatabase $db The database instance
	 * @param string $table The table name
	 * @return	string Return the structure as string
	 * @since 2.5.2
	 *
	protected function getInsertStatement(&$db, $table) {

		$insertStatement = "INSERT INTO `$table` (";

		$query = "SHOW FIELDS FROM {$table}";
		$db->setQuery($query);
		$fields = $db->loadAssocList();

		$count = count($fields);

		for ($i=0;$i<$count;$i++) {
			$field = $fields[$i];

			$insertStatement .= '`'.$field['Field'].'`';

			if ($i != $count-1) {
				$insertStatement .= ', ';
			}else{
				$insertStatement .= ') ';
			}
		}

		$insertStatement .= 'VALUES ';

		return $insertStatement;
	}

 	/**
	 * Getting the values of the table
	 *
	 * @param JDatabase $db The database instance
	 * @param string $table The table name
	 * @return	object Return the values of the table
	 * @since 2.5.2
	 *
	protected function getValues(&$db, $table, $offset = 0, $limit = 0) {

		// Getting the values
		$query = "SELECT * FROM {$table}";
		$db->setQuery($query, $offset, $limit);
		$values = $db->loadAssocList();

		return $values;
	}

 	/**
	 * Getting the values of the table
	 *
	 * @param JDatabase $db The database instance
	 * @param string $table The table name
	 * @return	object Return the values of the table
	 * @since 2.5.2
	 *
	protected function writeSqlStatementToFile($table, $folder, $sql, $filename = null) {

		// Setting the file name
		if ($filename != null) {
			$filename = $folder.DS.$filename.".sql";
		}else{
			$filename = $folder.DS.$table.".sql";
		}

		// Writing the structure to file
		if (!JFile::write($filename, $sql)) {
			return false;
		}

		return true;
	}

 	/**
	 * 
	 *
	 * @param 
	 * @param string $table The table name
	 * @return	object Return the values of the table
	 * @since 2.5.2
	 *
	protected function migrateTable(&$db, $folder, $filename) {

		$sqlfile = $folder.DS.$filename.".sql";

		// Import the sql file
		if ($this->populateDatabase($db, $sqlfile, $errors) > 0 ) {
			return false;
		}

		return true;
	}*/

	/**
	 * Migrate the folders.
	 *
	 * @return	boolean
	 * @since	1.1.0
	 */
	protected function migrateExtensionFolders()
	{
		$params = $this->getParams();
	
		if (!isset($this->state->folders))
		{
			$this->state->folders = new stdClass();
			$this->state->folders = $this->getCopyFolders();
		}
		while(($value = array_shift($this->state->folders)) !== null) {
			//$this->output("{$this->name} {$value}");
			$src = $params->path.DS.$value;
			$dest = JPATH_SITE.DS.$value;
			$copyFolderFunc = 'copyFolder_'.preg_replace('/[^\w\d]/', '_', $value);
			if (method_exists($this, $copyFolderFunc)) {
				// Use function called like copyFolder_media_kunena (for media/kunena)
				$ready = $this->$copyTableFunc($value);
				if (!$ready) {
					array_unshift($this->state->folders, $value);
				}
			} else {
				if (JFolder::exists($src) && !JFolder::exists($dest) ) {
					JFolder::copy($src, $dest);
				}
			}
			if ($this->checkTimeout()) {
				break;
			}
		}
		return empty($this->state->folders);
	}

	/**
	 * Fix extensions menu
	 *
	 * @return	boolean Ready
	 * @since	1.1.0
	 */
	protected function fixExtensionMenus()
	{
		// Get component object
		$component = JTable::getInstance ( 'extension', 'JTable', array('dbo'=>$this->_db) );
		$component->load(array('type'=>'component', 'element'=>$this->_getStepName()));

		// First fix all broken menu items
		$query = "UPDATE #__menu SET component_id={$this->_db->quote($component->extension_id)} WHERE type = 'component' AND link LIKE '%option={$this->_getStepName()}%'";
		$this->_db->setQuery ( $query );
		$this->_db->query ();

		return true;
	}

	/**
	 * Migrate custom information.
	 *
	 * @return	boolean Ready
	 * @since	1.1.0
	 */
	protected function migrateExtensionCustom()
	{
		return true;
	}

	/**
	 * A hook to be able to modify params prior as they are converted to JSON.
	 *
	 * @param	object	$object	A reference to the parameters as an object.
	 *
	 * @return	void
	 * @since	1.1.0
	 * @throws	Exception
	 */
	protected function migrateExtensionDataHook()
	{
		// Do customisation of the params field here for specific data.
	}

	/**
	 * Get update site information
	 *
	 * @return	array	Update site information or null
	 * @since	1.1.0
	 */
	protected function getUpdateSite() {
		if (empty($this->xml->updateservers->server[0])) {
			return null;
		}
		$server = $this->xml->updateservers->server[0];
		if (empty($server['url'])) {
			return null;
		}
		return array(
			'type'=> ($server['type'] ? $server['type'] : 'extension'),
			'priority'=> ($server['priority'] ? $server['priority'] : 1),
			'name'=> ($server['name'] ? $server['name'] : $this->name),
			'url'=> $server['url']
		);
	}

	/**
	 * Get folders to be migrated.
	 *
	 * @return	array	List of tables without prefix
	 * @since	1.1.0
	 */
	protected function getCopyFolders() {
		$folders = !empty($this->xml->folders->folder) ? $this->xml->folders->folder : array();
		$results = array();
		foreach ($folders as $folder) {
			$results[] = (string) $folder;
		}
		return $results;
	}

	/**
	 * Get directories to be migrated.
	 *
	 * @return	array	List of directories
	 * @since	1.1.0
	 */
	protected function getCopyTables() {
		$tables = !empty($this->xml->tables->table) ? $this->xml->tables->table : array();
		$results = array();
		foreach ($tables as $table) {
			$results[] = (string) $table;
		}
		return $results;
	}

} // end class
