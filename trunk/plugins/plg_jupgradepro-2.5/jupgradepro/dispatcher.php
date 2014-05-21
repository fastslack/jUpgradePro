<?php
/**
* @version $Id:
* @package Matware.jUpgradePro
* @copyright Copyright (C) 2005 - 2014 Matware. All rights reserved.
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
	private $skip_steps = array('banners', 'banners_clients', 'banners_tracks', 'categories', 'contacts', 'contents', 'contents_frontpage', 'ext_categories', 'ext_components', 'ext_modules', 'ext_plugins', 'generic', 'menus', 'menus_types', 'modules', 'modules_menu', 'newsfeeds', 'usergroupmap', 'users', 'weblinks');

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
		$keepid = isset($this->_parameters['HTTP_KEEPID']) ? $this->_parameters['HTTP_KEEPID'] : 0;

		// Fixing table if is extension
		$table = (substr($table, 0, 4) == 'ext_') ? substr($table, 4) : $table;

		// Check task is only to test the connection
		if ($task == 'check') {
			$xmlfile = JPATH_PLUGINS .'/system/jupgradepro/jupgradepro.xml';
			$xml = JFactory::getXML($xmlfile);
			$ret = (string) $xml->version[0];

			return $ret;
		}

		// Loading table
		if (!empty($table)) {
			JTable::addIncludePath(JPATH_ROOT .'/plugins/system/jupgradepro/jupgradepro/table');

			if (!in_array($name, $this->skip_steps)) {
				$class = JUpgradeproTable::getInstance('generic', 'JUpgradeproTable');
				$class->changeTable($table);
			}else{
				$class = JUpgradeproTable::getInstance($name, 'JUpgradeproTable');
			}

		}else if (isset($files)) {
			require_once JPATH_ROOT .'/plugins/system/jupgradepro/jupgradepro/files.php';
			$class = new JUpgradeproFiles();
		}

		// Get the method name
		$method = 'get'.ucfirst($task);

		// Does the method exist?
		if (method_exists($class, $method))
		{
			return ($task == 'rows') ? $class->$method($chunk, $keepid) : $class->$method();
		}
		else
		{
			return false;	
		}

	}
}
