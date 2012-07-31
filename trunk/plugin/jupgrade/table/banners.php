<?php
/**
* @version $Id:
* @package Matware.jUpgradePro
* @copyright Copyright (C) 2005 - 2012 Matware. All rights reserved.
* @author Matias Aguirre
* @email maguirre@matware.com.ar
* @link http://www.matware.com.ar/
* @license GNU General Public License version 2 or later; see LICENSE
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Banners table
 *
 * @package 	Joomla.Framework
 * @subpackage	Table
 * @since		1.5
 */
class JUpgradeTableBanners extends JUpgradeTable {
	/** @var int(11) */
	var $bid = null;
	/** @var int(11) */
	var $cid = null;
	/** @var varchar(30) */
	var $type = null;
	/** @var varchar(255) */
	var $name = null;
	/** @var varchar(255) */
	var $alias = null;
	/** @var int(11) */
	var $imptotal = null;
	/** @var int(11) */
	var $impmade = null;
	/** @var int(11) */
	var $clicks = null;
	/** @var varchar(100) */
	var $imageurl = null;
	/** @var varchar(200) */
	var $clickurl = null;
	/** @var datetime */
	var $date = null;
	/** @var tinyint(1) */
	var $showBanner = null;
	/** @var tinyint(1) */
	var $checked_out = null;
	/** @var datetime */
	var $checked_out_time = null;
	/** @var varchar(50) */
	var $editor = null;
	/** @var text */
	var $custombannercode = null;
	/** @var int(10) unsigned */
	var $catid = null;
	/** @var text */
	var $description = null;
	/** @var tinyint(1) unsigned */
	var $sticky = null;
	/** @var int(11) */
	var $ordering = null;
	/** @var datetime */
	var $publish_up = null;
	/** @var datetime */
	var $publish_down = null;
	/** @var text */
	var $tags = null;
	/** @var text */
	var $params = null;

	/**
	 * Table type
	 *
	 * @var string
	 */	
	var $_type = 'banners';	

	function __construct(&$db) {
		parent::__construct('#__banner', 'bid', $db);
	}

	/**
	 * Setting the conditions hook
	 *
	 * @return	void
	 * @since	3.0.0
	 * @throws	Exception
	 */
	public function getConditionsHook()
	{
		$conditions = array();
		
		$conditions['select'] = '`bid` AS id, `cid`, `type`, `name`, `alias`, `imptotal`, `impmade`, '
													.'`clicks`, `imageurl`, `clickurl`, `date`, `showBanner` AS state, `checked_out`, '
													.'`checked_out_time`, `editor`, `custombannercode`, `catid`, `description`, '
													.'`sticky`, `ordering`, `publish_up`, `publish_down`, `tags`, `params`'	;
		
		$conditions['where'] = array();
		$conditions['order'] = "bid ASC";
		
		return $conditions;
	}	
}
