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

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

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
	 * @var    string    Alias to manage history control
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
	 * @return  JTable  A database object
	 *
	 * @since    3.8
	 */
	public function getTable($type = 'Site', $prefix = 'JupgradeproTable', $config = array())
	{
		return Table::getInstance($type, $prefix, $config);
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   array    $data      An optional array of data for the form to interogate.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  JForm|boolean  A JForm object on success, false on failure
	 *
	 * @throws  Exception
	 * @since   3.8
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_jupgradepro.site', 'site',
			array('control'   => 'jform',
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
	 * @throws   Exception
	 * @since    3.8
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_jupgradepro.edit.site.data', array());

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
	 * @throws  Exception
	 * @since    3.8
	 */
	public function getItem($pk = null)
	{
		if (empty($pk))
		{
			$pk = (int) Factory::getApplication()->input->get('id', 0);
		}

		if ($pk !== 0)
		{
			$item = (array) parent::getItem($pk);

			if (empty($data['id']))
			{
				$data['id'] = (int) $pk;
			}
		}
		else
		{
			$item = array('database' => '[]', 'restful' => '[]', 'skips' => '[]');
		}

		$jsonlist = array('database', 'restful', 'skips');

		foreach ($jsonlist as $key => $value)
		{
			$jsondecode = json_decode($item[$value], true);
			$item       = array_merge($item, $jsondecode);
		}

		array_splice($item, 0, 1);

		return $item;
	}

	/**
	 * Method to save the site data
	 *
	 * @param   array  &$data  An array with the site data.
	 *
	 * @return  boolean  True if successful.
	 *
	 * @throws  Exception
	 * @since   3.8.0
	 */
	public function save($data)
	{
		// Save restful, db and skips as
		$db = $rest = $skip = array();

		foreach ($data as $key => &$value)
		{
			$tag = explode("_", $key);

			switch ($tag[0])
			{
				case 'db':
					$db[$key] = $value;
					unset($data[$key]);
					break;

				case 'rest':
					$rest[$key] = $value;
					unset($data[$key]);
					break;

				case 'skip':
					$skip[$key] = $value;
					unset($data[$key]);
					break;
			}
		}

		$data['database'] = json_encode($db);
		$data['restful']  = json_encode($rest);
		$data['skips']    = json_encode($skip);

		if (empty($data['id']))
		{
			$data['id'] = (int) Factory::getApplication()->input->get('id', 0);
		}

		return parent::save($data);
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
		if (empty($table->id))
		{
			// Set ordering to the last item if not set
			if (@$table->ordering === '')
			{
				$db = Factory::getDbo();
				$db->setQuery('SELECT MAX(ordering) FROM #__jupgradepro_sites');
				$max             = $db->loadResult();
				$table->ordering = $max + 1;
			}
		}
	}
}
