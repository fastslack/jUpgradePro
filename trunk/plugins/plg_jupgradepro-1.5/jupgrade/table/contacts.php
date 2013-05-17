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
 * JUpgradeTableContacts Table class
 */
class JUpgradeTableContacts extends JUpgradeTable {
	/** @var int(11) */
	var $id = null;
	/** @var varchar(255) */
	var $name = null;
	/** @var varchar(255) */
	var $alias = null;
	/** @var varchar(255) */
	var $con_position = null;
	/** @var text */
	var $address = null;
	/** @var varchar(100) */
	var $suburb = null;
	/** @var varchar(100) */
	var $state = null;
	/** @var varchar(100) */
	var $country = null;
	/** @var varchar(100) */
	var $postcode = null;
	/** @var varchar(255) */
	var $telephone = null;
	/** @var varchar(255) */
	var $fax = null;
	/** @var mediumtext */
	var $misc = null;
	/** @var varchar(255) */
	var $image = null;
	/** @var varchar(20) */
	var $imagepos = null;
	/** @var varchar(255) */
	var $email_to = null;
	/** @var tinyint(1) unsigned */
	var $default_con = null;
	/** @var tinyint(1) unsigned */
	var $published = null;
	/** @var int(11) unsigned */
	var $checked_out = null;
	/** @var datetime */
	var $checked_out_time = null;
	/** @var int(11) */
	var $ordering = null;
	/** @var text */
	var $params = null;
	/** @var int(11) */
	var $user_id = null;
	/** @var int(11) */
	var $catid = null;
	/** @var tinyint(3) unsigned */
	var $access = null;
	/** @var varchar(255) */
	var $mobile = null;
	/** @var varchar(255) */
	var $webpage = null;

	/**
	 * Table type
	 *
	 * @var string
	 */	
	var $_type = 'contacts';

	function __construct(&$db) {
		parent::__construct('#__contact_details', 'id', $db);
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
		// Fixing access
		$this->access = $this->access == 0 ? 1 : $this->access + 1;
		// Fixing language
		$this->language = '*';
    // Converting params to JSON
    $this->params = $this->convertParams($this->params);
	}
}
