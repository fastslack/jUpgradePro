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
 * Upgrade class for Usergroups
 *
 * This class maps the old 1.5 usergroups to the new 1.6 system.
 *
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @since		0.4.4
 */
class jUpgradeUsergroups extends jUpgrade
{
	/**
	 * @var		string	The name of the source database table.
	 * @since	0.4.4
	 */
	protected $source = '#__core_acl_aro_groups';

	/**
	 * @var		string	The name of the destination database table.
	 * @since	0.4.4
	 */
	protected $destination = '#__usergroups';
	
	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array
	 * @since	0.4.4
	 * @throws	Exception
	 */
	protected function &getSourceData()
	{
		$rows = parent::getSourceData(
			// Custom where clause.
			// We only want to get groups that we don't know about from custom group management extensions.
			// Our assumption is the core groups have not been tampered with (if they were, Joomla would not run well).
			'*',
			null,
			$this->db_old->nameQuote('id').' > 30'
		);

		// Set up the mapping table for the old groups to the new groups.
		$map = self::getUsergroupIdMap();

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
	 */
	public function upgrade()
	{
		if (parent::upgrade()) {
			// Rebuild the usergroup nested set values.
			$table = JTable::getInstance('Usergroup', 'JTable', array('dbo' => $this->_db));

			if (!$table->rebuild()) {
				echo JError::raiseError(500, $table->getError());
			}
		}
	}
}
