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
 * AroGroup table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JUpgradeTableAROGroup extends JUpgradeTable
{
	/** @var int Primary key */
	var $id			= null;

	var $parent_id	= null;

	var $name		= null;

	var $value		= null;

	var $lft		= null;

	var $rgt		= null;

	/**
	 * Table type
	 *
	 * @var string
	 */		
	var $_type = 'arogroup';	

	protected $usergroup_map = array(
		// Old	=> // New
		0		=> 0,	// ROOT
		28		=> 1,	// USERS (=Public)
		29		=> 1,	// Public Frontend
		18		=> 2,	// Registered
		19		=> 3,	// Author
		20		=> 4,	// Editor
		21		=> 5,	// Publisher
		30		=> 6,	// Public Backend (=Manager)
		23		=> 6,	// Manager
		24		=> 7,	// Administrator
		25		=> 8,	// Super Administrator
	);

	function __construct( &$db )
	{
		parent::__construct( '#__core_acl_aro_groups', 'id', $db );
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
		$where[] = "{$this->_tbl_key} > 30";
		
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
			// Note, if we are here, these are custom groups we didn't know about.
			if (isset($row['parent_id'])) {
				if ($row['parent_id'] <= 30) {
					$row['parent_id'] = $this->usergroup_map[$row['parent_id']];
				}
			}

			// Use the old groups name for the new title.
			$row['title'] = $row['name'];

			// Remove unused fields.
			unset($row['name']);
			unset($row['value']);
			unset($row['lft']);
			unset($row['rgt']);
		}

		return $rows;
	}
}
