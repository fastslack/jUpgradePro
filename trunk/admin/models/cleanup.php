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

		// Get the prefix
		$prefix = $this->_db->getPrefix();

		// Set all cid, status and cache to 0 
		$query = "UPDATE jupgrade_steps SET cid = 0, status = 0, cache = 0";
		$this->runQuery ($query);

		// Convert the params to array
		$core_skips = (array) $params;

		// Skiping the steps setted by user
		foreach ($core_skips as $k => $v) {
			$core = substr($k, 0, 9);
			$name = substr($k, 10, 18);

			if ($core == "skip_core") {
				if ($v == 1) {
					// Set all status to 0 and clear state
					$query = "UPDATE jupgrade_steps SET status = 2 WHERE name = '{$name}'";
					$this->runQuery ($query);

					if ($name == 'users') {
						$query = "UPDATE jupgrade_steps SET status = 2 WHERE name = 'arogroup'";
						$this->runQuery ($query);				

						$query = "UPDATE jupgrade_steps SET status = 2 WHERE name = 'usergroupmap'";
						$this->runQuery ($query);		
					}

				}
			}

			if ($k == 'skip_extensions') {
				if ($v == 1) {
					$query = "UPDATE jupgrade_steps SET status = 2 WHERE name = 'extensions'";
					$this->runQuery ($query);				
				}
			}
		}

		// Truncate the selected tables
		$tables = array();
		$tables[] = 'jupgrade_categories';
		$tables[] = 'jupgrade_menus';
		$tables[] = 'jupgrade_modules';
		$tables[] = "{$this->_db->getPrefix()}menu_types";
		$tables[] = "{$this->_db->getPrefix()}content";

		for ($i=0;$i<count($tables);$i++) {
			if ($jupgrade->canDrop) {
				$query = "TRUNCATE TABLE `{$tables[$i]}`";
			}else{
				$query = "DELETE FROM `{$tables[$i]}`";
			}
			$this->runQuery ($query);
		}

		// Cleanup the menu table
		if ($params->skip_core_menus != 1) {
			$query = "DELETE FROM {$this->_db->getPrefix()}menu WHERE id > 1";
			$this->runQuery ($query);
		}

		// Insert needed value
		$query = "INSERT INTO `jupgrade_menus` ( `old`, `new`) VALUES ( 0, 0)";
		$this->runQuery ($query);

		// Insert uncategorized id
		$query = "INSERT INTO `jupgrade_categories` (`old`, `new`) VALUES (0, 2)";
		$this->runQuery ($query);

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
			$query = "UPDATE {$prefix}users SET id = 10 WHERE username = 'admin'";
			$this->runQuery ($query);

			$query = "UPDATE {$prefix}user_usergroup_map SET user_id = 10 WHERE group_id = 8";
			$this->runQuery ($query);
		}

		// Done checks
		if (!jUpgradeProHelper::isCli())
			$this->returnError (100, 'DONE');
	}

	/**
	 * runQuery
	 *
	 * @return	none
	 * @since	3.0.0
	 */
	public function runQuery ($query)
	{
		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		return true;
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
