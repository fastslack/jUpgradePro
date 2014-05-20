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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * Upgrade class for menus
 *
 * This class takes the menus from the existing site and inserts them into the new site.
 *
 * @since	3.2.1
 */
class JUpgradeproMenus extends JUpgradepro
{

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

		if ($this->params->keep_ids == 1)
		{
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
	}

	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 *
	 * @since   12.2
	 */
	public function migrateLink(&$row)
	{
    // Fixing menus URLs
    if (strpos($row->link, 'option=com_content') !== false)
		{
      if (strpos($row->link, 'view=frontpage') !== false) {
        $row->link = 'index.php?option=com_content&view=featured';
      } 
    }

    if ( (strpos($row->link, 'Itemid=') !== false) AND $row->type == 'menulink')
		{
      // Extract the Itemid from the URL
      if (preg_match('|Itemid=([0-9]+)|', $row->link, $tmp))
			{
      	$item_id = $tmp[1];

        $row->params = $row->params . "\naliasoptions=".$item_id;
        $row->type = 'alias';
        $row->link = 'index.php?Itemid=';
      }
    }

    if (strpos($row->link, 'option=com_user&') !== false)
		{
      $row->link = preg_replace('/com_user/', 'com_users', $row->link);
      $row->component_id = 25;
			$row->option = 'com_users';

			// Change the register view to registration
      if (strpos($row->link, 'view=register') !== false)
			{
        $row->link = 'index.php?option=com_users&view=registration';
      }
			else if (strpos($row->link, 'view=user') !== false)
			{
        $row->link = 'index.php?option=com_users&view=profile';
      }
    }

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
    // End fixing menus URL's

		return $row;
	}

}
