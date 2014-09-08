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
 * Category table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JUpgradeTableExtensions_categories extends JUpgradeTable
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
	var $_type = 'ext_categories';	

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
		
		$conditions['order'] = "section DESC, ordering DESC";		
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
		$rows->params = $this->convertParams($rows->params);
		$rows->access = $rows->access == 0 ? 1 : $rows->access + 1;
		$rows->title = str_replace("'", "&#39;", $rows->title);
		$rows->description = str_replace("'", "&#39;", $rows->description);
		$rows->language = '*';

		$rows->extension = $rows->section;
		unset($rows->section);

		if ($rows->extension == 'com_banner') {
			$rows->extension = "com_banners";
		}else if ($rows->extension == 'com_contact_details') {
			$rows->extension = "com_contact";
		}

		// Correct alias
		if ($rows->alias == "") {
			$rows->alias = JFilterOutput::stringURLSafe($rows->title);
		}

		return $rows;
	}
}
