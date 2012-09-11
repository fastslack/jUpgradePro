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

require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/jupgrade.model.php';

/**
 * Ajax Model
 *
 * @package		MatWare
 * @subpackage	com_jupgrade
 */
class jUpgradeProModelAjax extends jUpgradeProModel
{
	/**
	 * Get the next step
	 *
	 * @return   step object
	 */
	public function getStep() {

		$step = $this->_getStep(JRequest::getVar('type'));

		// Require the file
		if (JFile::exists(JPATH_COMPONENT.'/includes/migrate_'.$step->name.'.php')) {
			require_once JPATH_COMPONENT.'/includes/migrate_'.$step->name.'.php';
		}

		// Getting the class name
		$class = $step->class;

		// Migrate the process.
		$jupgrade = new $class($step);

		$step->total =  $jupgrade->getSourceDatabaseTotal();

		// updating the status flag
		$this->_updateStep($step);

		// Encoding
		$json = json_encode($step);

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

		// Require the file
		if (JFile::exists(JPATH_COMPONENT.'/includes/migrate_'.$step->name.'.php')) {
			require_once JPATH_COMPONENT.'/includes/migrate_'.$step->name.'.php';
		}

		// Getting the class name
		$class = $step->class;

		// Migrate the process.
		$process = new $class($step);
		$process->upgrade();

		$this->_updateStep($step);

		$step->status = "OK";
		$step->text = "DONE";

		echo json_encode((array)$step);
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
			$query = "SELECT * FROM jupgrade_steps AS s WHERE s.status != 1 ORDER BY s.id ASC LIMIT 1";
		}

		$this->_db->setQuery($query);
		$step = $this->_db->loadObject();

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
