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
defined('_JEXEC') or die;

/**
 * The jUpgradePro ajax controller 
 *
 * @package     jUpgradePro
 * @subpackage  com_jupgradepro
 * @since       3.0.3
 */
class jUpgradeProControllerAjax extends JControllerLegacy
{
	/**
	 * @var		string	The context for persistent state.
	 * @since   3.0.3
	 */
	protected $context = 'com_jupgradepro.ajax';

	/**
	 * Proxy for getModel.
	 *
	 * @param   string	$name	The name of the model.
	 * @param   string	$prefix	The prefix for the model class name.
	 *
	 * @return  jUpgradeProModel
	 * @since   3.0.3
	 */
	public function getModel($name = '', $prefix = 'jUpgradeProModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	/**
	 * Run the jUpgradePro checks
	 */
	public function checks()
	{
		// Get the model for the view.
		$model = $this->getModel('Checks');

		// Running the checks
		try {
			$model->checks();
		} catch (Exception $e) {
			$model->returnError (500, $e->getMessage());
		}

	}

	/**
	 * Run the jUpgradePro cleanup
	 */
	public function cleanup()
	{
		// Get the model for the view.
		$model = $this->getModel('Cleanup');

		// Running the cleanup
		try {
			$model->cleanup();
		} catch (Exception $e) {
			$model->returnError (500, $e->getMessage());
		}
	}

	/**
	 * Run jUpgradePro step
	 */
	public function step()
	{
		// Get the model for the view.
		$model = $this->getModel('Step');

		// Running the step
		try {
			$model->step(false, true);
		} catch (Exception $e) {
			$model->returnError (500, $e->getMessage());
		}
	}

	/**
	 * Run jUpgradePro migrate
	 */
	public function migrate()
	{
		// Get the model for the view.
		$model = $this->getModel('Migrate');

		// Running the migrate
		try {
			$model->migrate();
		} catch (Exception $e) {
			$model->returnError (500, $e->getMessage());
		}
	}

	/**
	 * Run jUpgradePro extensions
	 */
	public function extensions()
	{
		// Get the model for the view.
		$model = $this->getModel('Extensions');

		// Running the extensions
		try {
			$model->extensions();
		} catch (Exception $e) {
			$model->returnError (500, $e->getMessage());
		}
	}
}
