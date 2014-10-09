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

JLoader::register('jUpgrade', JPATH_LIBRARIES.'/jupgrade/jupgrade.compat.php');
JLoader::register('JUpgradepro', JPATH_LIBRARIES.'/jupgrade/jupgrade.class.php');
JLoader::register('JUpgradeproDriver', JPATH_LIBRARIES.'/jupgrade/jupgrade.driver.class.php');
JLoader::register('JUpgradeproStep', JPATH_LIBRARIES.'/jupgrade/jupgrade.step.class.php');

/**
 * JUpgradeproModelChecks Model
 *
 * @package		jUpgradePro
 */
class JUpgradeproModelChecks extends JModelLegacy
{
	/**
	 * Initial checks in jUpgradePro
	 *
	 * @return	none
	 * @since	1.2.0
	 */
	function checks()
	{
		// Loading the helper
		JLoader::import('helpers.jupgradepro', JPATH_COMPONENT_ADMINISTRATOR);

		// Getting the component parameter with global settings
		$params = JUpgradeproHelper::getParams();

		// Getting the step instance
		$step = JUpgradeproStep::getInstance(false);

		// Get the new site Joomla! version
		$v = new JVersion();
		$new_version = $v->RELEASE;

		// Define tables array
		$old_columns = array();

		// Check for bad configurations
		if ($params->method == "rest") {

			if (!isset($params->rest_hostname) || !isset($params->rest_username) || !isset($params->rest_password) || !isset($params->rest_key) ) {
				throw new Exception('COM_JUPGRADEPRO_ERROR_REST_CONFIG');
			}

			if ($params->rest_hostname == 'http://www.example.org/' || $params->rest_hostname == '' || 
					$params->rest_username == '' || $params->rest_password == '' || $params->rest_key == '') {
				throw new Exception('COM_JUPGRADEPRO_ERROR_REST_CONFIG');
			}

			// Checking the RESTful connection
			$driver = JUpgradeproDriver::getInstance();
			$code = $driver->requestRest('check');

			switch ($code) {
				case 401:
					throw new Exception('COM_JUPGRADEPRO_ERROR_REST_501');
				case 402:
					throw new Exception('COM_JUPGRADEPRO_ERROR_REST_502');
				case 403:
					throw new Exception('COM_JUPGRADEPRO_ERROR_REST_503');
				case 405:
					throw new Exception('COM_JUPGRADEPRO_ERROR_REST_505');
				case 406:
					throw new Exception('COM_JUPGRADEPRO_ERROR_REST_506');
			}

			// Get the database parameters
			$old_tables = json_decode($driver->requestRest('tableslist'));
			$old_columns = json_decode($driver->requestRest('tablescolumns'));
			$old_prefix = substr($old_tables[10], 0, strpos($old_tables[10], '_')+1);

			// Get the extension version
			$xmlfile = JPATH_ADMINISTRATOR.'/components/com_jupgradepro/jupgradepro.xml';
			$xml = JFactory::getXML($xmlfile);
			$ext_version = (string) $xml->version[0];

			// Compare the versions
			if (version_compare($code, $ext_version, '!=')) {
				throw new Exception('COM_JUPGRADEPRO_ERROR_VERSION_NOT_MATCH');
			}
		}

		// Check for bad configurations
		if ($params->method == "database") {
			if ($params->old_hostname == '' || $params->old_username == '' || $params->old_db == '' || $params->old_dbprefix == '' ) {
				throw new Exception('COM_JUPGRADEPRO_ERROR_DATABASE_CONFIG');
			}

			// Get the database parameters
			$old_tables = JUpgradepro::getInstance($step)->_driver->_db_old->getTableList();
			$old_columns = JUpgradepro::getInstance($step)->_driver->_db_old->getTableColumns('#__users');
			$old_prefix = $params->old_dbprefix;
		}

		// Check the old site Joomla! version
		$old_version = $this->checkOldVersion($old_tables, $old_prefix, $old_columns);

		// Check if the version is fine
		if (empty($old_version) || empty($new_version)) {
			throw new Exception('COM_JUPGRADEPRO_ERROR_NO_VERSION');
		}

		// Save the versions to database
		$this->setVersion('old', $old_version);
		$this->setVersion('new', $new_version);

		// Checking tables
		$tables = $this->_db->getTableList();

		// Check if the tables exists if not populate install.sql
		$tablesComp = array();
		$tablesComp[] = 'categories';
		$tablesComp[] = 'default_categories';
		$tablesComp[] = 'default_menus';
		$tablesComp[] = 'errors';
		$tablesComp[] = 'extensions';
		$tablesComp[] = 'extensions_tables';
		$tablesComp[] = 'files_images';
		$tablesComp[] = 'files_media';
		$tablesComp[] = 'files_templates';
		$tablesComp[] = 'menus';
		$tablesComp[] = 'modules';
		$tablesComp[] = 'steps';

		foreach ($tablesComp as $table) {
			if (!in_array($this->_db->getPrefix() . 'jupgradepro_' . $table, $tables)) {
				if (JUpgradeproHelper::isCli()) {
					print("\n\033[1;37m-------------------------------------------------------------------------------------------------\n");
					print("\033[1;37m|  \033[0;34m	Installing jUpgradePro tables\n");
				}

				JUpgradeproHelper::populateDatabase($this->_db, JPATH_COMPONENT_ADMINISTRATOR.'/sql/install.sql');
				break;
			}
		}

		// Define the message array
		$message = array();
		$message['status'] = "ERROR";

		// Getting the data
		$query = $this->_db->getQuery(true);
		$query->select('COUNT(id)');
		$query->from("`#__jupgradepro_steps`");
		$this->_db->setQuery($query);
		$nine = $this->_db->loadResult();

		if ($nine < 10) {
			throw new Exception('COM_JUPGRADEPRO_ERROR_TABLE_STEPS_NOT_VALID');
		}

		// Check safe_mode_gid
		if (@ini_get('safe_mode_gid') && @ini_get('safe_mode')) {
			throw new Exception('COM_JUPGRADEPRO_ERROR_DISABLE_SAFE_GID');
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

			if ($core == "skip_exte") {
				if ($v == 0) {
					$flag = true;
				}
			}
		}

		if ($flag === false) {
			throw new Exception('COM_JUPGRADEPRO_ERROR_SKIPS_ALL');				
		}		

		// Checking tables
		if ($params->skip_core_contents != 1 && $params->keep_ids == 1) {
			$query->clear();
			$query->select('COUNT(id)');
			$query->from("`#__content`");
			$this->_db->setQuery($query);
			$content_count = $this->_db->loadResult();

			if ($content_count > 0) {
				throw new Exception('COM_JUPGRADEPRO_ERROR_DATABASE_CONTENT');
			}
		}

		// Checking tables
		if ($params->skip_core_users != 1) {
			$query->clear();
			$query->select('COUNT(id)');
			$query->from("`#__users`");
			$this->_db->setQuery($query);
			$users_count = $this->_db->loadResult();

			if ($users_count > 1) {
				throw new Exception('COM_JUPGRADEPRO_ERROR_DATABASE_USERS');
			}
		}

		// Checking tables
		if ($params->skip_core_categories != 1 && $params->keep_ids == 1) {
			$query->clear();
			$query->select('COUNT(id)');
			$query->from("`#__categories`");
			$this->_db->setQuery($query);
			$categories_count = $this->_db->loadResult();

			if ($categories_count > 7) {
				throw new Exception('COM_JUPGRADEPRO_ERROR_DATABASE_CATEGORIES');
			}
		}

		// Change protected to $observers object to disable it
		// @@ Prevent Joomla! 'Application Instantiation Error' when try to call observers
		// @@ See: https://github.com/joomla/joomla-cms/pull/3408
		if (version_compare($new_version, '3.0', '>=')) {
			$file = JPATH_LIBRARIES.'/joomla/observer/updater.php';
			$read = JFile::read($file);
			$read = str_replace("call_user_func_array(\$eventListener, \$params)", "//call_user_func_array(\$eventListener, \$params)", $read);
			$read = JFile::write($file, $read);

			require_once($file);
		}

		// Done checks
		if (!JUpgradeproHelper::isCli())
			$this->returnError (100, 'DONE');
	}

	/**
	 * Set old site Joomla! version
	 *
	 * @return	none
	 * @since	3.2.0
	 */
	public function setVersion ($site, $version)
	{
		// Set the ols site version
		$query = $this->_db->getQuery(true);
		$query->update('#__jupgradepro_version')->set("{$site} = '{$version}'");
		$this->_db->setQuery($query)->execute();
	}

	/**
	 * Check the Joomla! version from tables
	 *
	 * @return	version	The Joomla! version
	 * @since	3.2.0
	 */
	public function checkOldVersion ($tables, $prefix, $columns)
	{
		// Trim the prefix value
		$prefix = trim($prefix);

		// Set the tables to search
		$j10 = "{$prefix}bannerfinish";
		$j15 = "{$prefix}core_acl_aro";
		$j25 = "{$prefix}update_categories";
		$j30 = "{$prefix}assets";
		$j31 = "{$prefix}content_types";
		$j32 = $j33 = "{$prefix}postinstall_messages";

		// Check the correct version
		if (in_array($j10, $tables))
		{
			$version = "1.0";
		}
		else if(in_array($j15, $tables))
		{
			$version = "1.5";
		}
		else if(in_array($j30, $tables) && !in_array($j25, $tables) && !in_array($j31, $tables))
		{
			$version = "3.0";
		}
		else if(in_array($j31, $tables) && !in_array($j32, $tables))
		{
			$version = "3.1";
		}
		else if(in_array($j33, $tables) && array_key_exists('requireReset', $columns))
		{
			$version = "3.3";
		}
		else if(in_array($j32, $tables))
		{
			$version = "3.2";
		}
		else if(in_array($j25, $tables) || in_array($j30, $tables))
		{
			$version = "2.5";
		}

		return $version;
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
		print(json_encode($message));
		exit;
	}

} // end class
