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

require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.class.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.category.class.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.extensions.class.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.users.class.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.step.class.php';

/**
 * jUpgradePro Model
 *
 * @package		MatWare
 * @subpackage	com_jupgrade
 */
class jUpgradeProModel extends JModelLegacy
{
	/**
	 * Initial checks in jUpgrade
	 *
	 * @return	none
	 * @since	1.2.0
	 */
	function getChecks()
	{
		// Initialize jupgrade class
		$jupgrade = new jUpgrade;
		
		// Getting the component parameter with global settings
		$params = $jupgrade->getParams();

		// Checking tables
		$query = "SHOW TABLES";
		$jupgrade->_db->setQuery($query);
		$tables = $jupgrade->_db->loadColumn();
		
		$message = array();
		$message['status'] = "ERROR";

		if (!in_array('jupgrade_categories', $tables)) {
			$this->returnError (401, 'COM_JUPGRADEPRO_ERROR_TABLE_CAT');
		}
		
		if (!in_array('jupgrade_menus', $tables)) {
			$this->returnError (402, 'COM_JUPGRADEPRO_ERROR_TABLE_MENUS');
		}
		
		if (!in_array('jupgrade_modules', $tables)) {
			$this->returnError (403, 'COM_JUPGRADEPRO_ERROR_TABLE_MODULES');
		}
		
		if (!in_array('jupgrade_steps', $tables)) {
			$this->returnError (404, 'COM_JUPGRADEPRO_ERROR_TABLE_STEPS_NO_EXISTS');
		}		

		// Check if jupgrade_steps is fine
		$query = "SELECT COUNT(id) FROM `jupgrade_steps`";
		$jupgrade->_db->setQuery($query);
		$nine = $jupgrade->_db->loadResult();
		
		if ($nine < 10) {
			$this->returnError (405, 'COM_JUPGRADEPRO_ERROR_TABLE_STEPS_NOT_VALID');
		}
	
		// Check safe_mode_gid
		if (@ini_get('safe_mode_gid') && @ini_get('safe_mode')) {
			$this->returnError (411, 'COM_JUPGRADEPRO_ERROR_DISABLE_SAFE_GID');
		}

		// Check for bad configurations
		if ($params->method == "rest") {

			if (!isset($params->rest_hostname) || !isset($params->rest_username) || !isset($params->rest_password) || !isset($params->rest_key) ) {
				$this->returnError (412, 'COM_JUPGRADEPRO_ERROR_REST_CONFIG');
			}

			if ($params->rest_hostname == 'http://www.example.org/' || $params->rest_hostname == '' || 
					$params->rest_username == '' || $params->rest_password == '' || $params->rest_key == '') {
				$this->returnError (412, 'COM_JUPGRADEPRO_ERROR_REST_CONFIG');
			}

			// Checking the RESTful connection
			$code = $jupgrade->_driver->requestRest('check');

			switch ($code) {
				case 401:
					$this->returnError (501, 'COM_JUPGRADEPRO_ERROR_REST_501');
				case 402:
					$this->returnError (502, 'COM_JUPGRADEPRO_ERROR_REST_502');
				case 403:
					$this->returnError (503, 'COM_JUPGRADEPRO_ERROR_REST_503');
				case 405:
					$this->returnError (505, 'COM_JUPGRADEPRO_ERROR_REST_505');
				case 406:
					$this->returnError (506, 'COM_JUPGRADEPRO_ERROR_REST_506');
			}
		}

		// Check for bad configurations
		if ($params->method == "database") {
			if ($params->old_hostname == '' || $params->old_username == '' || $params->old_password == '' || $params->old_db == '' || $params->old_dbprefix == '' ) {
				$this->returnError (413, 'COM_JUPGRADEPRO_ERROR_DATABASE_CONFIG');
			}
		}

		// Convert the params to array
		$core_skips = (array) $params;
		$flag = false;

		// Check is all skips is set
		foreach ($core_skips as $k => $v) {
			$core = substr($k, 0, 9);
			$name = substr($k, 10, 18);

			if ($core == "skip_core") {
				if ($v == 0) {
					$flag = true;
				}
			}
		}

		if ($flag === false) {
			$this->returnError (414, 'COM_JUPGRADEPRO_ERROR_SKIPS_ALL');				
		}		

		// Checking tables
		if ($params->skip_core_categories != 1) {
			$query = "SELECT COUNT(id) FROM #__categories";
			$jupgrade->_db->setQuery($query);
			$categories_count = $jupgrade->_db->loadResult();

			if ($categories_count > 7) {
				$this->returnError (415, 'COM_JUPGRADEPRO_ERROR_DATABASE_CATEGORIES');
			}
		}

		// Checking tables
		if ($params->skip_core_contents != 1) {
			$query = "SELECT COUNT(id) FROM #__content";
			$jupgrade->_db->setQuery($query);
			$content_count = $jupgrade->_db->loadResult();

			if ($content_count > 0) {
				$this->returnError (416, 'COM_JUPGRADEPRO_ERROR_DATABASE_CONTENT');
			}
		}

		// Checking tables
		if ($params->skip_core_users != 1) {
			$query = "SELECT COUNT(id) FROM #__users";
			$jupgrade->_db->setQuery($query);
			$users_count = $jupgrade->_db->loadResult();

			if ($users_count > 1) {
				$this->returnError (417, 'COM_JUPGRADEPRO_ERROR_DATABASE_USERS');
			}
		}

		// Done checks
		if (!jUpgradeProHelper::isCli())
			$this->returnError (100, 'DONE');
	}

	/**
	 * Cleanup
	 *
	 * @return	none
	 * @since	1.2.0
	 */
	function getCleanup()
	{
		/**
		 * Initialize jupgrade class
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
	 * Get the next step
	 *
	 * @return   step object
	 */
	public function getStep($name = false, $json = true, $extensions = false) {

		$extensions = (bool) ($extensions != false) ? $extensions : JRequest::getCmd('extensions', '');

		$step = jUpgradeStep::getInstance(NULL, $extensions);

		$step->getStep($name);

		return $step->getParameters($json);
	}

	/**
	 * Migrate
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	function getMigrate($table = false, $json = true, $extensions = false) {

		$table = (bool) ($table != false) ? $table : JRequest::getCmd('table', '');
		$extensions = (bool) ($extensions != false) ? $extensions : JRequest::getCmd('extensions', '');

		// Init the jUpgrade instance
		$step = jUpgradeStep::getInstance($table, $extensions);
		$jupgrade = jUpgrade::getInstance($step);

		// Get the database structure
		if ($step->first == true && $extensions == 'tables') {
			$structure = $jupgrade->getTableStructure();
		}

		// Run the upgrade
		if ($step->total > 0) {
			$jupgrade->upgrade();
		}

		// Javascript flags
		if ( $step->cid == $step->stop+1 && $step->total != 0) {
			$step->next = true;
		}
		if ($step->name == $step->laststep) {
			$step->end = true;
		}

		$empty = false;
		if ($step->cid == 0 && $step->total == 0 && $step->start == 0 && $step->stop == 0) {
			$empty = true;
		} 

		if ($step->stop == 0) {
			$step->stop = -1;
		}

		// Update jupgrade_steps table if id = last_id
		if ( ( ($step->total <= $step->cid) || ($step->stop == -1) && ($empty == false) ) )
		{
			$step->next = true;
			$step->status = 2;

			$step->_updateStep();
		}

		return $step->getParameters($json);
	}

	/**
	 * Migrate
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	function getExtensions() {

		// Get the step
		$step = jUpgradeStep::getInstance('extensions', true);

		// Get jUpgradeExtensions instance
		$extensions = jUpgrade::getInstance($step);
		$success = $extensions->upgrade();

		if ($success === true) {
			$step->status = 2;
			$step->_updateStep();
			return true;
		}
	}

	/**
	 * getExtensionsList
	 *
	 * @return	none
	 * @since	3.0.0
	 */
	function getExtensionsList() {
		// updating the status flag
		$query = "SELECT * FROM jupgrade_extensions";
		$this->_db->setQuery($query);
		$extensions = $this->_db->loadAssocList();

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		return $extensions;
	}

	public function testRest($task, $table) {

		$step = jUpgradeStep::getInstance();
		$jupgrade = jUpgrade::getInstance($step);

		$response = $jupgrade->_driver->requestRest($task, $table);

		return $response;
	}

	/**
 	* Writes to file all the selected database tables structure with SHOW CREATE TABLE
	* @param string $table The table name
	*/
	public function getStructure() {

		$step = jUpgradeStep::getInstance(null, 'tables');

		// Get jUpgradeExtensions instance
		$jupgrade = jUpgrade::getInstance($step);
		$structure = $jupgrade->getTableStructure();

		return $structure;
	}

	/**
	 * Initial checks in jUpgrade
	 *
	 * @return	none
	 * @since	1.2.0
	 */
	function getParams()
	{
		// Initialize jupgrade class
		$jupgrade = new jUpgrade;
		$object = $jupgrade->getParams();
		
		echo json_encode($object);
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
} // end class
