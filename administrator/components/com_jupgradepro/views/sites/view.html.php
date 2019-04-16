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

use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Helper\ContentHelper;

use Jupgradenext\Upgrade\UpgradeHelper;

/**
 * View class for a list of sites to migrate.
 *
 * @since  3.8
 */
class JupgradeproViewSites extends HtmlView
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
	 * @since  3.8.0
	 */
	public function display($tpl = null)
	{
		JLoader::import('helpers.jupgradepro', JPATH_COMPONENT_ADMINISTRATOR);

		$this->state         = $this->get('State');
		$this->items         = $this->get('Items');
		$this->pagination    = $this->get('Pagination');
		$this->filterForm    = $this->get('FilterForm');
		$this->activeFilters = $this->get('ActiveFilters');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors));
		}

		//UpgradeHelper::addSubmenu('sites');
		$this->addToolbar();

		//$this->sidebar = JHtmlSidebar::render();
		parent::display($tpl);
	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @return void
	 *
	 * @since  3.8.0
	 */
	protected function addToolbar()
	{
		$state = $this->get('State');
		$canDo = ContentHelper::getActions('com_jupgradepro');

		ToolBarHelper::title(Text::_('COM_JUPGRADEPRO_TITLE_SITES'), 'stack article');

		// Check if the form exists before showing the add/edit buttons
		$formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/site';

		if (file_exists($formPath))
		{
			if ($canDo->get('core.create'))
			{
				ToolBarHelper::addNew('site.add', 'JTOOLBAR_NEW');
			}

			if ($canDo->get('core.edit') && isset($this->items[0]))
			{
				ToolBarHelper::editList('site.edit', 'JTOOLBAR_EDIT');
			}
		}

		if ($canDo->get('core.edit.state'))
		{
			if (isset($this->items[0]->state))
			{
				ToolBarHelper::divider();
				ToolBarHelper::custom('sites.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
				ToolBarHelper::custom('sites.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
			}
			elseif (isset($this->items[0]))
			{
				// If this component does not use state then show a direct delete button as we can not trash
				ToolBarHelper::deleteList('', 'site.delete', 'JTOOLBAR_DELETE');
			}

			if (isset($this->items[0]->checked_out))
			{
				ToolBarHelper::custom('sites.checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
			}
		}

		if (isset($this->items[0]))
		{
			// If this component does not use state then show a direct delete button as we can not trash
			ToolBarHelper::deleteList('', 'site.delete', 'JTOOLBAR_DELETE');
		}

		// Show trash and delete for components that uses the state field
		if (isset($this->items[0]->state))
		{
			if ($state->get('filter.state') == -2 && $canDo->get('core.delete'))
			{
				ToolBarHelper::deleteList('', 'sites.delete', 'JTOOLBAR_EMPTY_TRASH');
				ToolBarHelper::divider();
			}
			elseif ($canDo->get('core.edit.state'))
			{
				ToolBarHelper::trash('sites.trash', 'JTOOLBAR_TRASH');
				ToolBarHelper::divider();
			}
		}

		if ($canDo->get('core.admin'))
		{
			ToolBarHelper::preferences('com_jupgradepro');
		}

		ToolBarHelper::cancel('site.cancel', 'JTOOLBAR_BACK');

		// Set sidebar action - New in 3.0
		JHtmlSidebar::setAction('index.php?option=com_jupgradepro&view=products');
	}

	/**
	 * Method to order fields
	 *
	 * @return array
	 * @since  3.8.0
	 */
	protected function getSortFields()
	{
		return array(
			'a.`id`'       => Text::_('JGRID_HEADING_ID'),
			'a.`ordering`' => Text::_('JGRID_HEADING_ORDERING'),
			'a.`state`'    => Text::_('JSTATUS'),
			'a.`name`'     => Text::_('COM_JUPGRADEPRO_PRODUCTS_GROUP')
		);
	}

	/**
	 * Check if state is set
	 *
	 * @param   mixed  $state  State
	 *
	 * @return bool
	 * @since  3.8.0
	 */
	public function getState($state)
	{
		return isset($this->state->{$state}) ? $this->state->{$state} : false;
	}

	/**
	 * Fix JSON from database
	 *
	 * @param   string  $json  The Json to fix
	 *
	 * @return bool
	 * @since  3.8.0
	 */
	public function fixJSON($json)
	{
		$decode = json_decode($json);

		if (!isset($decode))
		{
			return false;
		}

		foreach ($decode as $key => &$value)
		{
			if ($key == 'db_password' || $key == 'rest_password')
			{
				$value = '*********************';
			}

			if ($value == "0")
			{
				$value = Text::_('JNO');
			}
			else if ($value == "1")
			{
				$value = Text::_('JYES');
			}

		}

		$return = '<pre>' . print_r($decode, 1) . '</pre>';

		$return = str_replace("stdClass Object", "", $return);

		return $return;
	}
}
