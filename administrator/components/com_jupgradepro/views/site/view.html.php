<?php
/**
 * jUpgradePro
 *
 * @version   $Id:
 * @package   jUpgradePro
 * @copyright Copyright (C) 2004 - 2019 Matware. All rights reserved.
 * @author    Matias Aguirre
 * @email     maguirre@matware.com.ar
 * @link      http://www.matware.com.ar/
 * @license   GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ContentHelper;

/**
 * View to edit a site to migrate
 *
 * @since  3.8.0
 */
class JupgradeproViewSite extends HtmlView
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
	 * @since  3.8.0
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

		if (JVersion::MAJOR_VERSION === 4) {
			$this->addToolbar40();
		}else{
			$this->addToolbar();
		}

		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @throws Exception
	 * @since  3.8.0
	 */
	protected function addToolbar()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$canDo = ContentHelper::getActions('com_jupgradepro');

		ToolBarHelper::title(Text::_('COM_JUPGRADEPRO_TITLE_ADDNEW'), 'folder-plus');

		// If not checked out, can save the item.
		if (($canDo->get('core.edit') || ($canDo->get('core.create'))))
		{
			ToolBarHelper::apply('site.apply', 'JTOOLBAR_APPLY');
			ToolBarHelper::save('site.save', 'JTOOLBAR_SAVE');
		}

		if (empty($this->item->id))
		{
			ToolBarHelper::cancel('site.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			ToolBarHelper::cancel('site.cancel', 'JTOOLBAR_CLOSE');
		}
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return  void
	 *
	 * @since  4.0.0
	 * @throws Exception
	 */
	protected function addToolbar40()
	{
		Factory::getApplication()->input->set('hidemainmenu', true);

		$user       = Factory::getUser();
		$userId     = $user->id;
		$isNew      = empty($this->item->id);

		$canDo = ContentHelper::getActions('com_jupgradepro');

		ToolBarHelper::title(Text::_('COM_JUPGRADEPRO_TITLE_ADDNEW'), 'folder-plus');

		$toolbarButtons = [];

		if ($isNew)
		{
			// For new records, check the create permission.
			if ($canDo->get('core.edit'))
			{
				ToolbarHelper::apply('stage.apply');
				$toolbarButtons = [['save', 'site.save'], ['save2new', 'site.save2new']];
			}

			ToolbarHelper::saveGroup(
				$toolbarButtons,
				'btn-success'
			);
		}
		else
		{
			// Since it's an existing record, check the edit permission, or fall back to edit own if the owner.
			$itemEditable = $canDo->get('core.edit') || ($canDo->get('core.edit.own') && $this->item->created_by == $userId);

			if ($itemEditable)
			{
				ToolbarHelper::apply('site.apply');
				$toolbarButtons = [['save', 'site.save']];

				// We can save this record, but check the create permission to see if we can return to make a new one.
				if ($canDo->get('core.create'))
				{
					$toolbarButtons[] = ['save2new', 'site.save2new'];
					$toolbarButtons[] = ['save2copy', 'site.save2copy'];
				}
			}

			ToolbarHelper::saveGroup(
				$toolbarButtons,
				'btn-success'
			);
		}

		ToolbarHelper::cancel('site.cancel');
		ToolbarHelper::divider();
	}
}
