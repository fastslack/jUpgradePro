<?php
/**
* @version $Id:
* @package Matware.jUpgradePro
* @copyright Copyright (C) 2005 - 2014 Matware. All rights reserved.
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
class JUpgradeproTableBanners_Clients extends JUpgradeproTable {
	/**
	 * Table type
	 *
	 * @var string
	 */	
	var $_type = 'banners_clients';	

	function __construct(&$db) {
		parent::__construct('#__banner_client', 'id', $db);
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
		
		$conditions['select'] = 'id, `name`, `state`, `contact`, `email`, `extrainfo`, `checked_out`, `checked_out_time`';
		
		$conditions['where'] = array();
		
		return $conditions;
	}
}
