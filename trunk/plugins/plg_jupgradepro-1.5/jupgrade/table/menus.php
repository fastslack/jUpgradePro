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
class JUpgradeTableMenus extends JUpgradeTable
{
	/** @var int Primary key */
	var $id					= null;
	/** @var string */
	var $menutype			= null;
	/** @var string */
	var $name				= null;
	/** @var string */
	var $title				= null;
	/** @var string */
	var $alias				= null;
	/** @var string */
	var $link				= null;
	/** @var int */
	var $type				= null;
	/** @var int */
	var $published			= null;
	/** @var int */
	var $componentid		= null;
	/** @var int */
	var $parent				= null;
	/** @var int */
	var $parent_id				= null;
	/** @var int */
	var $ordering			= null;
	/** @var boolean */
	var $checked_out		= 0;
	/** @var datetime */
	var $checked_out_time	= 0;
	/** @var string */
	var $browserNav			= null;
	/** @var int */
	var $access				= null;
	/** @var string */
	var $params				= null;
	/** @var int */
	var $home				= null;
	/** @var int */
	var $option				= null;

	/**
	 * Table type
	 *
	 * @var string
	 */	
	var $_type = 'menus';

	/**
	 * Constructor
	 *
	 * @access protected
	 * @param database A database connector object
	 */
	function __construct( &$db ) {
		parent::__construct( '#__menu', 'id', $db );
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
		
		$conditions['select'] = 'DISTINCT m.id, m.menutype, m.name, m.name AS title, m.alias, m.link, m.type, c.option, m.published, m.parent AS parent_id,'
			.' m.sublevel AS level, m.ordering, m.checked_out, m.checked_out_time, m.browserNav, m.access, m.params, m.home';
		
		$join = array();
		$join[] = "LEFT JOIN #__components AS c ON c.id = m.componentid";
		
		$conditions['where'] = array();
		$conditions['join'] = $join;
		$conditions['order'] = "m.id DESC";
		
		return $conditions;
	}

	/**
	 * 
	 *
	 * @access	public
	 * @param		Array	Result to migrate
	 * @return	Array	Migrated result
	 */
	function migrate(&$rows)
	{
		foreach ($rows as $row)
		{
			// Fixing access
			$row['access']++;
			// Fixing level
			$row['level']++;
			// Fixing language
			$row['language'] = '*';
		  // Converting params to JSON
		  $row['params'] = $this->convertParams($row['params']);
			// Fixing parent_id
			if (isset($row['parent_id'])) {
				if ($row['parent_id'] == 0) {
					$row['parent_id'] = 1;
				}
			}
		
		  // Fixing menus URLs
		  if (strpos($row['link'], 'option=com_content') !== false) {

		    if (strpos($row['link'], 'view=frontpage') !== false) {
		      $row['link'] = 'index.php?option=com_content&view=featured';
		    }
		  }

			if ( (strpos($row['link'], 'Itemid=') !== false) AND $row['type'] == 'menulink')
			{

				// Extract the Itemid from the URL
				if (preg_match('|Itemid=([0-9]+)|', $row['link'], $tmp)) {
					$item_id = $tmp[1];

					$row['params'] = $row['params'] . "\naliasoptions=".$item_id;
					$row['type'] = 'alias';
					$row['link'] = 'index.php?Itemid=';
				}
			}

			if (strpos($row['link'], 'option=com_user&') !== false)
			{
				$row['link'] = preg_replace('/com_user/', 'com_users', $row['link']);
				$row['component_id'] = 25;
				$row['option'] = 'com_users';

				// Change the register view to registration
				if (strpos($row['link'], 'view=register') !== false)
				{
					$row['link'] = 'index.php?option=com_users&view=registration';
				}
				else if (strpos($row['link'], 'view=user') !== false)
				{
					$row['link'] = 'index.php?option=com_users&view=profile';
				}
			}
		  // End fixing menus URL's
		}

		return $rows;
	}

	/**
	 * A hook to be able to modify params prior as they are converted to JSON.
	 *
	 * @param	object	$object	A reference to the parameters as an object.
	 *
	 * @return	void
	 * @since	0.4.
	 * @throws	Exception
	 */
	protected function convertParamsHook(&$object)
	{
		if (isset($object->menu_image)) {
			if((string)$object->menu_image == '-1'){
				$object->menu_image = '';
			}
		}
		$object->show_page_heading = (isset($object->show_page_title) && !empty($object->page_title)) ? $object->show_page_title : 0;
	}

}
