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

		// Do some custom post processing on the list.
		foreach ($this->extensions as $name=>&$row)
		{
			$state = new StdClass();
			$state->xmlfile = null;
			$state->phpfile = null;
			$state->extensions = null;

			$path = preg_replace($types, $directories, $name);

			if (is_dir(JPATH_ROOT."/administrator/{$path}")) {
				// Find j16upgrade.xml from the extension's administrator folders
				$files = (array) JFolder::files(JPATH_ROOT."/administrator/{$path}", '^jupgrade\.xml$', true, true);
				$state->xmlfile = array_shift( $files );
			}
			if (empty($state->xmlfile) && is_dir(JPATH_ROOT.'/'.$path)) {
				// Find j16upgrade.xml from the extension's folders
				$files = (array) JFolder::files(JPATH_ROOT.'/'.$path, '^jupgrade\.xml$', true, true);
				$state->xmlfile = array_shift( $files );
			}

			// Check default path for extensions files
			$default_path = JPATH_COMPONENT_ADMINISTRATOR;

			if (empty($state->xmlfile)) {
				// Find xml file from jUpgrade
				//$default_xmlfile = "{$default_path}/includes/extensions/{$name}.xml";

				$basename = substr($name, 4);

				$default_xmlfile = JPATH_PLUGINS."/jupgradepro/jupgradepro_{$basename}/extensions/{$name}.xml";

				if (file_exists($default_xmlfile)) {
					$state->xmlfile = $default_xmlfile;
				}
			}

			if (!empty($state->xmlfile)) {
				// Read xml definition file
				$xml = simplexml_load_file($state->xmlfile);

				if (!empty($xml->installer->file[0])) {
					$state->phpfile = JPATH_ROOT.'/'.trim($xml->installer->file[0]);
				}
				if (!empty($xml->installer->class[0])) {
					$state->class = trim($xml->installer->class[0]);
				}
			}
			if (empty($state->phpfile)) {
				// Find adapter from jUpgrade
				$default_phpfile = "{$default_path}/extensions/{$name}.php";
				if (file_exists($default_phpfile)) {
					$state->phpfile = $default_phpfile;
				}
			}
			if (empty($state->class)) {
				// Set default class name
				$state->class = preg_replace($types, $classes, $row->element);
			}


			if (!empty($state->phpfile) || !empty($state->xmlfile)) {

				$query = "INSERT INTO jupgrade_extensions (name, title, class) VALUES('{$name}', '{$xml->name}', '{$state->class}' )";
				$this->_db->setQuery($query);
				$this->_db->query();

				if (isset($xml->name) && isset($xml->collection)) {
					$query = "INSERT INTO #__update_sites (name, type, location, enabled) VALUES({$this->_db->quote($xml->name)}, 'collection',  {$this->_db->quote($xml->collection)}, 1 )";
					$this->_db->setQuery($query);
					$this->_db->query();
				}

				$row->params = $this->convertParams($row->params);

				if (!$this->_db->insertObject('#__extensions', $row)) {
					throw new Exception($this->_db->getErrorMsg());
				}

				// Adding +1 to count
				$this->count = $this->count+1;

				// Getting the extension id
				$row->id = $this->_db->insertid();

				// Adding tables to migrate
				if (!empty($xml->tables[0])) {

					foreach ($xml->tables[0]->table as $xml_ext) {
						//
						$table = new StdClass();
						$table->name = (string) $xml_ext;
						$table->eid = $row->id;
						$table->element = $row->element;
						$table->class = $state->class;

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

				// Cleanup
				unset ($row);
			} //end if
		}

		return $this->count;
	}
} // end class
