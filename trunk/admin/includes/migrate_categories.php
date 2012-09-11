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
 * Upgrade class for categories
 *
 * This class takes the categories from the existing site and inserts them into the new site.
 *
 * @since	0.4.5
 */
class jUpgradeCategories extends jUpgradeCategory
{
	/**
	 * @var		string	The name of the source database table.
	 * @since	0.4.5
	 */
	protected $source = '#__categories';

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
		$where[] = "section REGEXP '^[\\-\\+]?[[:digit:]]*\\.?[[:digit:]]*$'";
		
		$conditions['order'] = "section DESC, ordering DESC";		
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
		$params = $this->getParams();
	
		/**
		 * Inserting the categories
		 * @since	2.5.1
		 */
		// Content categories
		$this->section = 'com_content'; 
		// Get the source data.
		$categories = $this->loadData('categories');

		// Initialize values
		$aliases = array();
		$unique_alias_suffix = 1;
		$rootidmap = 0;

		// JTable::store() run an update if id exists so we create them first
		foreach ($categories as $category)
		{
			$object = new stdClass();

			$category = (array) $category;

			if ($category['id'] == 1) {
				$query = "SELECT id+1"
				." FROM #__categories"
				." ORDER BY id DESC LIMIT 1";
				$this->_db->setQuery($query);
				$rootidmap = $this->_db->loadResult();

				$object->id = $rootidmap;
				$category['old_id'] = $category['id'];
				$category['id'] = $rootidmap;
			}else{
				$object->id = $category['id'];
			}

			// Inserting the menu
			if (!$this->_db->insertObject($this->destination, $object)) {
				echo $this->_db->getErrorMsg();
			}
		}

		// Update the category
		foreach ($categories as $category)
		{
			$category = (array) $category;

			$category['asset_id'] = null;
			$category['parent_id'] = 1;
			$category['lft'] = null;
			$category['rgt'] = null;
			$category['level'] = null;

			if ($category['id'] == 1) {
				$category['id'] = $rootidmap;
			}

			// Check if has duplicated aliases
			$query = "SELECT alias"
			." FROM #__categories"
			." WHERE alias = ".$this->_db->quote($category['alias']);
			$this->_db->setQuery($query);
			$aliases = $this->_db->loadAssoc();

			$count = count($aliases);
			if ($count > 0) {
				$category['alias'] .= "-".rand(0, 99999);
			}

			$this->insertCategory($category);
		}

	}
}
