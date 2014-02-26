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

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Joomla! System JUpgradePro Plugin
 *
 * @package		Joomla
 * @subpackage	System
 */
class plgSystemJUpgradepro extends JPlugin
{
	function onAfterInitialise()
	{
		jimport('joomla.user.helper');
		JLoader::register('JRESTMessage', JPATH_ROOT .'/plugins/system/jupgradepro/jupgradepro/rest.php');
		JLoader::register('JRESTAuthorizer', JPATH_ROOT .'/plugins/system/jupgradepro/jupgradepro/authorizer.php');
		JLoader::register('JRESTDispatcher', JPATH_ROOT .'/plugins/system/jupgradepro/jupgradepro/dispatcher.php');
		JLoader::register('JUpgradeproTable', JPATH_ROOT .'/plugins/system/jupgradepro/jupgradepro/table.php');

		// Check if jupgrade_steps exists
		$this->checkStepTable();
		
		// Getting the database instance
		$db = JFactory::getDbo();

		$request = false;

		// Get the REST message from the current request.
		$rest = new JRESTMessage;
		
		if ($rest->loadFromRequest())
		{
			$request = true;
		}

		// Request was found
		if ($request == true) {

			// Check the username and pass
			$auth = new JRESTAuthorizer;

			if (!$auth->authorize($db, $rest->_parameters))
			{
				JResponse::setHeader('status', 400);
				JResponse::setBody('Invalid password.');
				JResponse::sendHeaders();
				exit;
			}

			// Check the username and pass
			$dispatcher = new JRESTDispatcher;
		
			$return = $dispatcher->execute($rest->_parameters);

			if ($return !== false) {
				echo $return;
			}else{
				JResponse::setHeader('status', 401);
				JResponse::setBody('Dispatch error.');
				JResponse::sendHeaders();
				exit;
			}

			exit; // Exit
		}

		//exit; // Exit test
		
	} // end method


	function checkStepTable()
	{
		// Getting the database instance
		$db = JFactory::getDbo();	

		$sqlfile = JPATH_ROOT .'/plugins/system/jupgradepro/jupgradepro/sql/install.sql';
	
		// Checking tables
		$tables = $db->getTableList();

		if (!in_array('jupgradepro_plugin_steps', $tables)) {
			$this->populateDatabase( $db, $sqlfile );
		}		

	} // end method

	/**
	 * populateDatabase
	 */
	function populateDatabase(& $db, $sqlfile)
	{
		if( !($buffer = file_get_contents($sqlfile)) )
		{
			return -1;
		}

		$queries = $db->splitSql($buffer);

		foreach ($queries as $query)
		{
			$query = trim($query);
			if ($query != '' && $query {0} != '#')
			{
				$db->setQuery($query);
				$db->query() or die($db->getErrorMsg());
			}
		}

		return true;
	}
	
} // end class
