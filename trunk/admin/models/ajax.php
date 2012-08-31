<?php
/**
 * jUpgrade
 *
 * @version		$Id: ajax.php
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @copyright	Copyright 2006 - 2011 Matias Aguire. All rights reserved.
 * @license		GNU General Public License version 2 or later.
 * @author		Matias Aguirre <maguirre@matware.com.ar>
 * @link		http://www.matware.com.ar
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.class.php';

/**
 * Ajax Model
 *
 * @package		MatWare
 * @subpackage	com_jupgrade
 */
class jUpgradeProModelAjax extends JModelLegacy
{
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

		// Checking tables
		$query = "SELECT COUNT(id) FROM #__categories";
		$jupgrade->_db->setQuery($query);
		$categories_count = $jupgrade->_db->loadResult();


		if ($categories_count > 7) {
			$message['number'] = 414;
			$message['text'] = JText::_('COM_JUPGRADEPRO_ERROR_DATABASE_CATEGORIES');
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
		$query = "UPDATE jupgrade_steps SET cid = 0, status = 0, state = '' WHERE name != 'extensions'";
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
	 * Migrate
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	function getMigrate() {

		// jUpgrade class
		$jupgrade = new jUpgrade;

		$step = $this->_getStep();

		// TODO: Error handler
		$this->_processStep($step);

		$this->_updateStep($step);

		$message['status'] = "OK";
		$message['step'] = $step->id;
		$message['name'] = $step->name;
		$message['text'] = 'DONE';
		echo json_encode($message);

	}

	/**
	 * New processStep
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	public function _processStep ($step)
	{
		// Require the file
		if (JFile::exists(JPATH_COMPONENT.'/includes/migrate_'.$step->name.'.php')) {
			require_once JPATH_COMPONENT.'/includes/migrate_'.$step->name.'.php';
		}

		switch ($step->name)
		{
			case 'users':
				// Migrate the users.
				$u1 = new jUpgradeUsers($step);
				$u1->upgrade();

				// Migrate the usergroups.
				$u2 = new jUpgradeUsergroups($step);
				$u2->upgrade();

				// Migrate the user-to-usergroup mapping.
				$u2 = new jUpgradeUsergroupMap($step);
				$u2->upgrade();

				break;
			case 'categories':
				// Migrate the Categories.
				$categories = new jUpgradeCategories($step);
				$categories->upgrade();

				break;
			case 'content':
				// Migrate the Content.
				$content = new jUpgradeContent($step);
				$content->upgrade();

				// Migrate the Frontpage Content.
				$frontpage = new jUpgradeContentFrontpage($step);
				$frontpage->upgrade();

				break;
			case 'menus':
				// Migrate the menu.
				$menu = new jUpgradeMenu;
				$menu->upgrade();

				// Migrate the menu types.
				$menutypes = new jUpgradeMenuTypes($step);
				$menutypes->upgrade();

				break;
			case 'modules':
				// Migrate the Modules.
				$modules = new jUpgradeModules($step);
				$modules->upgrade();

				// Migrate the Modules Menus.
				$modulesmenu = new jUpgradeModulesMenu($step);
				$modulesmenu->upgrade();

				break;
			case 'banners':
				// Migrate the categories of banners.
				$cat = new jUpgradeCategory($step);
				$cat->section = "com_banner";
				$cat->upgrade();

				// Migrate the banners.
				$banners = new jUpgradeBanners($step);
				$banners->upgrade();

				break;
			case 'contacts':
				// Migrate the categories of contacts.
				$cat = new jUpgradeCategory($step);
				$cat->section = "com_contact_details";
				$cat->upgrade();

				// Migrate the contacts.
				$contacts = new jUpgradeContacts($step);
				$contacts->upgrade();

				break;
			case 'newsfeeds':
				// Migrate the categories of newsfeeds.
				$cat = new jUpgradeCategory($step);
				$cat->section = "com_newsfeeds";
				$cat->upgrade();

				// Migrate the newsfeeds.
				$newsfeeds = new jUpgradeNewsfeeds;
				$newsfeeds->upgrade();

				break;
			case 'weblinks':
				// Migrate the categories of weblinks.
				$cat = new jUpgradeCategory($step);
				$cat->section = "com_weblinks";
				$cat->upgrade();

				// Migrate the weblinks.
				$weblinks = new jUpgradeWeblinks($step);
				$weblinks->upgrade();

				break;
			case 'extensions':
				require_once JPATH_COMPONENT.'/includes/jupgrade.category.class.php';
				require_once JPATH_COMPONENT.'/includes/jupgrade.extensions.class.php';				
	
				// Get jUpgradeExtensions instance
				$extension = jUpgradeExtensions::getInstance($step);
				$success = $extension->upgrade();

				break;
		}

	} // end method

	/**
	 * Migrate
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	function getExtensions() {

		// jUpgrade class
		$jupgrade = new jUpgrade;

		$step = $this->_getStep();

		// TODO: Error handler

		$this->_processExtensionStep($step);

		// Select the steps
		$query = "SELECT * FROM jupgrade_steps AS s WHERE s.extension = 1 ORDER BY s.id DESC LIMIT 1";
		$jupgrade->_db->setQuery($query);
		$lastid = $jupgrade->_db->loadResult();

		// Check for query error.
		$error = $jupgrade->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		$message['status'] = "OK";
		$message['step'] = $step->id;
		$message['name'] = $step->name;
		$message['lastid'] = $lastid;
		$message['text'] = 'DONE';
		echo json_encode($message);

	}

	/**
	 * Getting the next step
	 *
	 * @return   step object
	 */
	public function _getStep() {
		// Initialize jupgrade class
		$jupgrade = new jUpgrade;

		// Select the steps
		$query = "SELECT * FROM jupgrade_steps AS s WHERE s.status != 1 ORDER BY s.id ASC LIMIT 1";
		$jupgrade->_db->setQuery($query);
		$step = $jupgrade->_db->loadObject();

		// Check for query error.
		$error = $jupgrade->_db->getErrorMsg();

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
	public function _updateStep($step) {
		// Initialize jupgrade class
		$jupgrade = new jUpgrade;

		// updating the status flag
		$query = "UPDATE jupgrade_steps SET status = 1"
		." WHERE name = '{$step->name}'";
		$jupgrade->_db->setQuery($query);
		$jupgrade->_db->query();

		// Check for query error.
		$error = $jupgrade->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		return true;
	}

	/**
	 * processStep
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	public function _processExtensionStep ($step)
	{
		// Require the file
		require_once JPATH_COMPONENT.'/includes/jupgrade.extensions.class.php';	

		// Get jUpgradeExtensions instance
		$extension = jUpgradeExtensions::getInstance($step);
		$success = $extension->upgradeExtension();

		if ($extension->isReady())
		{
			$this->_updateStep($step);
		}
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
