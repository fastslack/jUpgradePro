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

JLoader::register('JUpgradepro', JPATH_LIBRARIES.'/jupgrade/jupgrade.class.php');
JLoader::register('jUpgrade', JPATH_LIBRARIES.'/jupgrade/jupgrade.compat.php');
JLoader::register('JUpgradeproStep', JPATH_LIBRARIES.'/jupgrade/jupgrade.step.class.php');
JLoader::register('JUpgradeproExtensions', JPATH_LIBRARIES.'/jupgrade/jupgrade.extensions.class.php');

/**
 * jUpgradePro Model
 *
 * @package		jUpgradePro
 */
class JUpgradeproModelExtensions extends JModelLegacy
{
	/**
	 * Migrate the extensions
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	function extensions() {

		// Get the step
		$step = JUpgradeproStep::getInstance('extensions', true);

		// Get jUpgradeExtensions instance
		$extensions = JUpgradepro::getInstance($step);
		$success = $extensions->upgrade();

		if ($success === true) {
			$step->status = 2;
			$step->_updateStep();

			if (!JUpgradeproHelper::isCli()) {
				print(1);
			}else{
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
		print(json_encode($message));
		exit;
	}

} // end class
