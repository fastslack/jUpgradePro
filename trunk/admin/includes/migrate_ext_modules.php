<?php
/**
 * jUpgrade
 *
 * @version		  $Id$
 * @package		  MatWare
 * @subpackage	com_jupgrade
 * @author      Matias Aguirre <maguirre@matware.com.ar>
 * @link        http://www.matware.com.ar
 * @copyright		Copyright 2006 - 2012 Matias Aguire. All rights reserved.
 * @license		  GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Upgrade class for Weblinks
 *
 * This class takes the weblinks from the existing site and inserts them into the new site.
 *
 * @since	3.0.0
 */
class jUpgradeExtModules extends jUpgrade
{
	/**
	 * @var		string	The name of the source database table.
	 * @since	3.0.0
	 */
	protected $source = '#__modules';

	/**
	 * @var		string	The name of the destination database table.
	 * @since	3.0.0
	 */
	protected $destination = '#__extensions';

	/**
	 * @var		string	The name of the source database table.
	 * @since	3.0.0
	 */
	protected $_tbl_key = 'id';

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
		
		$conditions['as'] = "m";
		
		$conditions['select'] = 'title as name, \'module\' AS type, `module` AS element, params';

		$where = array();
		$where[] = "module   NOT   IN   ('mod_mainmenu',   'mod_login',   'mod_popular',   'mod_latest',   'mod_stats',   'mod_unread',   'mod_online',   'mod_toolbar',   'mod_quickicon',   'mod_logged',   'mod_footer',   'mod_menu',   'mod_submenu',   'mod_status',   'mod_title',   'mod_login' )";
		
		$conditions['where'] = $where;

		$conditions['group_by'] = 'element';
		
		return $conditions;
	}

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 * @since	3.0.0
	 * @throws	Exception
	 */
	public function &getSourceDatabase()
	{
		// Getting the rows
		$rows = parent::getSourceDatabase();
print_r($rows);
		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			$row = (array) $row;
			// Converting params to JSON
			$row['params'] = $this->convertParams($row['params']);
			// Defaults
			$row['type'] = 'module';
		}

		return $rows;
	}
	
}
