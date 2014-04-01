<?php
/**
* jUpgradePro
*
* @version $Id:
* @package jUpgradePro
* @copyright Copyright (C) 2004 - 2014 Matware. All rights reserved.
* @author Matias Aguirre
* @email maguirre@matware.com.ar
* @link http://www.matware.com.ar/
* @license GNU General Public License version 2 or later; see LICENSE
*/
/**
 * Upgrade class for MenusTypes
 *
 * This class takes the menus from the existing site and inserts them into the new site.
 *
 * @since	0.4.5
 */
class JUpgradeproMenusTypes extends JUpgradepro
{
/*
SELECT id, menutype, name, type, published FROM jos_menu
WHERE menutype != 'mainmenu'
GROUP BY menutype
*/
	/**
	 * Setting the conditions hook
	 *
	 * @return	array
	 * @since	3.0.0
	 * @throws	Exception
	 */
	public static function getConditionsHook()
	{
		$conditions = array();
		
		$conditions['as'] = "m";
		
		$conditions['select'] = 'm.menutype, m.menutype AS title';

		$conditions['group_by'] = "m.menutype";

		$conditions['where'] = array();
		$conditions['where'][] = "m.menutype != 'mainmenu'";

		$conditions['order'] = "m.menutype ASC";
		
		return $conditions;
	}
}
