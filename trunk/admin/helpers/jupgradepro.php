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
defined('_JEXEC') or die;

/**
 * jUpgradePro Helper
 *
 * @package  Joomla.Administrator
 * @since    3.0.0
 */
class JUpgradeproHelper
{
	/**
	 * Check if the class is called from CLI
	 *
	 * @return  void	True if is running from cli
	 *
	 * @since   3.0.0
	 */
	public static function isCli()
	{
		return (php_sapi_name() === 'cli') ? true : false;
	}

	/**
	 * Getting the parameters 
	 *
	 * @return  bool	True on success
	 *
	 * @since   3.0.0
	 */
	public static function getParams($object = true)
	{
		// Getting the type of interface between web server and PHP
		$sapi = php_sapi_name();

		// Getting the params and Joomla version web and cli
		if ($sapi != 'cli') {
			$params	= JComponentHelper::getParams('com_jupgradepro');
		}else if ($sapi == 'cli') {
			$params = new JRegistry(new JConfig);
		}

		return ($object === true) ? $params->toObject() : $params;
	}

	/**
	 * Get the Joomla! version
	 *
	 * @return  string	The Joomla! version
	 *
	 * @since   3.2.0
	 */
	public static function getVersion($site)
	{
		$db = JFactory::getDBO();

		$query = $db->getQuery(true);
		$query->select($site);
		$query->from("`#__jupgradepro_version`");
		$query->limit(1);
		$db->setQuery($query);

		try {
			return $db->loadResult();
		} catch (RuntimeException $e) {
			throw new RuntimeException($e->getMessage());
		}
	}

	/**
	 * Require the correct file from step
	 *
	 * @return  int	The total number
	 *
	 * @since   3.0.0
	 */
	public static function requireClass($name, $xmlpath, $class)
	{
		if (!empty($name)) {

			// Loading the JFile class
			jimport('joomla.filesystem.file');

			$schema = JUpgradeproHelper::getVersion('old');

			$file_core = JPATH_COMPONENT_ADMINISTRATOR."/includes/schemas/{$schema}/{$name}.php";
			$file_checks = JPATH_COMPONENT_ADMINISTRATOR."/includes/extensions/{$name}.php";

			// Require the file
			if (JFile::exists($file_core)) {
				JLoader::register($class, $file_core);
			// Checks
			}else if (JFile::exists($file_checks)) {
				JLoader::register($class, $file_checks);
			// 3rd party extensions
			}else if (isset($xmlpath)) {

				$phpfile_strip = JFile::stripExt(JPATH_PLUGINS."/jupgradepro/".$xmlpath);

				if (JFile::exists("{$phpfile_strip}.php")) {
					JLoader::register($class, "{$phpfile_strip}.php");
				}
			}
		}
	}

	/**
	 * Getting the total 
	 *
	 * @return  int	The total number
	 *
	 * @since   3.0.0
	 */
	public static function getTotal(JUpgradeproStep $step = null)
	{
		JLoader::register('JUpgradeproDriver', JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.driver.class.php');

		$driver = JUpgradeproDriver::getInstance($step);

		return $driver->getTotal();
	}

	/**
	 * Populate a sql file
	 *
	 * @return  bool	True if succeful
	 *
	 * @since   3.1.0
	 */
	public static function populateDatabase(& $db, $sqlfile)
	{
		if( !($buffer = file_get_contents($sqlfile)) )
		{
			return -1;
		}

		$queries = $db->splitSql($buffer);

		foreach ($queries as $query)
		{
			$query = trim($query);
			if ($query != '' && $query {0} != '#')
			{
				$db->setQuery($query);
				try {
					$db->query();
				} catch (Exception $e) {
					throw new Exception($e->getMessage());
				}
			}
		}

		return true;
	}
}
