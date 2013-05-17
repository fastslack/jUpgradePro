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
 * Module table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JUpgradeTableModules_menu extends JUpgradeTable
{
	/** @var int Primary key */
	var $moduleid					= null;
	/** @var string */
	var $menuid				= null;

	/**
	 * Table type
	 *
	 * @var string
	 */	
	var $_type = 'modules_menu';

	/**
	 * Second key
	 *
	 * @var string
	 */	
	var $_tmp_key = 'menuid';

	/**
	 * Contructor
	 *
	 * @access protected
	 * @param database A database connector object
	 */
	function __construct( &$db ) {
		parent::__construct( '#__modules_menu', 'moduleid', $db );
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

		$conditions['select'] = "DISTINCT m.moduleid, m.menuid";

		$conditions['join'][] = "LEFT JOIN #__modules AS modules ON modules.id = m.moduleid";

		$conditions['where'][] = "m.moduleid NOT IN (1,2,3,4,8,13,14,15)";
		$conditions['where'][] = "modules.module IN ('mod_breadcrumbs', 'mod_footer', 'mod_mainmenu', 'mod_menu', 'mod_related_items', 'mod_stats', 'mod_wrapper', 'mod_archive', 'mod_custom', 'mod_latestnews', 'mod_mostread', 'mod_search', 'mod_syndicate', 'mod_banners', 'mod_feed', 'mod_login', 'mod_newsflash', 'mod_random_image', 'mod_whosonline' )";

		$conditions['order'] = "moduleid, menuid ASC";
				
		return $conditions;
	}
}
