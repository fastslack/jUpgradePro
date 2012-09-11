<?php
/**
 * jUpgrade
 *
 * @version			$Id$
 * @package		  MatWare
 * @subpackage	com_jupgrade
 * @author      Matias Aguirre <maguirre@matware.com.ar>
 * @link        http://www.matware.com.ar
 * @copyright		Copyright 2006 - 2011 Matias Aguire. All rights reserved.
 * @license		  GNU General Public License version 2 or later; see LICENSE.txt
 */
 
/**
 * Upgrade class for the Usergroup Map
 *
 * This translates the group mapping table from 1.5 to 1.6.
 * Group id's up to 30 need to be mapped to the new group id's.
 * Group id's over 30 can be used as is.
 * User id's are maintained in this upgrade process.
 *
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @since		0.4.4
 */
class jUpgradeUsergroupMap extends jUpgrade
{
	/**
	 * @var		string	The name of the source database table.
	 * @since	0.4.4
	 */
	protected $source = '#__core_acl_groups_aro_map';

	/**
	 * @var		string	The name of the destination database table.
	 * @since	0.4.4
	 */
	protected $destination = '#__user_usergroup_map';

	/**
	 * @var		string	The name of the source database table.
	 * @since	0.4.4
	 */
	protected $_tbl_key = 'aro_id';

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array
	 * @since	0.4.4
	 * @throws	Exception
	 */
	public function &getSourceDatabase()
	{
		$rows = parent::getSourceDatabase();

		// Set up the mapping table for the old groups to the new groups.
		$groupMap = jUpgradeUsergroups::getUsergroupIdMap();

		// Set up the mapping table for the ARO id's to the new user id's.
		$userMap = $this->getUserIdAroMap();

		// Do some custom post processing on the list.
		// The schema for old group map is: group_id, section_value, aro_id
		// The schema for new groups is: user_id, group_id
		foreach ($rows as &$row)
		{
			$row['user_id'] = $userMap[$row['aro_id']];

			// Note, if we are here, these are custom groups we didn't know about.
			if ($row['group_id'] <= 30) {
				$row['group_id'] = $groupMap[$row['group_id']];
			}

      // Chaging admin username and email
      if ($row['user_id'] == 62) {
					$row['user_id'] = 60;
      }

			// Remove unused fields.
			unset($row['section_value']);
			unset($row['aro_id']);
		}

		return $rows;
	}

	/**
	 * Method to get a map of the User id to ARO id.
	 *
	 * @returns	array	An array of the user id's keyed by ARO id.
	 * @since	0.4.4
	 * @throws	Exception on database error.
	 */
	protected function getUserIdAroMap()
	{
		$this->db_old->setQuery(
			'SELECT id, value' .
			' FROM #__core_acl_aro' .
			' ORDER BY id'
		);

		$map	= $this->db_old->loadAssocList('id', 'value');
		$error	= $this->db_old->getErrorMsg();

		// Check for query error.
		if ($error) {
			throw new Exception($error);
		}

		return $map;
	}
}
