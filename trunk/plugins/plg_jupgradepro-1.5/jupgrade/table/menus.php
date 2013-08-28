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
	function migrate( )
	{	
		// Fixing access
		$this->access++;
		// Fixing level
		$this->level++;
		// Fixing language
		$this->language = '*';
    // Converting params to JSON
    $this->params = $this->convertParams($this->params);
		// Fixing parent_id
		if (isset($this->parent_id)) {
			if ($this->parent_id == 0) {
				$this->parent_id = 1;
			}
		}
  
    // Fixing menus URLs
    if (strpos($this->link, 'option=com_content') !== false) {

      if (strpos($this->link, 'view=frontpage') !== false) {
        $this->link = 'index.php?option=com_content&view=featured';
      }
    }

		if ( (strpos($this->link, 'Itemid=') !== false) AND $this->type == 'menulink')
		{

			// Extract the Itemid from the URL
			if (preg_match('|Itemid=([0-9]+)|', $this->link, $tmp)) {
				$item_id = $tmp[1];

				$this->params = $this->params . "\naliasoptions=".$item_id;
				$this->type = 'alias';
				$this->link = 'index.php?Itemid=';
			}
		}

		if (strpos($this->link, 'option=com_user&') !== false)
		{
			$this->link = preg_replace('/com_user/', 'com_users', $this->link);
			$this->component_id = 25;
			$this->option = 'com_users';

			// Change the register view to registration
			if (strpos($this->link, 'view=register') !== false)
			{
				$this->link = 'index.php?option=com_users&view=registration';
			}
			else if (strpos($this->link, 'view=user') !== false)
			{
				$this->link = 'index.php?option=com_users&view=profile';
			}
		}
    // End fixing menus URL's
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
