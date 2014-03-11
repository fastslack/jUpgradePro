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
 * Menu table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JUpgradeTableExt_Components extends JUpgradeTable
{
	/** @var int Primary key */
	var $id					= null;
	/** @var string */
	var $name				= null;
	/** @var string */
	var $link				= null;
	/** @var string */
	var $menuid				= null;
	/** @var string */
	var $parent				= null;
	/** @var int */
	var $admin_menu_link	= null;
	/** @var int */
	var $admin_menu_alt			= null;
	/** @var int */
	var $option		= null;
	/** @var int */
	var $ordering			= null;
	/** @var int */
	var $iscore				= null;
	/** @var string */
	var $params				= null;
	/** @var int */
	var $enabled				= null;
	/** @var int */
	var $element				= null;
	/** @var int */
	var $type				= null;
	/** @var int */
	var $client_id				= null;

	/**
	 * Table type
	 *
	 * @var string
	 */	
	var $_type = 'ext_components';

	/**
	 * Constructor
	 *
	 * @access protected
	 * @param database A database connector object
	 */
	function __construct( &$db ) {
		parent::__construct( '#__components', 'id', $db );
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
		
		$conditions['as'] = "c";
		
		$conditions['select'] = 'name, `option` AS element, params';

		$where = array();
		$where[] = "c.parent = 0";
		$where[] = "c.option NOT IN ('com_admin', 'com_banners', 'com_cache', 'com_categories', 'com_checkin', 'com_config', 'com_contact', 'com_content', 'com_cpanel', 'com_frontpage', 'com_installer', 'com_jupgrade', 'com_languages', 'com_login', 'com_mailto', 'com_massmail', 'com_media', 'com_menus', 'com_messages', 'com_modules', 'com_newsfeeds', 'com_plugins', 'com_poll', 'com_search', 'com_sections', 'com_templates', 'com_user', 'com_users', 'com_weblinks', 'com_wrapper' )";
		
		$conditions['where'] = $where;
		
		return $conditions;
	}

	/**
	 * Migrate the data
	 *
	 * @access	public
	 * @param		Array	Result to migrate
	 * @return	Array	Migrated result
	 */
	function migrate(&$rows)
	{
		foreach ($rows as $row)
		{
		  // Converting params to JSON
		  $row['params'] = $this->convertParams($row['params']);
			// Defaults
			$row['type'] = 'component';
			$row['client_id'] = 1;
		}

		return $rows;
	}
}
