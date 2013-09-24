<?php
/**
* jUpgradePro
*
* @version $Id:
* @package jUpgradePro
* @copyright Copyright (C) 2004 - 2013 Matware. All rights reserved.
* @author Matias Aguirre
* @email maguirre@matware.com.ar
* @link http://www.matware.com.ar/
* @license GNU General Public License version 2 or later; see LICENSE
*/
// No direct access.
defined('_JEXEC') or die;

JLoader::register('jUpgrade', JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.class.php');
JLoader::register('jUpgradeStep', JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.step.class.php');
JLoader::register('jUpgradeExtensions', JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.extensions.class.php');

/**
 * jUpgradePro Model
 *
 * @package		jUpgradePro
 */
class jUpgradeProModelExtensions extends JModelLegacy
{
	/**
	 * Migrate the extensions
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	function extensions() {

		// Get the step
		$step = jUpgradeStep::getInstance('extensions', true);

		// Get jUpgradeExtensions instance
		$extensions = jUpgrade::getInstance($step);
		$success = $extensions->upgrade();
		if ($success === true) {
			$step->status = 2;
			$step->_updateStep ();
			if (! jUpgradeProHelper::isCli ()) {
				echo true;
			} else {
				return true;
			}
		}
	}
	
	/**
	 * returnError
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	public function returnError ($number, $text)
	{
		$message['number'] = $number;
		$message['text'] = JText::_($text);
		echo json_encode($message);
		exit;
	}
} // end class
