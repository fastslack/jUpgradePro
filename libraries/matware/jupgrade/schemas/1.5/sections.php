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
JLoader::register("JUpgradeproCategory", JPATH_LIBRARIES."/matware/jupgrade/category.php");

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
	 * Method to do pre-processes modifications before migrate
	 *
	 * @return	boolean	Returns true if all is fine, false if not.
	 * @since	3.2.2
	 * @throws	Exception
	 */
	public function beforeHook()
	{
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

			// Fix the access
			$row['access'] = $row['access'] == 0 ? 1 : $row['access'] + 1;

			$row['extension'] = 'com_section';
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
		// Fixing the parents
		$this->fixParents();
		// Insert existing categories
		$this->insertExisting();

		// Change protected to $observers object to disable it
		// @@ Prevent Joomla! 'Application Instantiation Error' when try to call observers
		// @@ See: https://github.com/joomla/joomla-cms/pull/3408
		if (version_compare(JUpgradeproHelper::getVersion('new'), '3.0', '>=')) {
			$file = JPATH_LIBRARIES.'/joomla/observer/updater.php';
			$read = JFile::read($file);
			$read = str_replace("//call_user_func_array(\$eventListener, \$params)", "call_user_func_array(\$eventListener, \$params)", $read);
			$read = JFile::write($file, $read);

			require_once($file);
		}
	}

	/**
	 * Update the categories parent's
	 *
	 * @return	void
	 * @since	3.0
	 */
	protected function fixParents()
	{
		$change_parent = $this->getMapList('categories', false, "section REGEXP '^[\\-\\+]?[[:digit:]]*\\.?[[:digit:]]*$' AND section != 0");

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
} // end class
