<?php
/**
 * jUpgrade
 *
 * @version		$Id$
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @copyright	Copyright 2004 - 2013 Matias Aguirre. All rights reserved.
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
class jUpgradeCheckExtensions extends jUpgradeExtensions
{
	/**
	 * count adapters
	 * @var int
	 * @since	1.1.0
	 */
	public $count = 0;
	protected $extensions = array();

	public function upgrade()
	{
		if (!$this->upgradeComponents()) {
			return false;
		}

		if (!$this->upgradeModules()) {
			return false;
		}

		if (!$this->upgradePlugins()) {
			return false;
		}

		return ($this->_processExtensions() == 0) ? false : true;
	}

	/**
	 * Upgrade the components
	 *
	 * @return	
	 * @since	1.1.0
	 * @throws	Exception
	 */
	protected function upgradeComponents()
	{
		// Getting the step
		$step = jUpgradeStep::getInstance('ext_components', true);

		// Get jUpgradeExtensionsComponents instance
		$components = jUpgrade::getInstance($step);
		$rows = $components->dataSwitch();

		$this->_addExtensions ( $rows, 'com' );

		$step->status = 2;
		$step->_updateStep(true);

		return true;
	}

	/**
	 * Upgrade the modules
	 *
	 * @return	
	 * @since	1.1.0
	 * @throws	Exception
	 */
	protected function upgradeModules()
	{
		// Getting the step
		$step = jUpgradeStep::getInstance('ext_modules', true);

		// Get jUpgradeExtensionsModules instance
		$modules = jUpgrade::getInstance($step);
		$rows = $modules->dataSwitch();

		$this->_addExtensions ( $rows, 'mod' );

		$step->status = 2;
		$step->_updateStep(true);

		return true;
	}

	/**
	 * Upgrade the plugins
	 *
	 * @return
	 * @since	1.1.0
	 * @throws	Exception
	 */
	protected function upgradePlugins()
	{
		// Getting the step
		$step = jUpgradeStep::getInstance('ext_plugins', true);

		// Get jUpgradeExtensionsPlugins instance
		$plugins = jUpgrade::getInstance($step);
		$rows = $plugins->dataSwitch();

		$this->_addExtensions ( $rows, 'plg' );

		$step->status = 2;
		$step->_updateStep(true);

		return true;
	}

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 * @since	0.4.5
	 * @throws	Exception
	 *
	protected function upgradeTemplates()
	{
		$this->destination = "#__extensions";

		$folders = JFolder::folders(JPATH_ROOT.DS.'templates');
		$folders = array_diff($folders, array("system", "beez"));
		sort($folders);
		//print_r($folders);

		$rows = array();
		// Do some custom post processing on the list.
		foreach($folders as $folder) {

			$row = array();
			$row['name'] = $folder;
			$row['type'] = 'template';
			$row['element'] = $folder;
			$row['params'] = '';
			$rows[] = $row;
		}

		$this->_addExtensions ( $rows, 'tpl' );
		return true;
	}*/

	protected function _addExtensions( $rows, $prefix )
	{
		// Create new indexed array
		foreach ($rows as &$row)
		{
			// Convert the array into an object.
			$row = (object) $row;
			$row->id = null;
			$row->element = strtolower($row->element);

			// Ensure that name is always using form: xxx_folder_name
			$name = preg_replace('/^'.$prefix.'_/', '', $row->element);
			if (!empty($row->folder)) {
				$element = preg_replace('/^'.$row->folder.'_/', '', $row->element);
				$row->name = ucfirst($row->folder).' - '.ucfirst($element);
				$name = $row->folder.'_'.$element;
			}
			$name = $prefix .'_'. $name;
			$this->extensions[$name] = $row;
		}
	}

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 * @since 1.1.0
	 * @throws	Exception
	 */
	protected function _processExtensions()
	{
		jimport('joomla.filesystem.folder');

		$types = array(
			'/^com_(.+)$/e',									// com_componentname
			'/^mod_(.+)$/e',									// mod_modulename
			'/^plg_(.+)_(.+)$/e',								// plg_folder_pluginname
			'/^tpl_(.+)$/e');									// tpl_templatename
		$directories = array(
			"'components/com_\\1'",								// compontens/com_componentname
			"'modules/mod_\\1'",								// modules/mod_modulename
			"'plugins/\\1/\\2'",								// plugins/type/pluginname
			"'templates/\\1'");									// templates/templatename
		$classes = array(
			"'jUpgradeComponent'.ucfirst('\\1')",				// jUpgradeComponentComponentname
			"'jUpgradeModule'.ucfirst('\\1')",					// jUpgradeModuleModulename
			"'jUpgradePlugin'.ucfirst('\\1').ucfirst('\\2')",	// jUpgradePluginPluginname
			"'jUpgradeTemplate'.ucfirst('\\1')");				// jUpgradeTemplateTemplatename

		// Getting the plugins list
		$query = $this->_db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');
		$query->where("type = 'plugin'");
		$query->where("folder = 'jupgradepro'");
		$query->where("enabled = 1");

		// Setting the query and getting the result
		$this->_db->setQuery($query);
		$plugins = $this->_db->loadObjectList();

		// Do some custom post processing on the list.
		foreach ($plugins as $plugin)
		{
			// Looking for xml files
			$files = (array) JFolder::files(JPATH_PLUGINS."/jupgradepro/{$plugin->element}/extensions", '\.xml$', true, true);

			foreach ($files as $xmlfile)
			{
				if (!empty($xmlfile)) {

					$element = JFile::stripExt(basename($xmlfile));

					if (array_key_exists($element, $this->extensions)) {

						$extension = $this->extensions[$element];

						// Read xml definition file
						$xml = simplexml_load_file($xmlfile);

						// Getting the php file
						if (!empty($xml->installer->file[0])) {
							$phpfile = JPATH_ROOT.'/'.trim($xml->installer->file[0]);
						}
						if (empty($phpfile)) {
							$default_phpfile = JPATH_PLUGINS."/jupgradepro/{$plugin->element}/extensions/{$element}.php";
							$phpfile = file_exists($default_phpfile) ? $default_phpfile : null;
						}

						// Getting the class
						if (!empty($xml->installer->class[0])) {
							$class = trim($xml->installer->class[0]);
						}
						if (empty($class)) {
							$class = preg_replace($types, $classes, $element);
						}

						// Saving the extensions and migrating the tables
						if ( !empty($phpfile) || !empty($xmlfile) ) {

							// Adding +1 to count
							$this->count = $this->count+1;

							// Reset the $query object
							$query->clear();

							// Inserting the step to jupgrade_extensions table
							$query->insert('jupgrade_extensions')->columns('name, title, class')->values("'{$element}', '{$xml->name}', '{$class}'");
							$this->_db->setQuery($query);
							$this->_db->execute();

							// Inserting the collection if exists
							if (isset($xml->name) && isset($xml->collection)) {
								$query->insert('#__update_sites')->columns('name, type, location, enabled')
									->values("'{$xml->name}', 'collection',  '{$xml->collection}, 1");
								$this->_db->setQuery($query);
								$this->_db->execute();
							}

							// Converting the params
							$extension->params = $this->convertParams($extension->params);

							// Saving the extension to #__extensions table
							if (!$this->_db->insertObject('#__extensions', $extension)) {
								throw new Exception($this->_db->getErrorMsg());
							}

							// Getting the extension id
							$extension->id = $this->_db->insertid();

							// Adding tables to migrate
							if (!empty($xml->tables[0])) {

								foreach ($xml->tables[0]->table as $xml_ext) {
									//
									$table = new StdClass();
									$table->name = (string) $xml_ext;
									$table->eid = $extension->id;
									$table->element = $element;
									$table->class = $class;

									$exists = $this->_driver->tableExists($table->name);

									if ($exists == 'YES'){
										if (!$this->_db->insertObject('jupgrade_extensions_tables', $table)) {
											throw new Exception($this->_db->getErrorMsg());
										}
									}
								}
							}

							// Add other extensions from the package
							if (!empty($xml->package[0])) {
								foreach ($xml->package[0]->extension as $xml_ext) {
									if (isset($this->extensions[(string) $xml_ext->name])) {
										$extension = $this->extensions[(string) $xml_ext->name];
										$state->extensions[] = (string) $xml_ext->name;

										$extension->params = $this->convertParams($extension->params);
										if (!$this->_db->insertObject('#__extensions', $extension)) {
											throw new Exception($this->_db->getErrorMsg());
										}
										unset ($this->extensions[(string) $xml_ext->name]);
									}
								}
							}

						} //end if

					} // end if

				} // end if

				unset($class);
				unset($phpfile);
				unset($xmlfile);

			} // end foreach
		} // end foreach

		return $this->count;
	}
} // end class
