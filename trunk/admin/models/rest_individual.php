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
class jUpgradeProModelRest_individual extends JModel
{
	/**
	 * Get the next step
	 *
	 * @return   step object
	 */
	public function getStep() {
	
		// Initialize jupgrade class
		$jupgrade = new jUpgrade;

		// Select the steps
		$query = "SELECT * FROM jupgrade_steps AS s WHERE s.status != 1 ORDER BY s.id ASC LIMIT 1";
		$this->_db->setQuery($query);
		$step = $this->_db->loadObject();

		// updating the status flag
		$query = "UPDATE jupgrade_steps SET status = 1"
		." WHERE name = '{$step->name}'";
		$this->_db->setQuery($query);
		$this->_db->query();

		// Check for query error.
		$error = $this->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		// JHttp instance
		jimport('joomla.http.http');
		$http = new JHttp();
		$data = $jupgrade->getRestData();
		
		// Getting the total
		$data['task'] = "total";
		$data['type'] = $step->name;
		$total = $http->get($jupgrade->params->get('rest_hostname'), $data);
		$step->total = (int) $total->body;
	
		$json = json_encode($step);

		return($json);
	}

	/**
	 * Get a single row
	 *
	 * @return   step object
	 */
	public function getRow() {

		// Initialize jupgrade class
		$jupgrade = new jUpgrade;

		// JHttp instance
		jimport('joomla.http.http');
		$http = new JHttp();
		$data = $jupgrade->getRestData();
		
		// Getting the total
		$data['task'] = "row";
		$data['type'] = JRequest::getVar('type');
		
		$response = $http->get($jupgrade->params->get('rest_hostname'), $data);
		if ($response->body != '') {
			$row = json_decode($response->body, true);
		}	
	
		$json = json_encode($row);

		return($json);
	}

	/**
	 * Migrate
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	function getMigrate() {

		$step = $this->_getStep(JRequest::getVar('type'));
		//print_r($step);

		// TODO: Error handler
		$this->_processStep($step);

		$message['status'] = "OK";
		$message['step'] = $step->id;
		$message['name'] = $step->name;
		$message['title'] = $step->title;
		$message['class'] = $step->class;
		$message['category'] = $step->category;
		$message['text'] = 'DONE';
		echo json_encode($message);
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
			case 'extensions':
				require_once JPATH_COMPONENT.'/includes/jupgrade.category.class.php';
				require_once JPATH_COMPONENT.'/includes/jupgrade.extensions.class.php';				
	
				// Get jUpgradeExtensions instance
				$extension = jUpgradeExtensions::getInstance($step);
				$success = $extension->upgrade();

				break;
			default:
				// Getting the class name
				$class = $step->class;

				// Migrate the process.
				$process = new $class($step);
				$process->upgrade();
		}

		$this->_updateStep($step);

	} // end method

	/**
	 * Getting the next step
	 *
	 * @return   step object
	 */
	public function _getStep($key = null) {
		// Initialize jupgrade class
		$jupgrade = new jUpgrade;

		// Select the steps
		if (isset($key)) {
			$query = "SELECT * FROM jupgrade_steps AS s WHERE s.name = '{$key}' ORDER BY s.id ASC LIMIT 1";
		}else{
			$query = "SELECT * FROM jupgrade_steps AS s WHERE s.status != 1 ORDER BY s.id ASC LIMIT 1";
		}

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
