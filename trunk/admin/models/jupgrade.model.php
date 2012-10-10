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
require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.database.class.php';

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
			$message['number'] = 401;
			$message['text'] = JText::_("COM_JUPGRADEPRO_ERROR_TABLE_CAT");
			echo json_encode($message);
			exit;
		}
		
		if (!in_array('jupgrade_menus', $tables)) {
			$message['number'] = 402;
			$message['text'] = JText::_("COM_JUPGRADEPRO_ERROR_TABLE_MENUS");
			echo json_encode($message);
			exit;
		}
		
		if (!in_array('jupgrade_modules', $tables)) {
			$message['number'] = 403;
			$message['text'] = JText::_("COM_JUPGRADEPRO_ERROR_TABLE_MODULES");
			echo json_encode($message);
			exit;
		}
		
		if (!in_array('jupgrade_steps', $tables)) {
			$message['number'] = 404;
			$message['text'] = JText::_("COM_JUPGRADEPRO_ERROR_TABLE_STEPS_NO_EXISTS");
			echo json_encode($message);
			exit;
		}		

		// Check if jupgrade_steps is fine
		$query = "SELECT COUNT(id) FROM `jupgrade_steps`";
		$jupgrade->_db->setQuery($query);
		$nine = $jupgrade->_db->loadResult();
		
		if ($nine < 10) {
			$message['number'] = 405;
			$message['text'] = JText::_("COM_JUPGRADEPRO_ERROR_TABLE_STEPS_NOT_VALID");
			echo json_encode($message);
			exit;
		}
	
		// Check safe_mode_gid
		if (@ini_get('safe_mode_gid')) {
			$message['number'] = 411;
			$message['text'] = JText::_('COM_JUPGRADEPRO_ERROR_DISABLE_SAFE_GID');
			echo json_encode($message);
			exit;
		}

		// Check for bad configurations
		if ($params->method == "rest") {
			if ($params->rest_hostname == 'http://www.example.org/' || $params->rest_hostname == '' || 
					$params->rest_username == '' || $params->rest_password == '' || $params->rest_key == '') {
				$message['number'] = 412;
				$message['text'] = JText::_('COM_JUPGRADEPRO_ERROR_REST_CONFIG');
				echo json_encode($message);
				exit;
			}

			// Checking the RESTful connection
			$code = $jupgrade->requestRest('check');

			switch ($code) {
				case 401:
					$message['number'] = 501;
					$message['text'] = JText::_('COM_JUPGRADEPRO_ERROR_REST_501');
					echo json_encode($message);
					exit;
				case 402:
					$message['number'] = 502;
					$message['text'] = JText::_('COM_JUPGRADEPRO_ERROR_REST_502');
					echo json_encode($message);
					exit;
				case 403:
					$message['number'] = 503;
					$message['text'] = JText::_('COM_JUPGRADEPRO_ERROR_REST_503');
					echo json_encode($message);
					exit;
				case 405:
					$message['number'] = 505;
					$message['text'] = JText::_('COM_JUPGRADEPRO_ERROR_REST_505');
					echo json_encode($message);
					exit;
				case 406:
					$message['number'] = 506;
					$message['text'] = JText::_('COM_JUPGRADEPRO_ERROR_REST_506');
					echo json_encode($message);
					exit;
			}
		}

		// Check for bad configurations
		if ($params->method == "database") {
			if ($params->hostname == 'localhost' || $params->hostname == '' || 
					$params->username == '' || $params->password == '' || $params->database == '' || $params->prefix == '' ) {
				$message['number'] = 413;
				$message['text'] = JText::_('COM_JUPGRADEPRO_ERROR_DATABASE_CONFIG');
				echo json_encode($message);
				exit;
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
			$message['number'] = 414;
			$message['text'] = JText::_('COM_JUPGRADEPRO_ERROR_SKIPS_ALL');
			echo json_encode($message);
			exit;			
		}		

		// Checking tables
		$query = "SELECT COUNT(id) FROM #__categories";
		$jupgrade->_db->setQuery($query);
		$categories_count = $jupgrade->_db->loadResult();

		if ($categories_count > 7) {
			$message['number'] = 415;
			$message['text'] = JText::_('COM_JUPGRADEPRO_ERROR_DATABASE_CATEGORIES');
			echo json_encode($message);
			exit;
		}

		// Checking tables
		$query = "SELECT COUNT(id) FROM #__content";
		$jupgrade->_db->setQuery($query);
		$content_count = $jupgrade->_db->loadResult();


		if ($content_count > 0) {
			$message['number'] = 416;
			$message['text'] = JText::_('COM_JUPGRADEPRO_ERROR_DATABASE_CONTENT');
			echo json_encode($message);
			exit;
		}

		// Checking tables
		$query = "SELECT COUNT(id) FROM #__users";
		$jupgrade->_db->setQuery($query);
		$users_count = $jupgrade->_db->loadResult();

		if ($users_count > 1) {
			$message['number'] = 417;
			$message['text'] = JText::_('COM_JUPGRADEPRO_ERROR_DATABASE_USERS');
			echo json_encode($message);
			exit;
		}

		// Done checks
		$message['status'] = "OK";
		$message['number'] = 100;
		$message['text'] = "DONE";
		echo json_encode($message);
		exit;
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
					$this->_db->setQuery($query);
					$this->_db->query();

					if ($name == 'users') {
						$query = "UPDATE jupgrade_steps SET status = 2 WHERE name = 'arogroup'";
						$this->_db->setQuery($query);
						$this->_db->query();				

						$query = "UPDATE jupgrade_steps SET status = 2 WHERE name = 'usergroupmap'";
						$this->_db->setQuery($query);
						$this->_db->query();		
					}

				}
			}

			if ($k == 'skip_extensions') {
				if ($v == 1) {
					$query = "UPDATE jupgrade_steps SET status = 2 WHERE name = 'extensions'";
					$this->_db->setQuery($query);
					$this->_db->query();					
				}
			}
		}

		// Cleanup 3rd extensions
		$query = "DELETE FROM jupgrade_steps WHERE id > 18";
		$this->_db->setQuery($query);
		//$this->_db->query();

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
			$this->_db->setQuery($query);
			$this->_db->query();
		}

		// Check for query error.
		$error = $this->_db->getErrorMsg();
		if ($error) {
			throw new Exception($error);
		}

		// Delete main menu
		$query = "DELETE FROM {$this->_db->getPrefix()}menu WHERE id > 1";
		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for query error.
		$error = $this->_db->getErrorMsg();
		if ($error) {
			throw new Exception($error);
		}

		// Insert needed value
		$query = "INSERT INTO `jupgrade_menus` ( `old`, `new`) VALUES ( 0, 0)";
		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		// Insert uncategorized id
		$query = "INSERT INTO `jupgrade_categories` (`old`, `new`) VALUES (0, 2)";
		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		// Delete uncategorized categories
		$query = "DELETE FROM {$prefix}categories WHERE id > 1";
		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		// Change the id of the admin user
		$query = "UPDATE {$prefix}users SET id = 10 WHERE username = 'admin'";
		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		$query = "UPDATE {$prefix}user_usergroup_map SET user_id = 10 WHERE group_id = 8";
		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		// Done checks
		$message['status'] = "OK";
		$message['number'] = 100;
		$message['text'] = "DONE";
		echo json_encode($message);
		exit;
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
				//echo "[[1]]\n";

				$step->start = 1;

				$step->cache = round($step->total / $limit) - 1;
				$step->stop = $limit;

				$step->first = true;

				// updating the status flag
				$this->_updateStep($step, 1, $step->cache);

				//echo $step->div;
			} else if ($step->cache == 1 && $step->status == 1) { 
				//echo "[[2]]\n";

				$step->start = $this->_getStartValue($step, $limit);

				$step->stop = $step->total;

				$step->next = true;

				// Mark if is the end of the step
				if ($step->name == $step->laststep) {
					$step->end = true;
				}

				// updating the status flag
				$this->_updateStep($step, 2, 0);

			} else if ($step->cache == 0 && $step->status == 1) { 
				//echo "[[3]]\n";

				$step->start = $this->_getStartValue($step, $limit);

				$step->stop = $step->total;

				$step->next = true;

				// Mark if is the end of the step
				if ($step->name == $step->laststep) {
					$step->end = true;
				}

				// updating the status flag
				$this->_updateStep($step, 2, 0);

			} else if ($step->cache > 0) { 
				//echo "[[4]]\n";
		
				$step->start = $this->_getStartValue($step, $limit);

				$step->stop = ($step->start - 1) + $limit;

				$step->middle = true;

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
			$step->stop = $step->total;

			// updating the status flag
			$this->_updateStep($step, 2, 0);
		}

		//echo "\n\n";
		//$stepo = $this->_getStep();
		//print_r($stepo);
		//echo "\n\n";

		unset($step->cid);
		unset($step->cache);
		unset($step->status);
		unset($step->laststep);
		//unset($step->class);
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

		$total = $process->getTotal();

		$step->cid = $step->cid + 1;
		$this->_updateStep($step, 1, false, $step->cid);

		if ($total == $step->cid) {
			$step->last = true;
			$this->_updateStep($step, 2, false, false);
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
	 * Migrate
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	function getTemplates() {

		// Require the file
		require_once JPATH_COMPONENT.'/includes/templates_db.php';

		// Migration 3rd party templates
		$templates = new jUpgradeTemplates;

		if ($templates->upgrade()) {
			$message['status'] = "OK";
			$message['number'] = 100;
			$message['text'] = "DONE";
		}

		echo json_encode($message);
	}

	/**
	 * Migrate
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	function getTemplatesfiles() {

		// Require the file
		require_once JPATH_COMPONENT.'/includes/templates_files.php';

		// Migration 3rd party templates
		$templates = new jUpgradeTemplatesFiles;

		if ($templates->upgrade()) {
			$message['status'] = "OK";
			$message['number'] = 100;
			$message['text'] = "DONE";
		}

		echo json_encode($message);
	}

	/**
	 * Migrate
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	function getFiles() {

		// Require the file
		require_once JPATH_COMPONENT.'/includes/migrate_files.php';

		// Migration 3rd party templates
		$templates = new jUpgradeFiles;

		if ($templates->upgrade()) {
			$message['status'] = "OK";
			$message['number'] = 100;
			$message['text'] = "DONE";
		}

		echo json_encode($message);
	}

}
