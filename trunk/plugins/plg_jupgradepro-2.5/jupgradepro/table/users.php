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
 * Users table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JUpgradeproTableUsers extends JUpgradeproTable
{
	/**
	* @param database A database connector object
	*/
	function __construct ( &$db )
	{
		parent::__construct( '#__users', 'id', $db );

		$this->_type = 'users';
	}
	
	/**
	 * 
	 *
	 */
	function migrate ()
	{
    // Chaging admin username and email
    if ($this->id == 62) {
      $this->username = $this->username.'v15';
      $this->email = $this->email.'v15';
    }

		unset($this->otpKey);
		unset($this->otep);
	}
}
