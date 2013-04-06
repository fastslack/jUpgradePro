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
 * Menu Types table
 *
 * @package 	Joomla.Framework
 * @subpackage	Table
 * @since		1.5
 */
class JUpgradeTableMenus_types extends JUpgradeTable
{
	/** @var int Primary key */
	var $id					= null;
	/** @var string */
	var $menutype			= null;
	/** @var string */
	var $title				= null;
	/** @var string */
	var $description		= null;

	/**
	 * Table type
	 *
	 * @var string
	 */	
	var $_type = 'menus_types';	

	/**
	 * Constructor
	 *
	 * @access protected
	 * @param database A database connector object
	 */
	function __construct( &$db )
	{
		parent::__construct( '#__menu_types', 'id', $db );
	}
}
