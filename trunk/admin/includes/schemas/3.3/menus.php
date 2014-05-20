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
JLoader::register('JUpgradeproMenus', JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.menus.class.php');
/**
 * Upgrade class for Menus
 *
 * This class takes the menus from the existing site and inserts them into the new site.
 *
 * @since	3.2.0
 */

class JUpgradeproMenu extends JUpgradeproMenus
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

		// Get the component parameters
		JLoader::import('helpers.jupgradepro', JPATH_COMPONENT_ADMINISTRATOR);
		$params = JUpgradeproHelper::getParams();

		if ($params->keep_ids == 1)
		{
			$conditions['order'] = "m.id DESC";
		}else{
			$conditions['order'] = "m.id ASC";
		}
		
		return $conditions;
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
				$query->clear();
				$query->select('id + 1');
				$query->from('#__menu');
				$query->order('id DESC');
				$query->limit(1);
				$this->_db->setQuery($query);
				$row->id = $this->_db->loadResult();
			}	

			// Not needed
			unset($row->id);
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
