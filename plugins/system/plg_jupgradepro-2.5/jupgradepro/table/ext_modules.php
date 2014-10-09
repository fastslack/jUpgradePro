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
 * Menu table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JUpgradeproTableExt_modules extends JUpgradeproTable
{
	/**
	 * Contructor
	 *
	 * @access protected
	 * @param database A database connector object
	 */
	function __construct( &$db ) {
		parent::__construct( '#__extensions', 'extension_id', $db );

		$this->_type = 'ext_modules';
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
		
		$conditions['as'] = "m";
		
		$conditions['select'] = '`name`, `element`, `type`, `folder`, `client_id`, `ordering`, `params`';

		$where = array();
		$where[] = "m.type = 'module'";
		$where[] = "m.element   NOT   IN   ('mod_mainmenu',   'mod_login',   'mod_popular',   'mod_latest',   'mod_stats',   'mod_unread',   'mod_online',   'mod_toolbar',   'mod_quickicon',   'mod_logged',   'mod_footer',   'mod_menu',   'mod_submenu',   'mod_status',   'mod_title',   'mod_login' )";
		
		$conditions['where'] = $where;
	
		return $conditions;
	}
}
