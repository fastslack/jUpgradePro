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

		$conditions['select'] = '`id`, `id` AS sid, `title`, \'\' AS `alias`, 1 AS parent_id, `section` AS extension, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`, `section`';

		$where_or = array();
		$where_or[] = "section REGEXP '^[\\-\\+]?[[:digit:]]*\\.?[[:digit:]]*$'";
		$where_or[] = "section IN ('com_banner', 'com_contact', 'com_contact_details', 'com_content', 'com_newsfeeds', 'com_sections', 'com_weblinks' )";
		$conditions['where_or'] = $where_or;

		$conditions['order'] = "id ASC, section ASC, ordering ASC";	

		return $conditions;
	}

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 * @since	0.5.6
	 * @throws	Exception
	 */
	public function databaseHook($rows = null)
	{
		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			$row['params'] = $this->convertParams($row['params']);
			$row['title'] = str_replace("'", "&#39;", $row['title']);
			$row['description'] = str_replace("'", "&#39;", $row['description']);

			if ($row['extension'] == 'com_banner') {
				$row['extension'] = "com_banners";
			}else if ($row['extension'] == 'com_contact_details') {
				$row['extension'] = "com_contact";
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
	public function dataHook($rows = null)
	{
		// Getting the destination table
		$table = $this->getDestinationTable();
		// Getting the component parameter with global settings
		$params = $this->getParams();
		// Content categories
		$this->section = 'com_content'; 
		// Get the total
		$total = count($rows);

		// JTable::store() run an update if id exists so we create them first
		if ($this->params->keep_ids == 1)
		{
			foreach ($rows as $category)
			{
				$category = (array) $category;

				// Check if id = 1
				if ($category['id'] == 1) {
					$rootidcategory = true;
					continue;
				}else{
					$id = $category['id'];
				}

				$query = $this->_db->getQuery(true);
				$query->insert('#__categories')->columns('`id`')->values($id);

				try {
					$this->_db->setQuery($query)->execute();
				} catch (RuntimeException $e) {
					throw new RuntimeException($e->getMessage());
				}
			}
		}

		// Update the category
		foreach ($rows as $category)
		{
			$category = (array) $category;

			// Check if id = 1
			if ($category['id'] == 1) {
				// Set correct values
				$category['root_id'] = 1;
				unset($category['id']);
				unset($category['sid']);
				unset($category['section']);
				// We need an object
				$category = (object) $category;

				try	{
					$this->_db->insertObject('#__jupgradepro_default_categories', $category);
				}	catch (Exception $e) {
					throw new Exception($e->getMessage());
				}

				// Updating the steps table
				$this->_step->_nextID($total);

				continue;
			}

			// Reset some fields
			$category['asset_id'] = $category['lft'] = $category['rgt'] = null;
			// Check if path is correct
			$category['path'] = empty($category['path']) ? $category['alias'] : $category['path'];
			// Fix the access
			$category['access'] = $category['access'] == 0 ? 1 : $category['access'] + 1;
			// Set the correct parent id
			$category['parent_id'] = $category['level'] = 1;

			// Insert the category
			$this->insertCategory($category);

			// Updating the steps table
			$this->_step->_nextID($total);
		}

		$rootcatobj = $this->getFirstCategory();

		// Insert the category id = 1
		if (is_array($rootcatobj) && $this->getTotal() == $this->_step->cid)
		{
			// Check if path is correct
			$rootcatobj['path'] = empty($rootcatobj['path']) ? $rootcatobj['alias'] : $rootcatobj['path'];
			// Fix the access
			$rootcatobj['access'] = $rootcatobj['access'] == 0 ? 1 : $rootcatobj['access'] + 1;
			// Set the correct parent id
			$rootcatobj['parent_id'] = $rootcatobj['level'] = 1;

			// Insert the category
			$this->insertCategory($rootcatobj);

			// Updating the steps table
			$this->_step->_nextID($total);
		}

		return false;
	}
}
