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

use Joomla\CMS\MVC\Model\AdminModel;
use Jupgradenext\Tables\Site;

/**
 * Jupgradepro model.
 *
 * @since  3.8
 */
class JupgradeproModelSite extends AdminModel
{
	/**
	 * @var      string    The prefix to use with controller messages.
	 * @since    3.8
	 */
	protected $text_prefix = 'COM_JUPGRADEPRO';

	/**
	 * @var   	string  	Alias to manage history control
	 * @since   3.8
	 */
	public $typeAlias = 'com_jupgradepro.site';

	/**
	 * @var null  Item data
	 * @since  3.8
	 */
	protected $item = null;

	/**
	 * Returns a reference to the a Table object, always creating it.
	 *
	 * @param   string  $type    The table type to instantiate
	 * @param   string  $prefix  A prefix for the table class name. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return    JTable    A database object
	 *
	 * @since    3.8
	 */
	public function getTable($type = 'Site', $prefix = 'JupgradeproTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm  A JForm object on success, false on failure
	 *
	 * @since    3.8
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Initialise variables.
		$app = JFactory::getApplication();

		// Get the form.
		$form = $this->loadForm(
			'com_jupgradepro.site', 'site',
			array('control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return   mixed  The data for the form.
	 *
	 * @since    3.8
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = JFactory::getApplication()->getUserState('com_jupgradepro.edit.site.data', array());

		if (empty($data))
		{
			if ($this->item === null)
			{
				$this->item = $this->getItem();
			}

			$data = $this->item;
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since    3.8
	 */
	public function getItem($pk = null)
	{

		if (empty($pk))
		{
			$pk = (int) JFactory::getApplication()->input->get('id', 0);
		}

		if ($pk !== 0)
    {
      $item = Site::find($pk)->toArray();
    }else{
			$item = new Site();
		}

		$jsonlist = array('database', 'restful', 'skips');

		foreach ($jsonlist as $key => $value) {

			$jsondecode = json_decode($item[$value], true);
			$item = array_merge($item, $jsondecode);
		}

		return (object) $item;
	}

	/**
	 * Method to duplicate an Product
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  Exception
	 */
	public function save($data)
	{
		$id = \JFactory::getApplication()->input->get('id', 0, 'int');

    if ($id !== 0)
    {
      $site = Site::find($id);
    }else{
			$site = new Site();
		}

		if ($site->saveSite($data))
		{
			return true;
		}

		return false;
	}

	/**
	 * Method to duplicate an Product
	 *
	 * @param   array  &$pks  An array of primary key IDs.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  Exception
	 */
	public function duplicate(&$pks)
	{
		$user = JFactory::getUser();

		// Access checks.
		if (!$user->authorise('core.create', 'com_jupgradepro'))
		{
			throw new Exception(JText::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
		}

		$dispatcher = JEventDispatcher::getInstance();
		$context    = $this->option . '.' . $this->name;

		// Include the plugins for the save events.
		JPluginHelper::importPlugin($this->events_map['save']);

		$table = $this->getTable();

		foreach ($pks as $pk)
		{
			if ($table->load($pk, true))
			{
				// Reset the id to create a new record.
				$table->id = 0;

				if (!$table->check())
				{
					throw new Exception($table->getError());
				}

				// Trigger the before save event.
				$result = $dispatcher->trigger($this->event_before_save, array($context, &$table, true));

				if (in_array(false, $result, true) || !$table->store())
				{
					throw new Exception($table->getError());
				}

				// Trigger the after save event.
				$dispatcher->trigger($this->event_after_save, array($context, &$table, true));
			}
			else
			{
				throw new Exception($table->getError());
			}
		}

		// Clean cache
		$this->cleanCache();

		return true;
	}

	/**
	 * Prepare and sanitise the table prior to saving.
	 *
	 * @param   JTable  $table  Table Object
	 *
	 * @return void
	 *
	 * @since    3.8
	 */
	protected function prepareTable($table)
	{
		jimport('joomla.filter.output');

		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = JFactory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__jupgradepro_sites');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}
}
