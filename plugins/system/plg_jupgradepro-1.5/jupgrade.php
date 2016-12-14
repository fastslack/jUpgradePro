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
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * Joomla! System JUpgrade Plugin
 *
 * @package		Joomla
 * @subpackage	System
 */
class plgSystemJUpgrade extends JPlugin
{
	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @access	protected
	 * @param	object	$subject The object to observe
	 * @param 	array   $config  An array that holds the plugin configuration
	 * @since	1.0
	 */
	function plgSystemJUpgrade(& $subject, $config) {

		parent::__construct($subject, $config);
	}

	function onAfterInitialise()
	{
		jimport('joomla.user.helper');
		require_once JPATH_ROOT .DS.'plugins' .DS.'system'.DS.'jupgrade'.DS.'rest.php';
		require_once JPATH_ROOT .DS.'plugins' .DS.'system'.DS.'jupgrade'.DS.'authorizer.php';
		require_once JPATH_ROOT .DS.'plugins' .DS.'system'.DS.'jupgrade'.DS.'dispatcher.php';
		require_once JPATH_ROOT .DS.'plugins' .DS.'system'.DS.'jupgrade'.DS.'table.php';

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

		$sqlfile = JPATH_ROOT .DS.'plugins'.DS.'system'.DS.'jupgrade'.DS.'sql'.DS.'install.sql';

		// Checking tables
		$query = "SHOW TABLES";
		$db->setQuery($query);
		$tables = $db->loadResultArray();

		if (!in_array('jupgrade_plugin_steps', $tables)) {
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
