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
 * Upgrade class for Menus
 *
 * This class takes the menus from the existing site and inserts them into the new site.
 *
 * @since	0.4.5
 */

class jUpgradeMenu extends jUpgrade
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
		
		$conditions['select'] = 'm.id, m.menutype, m.name, m.name AS title, m.alias, m.link, m.type, c.option, m.published, m.parent AS parent_id,'
			.' m.sublevel AS level, m.ordering, m.checked_out, m.checked_out_time, m.browserNav, m.access, m.params, m.home';
		
		$join = array();
		$join[] = "#__components AS c ON c.id = m.componentid";
		
		$conditions['where'] = array();
		$conditions['join'] = $join;
		$conditions['order'] = "m.id DESC";
		
		return $conditions;
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
		$this->insertDefaultMenus();
	}

	/**
	 * Insert the default menus deleted on cleanups to maintain the original id's
	 *
	 * @return	void
	 * @since	0.5.2
	 * @throws	Exception
	 */
	public static function insertDefaultMenus()
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

			// Getting the data
			$query->clear();
			$query->select('alias');
			$query->from('#__menu');
			$query->where("alias LIKE '{$menu['alias']}%'");
			$query->order('id DESC');
			$query->limit(1);
			$db->setQuery($query);
			$alias = $db->loadResult();

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

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 * @since	0.4.5
	 * @throws	Exception
	 */
	public function databaseHook($rows = null)
	{
		// Do some custom post processing on the list.
		foreach ($rows as $key => &$row) {
 
			$row = (array) $row;

			// Fixing access
			$row['access']++;

			// Fixing level
			$row['level']++;

			// Fixing language
			$row['language'] = '*';

			// Fixing parent_id
			if ($row['parent_id'] == 0) {
				$row['parent_id'] = 1;
			}

      // Converting params to JSON
      $row['params'] = $this->convertParams($row['params']);

      // Fixing menus URLs
      if (strpos($row['link'], 'option=com_content') !== false)
			{
        if (strpos($row['link'], 'view=frontpage') !== false) {
          $row['link'] = 'index.php?option=com_content&view=featured';
        } 
      }

      if ( (strpos($row['link'], 'Itemid=') !== false) AND $row['type'] == 'menulink')
			{

          // Extract the Itemid from the URL
          if (preg_match('|Itemid=([0-9]+)|', $row['link'], $tmp))
					{
          	$item_id = $tmp[1];

            $row['params'] = $row['params'] . "\naliasoptions=".$item_id;
            $row['type'] = 'alias';
            $row['link'] = 'index.php?Itemid=';
          }
      }

      if (strpos($row['link'], 'option=com_user&') !== false)
			{
        $row['link'] = preg_replace('/com_user/', 'com_users', $row['link']);
        $row['component_id'] = 25;
				$row['option'] = 'com_users';

				// Change the register view to registration
        if (strpos($row['link'], 'view=register') !== false)
				{
          $row['link'] = 'index.php?option=com_users&view=registration';
        }
				else if (strpos($row['link'], 'view=user') !== false)
				{
          $row['link'] = 'index.php?option=com_users&view=profile';
        }
      }
      // End fixing menus URL's
    }

		return $rows;
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
		if (isset($object->menu_image)) {
			if((string)$object->menu_image == '-1'){
				$object->menu_image = '';
			}
		}

		$object->show_page_heading = (isset($object->show_page_title) && !empty($object->page_title)) ? $object->show_page_title : 0;
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

		// Initialize values
		$unique_alias_suffix = 1;

		$total = count($rows);

		foreach ($rows as $row)
		{
			// Convert the array into an object.
			$row = (object) $row;

			// Get new/old id's values
			$menuMap = new stdClass();

			// Check if has duplicated aliases
			$query = "SELECT alias"
			." FROM #__menu"
			." WHERE alias = ".$this->_db->quote($row->alias);
			$this->_db->setQuery($query);
			$aliases = $this->_db->loadAssoc();

			$count = count($aliases);
			if ($count > 0) {
				$row->alias .= "-".rand(0, 99999);
			}

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

			// Fixing extension_id
			if (isset($row->option)) {
				$row->component_id = isset($extensions_ids[$row->option]) ? $extensions_ids[$row->option]->extension_id : 0;
			}
			
			// Fixes
			$row->title = $row->name;

			if (version_compare(PHP_VERSION, '3.0', '>='))
				unset($row->ordering);


			// Not needed
			unset($row->name);
			unset($row->option);
			unset($row->componentid);

      // Extract the id from the URL
      if (preg_match('|id=([0-9]+)|', $row->link, $tmp))
			{
				$id = $tmp[1];

				if ( (strpos($row->link, 'layout=blog') !== false) AND
					( (strpos($row->link, 'view=category') !== false) OR
					(strpos($row->link, 'view=section') !== false) ) ) {
						$catid = $this->getMapListValue('categories', 'categories', 'old = ' . $id);
						$row->link = "index.php?option=com_content&view=category&layout=blog&id={$catid}";
				} elseif (strpos($row->link, 'view=section') !== false) {
						$catid = $this->getMapListValue('categories', 'com_section', 'old = ' . $id);
						$row->link = 'index.php?option=com_content&view=category&layout=blog&id='.$catid;
				}
			}

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
}
