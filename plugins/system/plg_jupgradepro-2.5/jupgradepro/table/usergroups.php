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
 * Usergroups table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JUpgradeproTableUsergroups extends JUpgradeproTable
{
	function __construct( &$db )
	{
		parent::__construct( '#__usergroups', 'id', $db );

		$this->_type = 'usergroups';
	}

	/**
	 * Setting the conditions hook
	 *
	 * @return	array
	 * @since	3.0.0
	 * @throws	Exception
	 */
	public function getConditionsHook()
	{
		$conditions = array();

		$conditions['as'] = "ug";

		$conditions['select'] = 'ug.*';

		$conditions['where'] = array();

		if ($this->_keepid == 0)
		{
			$conditions['where'][] = "ug.id > 9";
		}

		$conditions['order'] = "ug.id ASC";

		return $conditions;
	}
}
