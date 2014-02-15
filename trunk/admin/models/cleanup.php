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
// No direct access.
defined('_JEXEC') or die;

JLoader::register('jUpgrade', JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.class.php');
JLoader::register('jUpgradeDriver', JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.driver.class.php');
JLoader::register('JUpgradeproStep', JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.step.class.php');

/**
 * jUpgradePro Model
 *
 * @package		jUpgradePro
 */
class JUpgradeproModelCleanup extends JModelLegacy
{
	/**
	 * Cleanup
	 *
	 * @return	none
	 * @since	1.2.0
	 */
	function cleanup()
	{
		// Loading the helper
		JLoader::import('helpers.jupgradepro', JPATH_COMPONENT_ADMINISTRATOR);
		// Importing helper tags
		jimport('cms.helper.tags');
		// Getting the component parameter with global settings
		$params = JUpgradeproHelper::getParams();

		// Initialise the tables array
		$del_tables = array();

		// If REST is enable, cleanup the source #__jupgradepro_steps table
		if ($params->method == 'rest') {
			$driver = JUpgradeDriver::getInstance();
			$code = $driver->requestRest('cleanup');
		}

		// Set all cid, status and cache to 0
		$query = $this->_db->getQuery(true);
		$query->update('#__jupgradepro_steps')->set('cid = 0, status = 0, cache = 0');
		$this->_db->setQuery($query)->execute();

		// Convert the params to array
		$core_skips = (array) $params;

		// Skiping the steps setted by user
		foreach ($core_skips as $k => $v) {
			$core = substr($k, 0, 9);
			$name = substr($k, 10, 18);

			if ($core == "skip_core") {
				if ($v == 1) {

					// Disable the the steps setted by user
					$this->updateStep($name);

					if ($name == 'users') {
						// Disable the sections step
						$this->updateStep('arogroup');

						// Disable the sections step
						$this->updateStep('usergroupmap');
					}

					if ($name == 'categories') {
						// Disable the sections step
						$this->updateStep('sections');
					}
				}
			}

			if ($k == 'skip_extensions') {
				if ($v == 1) {
					// Disable the extensions step
					$this->updateStep('extensions');
				}
				else if ($v == 0)
				{
					// Add the tables to truncate for extensions
					$del_tables[] = '#__jupgradepro_extensions_tables';
				}
			}
		}

		// Truncate menu types if menus are enabled
		if ($params->skip_core_categories != 1)
		{
			$del_tables[] = '#__jupgradepro_categories';
			$del_tables[] = '#__jupgradepro_default_categories';
		}

		// Truncate menu types if menus are enabled
		if ($params->skip_core_menus != 1)
		{
			$del_tables[] = '#__menu_types';
			$del_tables[] = '#__jupgradepro_menus';
		}

		// Truncate contents if are enabled
		if ($params->skip_core_modules != 1)
			$del_tables[] = '#__jupgradepro_modules';

		// Truncate contents if are enabled
		if ($params->skip_core_contents != 1)
			$del_tables[] = '#__content';

		for ($i=0;$i<count($del_tables);$i++) {
			$query->clear();
			$query->delete()->from("{$del_tables[$i]}");

			try {
				$this->_db->setQuery($query)->execute();
			} catch (RuntimeException $e) {
				throw new RuntimeException($e->getMessage());
			}
		}

		// Cleanup the menu table
		if ($params->skip_core_menus != 1) {

			// Insert needed value
			$query->clear();
			$query->insert('#__jupgradepro_menus')->columns('`old`, `new`')->values("0, 0");

			try {
				$this->_db->setQuery($query)->execute();
			} catch (RuntimeException $e) {
				throw new RuntimeException($e->getMessage());
			}

			// Clear the default database
			$query->clear();
			$query->delete()->from('#__jupgradepro_default_menus')->where('id > 100');

			try {
				$this->_db->setQuery($query)->execute();
			} catch (RuntimeException $e) {
				throw new RuntimeException($e->getMessage());
			}

			// Getting the menus
			$query->clear();
			// 3.0 Changes
			if (version_compare(PHP_VERSION, '3.0', '>=')) {
				$query->select("`menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `component_id`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `home`, `language`, `client_id`");
			}else{
				$query->select("`menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `home`, `language`, `client_id`");
			}

			$query->from("#__menu");
			$query->where("id > 100");
			$query->where("alias != 'home'");
			$query->order('id ASC');
			$this->_db->setQuery($query);

			try {
				$menus = $this->_db->loadObjectList();
			} catch (RuntimeException $e) {
				throw new RuntimeException($e->getMessage());
			}

			foreach ($menus as $menu)
			{
				// Convert the array into an object.
				$menu = (object) $menu;

				try {
					$this->_db->insertObject('#__jupgradepro_default_menus', $menu);
				} catch (RuntimeException $e) {
					throw new RuntimeException($e->getMessage());
				}
			}

			// Cleanup the entire menu
			$query->clear();
			$query->delete()->from('#__menu')->where('id > 1');

			try {
				$this->_db->setQuery($query)->execute();
			} catch (RuntimeException $e) {
				throw new RuntimeException($e->getMessage());
			}
		}

		// Delete uncategorised categories
		if ($params->skip_core_categories != 1) {

			// Insert uncategorized id
			$query->clear();
			$query->insert('#__jupgradepro_categories')->columns('`old`, `new`')->values("0, 2");
			try {
				$this->_db->setQuery($query)->execute();
			} catch (RuntimeException $e) {
				throw new RuntimeException($e->getMessage());
			}

			// Getting the menus
			$query->clear();
			$query->select("`id`, `parent_id`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`,  `params`, `created_user_id`");
			$query->from("#__categories");
			$query->where("id > 1");
			$query->order('id ASC');
			$this->_db->setQuery($query);

			try {
				$categories = $this->_db->loadObjectList();
			} catch (RuntimeException $e) {
				throw new RuntimeException($e->getMessage());
			}


			foreach ($categories as $category)
			{
				$id = $category->id;
				unset($category->id);

				$this->_db->insertObject('#__jupgradepro_default_categories', $category);

				// Getting the categories table
				$table = JTable::getInstance('Category', 'JTable');
				// Load it before delete. Joomla bug?
				$table->load($id);
				// Delete
				$table->delete($id);
			}
		}

		// Checking if modules were added.
		if ($params->skip_core_modules != 1) {
			$query->clear();
			$query->select('id');
			$query->from("`#__modules`");
			$query->order('id DESC');
			$query->limit(1);
			$this->_db->setQuery($query);

			try {
				$modules_id = $this->_db->loadResult();
			} catch (RuntimeException $e) {
				throw new RuntimeException($e->getMessage());
			}

			if ($modules_id > 86) {
				// Update the modules step
				$this->updateStep('modules');

				// Update the modules_menu step
				$this->updateStep('modules_menu');
			}

			// Cleanup the modules for 'site' unused modules
			$query->clear();
			$query->delete()->from('#__modules')->where('client_id = 0');

			try {
				$this->_db->setQuery($query)->execute();
			} catch (RuntimeException $e) {
				throw new RuntimeException($e->getMessage());
			}
		}

		// Done checks
		if (!JUpgradeproHelper::isCli())
			$this->returnError (100, 'DONE');
	}

	/**
	 * Update the status of one step
	 *
	 * @param		string  $name  The name of the table to update
	 *
	 * @return	none
	 *
	 * @since	3.1.1
	 */
	public function updateStep ($name)
	{
		// Get the JQuery object
		$query = $this->_db->getQuery(true);
		$query->clear();

		$query->update('#__jupgradepro_steps')->set('status = 2')->where('name = \''.$name.'\'');
		try {
			$this->_db->setQuery($query)->execute();
		} catch (RuntimeException $e) {
			throw new RuntimeException($e->getMessage());
		}
	}

	/**
	 * returnError
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	public function returnError ($number, $text)
	{
		$message['number'] = $number;
		$message['text'] = JText::_($text);
		echo json_encode($message);
		exit;
	}

} // end class
