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
/**
 * Upgrade class for Weblinks
 *
 * This class takes the weblinks from the existing site and inserts them into the new site.
 *
 * @since	3.0.0
 */
class JUpgradeproExtensionsModules extends JUpgradepro
{
	/**
	 * Setting the conditions hook
	 *
	 * @return	void
	 * @since	3.0.0
	 * @throws	Exception
	 */
	public static function getConditionsHook()
	{
		$conditions = array();
		
		$conditions['as'] = "m";
		
		$conditions['select'] = '`name`, `element`, `type`, `folder`, `client_id`, `ordering`, `params`';

		$where = array();
		$where[] = "m.type = 'module'";
		$where[] = "m.element   NOT   IN   ('mod_mainmenu',   'mod_login',   'mod_popular',   'mod_latest',   'mod_stats',   'mod_unread',   'mod_online',   'mod_toolbar',   'mod_quickicon',   'mod_logged',   'mod_footer',   'mod_menu',   'mod_submenu',   'mod_status',   'mod_title',   'mod_login' )";
		
		$conditions['where'] = $where;
	
		return $conditions;
	}
}
