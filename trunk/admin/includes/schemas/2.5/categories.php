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

		$conditions['select'] = '`id`, `id` AS sid, `title`, `alias`, `extension`, `description`, `published`, `checked_out`, `checked_out_time`, `access`, `params`';

		$where_or = array();
		$where_or[] = "extension REGEXP '^[\\-\\+]?[[:digit:]]*\\.?[[:digit:]]*$'";
		$where_or[] = "extension IN ('com_banners', 'com_contact', 'com_content', 'com_newsfeeds', 'com_sections', 'com_weblinks' )";

		$conditions['order'] = "id DESC, extension DESC";		
		$conditions['where_or'] = $where_or;
		
		return $conditions;
	}

	/**
	 * Sets the data in the destination database.
	 *
	 * @return	void
	 * @since	0.5.6
	 * @throws	Exception
	 */
	public function dataHook2($rows = null)
	{
		print_r($rows);
/*
		// Getting the category table
		$category = JTable::getInstance('Category', 'JTable', array('dbo' => $this->_db));

		// Remove id
		foreach ($rows as $cat)
		{
			unset($cat->id);
			unset($cat->sid);
		}

		// Insert the categories
		foreach ($rows as $cat)
		{
			// Bind data to save category
			if (!$category->bind($cat)) {
				echo JError::raiseError(500, $category->getError());
			}

			// Insert the category
			if (!@$category->store()) {
				echo JError::raiseError(500, $category->getError());
			}
		}
*/
		return false;
	}
}
