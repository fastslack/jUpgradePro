<?php
/**
 * jUpgradePro
 *
 * @version $Id:
 * @package jUpgradePro
 * @copyright Copyright (C) 2004 - 2018 Matware. All rights reserved.
 * @author Matias Aguirre
 * @email maguirre@matware.com.ar
 * @link http://www.matware.com.ar/
 * @license GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.controllerform');

use Joomla\Utilities\ArrayHelper;

/**
 * Product controller class.
 *
 * @since  3.8
 */
class JupgradeproControllerSite extends JControllerForm
{
	/**
	 * Constructor
	 *
	 * @throws Exception
	 */
	public function __construct()
	{
		$this->view_list = 'sites';
		parent::__construct();
	}

	/**
	 * Method to clone existing Products
	 *
	 * @return void
	 */
	public function delete()
	{
		// Check for request forgeries
		Jsession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		// Get id(s)
		$pks = $this->input->post->get('cid', array(), 'array');

		try
		{
			if (empty($pks))
			{
				throw new Exception(JText::_('COM_JUPGRADEPRO_NO_ELEMENT_SELECTED'));
			}

			ArrayHelper::toInteger($pks);
			$model = $this->getModel();
			$model->delete($pks);
			$this->setMessage(Jtext::_('COM_JUPGRADEPRO_ITEMS_SUCCESS_DELETED'));
		}
		catch (Exception $e)
		{
			JFactory::getApplication()->enqueueMessage($e->getMessage(), 'warning');
		}

		$this->setRedirect('index.php?option=com_jupgradepro&view=sites');
	}

  /**
	 * Method to cancel an edit.
	 *
	 * @param   string  $key  The name of the primary key of the URL variable.
	 *
	 * @return  boolean  True if access level checks pass, false otherwise.
	 *
	 * @since   12.2
	 */
	public function cancel($key = null)
	{
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$app = JFactory::getApplication();

		$this->setRedirect(
			JRoute::_(
				'index.php?option=com_jupgradepro', false
			)
		);

		return true;
	}
}
