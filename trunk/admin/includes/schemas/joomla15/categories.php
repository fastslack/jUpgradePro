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
// Require the category class
require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.category.class.php';

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
	 * Setting the conditions hook
	 *
	 * @return	void
	 * @since	3.0.0
	 * @throws	Exception
	 */
	public static function getConditionsHook()
	{
		$conditions = array();

		$conditions['select'] = '`id`, `id` AS sid, `title`, `alias`, `section` AS extension, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`';

		$where_or = array();
		$where_or[] = "section REGEXP '^[\\-\\+]?[[:digit:]]*\\.?[[:digit:]]*$'";
		$where_or[] = "section IN ('com_banner', 'com_contact', 'com_contact_details', 'com_content', 'com_newsfeeds', 'com_sections', 'com_weblinks' )";

		$conditions['order'] = "id DESC, section DESC, ordering DESC";		
		$conditions['where_or'] = $where_or;
		
		return $conditions;
	}

	/**
	 * Sets the data in the destination database.
	 *
	 * @return	void
	 * @since	0.4.
	 * @throws	Exception
	 */
	public function dataHook($rows = null)
	{
		// Getting the destination table
		$table = $this->getDestinationTable();
		// Getting the component parameter with global settings
		$params = $this->getParams();

		/**
		 * Inserting the categories
		 * @since	2.5.1
		 */
		// Content categories
		$this->section = 'com_content'; 

		// Initialize values
		$aliases = array();
		$unique_alias_suffix = 1;
		
		$rootidmap = (reset($rows)['id'])+1;

		// JTable::store() run an update if id exists so we create them first
		foreach ($rows as $category)
		{
			$object = new stdClass();

			$category = (array) $category;

			if ($category['id'] == 1) {

				$object->id = $rootidmap;
				$category['old_id'] = $category['id'];
				$category['id'] = $rootidmap;
			}else{
				$object->id = $category['id'];
			}

			// Inserting the categories
			if (!$this->_db->insertObject($table, $object)) {
				echo $this->_db->getErrorMsg();
			}
		}

		$total = count($rows);


		// Update the category
		foreach ($rows as $category)
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

			// Updating the steps table
			$this->_step->_nextID($total);
		}

		return false;
	}
}
