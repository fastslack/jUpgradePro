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

JLoader::register("JUpgradeproUser", JPATH_COMPONENT_ADMINISTRATOR."/includes/jupgrade.users.class.php");

/**
 * Upgrade class for the Usergroup Map
 *
 * This translates the group mapping table from 1.5 to 3.0.
 * Group id's up to 30 need to be mapped to the new group id's.
 * Group id's over 30 can be used as is.
 * User id's are maintained in this upgrade process.
 *
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @since		3.2.0
 */
class JUpgradeproUsergroupMap extends JUpgradeproUser
{
	/**
	 * Setting the conditions hook
	 *
	 * @return	array
	 * @since	3.1.0
	 * @throws	Exception
	 */
	public static function getConditionsHook()
	{
		$conditions = array();
		
		$conditions['where'] = array();
		$conditions['order'] = "user_id ASC";
		
		return $conditions;
	}

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array
	 * @since	0.4.4
	 * @throws	Exception
	 */
	public function &databaseHook($rows)
	{
		$remove = array();
/*
		// Set up the mapping table for the old groups to the new groups.
		$groupMap = $this->getUsergroupIdMap();

		// Do some custom post processing on the list.
		// The schema for old group map is: group_id, section_value, aro_id
		// The schema for new groups is: user_id, group_id
	
		$count = count($rows);

		for ($i=0;$i<$count;$i++)
		{
			$row = (array) $rows[$i];

			$row['user_id'] = $this->getUserIdAroMap($row['aro_id']);

			// Note, if we are here, these are custom groups we didn't know about.
			if ($row['group_id'] <= 30) {
				$row['group_id'] = $groupMap[$row['group_id']];
			}

			// Remove unused fields.
			unset($row['section_value']);
			unset($row['aro_id']);

			$rows[$i] = $row;
		}
*/
		return $rows;
	}

	/**
	 * Sets the data in the destination database.
	 *
	 * @return	void
	 * @since	0.4.
	 * @throws	Exception
	 */
	public function dataHook($rows)
	{
		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			$row = (array) $row;

			if (empty($row['user_id'])) {
				$row = false;
			}
		}

		return $rows;
	}
}
