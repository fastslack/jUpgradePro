<?php
/**
 * @version		$Id: menutypes.php 18162 2010-07-16 07:00:47Z ian $
 * @package		Joomla.Framework
 * @subpackage	Table
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 * Joomla! is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * JUpgradeTableBanners_tracks Table class
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
}
