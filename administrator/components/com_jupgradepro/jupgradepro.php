<?php
/**
 * jUpgradePro
 *
 * @version $Id:
 * @package jUpgradePro
 * @copyright Copyright (C) 2004 - 2018 Matware. All rights reserved.
 * @author Matias Aguirre
 * @email maguirre@matware.com.ar
 * @link http://www.matware.com.ar/
 * @license GNU General Public License version 2 or later; see LICENSE
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

// Turn off all error reporting
//error_reporting(0);
error_reporting(E_ALL);
ini_set( 'display_errors','1');

$loader = require JPATH_COMPONENT_ADMINISTRATOR . '/vendor/autoload.php';

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_jupgradepro'))
{
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}

// import joomla controller library
jimport('joomla.application.component.controller');

// Getting the controller
$controller	= JControllerLegacy::getInstance('JUpgradepro');
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
