<?php
/**
 * jUpgrade
 *
 * @version		$Id$
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @copyright	Copyright 2006 - 2011 Matias Aguire. All rights reserved.
 * @license		GNU General Public License version 2 or later.
 * @author		Matias Aguirre <maguirre@matware.com.ar>
 * @link		http://www.matware.com.ar
 */

/**
 * Upgrade class for 3rd party templates
 *
 * This class search for templates to be migrated
 *
 * @since	0.4.8
 */
class jUpgradeTemplates extends jUpgrade
{
	/**
	 * @var		string	The name of the destination database table.
	 * @since	0.4.8
	 */
	public $destination = '#__template_styles';


	/**
	 * Get the raw data for this part of the upgrade.
	 *
	 * @return	array	Returns a reference to the source data array.
	 * @since	0.4.8
	 * @throws	Exception
	 */
	protected function &getSourceData()
	{
		// Getting the params
		$params = $this->getParams();

		if ($params->path != '') {
			// Getting the folders list
			$folders = JFolder::folders($params->path.DS.'templates');
			$folders = array_diff($folders, array("system", "beez"));
			sort($folders);

			$rows = array();
			// Do some custom post processing on the list.
			for($i=0;$i<count($folders);$i++) {

				$rows[$i] = array();
				$rows[$i]['template'] = $folders[$i];
				$rows[$i]['client_id'] = 0;
				$rows[$i]['home'] = 0;
				$rows[$i]['title'] = $folders[$i];

			}
		}

		return $rows;
	}
}
