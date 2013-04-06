<?php
/**
 * jUpgradePro
 *
 * @version		    $Id$
 * @package		    MatWare
 * @subpackage    com_jupgradepro
 * @author        Matias Aguirre <maguirre@matware.com.ar>
 * @link          http://www.matware.com.ar
 * @copyright		  Copyright 2004 - 2013 Matias Aguirre. All rights reserved.
 * @license		    GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Turn off all error reporting
error_reporting(0);

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_jupgradepro')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// require helper file
//JLoader::register('jUpgradeProHelper', dirname(__FILE__) . '/helpers/jupgradepro.php');
 
// import joomla controller library
jimport('joomla.application.component.controller');
 
// Get an instance of the controller prefixed by jUpgradePro
//$controller = JController::getInstance('jUpgradePro');
// Perform the Request task
//$controller->execute(JRequest::getCmd('task'));

// Joomla 3.0
$controller	= JControllerLegacy::getInstance('jUpgradePro');
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
