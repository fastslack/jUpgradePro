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

/**
 * jUpgradePro Model
 *
 * @package		MatWare
 * @subpackage	com_jupgrade
 */
class jUpgradeProModel extends JModel
{
	/**
	 * @var      
	 * @since  3.0
	 */
	public $_db_old = null;


	function getOLDdb()
	{
		// Getting the parameters
		$params	= JComponentHelper::getParams('com_jupgradepro');

		// Creating old dabatase instance
		if ($params->get('method') == 'database') {

			$db_config['driver'] = $params->get('driver');
			$db_config['host'] = $params->get('hostname');
			$db_config['user'] = $params->get('username');
			$db_config['password'] = $params->get('password');
			$db_config['database'] = $params->get('database');
			$db_config['prefix'] = $params->get('prefix');

			return JDatabase::getInstance($db_config);
		}
	}

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
		if ($params->method == "rest" || $params->method == "rest_individual") {
			if ($params->rest_hostname == 'http://www.example.org/' || $params->rest_hostname == '' || 
					$params->rest_username == '' || $params->rest_password == '' ) {
				$message['number'] = 412;
				$message['text'] = JText::_('COM_JUPGRADEPRO_ERROR_REST_CONFIG');
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
		if ($params->method == 'rest' || $params->method == 'rest_individual') {
		
			jimport('joomla.http.http');

			// JHttp instance
			$http = new JHttp();		
		
			// Getting the rest data
			$data = $jupgrade->getRestData();
		
			// Getting the total
			$data['task'] = "cleanup";
			$response = $http->get($params->rest_hostname, $data);
		}

		// Get the prefix
		$prefix = $this->_db->getPrefix();

		// Set all status to 0 and clear state
		$query = "UPDATE jupgrade_steps SET cid = 0, status = 0, state = ''";
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
					$query = "UPDATE jupgrade_steps SET status = 1 WHERE name = '{$name}'";
					$this->_db->setQuery($query);
					$this->_db->query();

					if ($name == 'users') {
						$query = "UPDATE jupgrade_steps SET status = 1 WHERE name = 'arogroup'";
						$this->_db->setQuery($query);
						$this->_db->query();				

						$query = "UPDATE jupgrade_steps SET status = 1 WHERE name = 'usergroupmap'";
						$this->_db->setQuery($query);
						$this->_db->query();		
					}

				}
			}

			if ($k == 'skip_extensions') {
				if ($v == 1) {
					$query = "UPDATE jupgrade_steps SET status = 1 WHERE name = 'extensions'";
					$this->_db->setQuery($query);
					$this->_db->query();					
				}
			}
		}

		// Cleanup 3rd extensions
		$query = "DELETE FROM jupgrade_steps WHERE id > 18";
		$this->_db->setQuery($query);
		$this->_db->query();

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

		// Done checks
		$message['status'] = "OK";
		$message['number'] = 100;
		$message['text'] = "DONE";
		echo json_encode($message);
		exit;
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

}
