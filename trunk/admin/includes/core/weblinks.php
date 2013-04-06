<?php
/**
 * jUpgradePro
 *
 * @version		    $Id$
 * @package		    MatWare
 * @subpackage    com_jupgradepro
 * @author        Matias Aguirre <maguirre@matware.com.ar>
 * @link          http://www.matware.com.ar
 * @copyright		  Copyright 2004 - 2013 Matias Aguirre. All rights reserved.
 * @license		    GNU General Public License version 2 or later; see LICENSE.txt
 */
/**
 * Upgrade class for Weblinks
 *
 * This class takes the weblinks from the existing site and inserts them into the new site.
 *
 * @since	0.4.5
 */
class jUpgradeWeblinks extends jUpgrade
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

		$conditions['select'] = '`id`, `catid`, `title`, `alias`, `url`, `description`, `date`, `hits`, '
     .' `published` AS state, `checked_out`, `checked_out_time`, `ordering`, `archived`, `approved`,`params`';
				
		return $conditions;
	}

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 * @since	0.4.5
	 * @throws	Exception
	 */
	public function databaseHook($rows = null)
	{
		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			$row = (array) $row;

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
		// Getting the component parameter with global settings
		$params = $this->getParams();

		// Getting the categories id's
		$categories = $this->getMapList('categories', 'com_weblinks');

		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			// Convert the array into an object.
			$row = (array) $row;
			
			$cid = $row['catid'];
			$row['catid'] = &$categories[$cid]->new;

			$row['language'] = '*';

			if ($this->_version == '3.0') {
				$row['created'] = $row['date'];
				unset($row['approved']);
				unset($row['archived']);
				unset($row['date']);
				unset($row['sid']);
			}
		}

		return $rows;
	}
}
