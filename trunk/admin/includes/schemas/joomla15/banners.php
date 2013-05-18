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
 * Upgrade class for Banners
 *
 * This class takes the banners from the existing site and inserts them into the new site.
 *
 * @since       0.4.5
 */
class jUpgradeBanners extends jUpgrade
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
		
		$conditions['select'] = '`bid` AS id, `cid`, `type`, `name`, `alias`, `imptotal`, `impmade`, '
													.'`clicks`, `imageurl`, `clickurl`, `date`, `showBanner` AS state, `checked_out`, '
													.'`checked_out_time`, `editor`, `custombannercode`, `catid`, `description`, '
													.'`sticky`, `ordering`, `publish_up`, `publish_down`, `tags`, `params`'	;
		
		$conditions['where'] = array();
		
		return $conditions;
	}

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return      array   Returns a reference to the source data array.
	 * @since       0.4.5
	 * @throws      Exception
	 */
	public function databaseHook($rows = null)
	{
		// Getting the categories id's
		$categories = $this->getMapList('categories', 'com_banners');

		// Do some custom post processing on the list.
		foreach ($rows as $index => &$row)
		{
			$row = (array) $row;

			$row['params'] = $this->convertParams($row['params']);                        

			$cid = $row['catid'];
			$row['catid'] = &$categories[$cid]->new;
		}

		return $rows;
	}

	/**
	 * Sets the data in the destination database.
	 *
	 * @return      void
	 * @since       0.4.
	 * @throws      Exception
	 */
	public function dataHook($rows = null)
	{
		// Getting the component parameter with global settings
		$params = $this->getParams();	

		// Fixing the changes between versions
		foreach($rows as &$row)
		{
			$row = (array) $row;

			$temp = new JRegistry($row['params']);
			$temp->set('imageurl', 'images/banners/' . $row['imageurl']);
			$row['params'] = json_encode($temp->toObject());

			$row['language'] = '*';

			unset($row['imageurl']);
			unset($row['date']);
			unset($row['editor']);
			unset($row['tags']);
		}

		return $rows;
	}
}
