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
		$conditions = array();

		$conditions['select'] = '`id`, `id` AS sid, `title`, `alias`, `section`, `section` AS extension, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`';

		$where_or = array();
		$where_or[] = "section REGEXP '^[\\-\\+]?[[:digit:]]*\\.?[[:digit:]]*$'";
		$where_or[] = "section IN ('com_banner', 'com_contact', 'com_contact_details', 'com_content', 'com_newsfeeds', 'com_sections', 'com_weblinks' )";

		$conditions['order'] = "id DESC, section DESC, ordering DESC";		
		$conditions['where_or'] = $where_or;
		
		return $conditions;
	}

	/**
	 * Method to do pre-processes modifications before migrate
	 *
	 * @return	boolean	Returns true if all is fine, false if not.
	 * @since	3.2.0
	 * @throws	Exception
	 */
	public function beforeHook()
	{
		// Insert uncategorized id
		$query = $this->_db->getQuery(true);
		$query->insert('#__jupgradepro_categories')->columns('`old`, `new`')->values("0, 2");
		try {
			$this->_db->setQuery($query)->execute();
		} catch (RuntimeException $e) {
			throw new RuntimeException($e->getMessage());
		}

		if ($this->params->keep_ids == 1)
		{
			// Getting the categories
			$query->clear();
			$query->select("`id`, `parent_id`, `path`, `extension`, `title`, `alias`, `note`, `description`, `published`,  `params`, `created_user_id`");
			$query->from("#__categories");
			$query->where("id > 1");
			$query->order('id ASC');
			$this->_db->setQuery($query);

			try {
				$categories = $this->_db->loadObjectList();
			} catch (RuntimeException $e) {
				throw new RuntimeException($e->getMessage());
			}

			foreach ($categories as $category)
			{
				$id = $category->id;
				unset($category->id);

				$this->_db->insertObject('#__jupgradepro_default_categories', $category);

				// Getting the categories table
				$table = JTable::getInstance('Category', 'JTable');
				// Load it before delete. Joomla bug?
				$table->load($id);
				// Delete
				$table->delete($id);
			}
		}
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
		// Content categories
		$this->section = 'com_content'; 
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

			// Reset some fields
			$category['asset_id'] = $category['lft'] = $category['rgt'] = null;

			// Check if path is correct
			$category['path'] = empty($category['path']) ? $category['alias'] : $category['path'];

			if ($category['id'] == 1) {
				$category['id'] = $rootidmap;
			}

			// Insert the category
			$category['old_id'] = $category['id'];

			$this->insertCategory($category);

			// Updating the steps table
			$this->_step->_nextID($total);
		}

		return false;
	}
}
