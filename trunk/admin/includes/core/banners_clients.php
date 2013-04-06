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
 * Upgrade class for banners clients 
 *
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @since		2.5.2
 */
class jUpgradeBannersClients extends jUpgrade
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
		
		$conditions['select'] = '`cid` AS id, `name`, 1 AS `state`, `contact`, `email`, `extrainfo`, `checked_out`, `checked_out_time`';
		
		$conditions['where'] = array();
		
		return $conditions;
	}	
}
