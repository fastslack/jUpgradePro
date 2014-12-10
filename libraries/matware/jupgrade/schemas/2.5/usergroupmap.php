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

JLoader::register("JUpgradeproUser", JPATH_LIBRARIES."/matware/jupgrade/users.php");

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
