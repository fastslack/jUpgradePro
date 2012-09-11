<?php
/**
 * jUpgrade
 *
 * @version		$Id$
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @copyright	Copyright 2006 - 2011 Matias Aguire. All rights reserved.
 * @license		GNU General Public License version 2 or later.
 * @author		Matias Aguirre <maguirre@matware.com.ar>
 * @link		http://www.matware.com.ar
 */

// Require the category class
require_once JPATH_COMPONENT.'/includes/jupgrade.category.class.php';

/**
 * Upgrade class for sections
 *
 * This class takes the sections from the existing site and inserts them into the new site.
 *
 * @since	0.4.5
 */
class jUpgradeSections extends jUpgradeCategory
{
	/**
	 * @var		string	The name of the source database table.
	 * @since	0.4.5
	 */
	protected $source = '#__sections';

	/**
	 * @var		string	The name of the destination database table.
	 * @since	0.4.5
	 */
	protected $destination = '#__categories';

	/**
	 * @var		string	The key of the table
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
				
		$where = array();
		$where[] = "scope = 'content'";
		
		$conditions['where'] = $where;
		
		return $conditions;
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
		//$params = $this->getParams();

		// Get the source data.
		$sections = $this->loadData('sections');

		// Insert the sections
		foreach ($sections as $section)
		{
			$section = (array) $section;

			// Inserting the category
			$this->insertCategory($section);

			if ($section['old_id'] == $this->getLastid()) {
				$this->fixParents();
				$this->insertUncategorized();
			}
		}

	}


	protected function insertUncategorized()
	{
		// Require the files
		require_once JPATH_COMPONENT.DS.'includes'.DS.'helper.php';

		// The sql file with menus
		$sqlfile = JPATH_COMPONENT.DS.'sql'.DS.'categories.sql';

		// Import the sql file
	  if (JUpgradeHelper::populateDatabase($this->_db, $sqlfile, $errors) > 0 ) {
	  	return false;
	  }
		
		return true;
	}

	protected function fixParents()
	{

		$sectionmap = $this->getMapList('categories', 'com_section');
		$change_parent = $this->getMapList('categories', false, "section != 0");

		// Insert the sections
		foreach ($change_parent as $category)
		{
			// Getting the category table
			$table = JTable::getInstance('Category', 'JTable');
			$table->load($category->new);

			// Setting the location of the new category
			$table->setLocation($sectionmap[$category->section]->new, 'last-child');

			// Insert the category
			if (!$table->store()) {
				throw new Exception($table->getError());
			}
		}
	}

	protected function getLastId()
	{
		$method = $this->params->get('method');
	
		// Get the source data.
		if ($method == 'rest' || $method == 'rest_individual') {

			jimport('joomla.http.http');
	
			// JHttp instance
			$http = new JHttp();		
			$data = $this->getRestData();

			// Getting the total
			$data['task'] = "lastid";
			$data['type'] = "sections";
			$lastid = $http->get($this->params->get('rest_hostname'), $data);
			$lastid = (int) $lastid->body;

		} else if ($method == 'database') {
			//$rows = $this->getSourceData();
		}

		return $lastid;
	}

}
