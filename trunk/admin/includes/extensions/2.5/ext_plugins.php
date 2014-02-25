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
/**
 * Upgrade class for 3rd party extensions plugins
 *
 * This class takes the weblinks from the existing site and inserts them into the new site.
 *
 * @since	3.0.0
 */
class JUpgradeproExtensionsPlugins extends JUpgradepro
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
		
		$conditions['as'] = "p";
		
		$conditions['select'] = '`name`, `element`, `type`, `folder`, `client_id`, `ordering`, `params`';

		$where = array();
		$where[] = "`type` = 'plugin'";
		$where[] = "element   NOT   IN   ('joomla',   'ldap',   'gmail',   'openid',   'content',   'categories',   'contacts',   'sections',   'newsfeeds',   'weblinks',   'pagebreak',   'vote',   'emailcloak',   'geshi',   'loadmodule',   'pagenavigation', 'none',   'tinymce',   'xstandard',   'image',   'readmore',   'sef',   'debug',   'legacy',   'cache',   'remember', 'backlink', 'log', 'blogger', 'mtupdate' )";
		
		$conditions['where'] = $where;

		//$conditions['group_by'] = 'element';
		
		return $conditions;
	}
}
