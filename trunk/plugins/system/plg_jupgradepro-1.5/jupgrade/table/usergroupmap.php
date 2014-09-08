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
 * Usergroupmap table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JUpgradeTableUsergroupmap extends JUpgradeTable
{
	/** @var int Primary key */
	var $group_id			= null;

	var $section_id	= null;

	var $aro_id		= null;
	
	var $user_id		= null;	

	/**
	 * Table type
	 *
	 * @var string
	 */	
	var $_type = 'usergroupmap';

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
		parent::__construct( '#__core_acl_groups_aro_map', 'aro_id', $db );
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
		foreach ($rows as &$row)
		{
			// Do some custom post processing on the list.
			// The schema for old group map is: group_id, section_value, aro_id
			// The schema for new groups is: user_id, group_id
			$row['user_id'] = $this->_getUserIdAroMap($row['aro_id']);

			// Note, if we are here, these are custom groups we didn't know about.
			if ($row['group_id'] <= 30) {
				$row['group_id'] = $this->usergroup_map[$row['group_id']];
			}

			// Remove unused fields.
			unset($row['section_value']);
			unset($row['aro_id']);
		}

		return $rows;
	}

	/**
	 * Method to get a map of the User id to ARO id.
	 *
	 * @returns	array	An array of the user id's keyed by ARO id.
	 * @since	0.4.4
	 * @throws	Exception on database error.
	 */
	protected function _getUserIdAroMap($aro_id)
	{
		$db =& $this->getDBO();

		$db->setQuery(
			'SELECT value' .
			' FROM #__core_acl_aro' .
			' WHERE id = '.$aro_id
		);

		$return	= $db->loadResult();
		$error	= $db->getErrorMsg();

		// Check for query error.
		if ($error) {
			throw new Exception($error);
		}

		return $return;
	}
}
