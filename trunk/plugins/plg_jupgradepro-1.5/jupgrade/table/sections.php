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
		$this->title = str_replace("'", "&#39;", $this->title);
		$this->description = str_replace("'", "&#39;", $this->description);

		$this->extension = 'com_section';
		
		$this->old_id = $this->id;
		unset($this->id);

		// Correct alias
		if ($this->alias == "") {
			$this->alias = JFilterOutput::stringURLSafe($this->title);
		}
	}
}
