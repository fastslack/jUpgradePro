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

JLoader::register("jUpgradeUsersDefault", JPATH_COMPONENT_ADMINISTRATOR."/includes/jupgrade.users.class.php");

/**
 * Upgrade class for Usergroups
 *
 * This class maps the old 1.5 usergroups to the new 1.6 system.
 *
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @since		0.4.4
 */
class jUpgradeUsergroups extends jUpgradeUsersDefault
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
				
		$where = array();
		$where[] = "id > 30";
		
		$conditions['where'] = $where;
		
		return $conditions;
	}

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array
	 * @since	0.4.4
	 * @throws	Exception
	 */
	public function databaseHook($rows = null)
	{
		// Set up the mapping table for the old groups to the new groups.
		$map = $this->getUsergroupIdMap();

		// Do some custom post processing on the list.
		// The schema for old groups is: id, parent_id, name, lft, rgt, value
		// The schema for new groups is: id, parent_id, lft, rgt, title
		foreach ($rows as &$row)
		{
			// Note, if we are here, these are custom groups we didn't know about.
			if (isset($row['parent_id'])) {
				if ($row['parent_id'] <= 30) {
					$row['parent_id'] = $map[$row['parent_id']];
				}
			}

			// Use the old groups name for the new title.
			$row['title'] = $row['name'];

			// Remove unused fields.
			unset($row['name']);
			unset($row['value']);
			unset($row['lft']);
			unset($row['rgt']);
		}

		// TODO: Don't forget to do a rebuild on the groups table!

		return $rows;
	}

	/**
	 * The public entry point for the class.
	 *
	 * @return	void
	 * @since	0.4.4
	 * @throws	Exception
	 *
	public function upgrade()
	{
		if (parent::upgrade()) {
			// Rebuild the usergroup nested set values.
			$table = JTable::getInstance('Usergroup', 'JTable', array('dbo' => $this->_db));

			if (!$table->rebuild()) {
				echo JError::raiseError(500, $table->getError());
			}
		}
	}*/
}
