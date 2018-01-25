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
 * View class for a list of Jupgradepro.
 *
 * @since  3.8
 */
class JupgradeproViewSites extends JViewLegacy
{
	protected $items;

	protected $pagination;

	protected $state;

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
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->filterForm = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		JupgradeproHelper::addSubmenu('sites');
		$this->addToolbar();

		//$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since    1.6
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = JupgradeproHelper::getActions();

		JToolBarHelper::title(JText::_('COM_JUPGRADEPRO_TITLE_SITES'), 'stack article');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/site';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				JToolBarHelper::addNew('site.add', 'JTOOLBAR_NEW');

				if (isset($this->items[0]))
				{
					JToolbarHelper::custom('sites.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
				}
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				JToolBarHelper::editList('site.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::custom('sites.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				JToolBarHelper::custom('sites.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				JToolBarHelper::deleteList('', 'site.delete', 'JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->state))
			{
				JToolBarHelper::divider();
				JToolBarHelper::archiveList('sites.archive', 'JTOOLBAR_ARCHIVE');
			}

			if (isset($this->items[0]->checked_out))
			{
				JToolBarHelper::custom('sites.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				JToolBarHelper::deleteList('', 'sites.delete', 'JTOOLBAR_EMPTY_TRASH');
				JToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				JToolBarHelper::trash('sites.trash', 'JTOOLBAR_TRASH');
				JToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			JToolBarHelper::preferences('com_jupgradepro');
		}

		JToolBarHelper::cancel('site.cancel', 'JTOOLBAR_BACK');

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_jupgradepro&view=products');
	}

	/**
	 * Method to order fields
	 *
	 * @return void
	 */
	protected function getSortFields()
	{
		return array(
			'a.`id`' => JText::_('JGRID_HEADING_ID'),
			'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
			'a.`state`' => JText::_('JSTATUS'),
			'a.`name`' => JText::_('COM_JUPGRADEPRO_PRODUCTS_GROUP')
		);
	}

  /**
   * Check if state is set
   *
   * @param   mixed  $state  State
   *
   * @return bool
   */
  public function getState($state)
  {
      return isset($this->state->{$state}) ? $this->state->{$state} : false;
  }

	/**
   * Check if state is set
   *
   * @param   mixed  $state  State
   *
   * @return bool
   */
  public function fixJSON($json)
  {
		$decode = json_decode($json);

		if (!isset($decode))
		{
			return false;
		}

		foreach ($decode as $key => &$value) {
			if ($key == 'db_password' || $key == 'rest_password')
			{
				$value = '*********************';
			}

			if ($value == "0")
			{
				$value = JText::_('JNO');
			}else if ($value == "1")
			{
				$value = JText::_('JYES');
			}

		}

		$return = '<pre>'. print_r($decode,1) . '</pre>';

		$return = str_replace("stdClass Object", "", $return);

    return $return;
  }
}
