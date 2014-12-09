<?php
/**
* jUpgradePro
*
* @version $Id:
* @package jUpgradePro
* @copyright Copyright (C) 2004 - 2014 Matware. All rights reserved.
* @author Matias Aguirre
* @email maguirre@matware.com.ar
* @link http://www.matware.com.ar/
* @license GNU General Public License version 2 or later; see LICENSE
*/

JLoader::register('JUpgradeproExtensions', JPATH_LIBRARIES."/jupgrade/jupgrade.extensions.class.php");

/**
 * Upgrade class for 3rd party extensions
 *
 * This class search for extensions to be migrated
 *
 * @since	0.4.5
 */
class JUpgradeproCheckExtensions extends JUpgradeproExtensions
{
	/**
	 * count adapters
	 * @var int
	 * @since	1.1.0
	 */
	public $count = 0;
	protected $extensions = array();

	public function upgrade()
	{
		if (!$this->upgradeComponents()) {
			return false;
		}

		if (!$this->upgradeModules()) {
			return false;
		}

		if (!$this->upgradePlugins()) {
			return false;
		}

		return ($this->_processExtensions() == 0) ? false : true;
	}

} // end class
