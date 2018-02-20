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
 * Category table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JUpgradeproTableCategories extends JUpgradeproTable
{
	/**
	* @param database A database connector object
	*/
	function __construct( &$db )
	{
		parent::__construct( '#__categories', 'id', $db );

		$this->_type = 'categories';
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

		$conditions['select'] = '*';

		if ($this->_keepid == 1)
		{
			$where_or = array();
			$where_or[] = "extension REGEXP '^[\\-\\+]?[[:digit:]]*\\.?[[:digit:]]*$'";
			$where_or[] = "extension IN ('com_banners', 'com_contact', 'com_content', 'com_newsfeeds', 'com_sections', 'com_weblinks' )";
			$conditions['where_or'] = $where_or;
			$conditions['order'] = "id DESC, extension DESC";
		}else{
			$where = array();
			$where[] = "path != 'uncategorised'";
			$where[] = "(extension REGEXP '^[\-\+]?[[:digit:]]*\.?[[:digit:]]*$' OR extension IN ('com_banners', 'com_contact', 'com_content', 'com_newsfeeds', 'com_sections', 'com_weblinks' ))";
			$conditions['where'] = $where;
			$conditions['order'] = "parent_id DESC";
		}

		return $conditions;
	}
}
