<?php
/**
 * @version       $Id: 
 * @package       jUpgrade
 * @subpackage    jUpgradeCli
 * @copyright     CopyRight 2006-2012 Matware All rights reserved.
 * @author        Matias Aguirre
 * @email         maguirre@matware.com.ar
 * @link          http://www.matware.com.ar/
 * @license       GNU/GPL http://www.gnu.org/licenses/gpl-2.0-standalone.html
 */

/*
 * Change the following path to suit where you have cloned or installed the Joomla Platform repository.
 * The default setting assumes you have the joomla platform and examples folder at the same level.
 */

// Setup the base path related constant.
define('JPATH_BASE', dirname(__FILE__));
define('JPATH_SITE', dirname(__FILE__));
define('JPATH_ROOT', dirname(__FILE__));
define('JPATH_LIBRARIES', dirname(dirname(dirname(__FILE__))).'/joomla-cms.my/libraries'   );
define('JPATH_CACHE', dirname(dirname(dirname(__FILE__))).'/joomla-cms.my/cache'   );
define('JPATH_COMPONENT', dirname(dirname(__FILE__)).'/trunk/admin' );
define('JPATH_COMPONENT_ADMINISTRATOR', dirname(dirname(__FILE__)).'/trunk/admin');

// Import the Joomla! Platform
require JPATH_LIBRARIES.'/import.legacy.php';
// Import the JCli class from the platform.
jimport('joomla.application.cli');
// Require the files
jimport('joomla.filesystem.file');
jimport('joomla.database.database');
// Require the files
require_once JPATH_COMPONENT.'/includes/jupgrade.step.class.php';
require_once JPATH_COMPONENT.'/includes/jupgrade.class.php';
require_once JPATH_COMPONENT.'/includes/jupgrade.category.class.php';
require_once JPATH_COMPONENT.'/models/jupgrade.model.php';
require_once JPATH_COMPONENT.'/models/rest.php';
require_once JPATH_COMPONENT.'/models/ajax.php';
