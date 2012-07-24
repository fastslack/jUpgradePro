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
 * Rest Model
 *
 * @package		MatWare
 * @subpackage	com_jupgrade
 */
class jUpgradeProModelRest extends JModel
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
	 * Migrate
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	function getMigrate() {

		// jUpgrade class
		//$jupgrade = new jUpgrade;

		$step = $this->_getStep();

		// TODO: Error handler

		$this->_processStep($step);

		$message['status'] = "OK";
		$message['step'] = $step->id;
		$message['name'] = $step->name;
		$message['title'] = $step->title;
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

				break;
			case 'arogroup':

				// Migrate the usergroups.
				$u2 = new jUpgradeUsergroups($step);
				$u2->upgrade();
				break;
			case 'usergroupmap':
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


		$this->_updateStep($step);

	} // end method

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
