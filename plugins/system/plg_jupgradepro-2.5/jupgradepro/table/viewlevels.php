<?php
/**
* @version $Id:
* @package Matware.jUpgradePro
* @copyright Copyright (C) 2004 - 2018 Matware. All rights reserved.
* @author Matias Aguirre
* @email maguirre@matware.com.ar
* @link http://www.matware.com.ar/
* @license GNU General Public License version 2 or later; see LICENSE
*/

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Viewlevels table
 *
 * @package 	  Joomla.Framework
 * @subpackage  Table
 * @since       3.8.0
 */
class JUpgradeproTableViewlevels extends JUpgradeproTable
{
	function __construct( &$db )
	{
		parent::__construct( '#__viewlevels', 'id', $db );

		$this->_type = 'viewlevels';
	}

	/**
	 * Setting the conditions hook
	 *
	 * @return	array
	 * @since	  3.8.0
	 * @throws	Exception
	 */
	public function getConditionsHook()
	{
		$conditions = array();

		$conditions['as'] = "vl";

		$conditions['select'] = 'vl.*';

		$conditions['where'] = array();

		if ($this->_keepid == 0)
		{
			$conditions['where'][] = "vl.id > 5";
		}

		$conditions['order'] = "vl.id ASC";

		return $conditions;
	}
}
