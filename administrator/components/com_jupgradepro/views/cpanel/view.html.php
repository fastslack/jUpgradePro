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

// No direct access.
defined('_JEXEC') or die;

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\Router\Route;

jimport('joomla.application.component.view');

/**
 * @package        jUpgradePro
 *
 * @since          1.0.0
 */
class JUpgradeproViewCpanel extends HtmlView
{
	/**
	 * Display the view.
	 *
	 * @param   string  $tpl  The sub-template to display.
	 *
	 * @return  void
	 * @since   1.0.0
	 * @throws  Exception
	 */
	function display($tpl = null)
	{
		$url  = 'https://www.matware.com.ar/projects/jupgradepro/jupgradepro-documentation.html';
		$url2 = 'http://www.matware.com.ar/jupgradepro-3rd-extensions-plugins/level.html';

		// Add a back button.
		ToolbarHelper::title(Text::_('jUpgradePro'), 'database');

		ToolbarHelper::link(Route::_('index.php?option=com_jupgradepro&view=site&layout=edit'), 'COM_JUPGRADEPRO_LINK_ADDNEW', 'folder-plus');
		ToolbarHelper::link(Route::_('index.php?option=com_jupgradepro&view=sites'), 'COM_JUPGRADEPRO_TITLE_SITES', 'list');

		//JToolbarHelper::preferences('com_jupgradepro', '500');
		ToolbarHelper::spacer();
		ToolbarHelper::help('help', false, $url);
		ToolbarHelper::spacer();

		$xmlfile = JPATH_COMPONENT_ADMINISTRATOR . '/jupgradepro.xml';
		$xml     = simplexml_load_file($xmlfile);

		$this->version = (string) $xml->version[0];

		parent::display($tpl);
	}
}
