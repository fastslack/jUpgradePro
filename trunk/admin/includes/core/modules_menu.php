<?php
/**
 * jUpgrade
 *
 * @version		  $Id$
 * @package		  MatWare
 * @subpackage	com_jupgrade
 * @author      Matias Aguirre <maguirre@matware.com.ar>
 * @link        http://www.matware.com.ar
 * @copyright		Copyright 2006 - 2011 Matias Aguire. All rights reserved.
 * @license		  GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Upgrade class for modules menu
 *
 * This class takes the modules from the existing site and inserts them into the new site.
 *
 * @since	0.4.5
 */
class jUpgradeModulesMenu extends jUpgrade
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

		$conditions['select'] = "DISTINCT moduleid, menuid";

		$conditions['join'][] = "LEFT JOIN #__modules AS modules ON modules.id = m.moduleid";

		$conditions['where'][] = "m.moduleid NOT IN (1,2,3,4,8,13,14,15)";
		$conditions['where'][] = "modules.module IN ('mod_breadcrumbs', 'mod_footer', 'mod_mainmenu', 'mod_menu', 'mod_related_items', 'mod_stats', 'mod_wrapper', 'mod_archive', 'mod_custom', 'mod_latestnews', 'mod_mostread', 'mod_search', 'mod_syndicate', 'mod_banners', 'mod_feed', 'mod_login', 'mod_newsflash', 'mod_random_image', 'mod_whosonline' )";
				
		return $conditions;
	}

	/**
	 * Sets the data in the destination database.
	 *
	 * @return	void
	 * @since	0.4.
	 * @throws	Exception
	 */
	public function dataHook($rows = null)
	{
		// 
		foreach ($rows as &$row)
		{
			// Convert the array into an object.
			$row = (object) $row;

			$custom = "old = {$row->moduleid}";
			$mapped = $this->getMapListValue("modules", false, $custom);

			$row->moduleid = isset($mapped) ? $mapped : $row->moduleid+99999;
		}

		return $rows;
	}
}
