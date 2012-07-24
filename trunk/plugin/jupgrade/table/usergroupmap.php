<?php
/**
* @version $Id:
* @package Matware.jUpgradePro
* @copyright Copyright (C) 2005 - 2012 Matware. All rights reserved.
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
	 * 
	 *
	 * @access	public
	 */
	function migrate( )
	{		
		// Set up the mapping table for the ARO id's to the new user id's.
		$userMap = $this->_getUserIdAroMap();

		// Do some custom post processing on the list.
		// The schema for old group map is: group_id, section_value, aro_id
		// The schema for new groups is: user_id, group_id
		$this->user_id = $userMap[$this->aro_id]['value'];

		// Note, if we are here, these are custom groups we didn't know about.
		if ($this->group_id <= 30) {
			$this->group_id = $this->usergroup_map[$this->group_id];
		}

    // Chaging admin username and email
    if ($this->user_id == 62) {
				$this->user_id = 60;
    }

		// Remove unused fields.
		unset($this->section_value);
		unset($this->aro_id);
	}

	/**
	 * Method to get a map of the User id to ARO id.
	 *
	 * @returns	array	An array of the user id's keyed by ARO id.
	 * @since	0.4.4
	 * @throws	Exception on database error.
	 */
	protected function _getUserIdAroMap()
	{
		$db =& $this->getDBO();
	
		$db->setQuery(
			'SELECT id, value' .
			' FROM #__core_acl_aro' .
			' ORDER BY id'
		);

		$map	= $db->loadAssocList('id', 'value');
		$error	= $db->getErrorMsg();

		// Check for query error.
		if ($error) {
			throw new Exception($error);
		}

		return $map;
	}
}
