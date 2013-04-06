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
// No direct access.
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );

/**
 * @package		MatWare
 * @subpackage	com_jupgrade
 */
class jupgradeProViewRest extends JViewLegacy
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
		$task = JRequest::getCmd('task', '');

		if ($task == '') { 
			echo JText::_('ERROR: task not found');
		}

		$return	= $this->get(ucfirst($task));		
	
		echo $return;
	}
}
