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

		$where_or = array();
		$where_or[] = "section REGEXP '^[\\-\\+]?[[:digit:]]*\\.?[[:digit:]]*$'";
		$where_or[] = "section IN ('com_banner', 'com_contact', 'com_contact_details', 'com_content', 'com_newsfeeds', 'com_sections', 'com_weblinks' )";

		$conditions['order'] = "id DESC, section DESC, ordering DESC";		
		$conditions['where_or'] = $where_or;
		
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
			$row['params'] = $this->convertParams($row['params']);
			$row['title'] = str_replace("'", "&#39;", $row['title']);
			$row['description'] = str_replace("'", "&#39;", $row['description']);

			$row['extension'] = $row['section'];
			unset($row['section']);

			if ($row['extension'] == 'com_banner') {
				$row['extension'] = "com_banners";
			}else if ($row['extension'] == 'com_contact_details') {
				$row['extension'] = "com_contact";
			}

			// Correct alias
			if ($row['alias'] == "") {
				$row['alias'] = JFilterOutput::stringURLSafe($row['title']);
			}
		}

		return $rows;
	}
}
