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
 * JUpgradeTableNewsfeeds Table class
 */
class JUpgradeTableNewsfeeds extends JUpgradeTable {
	/** @var int(11) */
	var $catid = null;
	/** @var int(11) */
	var $id = null;
	/** @var text */
	var $name = null;
	/** @var varchar(255) */
	var $alias = null;
	/** @var text */
	var $link = null;
	/** @var varchar(200) */
	var $filename = null;
	/** @var tinyint(1) */
	var $published = null;
	/** @var int(11) unsigned */
	var $numarticles = null;
	/** @var int(11) unsigned */
	var $cache_time = null;
	/** @var tinyint(3) unsigned */
	var $checked_out = null;
	/** @var datetime */
	var $checked_out_time = null;
	/** @var int(11) */
	var $ordering = null;
	/** @var tinyint(4) */
	var $rtl = null;

	/**
	 * Table type
	 *
	 * @var string
	 */	
	var $_type = 'newsfeeds';

	function __construct(&$db) {
		parent::__construct('#__newsfeeds', 'id', $db);
	}
}
