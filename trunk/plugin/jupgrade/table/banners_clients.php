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
 * JUpgradeTableBannersClients
 *
 * @package 	jUpgrade
 * @subpackage	Table
 * @since		1.5
 */
class JUpgradeTableBanners_Clients extends JUpgradeTable {
	/** @var int(11) */
	var $id = null;
	/** @var varchar(255) */
	var $name = null;
	/** @var varchar(255) */
	var $contact = null;
	/** @var varchar(255) */
	var $email = null;
	/** @var text */
	var $extrainfo = null;
	/** @var tinyint(1) */
	var $checked_out = null;
	/** @var time */
	var $checked_out_time = null;
	/** @var varchar(50) */
	var $editor = null;

	/**
	 * Table type
	 *
	 * @var string
	 */	
	var $_type = 'banners_clients';	

	function __construct(&$db) {
		parent::__construct('#__bannerclient', 'cid', $db);
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
		
		$conditions['select'] = '`cid` AS id, `name`, `contact`, `email`, `extrainfo`, `checked_out`, `checked_out_time`';
		
		$conditions['where'] = array();
		
		return $conditions;
	}	

	/**
	 * 
	 *
	 * @access	public
	 * @param		Array	Result to migrate
	 * @return	Array	Migrated result
	 */
	function migrate( )
	{	
		unset($this->cid);
	}	
}
