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
 * Upgrade class for Users
 *
 * This class takes the users from the existing site and inserts them into the new site.
 *
 * @since	0.4.4
 */
class jUpgradeUsers extends jUpgradeUsersDefault
{
	/**
	 * @var		string	The name of the source database table.
	 * @since	0.4.4
	 */
	protected $source = '#__users';

	/**
	 * @var		string	The name of the source database table.
	 * @since	0.4.4
	 */
	protected $_tbl_key = 'id';

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 * @since	0.4.4
	 * @throws	Exception
	 */
	public function &getSourceDatabase()
	{
		$rows = parent::getSourceDatabase();

		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			$row['params'] = $this->convertParams($row['params']);

      // Chaging admin username and email
      if ($row['id'] == 62) {
          $row['username'] = $row['username'].'v15';
          $row['email'] = $row['email'].'v15';
      }

			// Remove unused fields. 
			$gid = 'gid';
			unset($row[$gid]);
		}

		return $rows;
	}

	/**
	 * Sets the data in the destination database.
	 *
	 * @return	void
	 * @since	0.4.
	 * @throws	Exception
	 */
	protected function setDestinationData($rows = null)
	{
		// Get the source data.
		$rows = $this->loadData('users');

		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			$row = (array) $row;

			if ($this->_version == '3.0') {
				unset($row['usertype']);
			}
		}

		$this->insertData($rows);
	}

	/**
	 * A hook to be able to modify params prior as they are converted to JSON.
	 *
	 * @param	object	$object	A reference to the parameters as an object.
	 *
	 * @return	void
	 * @since	0.4.
	 * @throws	Exception
	 */
	protected function convertParamsHook(&$object)
	{
		$object->timezone = 'UTC';
	}
}
