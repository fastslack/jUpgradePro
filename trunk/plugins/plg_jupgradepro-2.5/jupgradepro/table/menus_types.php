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
 * Menu Types table
 *
 * @package 	Matware
 * @subpackage	jUpgradePro
 * @since		3.2.0
 */
class JUpgradeproTableMenus_types extends JUpgradeproTable
{
	/**
	 * Constructor
	 *
	 * @access protected
	 * @param database A database connector object
	 */
	function __construct( &$db )
	{
		parent::__construct( '#__menu_types', 'id', $db );

		$this->_type = 'menus_types';
	}

	/**
	 * Setting the conditions hook
	 *
	 * @return	void
	 * @since	3.2.0
	 * @throws	Exception
	 */
	public function getConditionsHook()
	{
		$conditions = array();

		$conditions['select'] = "*";

		$conditions['where'][] = "id != 1";
				
		return $conditions;
	}
}
