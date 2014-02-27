<?php
/**
* jUpgradePro
*
* @version $Id:
* @package jUpgradePro
* @copyright Copyright (C) 2004 - 2014 Matware. All rights reserved.
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
 * @package 	jUpgradePro
 * @subpackage		Plugin
 * @since	1.0
 */
class JUpgradeproTableExtensions_categories extends JUpgradeproTable
{
	/**
	* @param database A database connector object
	*/
	function __construct( &$db )
	{
		parent::__construct( '#__categories', 'id', $db );

		$this->_type = 'ext_categories';
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
		$where[] = "extension REGEXP '^[\\-\\+]?[[:digit:]]*\\.?[[:digit:]]*$'";
		
		$conditions['order'] = "id DESC, extension DESC";
		$conditions['where'] = $where;
		
		return $conditions;
	}
}
