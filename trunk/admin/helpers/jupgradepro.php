<?php
/**
 * jUpgrade
 *
 * @version		  $Id$
 * @package		  MatWare
 * @subpackage	com_jupgrade
 * @author      Matias Aguirre <maguirre@matware.com.ar>
 * @link        http://www.matware.com.ar
 * @copyright		Copyright 2004 - 2013 Matias Aguirre. All rights reserved.
 * @license		  GNU General Public License version 2 or later; see LICENSE.txt
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
	 * Getting the total 
	 *
	 * @return  int	The total number
	 *
	 * @since   3.0.0
	 */
	public static function getTotal()
	{

/*
		// Getting the params and Joomla version web and cli
		if (!jUpgradeProHelper::isCli()) {
			$params	= JComponentHelper::getParams('com_jupgradepro');
		}else{
			$params = new JRegistry(new JConfig);
		}
*/
		return $total;
	}
}
