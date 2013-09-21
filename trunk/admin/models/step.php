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

/**
 * jUpgradePro Model
 *
 * @package		jUpgradePro
 */
class jUpgradeProModelStep extends JModelLegacy
{
	/**
	 * Initial checks in jUpgradePro
	 *
	 * @return	none
	 * @since	1.2.0
	 */
	public function step($name = false, $json = true, $extensions = false) {

		// Check if extensions exists if not get it from URI request
		$extensions = (bool) ($extensions != false) ? $extensions : JRequest::getCmd('extensions', false);

		// Getting the jUpgradeStep instance
		$step = jUpgradeStep::getInstance(null, $extensions);

		// Check if name exists
		$name = !empty($name) ? $name : $step->name;

		// Get the next step
		$step->getStep($name);

		if (!jUpgradeProHelper::isCli()) {
			echo $step->getParameters();
		}else{
			return $step->getParameters();
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
