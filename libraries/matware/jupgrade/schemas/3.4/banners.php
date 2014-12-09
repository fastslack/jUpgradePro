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
/**
 * Upgrade class for Banners
 *
 * This class takes the banners from the existing site and inserts them into the new site.
 *
 * @since       3.2.0
 */
class JUpgradeproBanners extends JUpgradepro
{
	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 * @since		3.2.0
	 * @throws	Exception
	 */
	public function &dataHook($rows)
	{
		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			$row = (array) $row;

			// Remove unused fields.
			if (version_compare(JUpgradeproHelper::getVersion('new'), '2.5', '=')) {
				unset($row['created_by']);
				unset($row['created_by_alias']);
				unset($row['modified']);
				unset($row['modified_by']);
				unset($row['version']);
			}
		}
		
		return $rows;
	}
}
