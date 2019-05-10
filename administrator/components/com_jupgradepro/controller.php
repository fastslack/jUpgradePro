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

use Joomla\CMS\MVC\Controller\BaseController;

/**
 * General Controller of jUpgradePro component
 *
 * @since  1.0.0
 */
class JUpgradeproController extends BaseController
{
	/**
	 * display task
	 *
	 * @param   boolean  $cachable
	 * @param   array    $urlparams
	 *
	 * @return object
	 * @throws Exception
	 * @since  1.0.0
	 */
	function display($cachable = false, $urlparams = array())
	{
		$input = JFactory::getApplication()->input;

		$view = $input->get('view', false);

		if ($view == false)
		{
			$input->set('view', 'cpanel');
		}

		return parent::display();
	}
}
