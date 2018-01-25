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

// No direct access.
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );

/**
 * @package		MatWare
 * @subpackage	com_jupgrade
 */
class JUpgradeproViewCpanel extends JViewLegacy
{
	/**
	 * Display the view.
	 *
	 * @param	string	$tpl	The subtemplate to display.
	 *
	 * @return	void
	 */
	function display($tpl = null)
	{
		// Get DBo
		$db = JFactory::getDBO();

		$url = 'https://www.matware.com.ar/projects/jupgradepro/jupgradepro-documentation.html';
		$url2 = 'http://www.matware.com.ar/jupgradepro-3rd-extensions-plugins/level.html';

		$bar = JToolbar::getInstance('toolbar');

		// Add a back button.
		JToolbarHelper::title(JText::_( 'jUpgradePro' ), 'database');

		JToolbarHelper::link(JRoute::_('index.php?option=com_jupgradepro&view=site&layout=edit'), 'COM_JUPGRADEPRO_LINK_ADDNEW', 'folder-plus');
		JToolbarHelper::link(JRoute::_('index.php?option=com_jupgradepro&view=sites'), 'COM_JUPGRADEPRO_TITLE_SITES', 'list');

		//JToolbarHelper::preferences('com_jupgradepro', '500');
		JToolbarHelper::spacer();
		JToolbarHelper::help('help', false, $url);
		JToolbarHelper::spacer();

		$xmlfile = JPATH_COMPONENT_ADMINISTRATOR.'/jupgradepro.xml';

		$xml = JFactory::getXML($xmlfile);

		$this->version = (string) $xml->version[0];

		parent::display($tpl);
	}
}
