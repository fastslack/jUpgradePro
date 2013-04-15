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
class jUpgradeProModelChecks extends JModelLegacy
{
	/**
	 * Initial checks in jUpgradePro
	 *
	 * @return	none
	 * @since	1.2.0
	 */
	function checks()
	{
		// Initialize jUpgradePro class
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

		// Getting the data
		$query = $this->_db->getQuery(true);
		$query->select('COUNT(id)');
		$query->from("`jupgrade_steps`");
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
		if ($params->skip_core_contents != 1) {
			$query->clear();
			$query->select('COUNT(id)');
			$query->from("`#__content`");
			$jupgrade->_db->setQuery($query);
			$content_count = $jupgrade->_db->loadResult();


			if ($content_count > 0) {
				$this->returnError (416, 'COM_JUPGRADEPRO_ERROR_DATABASE_CONTENT');
			}
		}

		// Checking tables
		if ($params->skip_core_users != 1) {
			$query->clear();
			$query->select('COUNT(id)');
			$query->from("`#__users`");
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
