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
// Require the category class
require_once JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.category.class.php';

/**
 * Upgrade class for categories
 *
 * This class takes the categories from the existing site and inserts them into the new site.
 *
 * @since	3.2.0
 */
class JUpgradeproCategories extends JUpgradeproCategory
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
		// Get the component parameters
		JLoader::import('helpers.jupgradepro', JPATH_COMPONENT_ADMINISTRATOR);
		$params = JUpgradeproHelper::getParams();

		$conditions = array();
		$conditions['select'] = '*';

		if ($params->keep_ids == 1)
		{
			$where_or = array();
			$where_or[] = "extension REGEXP '^[\\-\\+]?[[:digit:]]*\\.?[[:digit:]]*$'";
			$where_or[] = "extension IN ('com_banners', 'com_contact', 'com_content', 'com_newsfeeds', 'com_sections', 'com_weblinks' )";
			$conditions['where_or'] = $where_or;
			$conditions['order'] = "id DESC, extension DESC";	
		}else{
			$where = array();
			$where[] = "path != 'uncategorised'";
			$where[] = "(extension REGEXP '^[\-\+]?[[:digit:]]*\.?[[:digit:]]*$' OR extension IN ('com_banners', 'com_contact', 'com_content', 'com_newsfeeds', 'com_sections', 'com_weblinks' ))";
			$conditions['where'] = $where;
			$conditions['order'] = "parent_id DESC";	
		}

		return $conditions;
	}

	/**
	 * Sets the data in the destination database.
	 *
	 * @return	void
	 * @since	0.5.6
	 * @throws	Exception
	 */
	public function dataHook($rows = null)
	{
		// Getting the destination table
		$table = $this->getDestinationTable();
		// Getting the component parameter with global settings
		$params = $this->getParams();
		// Initialize values
		$rootidmap = 0;

		// JTable::store() run an update if id exists so we create them first
		if ($this->params->keep_ids == 1)
		{
			foreach ($rows as $category)
			{
				$object = new stdClass();

				$category = (array) $category;

				if ($category['id'] == 1) {
					$query = $this->_db->getQuery(true);
					$query->select("`id` + 1");
					$query->from("#__categories");
					$query->where("id > 1");
					$query->order('id DESC');
					$query->limit(1);
					$this->_db->setQuery($query);

					$object->id = $category['id'] = $rootidmap;
				}else{
					$object->id = $category['id'];
				}

				// Inserting the categories
				try {
					$this->_db->insertObject($table, $object);
				} catch (RuntimeException $e) {
					throw new RuntimeException($e->getMessage());
				}
			}
		}

		$total = count($rows);

		// Update the category
		foreach ($rows as $category)
		{
			$category = (array) $category;

			if ($category['id'] == 1) {
				$category['id'] = $rootidmap;
			}

			// Update the category data
			$this->insertCategory($category);

			// Updating the steps table
			$this->_step->_nextID($total);
		}

		return false;
	}

	/**
	 * Run custom code after hooks
	 *
	 * @return	void
	 * @since	3.0
	 */
	public function afterHook()
	{
		// Fixing the parents
		$this->fixParents();
		// Insert existing categories
		//$this->insertExisting();
	}

	/**
	 * Update the categories parent's
	 *
	 * @return	void
	 * @since	3.0
	 */
	protected function fixParents()
	{
		$change_parent = $this->getMapList('categories', false, "section != 0");

		// Insert the sections
		foreach ($change_parent as $category)
		{
			// Getting the category table
			$table = JTable::getInstance('Category', 'JTable');
			$table->load($category->new);

			$custom = "old = {$category->section}";
			$parent = $this->getMapListValue('categories', false, $custom);

			// Setting the location of the new category
			$table->setLocation($parent, 'last-child');

			// Insert the category
			if (!@$table->store()) {
				throw new Exception($table->getError());
			}
		}
	}
}
