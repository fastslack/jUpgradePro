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
class jUpgradeProHelper
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
		return (PHP_SAPI === 'cli') ? true : false;
	}

	/**
	 * Getting the parameters 
	 *
	 * @return  bool	True on success
	 *
	 * @since   3.0.0
	 */
	public static function getParams()
	{
		// Getting the params and Joomla version web and cli
		if (!jUpgradeProHelper::isCli()) {
			$params	= JComponentHelper::getParams('com_jupgradepro');
		}else{
			$params = new JRegistry(new JConfig);
		}

		return $params;
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

			$file_core = JPATH_COMPONENT_ADMINISTRATOR."/includes/core/{$name}.php";
			$file_checks = JPATH_COMPONENT_ADMINISTRATOR."/includes/extensions/{$name}.php";

			// Require the file
			if (JFile::exists($file_core)) {

				if ($name = 'users' || $name = 'usergroupmap' || $name = 'arogroup') {
					JLoader::register("jUpgradeUsersDefault", JPATH_COMPONENT_ADMINISTRATOR."/includes/jupgrade.users.class.php");
				}

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
	public static function getTotal($step)
	{
		JLoader::register('jUpgradeDriver', JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.driver.class.php');

		$driver = JUpgradeDriver::getInstance($step);

		return $driver->getTotal();
	}
}
