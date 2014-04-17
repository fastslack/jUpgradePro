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
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die;

/**
 * Upgrade class for menus
 *
 * This class takes the menus from the existing site and inserts them into the new site.
 *
 * @since	3.2.1
 */
class JUpgradeproMenus extends JUpgradepro
{

	/**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 *
	 * @since   12.2
	 */
	public function migrateLink(&$row)
	{
    // Fixing menus URLs
    if (strpos($row->link, 'option=com_content') !== false)
		{
      if (strpos($row->link, 'view=frontpage') !== false) {
        $row->link = 'index.php?option=com_content&view=featured';
      } 
    }

    if ( (strpos($row->link, 'Itemid=') !== false) AND $row->type == 'menulink')
		{
      // Extract the Itemid from the URL
      if (preg_match('|Itemid=([0-9]+)|', $row->link, $tmp))
			{
      	$item_id = $tmp[1];

        $row->params = $row->params . "\naliasoptions=".$item_id;
        $row->type = 'alias';
        $row->link = 'index.php?Itemid=';
      }
    }

    if (strpos($row->link, 'option=com_user&') !== false)
		{
      $row->link = preg_replace('/com_user/', 'com_users', $row->link);
      $row->component_id = 25;
			$row->option = 'com_users';

			// Change the register view to registration
      if (strpos($row->link, 'view=register') !== false)
			{
        $row->link = 'index.php?option=com_users&view=registration';
      }
			else if (strpos($row->link, 'view=user') !== false)
			{
        $row->link = 'index.php?option=com_users&view=profile';
      }
    }

    // Extract the id from the URL
    if (preg_match('|id=([0-9]+)|', $row->link, $tmp))
		{
			$id = $tmp[1];

			if ( (strpos($row->link, 'layout=blog') !== false) AND
				( (strpos($row->link, 'view=category') !== false) OR
				(strpos($row->link, 'view=section') !== false) ) ) {
					$catid = $this->getMapListValue('categories', 'categories', 'old = ' . $id);
					$row->link = "index.php?option=com_content&view=category&layout=blog&id={$catid}";
			} elseif (strpos($row->link, 'view=section') !== false) {
					$catid = $this->getMapListValue('categories', 'com_section', 'old = ' . $id);
					$row->link = 'index.php?option=com_content&view=category&layout=blog&id='.$catid;
			}
		}
    // End fixing menus URL's

		return $row;
	}

}
