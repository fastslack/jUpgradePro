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
/**
 * Upgrade class for 3rd party extensions
 *
 * This class search for extensions to be migrated
 *
 * @since	0.4.5
 */
class JUpgradeproExtensions extends JUpgradepro
{
	/**
	 * @var      
	 * @since  3.0
	 */
	public $xml = null;

	function __construct($step = null)
	{
		parent::__construct($step);

		$name = $this->_step->_getStepName();

		if (!empty($this->_step->xmlpath)) {
			// Find xml file from jUpgrade
			$default_xmlfile = JPATH_PLUGINS."/jupgradepro/{$this->_step->xmlpath}";

			if (file_exists($default_xmlfile)) {
				$this->xml = simplexml_load_file($default_xmlfile);
			}
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
			$this->ready = $this->setDestinationData();

			if ($this->ready)
			{
				//$this->ready = $this->migrateExtensionTables();
			}
			if ($this->ready)
			{
				//$this->ready = $this->fixExtensionMenus();
			}
			if ($this->ready)
			{
				//$this->ready = $this->migrateExtensionCustom();
			}

			//$debug = $this->_step->getParameters(false);

			//print_r($debug);

		

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
	 * Hook to do custom migration after all steps
	 *
	 * @return	boolean Ready
	 * @since	3.0.3
	 */
	protected function afterAllStepsHook()
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
		$component->load(array('type'=>'component', 'element'=>$this->_step->_getStepName()));

		// First fix all broken menu items
		$query = "UPDATE #__menu SET component_id={$this->_db->quote($component->extension_id)} WHERE type = 'component' AND link LIKE '%option={$this->_step->_getStepName()}%'";
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
