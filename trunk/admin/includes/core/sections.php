<?php
/**
 * jUpgradePro
 *
 * @version		    $Id$
 * @package		    MatWare
 * @subpackage    com_jupgradepro
 * @author        Matias Aguirre <maguirre@matware.com.ar>
 * @link          http://www.matware.com.ar
 * @copyright		  Copyright 2004 - 2013 Matias Aguirre. All rights reserved.
 * @license		    GNU General Public License version 2 or later; see LICENSE.txt
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
class jUpgradeSections extends jUpgradeCategory
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
		$this->fixParents();
		$this->insertUncategorized();
	}

	protected function insertUncategorized()
	{
		$uncategorised = array('com_content', 'com_banners', 'com_contact', 'com_newsfeeds', 'com_weblinks', 'com_users.notes');

		//for($i=2;$i<=7;$i++) {
		foreach ($uncategorised as $uncat) {
			// Rebuild the categories table
			$table = JTable::getInstance('Category', 'JTable');

			// Setting the data
			$array = array();
			$array['extension'] = $uncat;
			$array['path'] = $array['alias'] = 'uncategorised';
			$array['title'] = 'Uncategorised';
			$array['access'] = $array['published'] = 1;
			$array['params'] = ($uncat == 'com_banners') ? '{"target":"","image":"","foobar":""}' : '{"target":"","image":""}';
			$array['metadata'] = '{"page_title":"","author":"","robots":""}';
			$array['created_user_id'] = 42;
			$array['language'] = '*';

			// Setting the default rules
			$rules = array();
			$rules['core.create'] = $rules['core.delete'] = $rules['core.edit'] = $rules['core.edit.state'] = $rules['core.edit.own'] = '';
			$array['rules'] = $rules;

			// Setting the location of the new category
			$table->setLocation(1, 'last-child');
			//
			$table->bind($array);
			// Store
			$table->store();
		}
	
		return true;
	}

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
			$parent = $this->getMapListValue('categories', 'com_section', $custom);

			// Setting the location of the new category
			$table->setLocation($parent, 'last-child');

			// Insert the category
			if (!@$table->store()) {
				throw new Exception($table->getError());
			}
		}
	}
}
