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

use Jupgradenext\Steps;
use Jupgradenext\Models\Checks;
use Jupgradenext\Models\Cleanup;
use Jupgradenext\Models\Migrate;
use Jupgradenext\Models\Sites;
use Jupgradenext\Models\Step;
use Joomla\DI\Container;

/**
 * The jUpgradePro ajax controller
 *
 * @package     jUpgradePro
 * @subpackage  com_jupgradepro
 * @since       3.0.3
 */
class JupgradeproControllerAjax extends JControllerLegacy
{
	/**
	 * @var		string	The context for persistent state.
	 * @since   3.0.3
	 */
	protected $context = 'com_jupgradepro.ajax';

	private $container;

	/**
	 * Proxy for getModel.
	 *
	 * @param   string	$name	The name of the model.
	 * @param   string	$prefix	The prefix for the model class name.
	 *
	 * @return  jUpgradeProModel
	 * @since   3.0.3
	 */
	public function getModel($name = '', $prefix = 'JUpgradeproModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));
		return $model;
	}

	/**
	 * Create container
	 */
	public function createContainer()
	{
		// Get a new DI container
		$this->container = new Container;

		$site = JFactory::getApplication()->input->get('site', false);

		// Set input to container
		$input = JFactory::getApplication()->input;
		$this->container->share('input', function (Container $c) use ($input) {
			return $input;
		}, true);

		// Set input to container
		$db = JFactory::getDbo();
		$this->container->share('db', function (Container $c) use ($db) {
			return $db;
		}, true);

		//$this->container->registerServiceProvider(new \Providers\DatabaseServiceProvider);
		$this->container->registerServiceProvider(new \Providers\SitesServiceProvider);
		$this->container->registerServiceProvider(new \Providers\StepsServiceProvider);

		// Set input to container
		$sites = $this->container->get('sites');
		$siteDbo = $sites->getSiteDbo($site);

		if ($siteDbo == false)
		{
			echo \JText::_('COM_JUPGRADEPRO_ERROR_SITE_NOT_EXIST');
			JFactory::getApplication()->close();
		}

		$this->container->share('external', function (Container $c) use ($siteDbo) {
			return $siteDbo;
		}, true);

		// Get the new site Joomla! version
		$v = new \JVersion();
		$version = (string) $v->RELEASE;

		$this->container->share('origin_version', function (Container $c) use ($version) {
			return $version;
		}, true);

		$this->container->share('default_site', function (Container $c) use ($site) {
			return $site;
		}, true);
	}

	/**
	 * Run the jUpgradePro checks
	 */
	public function checks()
	{
		// Create container
		$this->createContainer();

		// Get the model for the view.
		$checks = new Checks($this->container);

		// Running the migrate
		try {
			$checks->checks();
		} catch (Exception $e) {
			$checks->returnError (500, $e->getMessage());
		}
	}

	/**
	 * Run the jUpgradePro cleanup
	 */
	public function cleanup()
	{
		// Create container
		$this->createContainer();

		// Get the model for the view.
		$model = new Cleanup($this->container);

		// Running the cleanup
		try {
			echo $model->cleanup();
		} catch (Exception $e) {
			$model->returnError (500, $e->getMessage());
		}
	}

	/**
	 * Run jUpgradePro step
	 */
	public function step()
	{
		// Create container
		$this->createContainer();

		// Get the model for the view.
		$model = new Step($this->container);

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
		// Create container
		$this->createContainer();

		// Get the model for the view.
		$model = new Migrate($this->container);

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

	/**
	 * Get the component params
	 */
	public function check()
	{
		$return = '';
		$this->_db = JFactory::getDbo();

		$app = JFactory::getApplication();

		$site = $app->input->get('site', false);

		// Get a new DI container
		$this->createContainer();

		$model = new Checks($this->container);
		$version = $model->checkSite();

		if ($version != false)
		{
			echo "Joomla! version {$version} found!";
		}else{
			echo 'Check failed. Joomla! do not found.';
		}

		$app->close();
	}

	/**
	 * Get the component params
	 */
	public function show()
	{
		$return = '';
		$this->_db = JFactory::getDbo();

		// Get params
		JLoader::import('helpers.jupgradepro', JPATH_COMPONENT_ADMINISTRATOR);
		$params = JUpgradeproHelper::getParams();

		$app = JFactory::getApplication();

		$task = $app->input->get('command', false);

		if ($task == 'config')
		{
			$site = $app->input->get('site', false);

			// Create a new query object.
			$query	= $this->_db->getQuery(true);

			// Select the required fields from the table.
			$query->select("*");
			$query->from('#__jupgradepro_sites AS s');
			$query->where("s.name = '{$site}'");
			$query->limit(1);

			// Set query
			$this->_db->setQuery($query);

			// Execute the query
			try {
				$item = $this->_db->loadObject();
			} catch (RuntimeException $e) {
				throw new RuntimeException($e->getMessage());
			}

			$method = $item->method;
			$json = $item->$method;

			$return .= "\n".JText::_('COM_JUPGRADEPRO_CONFIG_NAME').": [[g;grey;]{$item->name}]";
			$return .= "\n".JText::_('COM_JUPGRADEPRO_TITLE_METHOD').": [[g;grey;]{$item->method}]";
			$return .= "\n".JText::_('COM_JUPGRADEPRO_TITLE_LIMIT').": [[g;grey;]{$item->chunk_limit}]\n";
			$return .= $this->fixJSON($json);

			print($return);

		}elseif ($task == 'sites') {

			// Get the model for the view.
			$model = $this->getModel('Sites');

			$items = $model->getItems();

			$return .= "\n".JText::_('COM_JUPGRADEPRO_CONFIG_NAME') . "        |     " . JText::_('COM_JUPGRADEPRO_TITLE_METHOD');
			$return .= "\n------------------------------------------\n";

			foreach ($items as $key => $value) {
				$return .= "[[g;grey;]{$value->name}]            |     " . $value->method . "\n";
			}

			print($return);
		}
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

		$return = print_r($decode,1);

		$return = str_replace("stdClass Object", "", $return);

    return $return;
  }

	/**
	 * returnError
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	public function returnError ($code, $message, $debug = false)
	{
		$response = array();
		$response['code'] = $code;
		$response['message'] = \JText::_($message);

		if ($debug != false)
		{
			$message['debug'] = $debug;
		}

		print(json_encode($response));
		exit;
	}
}
