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

JLoader::register('JUpgradepro', JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.class.php');
JLoader::register('jUpgrade', JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.compat.php');
JLoader::register('JUpgradeproStep', JPATH_COMPONENT_ADMINISTRATOR.'/includes/jupgrade.step.class.php');

/**
 * jUpgradePro Model
 *
 * @package		jUpgradePro
 */
class JUpgradeproModelMigrate extends JModelLegacy
{
	/**
	 * Migrate
	 *
	 * @return	none
	 * @since	3.0.3
	 */
	function migrate($table = false, $json = true, $extensions = false) {

		$table = (bool) ($table != false) ? $table : JRequest::getCmd('table', '');
		$extensions = (bool) ($extensions != false) ? $extensions : JRequest::getCmd('extensions', '');

		// Init the jUpgrade instance
		$step = JUpgradeproStep::getInstance($table, $extensions);
		$jupgrade = JUpgradepro::getInstance($step);

		// Get the database structure
		if ($step->first == true && $extensions == 'tables') {
			$structure = $jupgrade->getTableStructure();
		}

		// Run the upgrade
		if ($step->total > 0) {
			try
			{
				$jupgrade->upgrade();
			}
			catch (Exception $e)
			{
				throw new Exception($e->getMessage());
			}
		}

		// Javascript flags
		if ( $step->cid == $step->stop+1 && $step->total != 0) {
			$step->next = true;
		}
		if ($step->name == $step->laststep) {
			$step->end = true;
		}

		$empty = false;
		if ($step->cid == 0 && $step->total == 0 && $step->start == 0 && $step->stop == 0) {
			$empty = true;
		} 

		if ($step->stop == 0) {
			$step->stop = -1;
		}

		// Update #__jupgradepro_steps table if id = last_id
		if ( ( ($step->total <= $step->cid) || ($step->stop == -1) && ($empty == false) ) )
		{
			$step->next = true;
			$step->status = 2;

			$step->_updateStep();
		}

		if (!JUpgradeproHelper::isCli()) {
			echo $step->getParameters();
		}else{
			return $step->getParameters();
		}
	}
} // end class
