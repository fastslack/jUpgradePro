<?php
/**
 * jUpgrade
 *
 * @version             $Id$
 * @package             MatWare
 * @subpackage          com_jupgrade
 * @copyright           Copyright 2006 - 2011 Matias Aguire. All rights reserved.
 * @license             GNU General Public License version 2 or later.
 * @author              Matias Aguirre <maguirre@matware.com.ar>
 * @link                http://www.matware.com.ar
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
	 * @var		string	The name of the source database table.
	 * @since	2.5.2
	 */
	protected $source = '#__bannerclient';

	/**
	 * @var         string  The name of the destination database table.
	 * @since       2.5.2
	 */
	protected $destination = '#__banner_clients';

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return      array   Returns a reference to the source data array.
	 * @since       2.5.2
	 * @throws      Exception
	 */
	protected function &getSourceData()
	{
		$rows = parent::getSourceData('`cid` AS id, `name`, `contact`, `email`, `extrainfo`, `checked_out`, `checked_out_time`'); 

		return $rows;
	}
}
