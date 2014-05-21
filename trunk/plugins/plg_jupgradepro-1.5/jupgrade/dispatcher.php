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
defined('_JEXEC') or die;

/**
 * REST Request Dispatcher class 
 *
 * @package     Joomla.Platform
 * @subpackage  REST
 * @since       3.0
 */
class JRESTDispatcher
{
	/**
	 * @var    array  Associative array of parameters for the REST message.
	 * @since  3.0
	 */
	private $_parameters = array();

	/**
	 * @var    JUpgradeTable  JUpgradeTable object
	 * @since  3.0
	 */
	private $_table = array();

	/**
	 * @var    array  A list with the steps to skip
	 * @since  3.0
	 */
	private $skip_steps = array('arogroup', 'banners', 'banners_clients', 'banners_tracks', 'categories', 'contacts', 'contents', 'contents_frontpage', 'ext_categories', 'ext_components', 'ext_modules', 'ext_plugins', 'generic', 'menus', 'menus_types', 'modules', 'modules_menu', 'newsfeeds', 'sections', 'usergroupmap', 'users', 'weblinks');

	/**
	 * 
	 *
	 * @return  boolean
	 *
	 * @since   3.0
	 */
	public function execute($parameters)
	{
		// Getting the database instance
		$db = JFactory::getDbo();	
	
		// Loading params
		$this->_parameters = $parameters;

		$task = isset($this->_parameters['HTTP_TASK']) ? $this->_parameters['HTTP_TASK'] : '';
		$name = $table = !empty($this->_parameters['HTTP_TABLE']) ? $this->_parameters['HTTP_TABLE'] : 'generic';
		$files = isset($this->_parameters['HTTP_FILES']) ? $this->_parameters['HTTP_FILES'] : '';
		$chunk = isset($this->_parameters['HTTP_CHUNK']) ? $this->_parameters['HTTP_CHUNK'] : '';

		// Fixing table if is extension
		$table = (substr($table, 0, 4) == 'ext_') ? substr($table, 4) : $table;

		// Check task is only to test the connection
		if ($task == 'check') {
			$xmlfile = JPATH_PLUGINS .'/system/jupgrade.xml';
			$xml = new JSimpleXML;
			$xml->loadFile($xmlfile);
			$ret = (string) $xml->document->version[0]->_data;

			return $ret;
		}

		// Loading table
		if (!empty($table)) {
			JTable::addIncludePath(JPATH_PLUGINS .'/system/jupgrade/table');

			if (!in_array($name, $this->skip_steps)) {
				$class = JUpgradeTable::getInstance('generic', 'JUpgradeTable');
				$class->changeTable($table);
			}else{
				$class = JUpgradeTable::getInstance($name, 'JUpgradeTable');
			}

		}else if (isset($files)) {
			require_once JPATH_PLUGINS .'/system/jupgrade/files.php';
			$class = new JUpgradeFiles();
		}

		// Get the method name
		$method = 'get'.ucfirst($task);

		// Does the method exist?
		if (method_exists($class, $method))
		{
			return ($task == 'rows') ? $class->$method($chunk) : $class->$method();
		}
		else
		{
			return false;	
		}
	}
}
