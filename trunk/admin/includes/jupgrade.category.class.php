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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * Upgrade class for categories
 *
 * This class takes the categories banners from the existing site and inserts them into the new site.
 *
 * @since	1.2.2
 */
class JUpgradeproCategory extends JUpgradepro
{
	/**
	 * @var		string	The name of the section of the categories.
	 * @since	1.2.2
	 */
	public $section = '';

	/**
	 * @var		string	The name of the destination database table.
	 * @since	3.0.0
	 */
	protected $destination = '#__categories';

	/**
	 * @var		string	The key of the table
	 * @since	3.0.0
	 */
	protected $_tbl_key = 'id';

	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 * @since	0.5.6
	 * @throws	Exception
	 */
	public function databaseHook($rows = null)
	{
		// Initialize values
		$aliases = array();
		$unique_alias_suffix = 1;

		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			$row = (array) $row;

			$row['params'] = $this->convertParams($row['params']);
			$row['title'] = str_replace("'", "&#39;", $row['title']);
			$row['description'] = str_replace("'", "&#39;", $row['description']);

			if ($row['extension'] == 'com_banner') {
				$row['extension'] = "com_banners";
			}else if ($row['extension'] == 'com_contact_details') {
				$row['extension'] = "com_contact";
			}

			// Correct alias
			if ($row['alias'] == "") {
				$row['alias'] = JFilterOutput::stringURLSafe($row['title']);
			}

			// The Joomla 2.5/3.0+ database structure does not allow duplicate aliases
			if (in_array($row['alias'], $aliases, true)) {
				$row['alias'] = $row['alias'].$unique_alias_suffix;
				$unique_alias_suffix++;
			}
			$aliases[] = $row['alias'];
		}

		return $rows;
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
		// Remove id
		foreach ($rows as $category)
		{
			unset($category->id);
		}

		// Insert the categories
		foreach ($rows as $category)
		{
			$this->insertCategory($category);
		}

		return false;
	}

	/**
	 * The public entry point for the class.
	 *
	 * @return	void
	 * @since	0.5.6
	 * @throws	Exception
	 */
	public function upgrade()
	{
		if (parent::upgrade()) {
			// Rebuild the categories table
			$table = JTable::getInstance('Category', 'JTable', array('dbo' => $this->_db));

			if (!$table->rebuild()) {
				echo JError::raiseError(500, $table->getError());
			}
		}
	}

	/**
	 * Inserts a category
	 *
	 * @access  public
	 * @param   row  An array whose properties match table fields
	 * @since	0.4.
	 */
	public function insertCategory($row, $parent = false)
	{
		// Getting the category table
		$category = JTable::getInstance('Category', 'JTable', array('dbo' => $this->_db));

		// Get section and old id
		$oldlist = new stdClass();
		$oldlist->section = !empty($row['extension']) ? $row['extension'] : 0;
		$oldlist->old = isset($row['old_id']) ? $row['old_id'] : $row['id'];
		unset($row['old_id']);

		// Setting the default rules
		$rules = array();
		$rules['core.create'] = $rules['core.delete'] = $rules['core.edit'] = $rules['core.edit.state'] = $rules['core.edit.own'] = '';
		$row['rules'] = $rules;

		// Correct extension
		if (isset($row['extension'])) {
			if (is_numeric($row['extension']) || $row['extension'] == "" || $row['extension'] == "category") {
				$row['extension'] = "com_content";
			}

			// Fixing extension name if it's section
			if ($row['extension'] == 'com_section') {
				$row['extension'] = "com_content";

				$category->setLocation(1, 'last-child');
			}
		}

		// Getting the data
		$query = $this->_db->getQuery(true);
		$query->select('alias');
		$query->from('#__categories');
		$query->where("alias LIKE '{$row['alias']}%'");
		$query->order('id DESC');
		$query->limit(1);
		$this->_db->setQuery($query);
		$alias = $this->_db->loadColumn();

		// Prevent MySQL duplicate error
		// @@ Duplicate entry for key 'idx_client_id_parent_id_alias_language'
		$c = count($alias);
		if ($c > 0)
		{
			$row['alias'] = $alias[$c-1] . str_repeat("~", $c);
		}

		// @@ TODO: maybe $parent flag is unused
		// If has parent made $path and get parent id
		if ($parent !== false) {
			// Setting the location of the new category
			$category->setLocation($parent, 'last-child');
		}

		// Bind data to save category
		if (!$category->bind($row)) {
			throw new Exception($category->getError());
		}

		// Insert the category
		if (!$category->store()) {
			throw new Exception($category->getError());
		}

		// Get new id
		$oldlist->new = $category->id;

		// Save old and new id
		if (!$this->_db->insertObject('#__jupgradepro_categories', $oldlist)) {
			throw new Exception($this->_db->getErrorMsg());
		}

	 	return true;
	}
}
