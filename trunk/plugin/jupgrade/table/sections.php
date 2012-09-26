<?php
/**
* @version		$Id: section.php 14401 2010-01-26 14:10:00Z louis $
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
 * Section table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JUpgradeTableSections extends JUpgradeTable
{
	/** @var int Primary key */
	var $id					= null;
	/** @var int Primary key */
	var $old_id					= null;
	/** @var string The menu title for the section (a short name)*/
	var $title				= null;
	/** @var string The full name for the section*/
	var $name				= null;
	/** @var string The alias for the section*/
	var $alias				= null;
	/** @var string */
	var $image				= null;
	/** @var string */
	var $scope				= null;
	/** @var int */
	var $image_position		= null;
	/** @var string */
	var $description		= null;
	/** @var boolean */
	var $published			= null;
	/** @var boolean */
	var $checked_out		= 0;
	/** @var time */
	var $checked_out_time	= 0;
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
	var $_type = 'sections';	

	/**
	* @param database A database connector object
	*/
	function __construct( &$db ) {
		parent::__construct( '#__sections', 'id', $db );
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
		$where[] = "scope = 'content'";
		
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

		$this->extension = 'com_section';
		
		$this->old_id = $this->id;
		unset($this->id);

		// Correct alias
		if ($this->alias == "") {
			$this->alias = JFilterOutput::stringURLSafe($this->title);
		}
	}
}
