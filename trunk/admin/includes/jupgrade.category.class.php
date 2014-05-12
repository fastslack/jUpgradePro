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
				throw new Exception($table->getError());
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

		// Disable observers calls
		// @@ Prevent Joomla! 'Application Instantiation Error' when try to call observers
		// @@ See: https://github.com/joomla/joomla-cms/pull/3408
		if (version_compare(JUpgradeproHelper::getVersion('new'), '3.0', '>=')) {
			//$category->_observers->doCallObservers(false);
		}

		// Get section and old id
		$oldlist = new stdClass();
		$oldlist->section = !empty($row['extension']) ? $row['extension'] : 0;
		$oldlist->old = isset($row['old_id']) ? (int) $row['old_id'] : (int) $row['id'];
		unset($row['old_id']);

		// Setting the default rules
		$rules = array();
		$rules['core.create'] = $rules['core.delete'] = $rules['core.edit'] = $rules['core.edit.state'] = $rules['core.edit.own'] = '';
		$row['rules'] = $rules;

		// Correct extension
		if (isset($row['extension'])) {
			if ($this->params->keep_ids == 1 && is_numeric($row['extension']) || $row['extension'] == "" || $row['extension'] == "category") {
				$row['extension'] = "com_content";
			}

			// Fixing extension name if it's section
			if ($row['extension'] == 'com_section') {
				$row['id'] = 0;
				$row['extension'] = "com_content";
				$category->setLocation(1, 'last-child');
			}
		}

		// Fix language
		$row['language'] = !empty($row['language']) ? $row['language'] : '*';

		// Check if path is correct
		$row['path'] = empty($row['path']) ? $row['alias'] : $row['path'];

		// Check if has duplicated aliases
		$alias = $this->getAlias('#__categories', $row['alias']);

		// Prevent MySQL duplicate error
		// @@ Duplicate entry for key 'idx_client_id_parent_id_alias_language'
		$row['alias'] = (!empty($alias)) ? $alias."~" : $row['alias'];

		// Remove the default id if keep ids parameters is not enabled
		if ($this->params->keep_ids != 1) {
			unset($row['id']);

			if ($row['parent_id'] != 1) {
				$oldlist->section = $row['parent_id'];
			}else{
				$parent = 1;
			}
		}

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
		$oldlist->new = (int) $category->id;

		// Insert the row backup
		if (!$this->_db->insertObject('#__jupgradepro_categories', $oldlist)) {
			throw new Exception($this->_db->getErrorMsg());
		}

	 	return true;
	}
}
