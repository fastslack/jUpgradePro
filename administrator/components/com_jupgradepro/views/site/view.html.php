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

jimport('joomla.application.component.view');

/**
 * View to edit
 *
 * @since  3.8
 */
class JupgradeproViewSite extends JViewLegacy
{
	protected $state;

	protected $item;

	protected $form;

	/**
	 * Display the view
	 *
	 * @param   string  $tpl  Template name
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	public function display($tpl = null)
	{
		JLoader::import('helpers.jupgradepro', JPATH_COMPONENT_ADMINISTRATOR);

		$this->state = $this->get('State');
		$this->item  = $this->get('Item');
		$this->form  = $this->get('Form');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @throws Exception
	 */
	protected function addToolbar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);

		$user  = JFactory::getUser();
		$isNew = ($this->item->id == 0);

		if (isset($this->item->checked_out))
		{
			$checkedOut = !($this->item->checked_out == 0 || $this->item->checked_out == $user->get('id'));
		}
		else
		{
			$checkedOut = false;
		}

		$canDo = JupgradeproHelper::getActions();

		JToolBarHelper::title(JText::_('COM_JUPGRADEPRO_TITLE_ADDNEW'), 'folder-plus');

		// If not checked out, can save the item.
		if (($canDo->get('core.edit') || ($canDo->get('core.create'))))
		{
			JToolBarHelper::apply('site.apply', 'JTOOLBAR_APPLY');
			JToolBarHelper::save('site.save', 'JTOOLBAR_SAVE');
		}

		if (empty($this->item->id))
		{
			JToolBarHelper::cancel('site.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			JToolBarHelper::cancel('site.cancel', 'JTOOLBAR_CLOSE');
		}
	}
}
