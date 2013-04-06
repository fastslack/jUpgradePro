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
	 * @since   1.6
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
		// Get the document object.
		$document	= JFactory::getDocument();
		$vName		= 'Checks';

		// Get the model for the view.
		$model = $this->getModel($vName);

		// @TODO Create document
		echo $model->checks();
	}

	/**
	 * Run the jUpgradePro cleanup
	 */
	public function cleanup()
	{
		// Get the document object.
		$document	= JFactory::getDocument();
		$vName		= 'Cleanup';

		// Get the model for the view.
		$model = $this->getModel($vName);

		// @TODO Create document
		echo $model->cleanup();
	}

	/**
	 * Run jUpgradePro step
	 */
	public function step()
	{
		// Get the document object.
		$document	= JFactory::getDocument();
		$vName		= 'Step';

		// Get the model for the view.
		$model = $this->getModel($vName);

		// @TODO Create document
		echo $model->step();
	}

	/**
	 * Run jUpgradePro migrate
	 */
	public function migrate()
	{
		// Get the document object.
		$document	= JFactory::getDocument();
		$vName		= 'Migrate';

		// Get the model for the view.
		$model = $this->getModel($vName);

		// @TODO Create document
		echo $model->migrate();
	}

	/**
	 * Run jUpgradePro extensions
	 */
	public function extensions()
	{
		// Get the document object.
		$document	= JFactory::getDocument();
		$vName		= 'Extensions';

		// Get the model for the view.
		$model = $this->getModel($vName);

		// @TODO Create document
		echo $model->extensions();
	}
}
