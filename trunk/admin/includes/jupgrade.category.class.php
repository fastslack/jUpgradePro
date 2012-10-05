<?php
/**
 * jUpgrade
 *
 * @version		  $Id: 
 * @package		  MatWare
 * @subpackage	com_jupgrade
 * @author      Matias Aguirre <maguirre@matware.com.ar>
 * @link        http://www.matware.com.ar
 * @copyright		Copyright 2006 - 2011 Matias Aguirre. All rights reserved.
 * @license		  GNU General Public License version 2 or later; see LICENSE.txt
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
class jUpgradeCategory extends jUpgrade
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

/*
		if ($this->section == 'com_content' && $this->source == '#__categories') {
			$select = '`id`, `id` AS sid, `title`, `alias`, `section` AS extension, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`';
			$where = "section REGEXP '^[\\-\\+]?[[:digit:]]*\\.?[[:digit:]]*$'";
			$order = 'section ASC, ordering ASC';
		}else if ($this->source == '#__categories') {
			$select = '`id`, `id` AS sid, `title`, `alias`, `section` AS extension, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`';
			$where = "section = '{$this->section}'";
			$order = 'ordering ASC';
		}else if ($this->source == '#__sections') {
			$select = '`id` AS sid, `title`, `alias`, \'com_section\' AS extension, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`';
			$where = "scope = 'content'";
			$order = 'ordering ASC';
		}

		$rows = parent::getSourceData(
			$select,
		  null,
			$where,
			$order
		);
*/
		// Initialize values
		$aliases = array();
		$unique_alias_suffix = 1;

		// Do some custom post processing on the list.
		foreach ($rows as &$row)
		{
			$row['params'] = $this->convertParams($row['params']);
			$row['access'] = $row['access'] == 0 ? 1 : $row['access'] + 1;
			$row['title'] = str_replace("'", "&#39;", $row['title']);
			$row['description'] = str_replace("'", "&#39;", $row['description']);
			$row['language'] = '*';

			if ($row['extension'] == 'com_banner') {
				$row['extension'] = "com_banners";
			}else if ($row['extension'] == 'com_contact_details') {
				$row['extension'] = "com_contact";
			}

			// Correct alias
			if ($row['alias'] == "") {
				$row['alias'] = JFilterOutput::stringURLSafe($row['title']);
			}

			// The Joomla 1.6 database structure does not allow duplicate aliases
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
	 * @param   object  An object whose properties match table fields
	 * @since	0.4.
	 */
	public function insertCategory($object, $parent = false)
	{
		// Getting the category table
		$category = JTable::getInstance('Category', 'JTable', array('dbo' => $this->_db));

		// Get section and old id
		$oldlist = new stdClass();
		$oldlist->section = !empty($object['extension']) ? $object['extension'] : 0;
		$oldlist->old = isset($object['old_id']) ? $object['old_id'] : $object['id'];
		unset($object['old_id']);

		// Correct extension
		if (isset($object['extension'])) {
			if (is_numeric($object['extension']) || $object['extension'] == "" || $object['extension'] == "category") {
				$object['extension'] = "com_content";
			}

			// Fixing extension name if it's section
			if ($object['extension'] == 'com_section') {
				$object['extension'] = "com_content";

				$category->setLocation(1, 'last-child');
			}
		}

		// @@ TODO: maybe $parent flag is unused
		// If has parent made $path and get parent id
		if ($parent !== false) {
			// Setting the location of the new category
			$category->setLocation($parent, 'last-child');
		}

		// Bind data to save category
		if (!$category->bind($object)) {
			echo JError::raiseError(500, $category->getError());
		}

		// Insert the category
		if (!@$category->store()) {
			echo JError::raiseError(500, $category->getError());
		}

		// Get new id
		$oldlist->new = $category->id;

		// Save old and new id
		if (!$this->_db->insertObject('jupgrade_categories', $oldlist)) {
			throw new Exception($this->_db->getErrorMsg());
		}

	 	return true;
	}
}
