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

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Upgrade class for Banners tracks
 *
 * This class takes the banners tracks from the existing site and send them into the new site.
 *
 * @since	0.4.5
 */
class JUpgradeTableBanners_tracks extends JUpgradeTable {
	/** @var date */
	var $track_date = null;
	/** @var int(10) unsigned */
	var $track_type = null;
	/** @var int(10) unsigned */
	var $banner_id = null;

	/**
	 * Table type
	 *
	 * @var string
	 */	
	var $_type = 'banners_tracks';	

	function __construct(&$db) {
		parent::__construct('#__bannertrack', 'banner_id', $db);
	}

	/**
	 * Setting the conditions hook
	 *
	 * @return	array
	 * @since	3.1.0
	 * @throws	Exception
	 */
	public function getConditionsHook()
	{
		$conditions = array();
		
		$conditions['where'] = array();

		$conditions['group_by'] = "banner_id";
		
		return $conditions;
	}
}
