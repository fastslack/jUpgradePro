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
	 * Setting the conditions hook
	 *
	 * @return	void
	 * @since	3.0.0
	 * @throws	Exception
	 */
	public function getConditionsHook()
	{
		$conditions = array();

		$conditions['select'] = '`id` AS old_id, `title`, `alias`, \'com_section\' AS extension, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`';

		$where = array();
		$where[] = "scope = 'content'";
		
		$conditions['where'] = $where;
		
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
		$rows = parent::getSourceDatabase();
	
		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			$row['params'] = $this->convertParams($row['params']);
			$row['access'] = $row['access'] == 0 ? 1 : $row['access'] + 1;
			$row['title'] = str_replace("'", "&#39;", $row['title']);
			$row['description'] = str_replace("'", "&#39;", $row['description']);
			$row['language'] = '*';

			$row['extension'] = 'com_section';

			// Correct alias
			if ($row['alias'] == "") {
				$row['alias'] = JFilterOutput::stringURLSafe($row['title']);
			}
		}

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
		// Get the source data.
		$sections = $this->loadData('sections');

		// Insert the sections
		foreach ($sections as $section)
		{
			$section = (array) $section;

			// Inserting the category
			$this->insertCategory($section);

			if ($section['old_id'] == $this->getLastId()) { 
				$this->fixParents();
				$this->insertUncategorized();
			}
		}

	}


	protected function insertUncategorized()
	{
		// Require the files
		//require_once JPATH_COMPONENT.DS.'includes'.DS.'helper.php';

		// The sql file with menus
		$sqlfile = JPATH_COMPONENT.DS.'sql'.DS.'categories.sql';

		// Import the sql file
	  if ($this->populateDatabase($this->_db, $sqlfile, $errors) > 0 ) {
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
}
