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
JLoader::register('JUpgradeproDriver', JPATH_LIBRARIES.'/jupgrade/jupgrade.driver.class.php');
JLoader::register('JUpgradeproStep', JPATH_LIBRARIES.'/jupgrade/jupgrade.step.class.php');

/**
 * jUpgradePro Model
 *
 * @package		jUpgradePro
 */
class JUpgradeproModelCleanup extends JModelLegacy
{
	/**
	 * Cleanup
	 *
	 * @return	none
	 * @since	1.2.0
	 */
	function cleanup()
	{
		// Loading the helper
		JLoader::import('helpers.jupgradepro', JPATH_COMPONENT_ADMINISTRATOR);
		// Importing helper tags
		jimport('cms.helper.tags');
		// Getting the component parameter with global settings
		$params = JUpgradeproHelper::getParams();

		// Initialise the tables array
		$del_tables = array();

		// If REST is enable, cleanup the source #__jupgradepro_steps table
		if ($params->method == 'rest') {
			$driver = JUpgradeproDriver::getInstance();
			$code = $driver->requestRest('cleanup');
		}

		// Set all cid, status and cache to 0
		$query = $this->_db->getQuery(true);
		$query->update('#__jupgradepro_steps')->set('cid = 0, status = 0, cache = 0, total = 0, stop = 0, start = 0, stop = 0, first = 0, debug = \'\'');
		$this->_db->setQuery($query)->execute();

		// Convert the params to array
		$core_skips = (array) $params;

		// Skiping the steps setted by user
		foreach ($core_skips as $k => $v) {
			$core = substr($k, 0, 9);
			$name = substr($k, 10, 18);

			if ($core == "skip_core") {
				if ($v == 1) {

					// Disable the the steps setted by user
					$this->updateStep($name);

					if ($name == 'users') {
						// Disable the sections step
						$this->updateStep('arogroup');

						// Disable the sections step
						$this->updateStep('usergroupmap');
					}

					if ($name == 'categories') {
						// Disable the sections step
						$this->updateStep('sections');
					}
				}
			}

			if ($k == 'skip_extensions') {
				if ($v == 1) {
					// Disable the extensions step
					$this->updateStep('extensions');
				}
				else if ($v == 0)
				{
					// Add the tables to truncate for extensions
					$del_tables[] = '#__jupgradepro_extensions_tables';
				}
			}
		}

		// Truncate menu types if menus are enabled
		if ($params->skip_core_categories != 1)
		{
			$del_tables[] = '#__jupgradepro_categories';
			$del_tables[] = '#__jupgradepro_default_categories';

			$query->clear();
			$query->insert('#__jupgradepro_categories')->columns('`old`, `new`')->values("0, 2");
			try {
				$this->_db->setQuery($query)->execute();
			} catch (RuntimeException $e) {
				throw new RuntimeException($e->getMessage());
			}
		}

		// Truncate menu types if menus are enabled
		if ($params->skip_core_menus != 1 && $params->keep_ids == 1)
		{
			$del_tables[] = '#__menu_types';
			$del_tables[] = '#__jupgradepro_menus';
		}

		// Truncate contents if are enabled
		if ($params->skip_core_modules != 1)
			$del_tables[] = '#__jupgradepro_modules';

		// Truncate contents if are enabled
		if ($params->skip_core_contents != 1 && $params->keep_ids == 1)
			$del_tables[] = '#__content';

		for ($i=0;$i<count($del_tables);$i++) {
			$query->clear();
			$query->delete()->from("{$del_tables[$i]}");

			try {
				$this->_db->setQuery($query)->execute();
			} catch (RuntimeException $e) {
				throw new RuntimeException($e->getMessage());
			}
		}

		// Done checks
		if (!JUpgradeproHelper::isCli())
			$this->returnError (100, 'DONE');
	}

	/**
	 * Update the status of one step
	 *
	 * @param		string  $name  The name of the table to update
	 *
	 * @return	none
	 *
	 * @since	3.1.1
	 */
	public function updateStep ($name)
	{
		// Get the version
		$version = JUpgradeproHelper::getVersion('old');

		// Get the JQuery object
		$query = $this->_db->getQuery(true);

		$query->update('#__jupgradepro_steps')->set('status = 2')->where('name = \''.$name.'\'')->where('version = \''.$version.'\'');
		try {
			$this->_db->setQuery($query)->execute();
		} catch (RuntimeException $e) {
			throw new RuntimeException($e->getMessage());
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
