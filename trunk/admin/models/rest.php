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
 * Rest Model
 *
 * @package		MatWare
 * @subpackage	com_jupgrade
 */
class jUpgradeProModelRest extends jUpgradeProModel
{
	/**
	 * Get the next step
	 *
	 * @return   step object
	 */
	public function getStep() {

		// Getting the steps
		$step = $this->_getStep();

		// Initialize jupgrade class
		$jupgrade = new jUpgrade;

		// JHttp instance
		jimport('joomla.http.http');
		$http = new JHttp();
		$data = $jupgrade->getRestData();
		
		// Getting the total
		$data['task'] = "total";
		$data['table'] = $step->name;
		$total = $http->get($jupgrade->params->get('rest_hostname'), $data);
		$step->total = (int) $total->body;

		if ($step->name == $step->laststep) {
			$step->end = true;
		}

		// updating the status flag
		$this->_updateStep($step);

		// Encoding
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
		$data['table'] = JRequest::getVar('table');
		
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
}
