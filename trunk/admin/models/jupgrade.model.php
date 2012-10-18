<?php
/**
 * jUpgrade
 *
 * @version		$Id: jupgrade.model.php
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @copyright	Copyright 2006 - 2012 Matias Aguire. All rights reserved.
 * @license		GNU General Public License version 2 or later.
 * @author		Matias Aguirre <maguirre@matware.com.ar>
 * @link		http://www.matware.com.ar
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.class.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.category.class.php';
require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.users.class.php';

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
		if (@ini_get('safe_mode_gid')) {
			$this->returnError (411, 'COM_JUPGRADEPRO_ERROR_DISABLE_SAFE_GID');
		}

		// Check for bad configurations
		if ($params->method == "rest") {
			if ($params->rest_hostname == 'http://www.example.org/' || $params->rest_hostname == '' || 
					$params->rest_username == '' || $params->rest_password == '' || $params->rest_key == '') {
				$this->returnError (412, 'COM_JUPGRADEPRO_ERROR_REST_CONFIG');
			}

			// Checking the RESTful connection
			$code = $jupgrade->requestRest('check');

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
			if ($params->hostname == 'localhost' || $params->hostname == '' || 
					$params->username == '' || $params->password == '' || $params->database == '' || $params->prefix == '' ) {
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
			$jupgrade->requestRest('cleanup');
		}

		// Get the prefix
		$prefix = $this->_db->getPrefix();

		// Set all cid, status and cache to 0 
		$query = "UPDATE jupgrade_steps SET cid = 0, status = 0, cache = 0";
		$this->_db->setQuery($query);
		$this->_db->query();

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

		// Cleanup 3rd extensions
		$query = "DELETE FROM jupgrade_steps WHERE id > 18";
		//$this->runQuery ($query);

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

		// Delete uncategorized categories
		if ($params->skip_core_categories != 1) {
			$query = "DELETE FROM {$prefix}categories WHERE id > 1";
			$this->runQuery ($query);
		}

		// Change the id of the admin user
		if ($params->skip_core_users != 1) {
			$query = "UPDATE {$prefix}users SET id = 10 WHERE username = 'admin'";
			$this->runQuery ($query);

			$query = "UPDATE {$prefix}user_usergroup_map SET user_id = 10 WHERE group_id = 8";
			$this->runQuery ($query);
		}

		// Done checks
		if (class_exists('JVersion'))
			$this->returnError (100, 'DONE');
	}

	/**
	 * Get the next step
	 *
	 * @return   step object
	 */
	public function getStep($name = false, $json = true) {

		if (class_exists('JVersion')) {
			// Getting the parameters
			$this->params	= JComponentHelper::getParams('com_jupgradepro');
		}else{
			$this->params = new JRegistry(new JConfig);
		}

		$limit = $this->params->get('cache_limit');

		// Getting the steps
		$step = ($name != false) ? $this->_getStep($name) : $this->_getStep();

		if (empty($step)) {
			return false;
		}

		// Getting total
		$object = jUpgrade::getInstance($step);		
		$step->total = (int) $object->getTotal();

		$step->cid = $step->cid + 1;

		if ($step->total > $limit) {

			if ($step->cache == 0 && $step->status == 0) {

				$step->start = 1;
				$step->cache = round($step->total / $limit) - 1;
				$step->stop = $limit;
				$step->first = true;

				// updating the status flag
				$this->_updateStep($step, 1, $step->cache);

			} else if ($step->cache == 0 && $step->status == 1) { 

				$step->start = $this->_getStartValue($step, $limit);
				$step->next = true;

				// Mark if is the end of the step
				if ($step->name == $step->laststep) {
					$step->end = true;
				}

				// updating the status flag
				$this->_updateStep($step, 2, 0);

			} else if ($step->cache > 0) { 
		
				$step->start = $this->_getStartValue($step, $limit);
				$step->stop = ($step->start - 1) + $limit;

				if ($step->stop > $step->total) {
					$step->next = true;
				}else{
					$step->middle = true;
					unset($step->cid);
				}

				// updating the status flag
				$this->_updateStep($step, 1, $step->cache - 1);
			}

		}else if ($step->total == 0) {
			// updating the status flag
			$this->_updateStep($step, 2, 0);

			$step->start = 0;
			$step->stop = -1;

			// Mark if is the end of the step
			if ($step->name == $step->laststep) {
				$step->end = true;
			}

		}else{

			// Mark if is the end of the step
			if ($step->name == $step->laststep) {
				$step->end = true;
			}

			$step->start = 1;
			$step->next = true;

			// updating the status flag
			$this->_updateStep($step, 2, 0);
		}

		// Go to the next step
		if ($step->next == true) {
			$step->stop = $step->total;
		}

		unset($step->cache);
		unset($step->status);
		unset($step->laststep);
		unset($step->extension);

		// Encoding
		if ($json == true) {
			$return = json_encode($step);
		}else{
			$return = $step;
		}

		return($return);
	}


	/**
	 * Migrate
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	function getMigrateAll($table = false, $json = true) {

		$table = ($table == false) ? JRequest::getVar('table') : $table;

		$step = $this->_getStep($table);

		$process = jUpgrade::getInstance($step);
		$process->upgrade();

		unset($step->cache);
		unset($step->status);
		unset($step->laststep);
		unset($step->class);
		unset($step->extension);

		// Encoding
		if ($json == true) {
			$return = json_encode($step);
		}else{
			$return = $step;
		}

		return $return;
	}

	/**
	 * Migrate
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	function getMigrate($table = false, $json = true) {

		$table = ($table == false) ? JRequest::getVar('table') : $table;

		$step = $this->_getStep($table);
		$process = jUpgrade::getInstance($step);
		$process->upgrade();

		$step = $this->_getStep($table);

		// Getting the total
		$total = $process->getTotal();

		if ($total <= $step->cid) {
			$step->last = true;
			$this->_updateStep($step, 2, false, $total);
		}

		unset($step->cache);
		unset($step->status);
		unset($step->laststep);
		unset($step->class);
		unset($step->extension);

		// Encoding
		if ($json == true) {
			$return = json_encode($step);
		}else{
			$return = $step;
		}

		return $return;
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
	 * Getting the next step
	 *
	 * @return   step object
	 */
	public function _getStep($key = null) {
		// Select the steps
		if (isset($key)) {
			$query = "SELECT * FROM jupgrade_steps AS s WHERE s.name = '{$key}' ORDER BY s.id ASC LIMIT 1";
		}else{
			$query = "SELECT * FROM jupgrade_steps AS s WHERE s.status != 2 ORDER BY s.id ASC LIMIT 1";
		}

		$this->_db->setQuery($query);
		$step = $this->_db->loadObject();

		if ($step == '') {
			return false;
		}

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		// Select last step
		$query = "SELECT name FROM jupgrade_steps WHERE status = 0 ORDER BY id DESC LIMIT 1";
		$this->_db->setQuery($query);
		$step->laststep = $this->_db->loadResult();

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		// Check if steps is an object
		if (is_object($step)) {
		  return $step;
		}else{
			return false;
		}
	}

	/**
	 * updateStep
	 *
	 * @return	none
	 * @since	2.5.2
	 */
	public function _updateStep($step, $status = 1, $cache = 0, $cid = false) {

		if ($cache !== false) {
			$cache = ", cache = {$cache}";
		}
		if ($cid !== false) {
			$cid = ", cid = {$cid}";
		}

		// updating the status flag
		$query = "UPDATE jupgrade_steps SET status = {$status} {$cache} {$cid}"
		." WHERE name = '{$step->name}'";
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
	 * Get a single row
	 *
	 * @return   step object
	 */
	public function requestRest($task = 'total', $table = false) {

		// Initialize jupgrade class
		$jupgrade = new jUpgrade;

		// JHttp instance
		jimport('joomla.http.http');
		$http = new JHttp();
		$data = $jupgrade->getRestData();
		
		// Getting the total
		$data['task'] = $task;
		$data['table'] = $table;
		$request = $http->get($jupgrade->params->get('rest_hostname'), $data);
		return $request->body;
	}

	function _getStartValue($step, $limit) {
		$orig_cache = round($step->total / $limit );
		$prod = ($orig_cache - $step->cache);
		return ($limit * $prod) + 1;
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
