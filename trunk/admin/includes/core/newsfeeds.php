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
/**
 * Upgrade class for Newsfeeds
 *
 * This class takes the newsfeeds from the existing site and inserts them into the new site.
 *
 * @since	0.4.5
 */
class jUpgradeNewsfeeds extends jUpgrade
{
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
		$categories = $this->getMapList('categories', 'com_newsfeeds');

		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			$row = (array) $row;

			$row['access'] = 1;
			$row['language'] = '*';

			if ($this->_version == '3.0') {
				unset($row['filename']);
			}

			$cid = $row['catid'];
			$row['catid'] = &$categories[$cid]->new;

			unset($row['filename']);
		}

		return $rows;
	}
}
