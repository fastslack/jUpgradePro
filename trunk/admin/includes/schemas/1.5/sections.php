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
 * Upgrade class for sections
 *
 * This class takes the sections from the existing site and inserts them into the new site.
 *
 * @since	0.4.5
 */
class JUpgradeproSections extends JUpgradeproCategory
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
	public function databaseHook($rows = null)
	{	
		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			$row = (array) $row;

			$row['params'] = $this->convertParams($row['params']);
			$row['title'] = str_replace("'", "&#39;", $row['title']);
			$row['description'] = str_replace("'", "&#39;", $row['description']);

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
	public function dataHook($rows = null)
	{
		$total = count($rows);

		// Insert the sections
		foreach ($rows as $section)
		{
			$section = (array) $section;

			// Inserting the category
			$this->insertCategory($section);

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
		// Insert existing categories
		$this->insertExisting();
		// Fixing the parents
		$this->fixParents();
	}

	/**
	 * Update the categories parent's
	 *
	 * @return	void
	 * @since	3.0
	 */
	protected function fixParents()
	{
		$change_parent = $this->getMapList('categories', false, "section REGEXP '^[\\-\\+]?[[:digit:]]*\\.?[[:digit:]]*$'");

		// Insert the sections
		foreach ($change_parent as $category)
		{
			// Getting the category table
			$table = JTable::getInstance('Category', 'JTable');
			$table->load($category->new);

			$custom = "old = {$category->section}";

			$parent = $this->getMapListValue('categories', 'com_section', $custom);

			if (!empty($parent))
			{
				// Setting the location of the new category
				$table->setLocation($parent, 'last-child');

				// Insert the category
				if (!$table->store()) {
					throw new Exception($table->getError());
				}
			}
		}
	}

	/**
	 * Insert existing categories saved in cleanup step
	 *
	 * @return	void
	 * @since	3.0
	 */
	protected function insertExisting()
	{
		// Getting the database instance
		$db = JFactory::getDbo();

		// Getting the data
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__jupgradepro_default_categories');
		$query->order('id ASC');
		$db->setQuery($query);
		$categories = $db->loadAssocList();

		foreach ($categories as $category) {

			// Unset id
			$category['id'] = 0;

			// Looking for parent
			$parent = 1;
			$explode = explode("/", $category['path']);

			if (count($explode) > 1) {

				// Getting the data
				$query = $db->getQuery(true);
				$query->select('id');
				$query->from('#__categories');
				$query->where("path = '{$explode[0]}'");
				$query->order('id ASC');
				$query->limit(1);

				$db->setQuery($query);
				$parent = $db->loadResult();

			}

			// Inserting the category
			$this->insertCategory($category, $parent);
		}

	} // end method
} // end class
