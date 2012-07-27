<?php
/**
* @version		$Id: category.php 14401 2010-01-26 14:10:00Z louis $
* @package		Joomla.Framework
* @subpackage	Table
* @copyright	Copyright (C) 2005 - 2010 Open Source Matters. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Joomla! is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Category table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JUpgradeTableCategories extends JUpgradeTable
{
	/** @var int Primary key */
	var $id					= null;
	/** @var int */
	var $parent_id			= null;
	/** @var string The menu title for the category (a short name)*/
	var $title				= null;
	/** @var string The full name for the category*/
	var $name				= null;
	/** @var string The the alias for the category*/
	var $alias				= null;
	/** @var string */
	var $image				= null;
	/** @var string */
	var $section				= null;
	/** @var string */
	var $extension				= null;
	/** @var int */
	var $image_position		= null;
	/** @var string */
	var $description			= null;
	/** @var boolean */
	var $published			= null;
	/** @var boolean */
	var $checked_out			= 0;
	/** @var time */
	var $checked_out_time		= 0;
	/** @var int */
	var $ordering			= null;
	/** @var int */
	var $access				= null;
	/** @var string */
	var $params				= null;

	/**
	 * Table type
	 *
	 * @var string
	 */	
	var $_type = 'categories';	

	/**
	* @param database A database connector object
	*/
	function __construct( &$db )
	{
		parent::__construct( '#__categories', 'id', $db );
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
				
		$where = array();
		$where[] = "section REGEXP '^[\\-\\+]?[[:digit:]]*\\.?[[:digit:]]*$'";
		
		$conditions['order'] = "section ASC, ordering ASC";		
		$conditions['where'] = $where;
		
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
		$this->params = $this->convertParams($this->params);
		$this->access = $this->access == 0 ? 1 : $this->access + 1;
		$this->title = str_replace("'", "&#39;", $this->title);
		$this->description = str_replace("'", "&#39;", $this->description);
		$this->language = '*';

		$this->extension = $this->section;
		unset($this->section);

		if ($this->extension == 'com_banner') {
			$this->extension = "com_banners";
		}else if ($this->extension == 'com_contact_details') {
			$this->extension = "com_contact";
		}

		// Correct alias
		if ($this->alias == "") {
			$this->alias = JFilterOutput::stringURLSafe($this->title);
		}
	}

}
