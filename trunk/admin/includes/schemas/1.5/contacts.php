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
// Require the category class
require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.category.class.php';

/**
 * Upgrade class for Contacts
 *
 * This class takes the contacts from the existing site and inserts them into the new site.
 *
 * @since	0.4.5
 */
class JUpgradeproContacts extends JUpgradepro
{
	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 * @since	0.4.5
	 * @throws	Exception
	 */
	public function databaseHook($rows)
	{
		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			$row = (array) $row;

			$row['language'] = '*';
			$row['access'] = $row['access'] == 0 ? 1 : $row['access'] + 1;
			$row['params'] = $this->convertParams($row['params']);
		}

		return $rows;
	}

	/**
	 * Sets the data in the destination database.
	 *
	 * @return	void
	 * @since	3.0.
	 * @throws	Exception
	 */
	public function dataHook($rows = null)
	{
		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			$row = (array) $row;

			if (version_compare(PHP_VERSION, '3.0', '>=')) {
				unset($row['imagepos']);
			}
		}

		return $rows;
	}
}
