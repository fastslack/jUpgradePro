<?php
/**
 * jUpgradePro
 *
 * @version   $Id:
 * @package   jUpgradePro
 * @copyright Copyright (C) 2004 - 2019 Matware. All rights reserved.
 * @author    Matias Aguirre
 * @email     maguirre@matware.com.ar
 * @link      http://www.matware.com.ar/
 * @license   GNU General Public License version 2 or later; see LICENSE
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Filesystem\File;

// Unset memory limit
ini_set('memory_limit', '-1');

// Load composer
$autoload = JPATH_COMPONENT_ADMINISTRATOR . '/vendor/autoload.php';

if (File::exists($autoload))
{
	$loader = require $autoload;
}

// Access check.
if (!Factory::getUser()->authorise('core.manage', 'com_jupgradepro'))
{
	return JError::raiseWarning(404, Text::_('JERROR_ALERTNOAUTHOR'));
}

$task = Factory::getApplication()->input->get('task');

// Getting the controller
$controller = BaseController::getInstance('JUpgradepro');
$controller->execute(Factory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
