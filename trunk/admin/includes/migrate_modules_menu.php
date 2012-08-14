<?php
/**
 * jUpgrade
 *
 * @version		  $Id$
 * @package		  MatWare
 * @subpackage	com_jupgrade
 * @author      Matias Aguirre <maguirre@matware.com.ar>
 * @link        http://www.matware.com.ar
 * @copyright		Copyright 2006 - 2011 Matias Aguire. All rights reserved.
 * @license		  GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Upgrade class for modules menu
 *
 * This class takes the modules from the existing site and inserts them into the new site.
 *
 * @since	0.4.5
 */
class jUpgradeModulesMenu extends jUpgrade
{
	/**
	 * @var		string	The name of the source database table.
	 * @since	0.4.5
	 */
	protected $source = '#__modules_menu AS m';

	/**
	 * @var		string	The name of the destination database table.
	 * @since	0.4.5
	 */
	protected $destination = '#__modules_menu';

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 * @since	0.4.5
	 * @throws	Exception
	 */
	protected function &getSourceData()
	{
		// Creating the query
		//$where = "m.moduleid NOT IN (2,3,4,8,13,14,15)";

		$join = array();
		$join[] = "INNER JOIN jupgrade_modules AS map ON  map.old = m.moduleid";
		$join[] = "INNER JOIN jupgrade_menus AS men ON  men.old = m.menuid";

		// Getting the data
		$rows = parent::getSourceData(
			'map.new AS moduleid, men.new AS menuid',
		  $join,
			$where,
			'm.moduleid'
		);

		return $rows;
	}

	/**
	 * Sets the data in the destination database.
	 *
	 * @return	void
	 * @since	0.4.
	 * @throws	Exception
	 */
	protected function setDestinationData()
	{
		// Clean the modules table
		$query = "DELETE FROM {$this->destination} WHERE id >= 1";
		$this->_db->setQuery($query);
		$this->_db->query();

		//
		//parent::setDestinationData();

/*
		// Require the files
		require_once JPATH_COMPONENT.DS.'includes'.DS.'helper.php';

		// The sql file with menus
		$sqlfile = JPATH_COMPONENT.DS.'sql'.DS.'modules_menu.sql';

		// Import the sql file
	  if (JUpgradeHelper::populateDatabase($this->_db, $sqlfile, $errors) > 0 ) {
	  	return false;
	  }
*/
	}
}
