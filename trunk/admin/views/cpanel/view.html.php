<?php
/**
 * jUpgrade
 *
 * @version		$Id$
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @copyright	Copyright 2006 - 2011 Matias Aguire. All rights reserved.
 * @license		GNU General Public License version 2 or later.
 * @author		Matias Aguirre <maguirre@matware.com.ar>
 * @link		http://www.matware.com.ar
 */

// No direct access.
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );

/**
 * @package		MatWare
 * @subpackage	com_jupgrade
 */
class jupgradeProViewCpanel extends JViewLegacy
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
		JToolbarHelper::title(JText::_( 'jUpgradePro' ), 'jupgrade');
		JToolbarHelper::custom('cpanel', 'back.png', 'back_f2.png', 'Back', false, false);
		JToolbarHelper::preferences('com_jupgradepro', '500');
		JToolbarHelper::spacer();
		JToolbarHelper::custom('help', 'help.png', 'help_f2.png', 'Help', false, false);
		JToolbarHelper::spacer();

		// Get params
		JLoader::import('helpers.jupgradepro', JPATH_COMPONENT_ADMINISTRATOR);
		$params = jUpgradeProHelper::getParams();

		//
		// Joomla bug: JInstaller not save the defaults params reading config.xml
		//
		$db = JFactory::getDBO();

		if (!$params->get('method')) {
			$default_params = '{"method":"rest","rest_hostname":"http:\/\/www.example.org\/","rest_username":"","rest_password":"","rest_key":"","path":"","driver":"mysql","hostname":"localhost","username":"","password":"","database":"","prefix":"jos_","skip_checks":"0","skip_files":"1","skip_templates":"1","skip_extensions":"1","skip_core_users":"0","skip_core_categories":"0","skip_core_sections":"0","skip_core_contents":"0","skip_core_contents_frontpage":"0","skip_core_menus":"0","skip_core_menus_types":"0","skip_core_modules":"0","skip_core_modules_menu":"0","skip_core_banners":"0","skip_core_banners_clients":"0","skip_core_banners_tracks":"0","skip_core_contacts":"0","skip_core_newsfeeds":"0","skip_core_weblinks":"0","positions":"0","debug":"0"}';

			$query = "UPDATE #__extensions SET `params` = '{$default_params}' WHERE `element` = 'com_jupgradepro'";
			$db->setQuery( $query );
			$db->query();

			// Get params.. again
			$params		= jUpgradeProHelper::getParams();
		}

		// Load mooTools
		//JHTML::_('behavior.mootools'); // 2.5
		JHtml::_('behavior.framework', true);

		$xmlfile = JPATH_COMPONENT_ADMINISTRATOR.'/jupgradepro.xml';

		$xml = JFactory::getXML($xmlfile);

		$this->params =	$params;
		$this->version = $xml->version[0];

		parent::display($tpl);
	}
}
