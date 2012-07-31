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
 * JUpgradeTableWeblinks Table class
 */
class JUpgradeTableWeblinks extends JUpgradeTable {
	/** @var int(11) unsigned */
	var $id = null;
	/** @var int(11) */
	var $catid = null;
	/** @var int(11) */
	var $sid = null;
	/** @var varchar(250) */
	var $title = null;
	/** @var varchar(255) */
	var $alias = null;
	/** @var varchar(250) */
	var $url = null;
	/** @var text */
	var $description = null;
	/** @var datetime */
	var $date = null;
	/** @var int(11) */
	var $hits = null;
	/** @var tinyint(1) */
	var $published = null;
	/** @var int(11) */
	var $checked_out = null;
	/** @var datetime */
	var $checked_out_time = null;
	/** @var int(11) */
	var $ordering = null;
	/** @var tinyint(1) */
	var $archived = null;
	/** @var tinyint(1) */
	var $approved = null;
	/** @var text */
	var $params = null;

	/**
	 * Table type
	 *
	 * @var string
	 */	
	var $_type = 'weblinks';

	function __construct(&$_db) {
		parent::__construct('#__weblinks', 'id', $_db);
	}


}
