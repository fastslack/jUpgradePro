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
		$join[] = "LEFT JOIN #__components AS c ON c.id = m.componentid";
		
		$conditions['where'] = array();
		$conditions['join'] = $join;
		$conditions['order'] = "m.id DESC";
		
		return $conditions;
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

		// Getting the data
		$query = $db->getQuery(true);
		$query->select('extension_id');
		$query->from('#__extensions');
		$query->where("element = 'com_jupgradepro'");
		$query->order('extension_id ASC');
		$query->limit(1);

		$db->setQuery($query);
		$jupgradepro_id = $db->loadResult();
		$jupgradepro_id = isset($jupgradepro_id) ? $jupgradepro_id : 10001;

		// Default menus
		$menus = array(
			array('com_banners', 'Banners', 'index.php?option=com_banners', 'class:banners', 4),
			array('com_banners', 'Banners/Banners', 'index.php?option=com_banners', 'class:banners', 4),
			array('com_banners_categories', 'Banners/Categories', 'index.php?option=com_categories&extension=com_banners', 'class:banners-cat', 6),
			array('com_banners_clients', 'Banners/Clients', 'index.php?option=com_banners&view=clients', 'class:banners-clients', 4),
			array('com_banners_tracks', 'Banners/Tracks', 'index.php?option=com_banners&view=tracks', 'class:banners-tracks', 4),
			array('com_contact', 'Contacts', 'index.php?option=com_contact', 'class:contact', 8),
			array('com_contact', 'Contacts/Contacts', 'index.php?option=com_contact', 'class:contact', 8),
			array('com_contact_categories', 'Contacts/Categories', 'index.php?option=com_categories&extension=com_contact', 'class:contact-cat', 6),
			array('com_messages', 'Messaging', 'index.php?option=com_messages', 'class:messages', 15),
			array('com_messages_add', 'Messaging/New Private Message', 'index.php?option=com_messages&task=message.add', 'class:messages-add', 15),
			array('com_messages_read', 'Messaging/Read Private Message', 'index.php?option=com_messages', 'class:messages-read', 15),
			array('com_newsfeeds', 'News Feeds', 'index.php?option=com_newsfeeds', 'class:newsfeeds', 17),
			array('com_newsfeeds_feeds', 'News Feeds/Feeds', 'index.php?option=com_newsfeeds', 'class:newsfeeds', 17),
			array('com_newsfeeds_categories', 'News Feeds/Categories', 'index.php?option=com_categories&extension=com_newsfeeds', 'class:newsfeeds-cat', 6),
			array('com_redirect', 'Redirect', 'index.php?option=com_redirect', 'class:redirect', 24),
			array('com_search', 'Basic Search', 'index.php?option=com_search', 'class:search', 19),
			array('com_weblinks', 'Weblinks', 'index.php?option=com_weblinks', 'class:weblinks', 21),
			array('com_weblinks_links', 'Weblinks/Links', 'index.php?option=com_weblinks', 'class:weblinks', 21),
			array('com_weblinks_categories', 'Weblinks/Categories', 'index.php?option=com_categories&extension=com_weblinks', 'class:weblinks-cat', 6),
			array('com_finder', 'Smart Search', 'index.php?option=com_finder', 'class:finder', 27),
			array('com_joomlaupdate', 'Joomla! Update', 'index.php?option=com_joomlaupdate', 'class:joomlaupdate', 28),
			array('com_jupgradepro', 'jUpgradePro', 'index.php?option=com_jupgradepro', 'class:jupgradepro', $jupgradepro_id )
		);


		foreach ($menus as $menu) {

			$parent = 1;

			// Rebuild the categories table
			$table = JTable::getInstance('Menu', 'JTable');

			$explode = explode("/", $menu[1]);

			// Setting the data
			$array = array();
			$array['menutype'] = 'menu';
			$array['type'] = 'component';
			$array['title'] = $menu[0];
			$array['alias'] = (count($explode) > 1) ? $explode[1] :$menu[1];
			$array['path'] = $menu[1];
			$array['link'] = $menu[2];
			$array['img'] = $menu[3];
			$array['published'] = 0;
			$array['component_id'] = $menu[4];
			$array['client_id'] = 1;
			$array['language'] = '*';

			if (count($explode) > 1) {

				// Getting the data
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__menu');
				$query->where("path = '{$explode[0]}'");
				$query->order('id ASC');
				$query->limit(1);

				$db->setQuery($query);
				$parent = $db->loadResult();

			}

			// Setting the location of the new category
			$table->setLocation($parent, 'last-child');
			//
			$table->bind($array);
			// Store
			$table->store();
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
/*
		// Creating the query
		$join = array();
		$join[] = "LEFT JOIN #__components AS c ON c.id = m.componentid";
		//$join[] = "LEFT JOIN #__extensions AS e ON e.element = c.option";

		$rows = parent::getSourceData(
			 ' m.id, m.menutype, m.name AS title, m.alias, m.link, m.type, c.option,'
			//.' m.published, m.parent AS parent_id, e.extension_id AS component_id,'
			.' m.published, m.parent AS parent_id,'
			.' m.sublevel AS level, m.ordering, m.checked_out, m.checked_out_time, m.browserNav,'
			.' m.access, m.params, m.lft, m.rgt, m.home',
			$join,
			null,
			'm.id DESC'
		);
 */

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
      if (strpos($row['link'], 'option=com_content') !== false) {

        if (strpos($row['link'], 'view=frontpage') !== false) {
          $row['link'] = 'index.php?option=com_content&view=featured';

        } 
        /*
        else {
          // Extract the id from the URL
          if (preg_match('|id=([0-9]+)|', $row['link'], $tmp)) {

            $id = $tmp[1];
            if ( (strpos($row['link'], 'layout=blog') !== false) AND
               ( (strpos($row['link'], 'view=category') !== false) OR
                 (strpos($row['link'], 'view=section') !== false) ) ) {
            				$row['link'] = 'index.php?option=com_content&view=category&layout=blog&id='.$categories[$id]->new;
            } elseif (strpos($row['link'], 'view=section') !== false) {
              $row['link'] = 'index.php?option=com_content&view=category&layout=blog&id='.$sections[$id]->new;
            }
          }
        }*/
      }

      if ( (strpos($row['link'], 'Itemid=') !== false) AND $row['type'] == 'menulink') {

          // Extract the Itemid from the URL
          if (preg_match('|Itemid=([0-9]+)|', $row['link'], $tmp)) {
          	$item_id = $tmp[1];

            $row['params'] = $row['params'] . "\naliasoptions=".$item_id;
            $row['type'] = 'alias';
            $row['link'] = 'index.php?Itemid=';
          }
      }

      if (strpos($row['link'], 'option=com_user&') !== false) {
        $row['link'] = preg_replace('/com_user/', 'com_users', $row['link']);
        $row['component_id'] = 25;
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

		// Getting the categories id's
		$categories = $this->getMapList();
		$sections = $this->getMapList('categories', 'com_section');
		
		// Getting the extensions id's of the new Joomla installation
		$query = "SELECT extension_id, element"
		." FROM #__extensions";
		$this->_db->setQuery($query);
		$extensions_ids = $this->_db->loadObjectList('element');	

		// Initialize values
		//$aliases = array();
		$unique_alias_suffix = 1;

		$total = count($rows);

		foreach ($rows as $row)
		{
			// Convert the array into an object.
			$row = (object) $row;

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

			if (version_compare(PHP_VERSION, '3.0', '>=')) {
				unset($row->ordering);
			}

			// Not needed
			unset($row->name);
			unset($row->option);
			unset($row->componentid);

      // Extract the id from the URL
      if (preg_match('|id=([0-9]+)|', $row->link, $tmp)) {

        $id = $tmp[1];
        if ( (strpos($row->link, 'layout=blog') !== false) AND
           ( (strpos($row->link, 'view=category') !== false) OR
             (strpos($row->link, 'view=section') !== false) ) ) {
             		$catid = isset($categories[$id]->new) ? $categories[$id]->new : $id;
        				$row->link = "index.php?option=com_content&view=category&layout=blog&id={$catid}";
        } elseif (strpos($row->link, 'view=section') !== false) {
        	$sectionid = isset($sections[$id]->new) ? $sections[$id]->new : $id;
          $row->link = 'index.php?option=com_content&view=category&layout=blog&id='.$sectionid;
        }
      }

			// Inserting the menu
			if (!$this->_db->insertObject($table, $row)) {
				throw new Exception($this->_db->getErrorMsg());
			}

			// Get new/old id's values
			$menuMap = new stdClass();
			$menuMap->old = $row->id;
			$menuMap->new = $this->_db->insertid();

			// Save old and new id
			if (!$this->_db->insertObject('jupgrade_menus', $menuMap)) {
				throw new Exception($this->_db->getErrorMsg());
			}

			// Updating the steps table
			$this->_step->_nextID($total);
		}

		return false;
	}

	/**
	 * The public entry point for the class.
	 *
	 * @return	void
	 * @since	0.5.2
	 * @throws	Exception
	 */
	public function upgrade()
	{
		if (parent::upgrade()) {
			// Rebuild the menu nested set values.
			$table = JTable::getInstance('Menu', 'JTable', array('dbo' => $this->_db));

			if (!$table->rebuild()) {
				echo JError::raiseError(500, $table->getError());
			}
		}
	}
}
