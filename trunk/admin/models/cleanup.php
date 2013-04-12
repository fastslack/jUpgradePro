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

/**
 * jUpgradePro Model
 *
 * @package		jUpgradePro
 */
class jUpgradeProModelCleanup extends JModelLegacy
{
	/**
	 * Cleanup
	 *
	 * @return	none
	 * @since	1.2.0
	 */
	function cleanup()
	{
		/**
		 * Initialize jUpgradePro class
		 */
		$jupgrade = new jUpgrade;

		// Getting the component parameter with global settings
		$params = $jupgrade->getParams();

		// If REST is enable, cleanup the source jupgrade_steps table
		if ($params->method == 'rest') {
			$jupgrade->_driver->requestRest('cleanup');
		}

		// Set all cid, status and cache to 0
		$query = $this->_db->getQuery(true);
		$query->update('jupgrade_steps')->set('cid = 0, status = 0, cache = 0');
		$this->_db->setQuery($query)->execute();

		// Convert the params to array
		$core_skips = (array) $params;

		// Skiping the steps setted by user
		foreach ($core_skips as $k => $v) {
			$core = substr($k, 0, 9);
			$name = substr($k, 10, 18);

			if ($core == "skip_core") {
				if ($v == 1) {
					$query->clear();
					// Set all status to 0 and clear state
					$query->update('jupgrade_steps')->set('status = 2')->where("name = '{$name}'");
					$this->_db->setQuery($query)->execute();

					if ($name == 'users') {
						$query->clear();
						$query->update('jupgrade_steps')->set('status = 2')->where('name = \'arogroup\'');
						$this->_db->setQuery($query)->execute();

						$query->clear();
						$query->update('jupgrade_steps')->set('status = 2')->where('name = \'usergroupmap\'');
						$this->_db->setQuery($query)->execute();
					}

				}
			}

			if ($k == 'skip_extensions') {
				if ($v == 1) {
					$query->clear();
					$query->update('jupgrade_steps')->set('status = 2')->where('name = \'extensions\'');
					$this->_db->setQuery($query)->execute();
				}
			}
		}

		// Truncate the selected tables
		$tables = array();
		$tables[] = 'jupgrade_categories';
		$tables[] = 'jupgrade_menus';
		$tables[] = 'jupgrade_menus_default';
		$tables[] = 'jupgrade_modules';
		$tables[] = '#__menu_types';
		$tables[] = '#__content';

		for ($i=0;$i<count($tables);$i++) {
			$query->clear();
			$query->delete()->from("{$tables[$i]}");
			$this->_db->setQuery($query)->execute();
		}

		// Cleanup the menu table
		if ($params->skip_core_menus != 1) {

			// Getting the menus
			$query->clear();
			$query->select("title, path, link, img, component_id");
			$query->from("#__menu");
			$query->where("id > 1");
			$query->order('id ASC');
			$this->_db->setQuery($query);
			$menus = $this->_db->loadObjectList();

			foreach ($menus as $menu)
			{
				// Convert the array into an object.
				$menu = (object) $menu;

				$this->_db->insertObject('jupgrade_menus_default', $menu);
			}

			$query->clear();
			$query->delete()->from('#__menu')->where('id > 1');
			$this->_db->setQuery($query)->execute();
		}

		// Insert needed value
		$query->clear();
		$query->insert('jupgrade_menus')->columns('`old`, `new`')->values("0, 0");
		$this->_db->setQuery($query)->execute();

		// Insert uncategorized id
		$query->clear();
		$query->insert('jupgrade_categories')->columns('`old`, `new`')->values("0, 2");
		$this->_db->setQuery($query)->execute();
			// Getting the data
			$query->clear();
			$query->select("username");
			$query->from("#__users");
			$query->where("name = 'Super User'");
			$query->order('id ASC');
			$query->limit(1);
			$this->_db->setQuery($query);
			$superuser = $this->_db->loadResult();
		// Delete uncategorised categories
		if ($params->skip_core_categories != 1) {
			for($i=2;$i<=7;$i++) {
				// Getting the categories table
				$table = JTable::getInstance('Category', 'JTable');

				// Load it before delete. Joomla bug?
				$table->load($i);

				// Delete
				$table->delete($i);
			}
		}

		// Change the id of the admin user
		if ($params->skip_core_users != 1) {

			// Getting the data
			$query->clear();
			$query->select("username");
			$query->from("#__users");
			$query->where("name = 'Super User'");
			$query->order('id ASC');
			$query->limit(1);
			$this->_db->setQuery($query);
			$superuser = $this->_db->loadResult();

			// Updating the super user id to 10
			$query->clear();
			$query->update("#__users");
			$query->set("`id` = 10");
			$query->where("username = '{$superuser}'");
			// Execute the query
			$this->_db->setQuery($query)->execute();

			// Updating the user_usergroup_map
			$query->clear();
			$query->update("#__user_usergroup_map");
			$query->set("`user_id` = 10");
			$query->where("`group_id` = 8");
			// Execute the query
			$this->_db->setQuery($query)->execute();
		}

		// Done checks
		if (!jUpgradeProHelper::isCli())
			$this->returnError (100, 'DONE');
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
