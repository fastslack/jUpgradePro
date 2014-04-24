<?php
/**
* jUpgradePro
*
* @version $Id:
* @package jUpgradePro
* @copyright Copyright (C) 2004 - 2014 Matware. All rights reserved.
* @author Matias Aguirre
* @email maguirre@matware.com.ar
* @link http://www.matware.com.ar/
* @license GNU General Public License version 2 or later; see LICENSE
*/
/**
 * Upgrade class for Menus
 *
 * This class takes the menus from the existing site and inserts them into the new site.
 *
 * @since	3.2.0
 */

class JUpgradeproMenu extends JUpgradepro
{
	/**
	 * Setting the conditions hook
	 *
	 * @return	array
	 * @since	3.0.0
	 * @throws	Exception
	 */
	public static function getConditionsHook()
	{
		$conditions = array();
		
		$conditions['as'] = "m";
		
		$conditions['select'] = 'm.*';
		
		$conditions['where'] = array();
		$conditions['where'][] = "m.alias != 'root'";

		$conditions['order'] = "m.id DESC";
		
		return $conditions;
	}

	/**
	 * Method to be called before migrate any data
	 *
	 * @return	array
	 * @since	3.2.0
	 * @throws	Exception
	 */
	public function beforeHook()
	{
		// Insert needed value
		$query = $this->_db->getQuery(true);
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
		if (version_compare(JUpgradeproHelper::getVersion('new'), '3.0', '>=')) {
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

	/**
	 * Sets the data in the destination database.
	 *
	 * @return	void
	 * @since	0.4.
	 * @throws	Exception
	 */
	public function dataHook($rows = null)
	{
		$params = $this->getParams();
		$table	= $this->getDestinationTable();

		// Getting the extensions id's of the new Joomla installation
		$query = "SELECT extension_id, element"
		." FROM #__extensions";
		$this->_db->setQuery($query);
		$extensions_ids = $this->_db->loadObjectList('element');	

		$total = count($rows);

		foreach ($rows as $row)
		{
			// Convert the array into an object.
			$row = (object) $row;

			// Getting the duplicated alias
			$alias = $this->getAlias('#__menu', $row->alias);

			// Prevent MySQL duplicate error
			// @@ Duplicate entry for key 'idx_client_id_parent_id_alias_language'
			$row->alias = (!empty($alias)) ? $alias."~" : $row->alias;

			// Get new/old id's values
			$menuMap = new stdClass();

			// Save the old id
			$menuMap->old = $row->id;

			// Fixing id if == 1 (used by root)
			if ($row->id == 1) {
				$query = "SELECT id"
				." FROM #__menu"
				." ORDER BY id DESC LIMIT 1";
				$this->_db->setQuery($query);
				$lastid = $this->_db->loadResult();	

				$row->id = $lastid + 1;
			}	

			// Not needed
			unset($row->name);
			unset($row->option);
			unset($row->componentid);
			unset($row->ordering);

			// Inserting the menu
			try	{
				$this->_db->insertObject($table, $row);
			}	catch (Exception $e) {
				throw new Exception($e->getMessage());
			}

			// Save the new id
			$menuMap->new = $this->_db->insertid();

			// Save old and new id
			try	{
				$this->_db->insertObject('#__jupgradepro_menus', $menuMap);
			}	catch (Exception $e) {
				throw new Exception($e->getMessage());
			}

			// Updating the steps table
			$this->_step->_nextID($total);
		}

		return false;
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
		//$this->insertDefaultMenus();
	}

	/**
	 * Insert the default menus deleted on cleanups to maintain the original id's
	 *
	 * @return	void
	 * @since	0.5.2
	 * @throws	Exception
	 */
	public function insertDefaultMenus()
	{
		jimport('joomla.table.table');

		// Getting the database instance
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		// Getting the table and query
		$table = JTable::getInstance('Menu', 'JTable');

		// Getting the data
		$query->select('`menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `home`, `language`, `client_id`');
		$query->from('#__jupgradepro_default_menus');
		$query->order('id ASC');
		$db->setQuery($query);
		$menus = $db->loadAssocList();

		foreach ($menus as $menu) {

			// Unset id
			$menu['id'] = 0;

			// Getting the duplicated alias
			$alias = $this->getAlias('#__menu', $menu['alias']);

			// Prevent MySQL duplicate error
			// @@ Duplicate entry for key 'idx_client_id_parent_id_alias_language'
			$menu['alias'] = (!empty($alias)) ? $alias."~" : $menu['alias'];

			// Looking for parent
			$parent = 1;
			$explode = explode("/", $menu['path']);

			if (count($explode) > 1) {

				$query->clear();
				$query->select('id');
				$query->from('#__menu');
				$query->where("path = '{$explode[0]}'");
				$query->order('id ASC');
				$query->limit(1);

				$db->setQuery($query);
				$parent = $db->loadResult();
			}

			// Resetting the table object
			$table->reset();
			// Setting the location of the new category
			$table->setLocation($parent, 'last-child');
			// Bind the data
			$table->bind($menu);
			// Store to database
			$table->store();
		}

		$table->reset();

		if (!$table->rebuild()) {
			echo JError::raiseError(500, $table->getError());
		}
	}
}
