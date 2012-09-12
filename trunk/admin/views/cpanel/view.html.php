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

		// get params
		$params		= JComponentHelper::getParams('com_jupgradepro');

		// Set timelimit to 0
		if(!@ini_get('safe_mode')) {
			if ($params->get('timelimit') == 0) {
				set_time_limit(0);
			}
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
