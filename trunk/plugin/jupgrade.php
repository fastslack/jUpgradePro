<?php
/**
* @version $Id:
* @package Matware.jUpgradePro
* @copyright Copyright (C) 2005 - 2012 Matware. All rights reserved.
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
		require_once JPATH_PLUGINS . '/system/jupgrade/rest.php';
		require_once JPATH_PLUGINS . '/system/jupgrade/authorizer.php';
		require_once JPATH_PLUGINS . '/system/jupgrade/dispatcher.php';
		require_once JPATH_PLUGINS . '/system/jupgrade/table.php';
		
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
			}

			// Check the username and pass
			$dispatcher = new JRESTDispatcher;
		
			$return = $dispatcher->execute($rest->_parameters);

			if ($return !== false) {
				echo $return;
			}else{
				JResponse::setHeader('status', 400);
				JResponse::setBody('Dispatch error.');
				JResponse::sendHeaders();			
			}

			exit; // Exit
		}
		
		//exit; // Exit test
		
	} // end method
	
} // end class
