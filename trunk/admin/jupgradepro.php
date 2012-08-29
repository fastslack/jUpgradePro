<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_jupgradepro')) 
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}
 
// require helper file
JLoader::register('jUpgradeProHelper', dirname(__FILE__) . '/helpers/jupgradepro.php');
 
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
