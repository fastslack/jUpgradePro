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
	 * Get total of the rows of the table
	 *
	 * @access	public
	 * @return	int	The total of rows
	 */
	public function total( )
	{
		$db =& $this->getDBO();

		$query = "SELECT COUNT(*) FROM {$this->_tbl}"
		. " WHERE {$this->_tbl_key} > 30";
		$db->setQuery( $query );

		$result = $db->loadResult( );

		if ($result) {
			return (int)$result;
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * 
	 *
	 * @access	public
	 */
	function migrate( )
	{	
		// Note, if we are here, these are custom groups we didn't know about.
		if (isset($this->parent_id)) {
			if ($this->parent_id <= 30) {
				$this->parent_id = $this->usergroup_map[$this->parent_id];
			}
		}

		// Use the old groups name for the new title.
		$this->title = $this->name;

		// Remove unused fields.
		unset($this->name);
		unset($this->value);
		unset($this->lft);
		unset($this->rgt);
	}
}
