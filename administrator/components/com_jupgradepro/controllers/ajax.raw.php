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

use Joomla\DI\Container;
use Joomla\CMS\Filesystem\Folder;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Language\Text;

use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\StringOutput;
use Symfony\Component\Console\Input\ArrayInput;

use Jupgradenext\Steps;
use Jupgradenext\Models\Checks;
use Jupgradenext\Models\Cleanup;
use Jupgradenext\Models\Migrate;
use Jupgradenext\Models\Sites;
use Jupgradenext\Models\Step;
use Jupgradenext\Models\Extensions;
use Jupgradenext\Upgrade\UpgradeHelper;

/**
 * The jUpgradePro ajax controller
 *
 * @package     jUpgradePro
 * @subpackage  com_jupgradepro
 * @since       3.8.0
 */
class JupgradeproControllerAjax extends AdminController
{
	/**
	 * @var     string    The context for persistent state.
	 * @since   3.8.0
	 */
	protected $context = 'com_jupgradepro.ajax';

	private $container;

	protected $composer_data = array(
		'url'  => 'https://getcomposer.org/composer.phar',
		'dir'  => '/administrator/components/com_jupgradepro/',
		'bin'  => '/media/com_jupgradepro/phar/composer.phar',
		'json' => '/administrator/components/com_jupgradepro/composer.json',
		'conf' => array(
			"minimum-stability" => "dev"
		)
	);

	/**
	 * Proxy for getModel.
	 *
	 * @param   string  $name    The name of the model.
	 * @param   string  $prefix  The prefix for the model class name.
	 *
	 * @return  object
	 * @since   3.8.0
	 */
	public function getModel($name = '', $prefix = 'JupgradeproModel', $config = array())
	{
		$model = parent::getModel($name, $prefix, array('ignore_request' => true));

		return $model;
	}

	/**
	 * Create container
	 *
	 * @since   3.8.0
	 */
	public function createContainer()
	{
		$this->checkLibraries();

		// Get a new DI container
		$this->container = new Container;

		$site   = Factory::getApplication()->input->get('site', false);
		$config = Factory::getConfig();

		// Set config to container
		$this->container->share('config', function (Container $c) use ($config) {
			return $config;
		}, true);

		// Set default site to container
		$this->container->share('default_site', function (Container $c) use ($site) {
			return $site;
		}, true);

		// Set input to container
		$input = Factory::getApplication()->input;
		$this->container->share('input', function (Container $c) use ($input) {
			return $input;
		}, true);

		// Set input to container
		$db = Factory::getDbo();
		$this->container->share('db', function (Container $c) use ($db) {
			return $db;
		}, true);

		// Get extensions input
		$extensions = $input->get('extensions', false);
		$this->container->share('extensions', function (Container $c) use ($extensions) {
			return $extensions;
		}, true);

		//$this->container->registerServiceProvider(new \Providers\DatabaseServiceProvider);
		$this->container->registerServiceProvider(new \Providers\SitesServiceProvider);
		$this->container->registerServiceProvider(new \Providers\StepsServiceProvider);

		// Set input to container
		$sites      = $this->container->get('sites');
		$siteConfig = $sites->getSite();

		if ($siteConfig['method'] == 'database')
		{
			$siteDbo = $sites->getSiteDbo($site);

			if ($siteDbo == false)
			{
				$return            = array();
				$return['code']    = 500;
				$return['message'] = Text::_('COM_JUPGRADEPRO_ERROR_SITE_NOT_EXIST');

				print(json_encode($return));
				Factory::getApplication()->close();
			}

			$this->container->share('external', function (Container $c) use ($siteDbo) {
				return $siteDbo;
			}, true);
		}

		// Get the new site Joomla! version
		$version = constant("\\Joomla\\CMS\\Version::MAJOR_VERSION") . "." . constant("\\Joomla\\CMS\\Version::MINOR_VERSION");

		$this->container->share('origin_version', function (Container $c) use ($version) {
			return $version;
		}, true);
	}

	/**
	 * Run the jUpgradePro checks
	 *
	 * @since   3.8.0
	 */
	public function checks()
	{
		// Create container
		$this->createContainer();

		// Get the model for the view.
		$checks = new Checks($this->container);

		// Running the migrate
		try
		{
			$checks->checks();
		}
		catch (Exception $e)
		{
			$checks->returnError(500, $e->getMessage());
		}
	}

	/**
	 * Run the jUpgradePro cleanup
	 *
	 * @since   3.8.0
	 */
	public function cleanup()
	{
		// Create container
		$this->createContainer();

		// Get the model for the view.
		$model = new Cleanup($this->container);

		// Running the cleanup
		try
		{
			echo $model->cleanup();
		}
		catch (Exception $e)
		{
			$model->returnError(500, $e->getMessage());
		}
	}

	/**
	 * Run jUpgradePro extensions
	 *
	 * @since   3.8.0
	 */
	public function cleantable()
	{
		// Create container
		$this->createContainer();

		// Set all cid, status and cache to 0
		$query = $this->container->get('db')->getQuery(true);
		$query->update('#__jupgradepro_steps')->set('cid = 0, status = 0, cache = 0, total = 0, stop = 0, start = 0, first = 0, debug = \'\'');

		try
		{
			$this->container->get('db')->setQuery($query)->execute();
		}
		catch (Exception $e)
		{
			$this->returnError(500, $e->getMessage());
		}
	}

	/**
	 * Run jUpgradePro step
	 *
	 * @since   3.8.0
	 */
	public function step()
	{
		// Create container
		$this->createContainer();

		// Get the model for the view.
		$model = new Step($this->container);

		// Running the step
		try
		{
			$model->step(false, true);
		}
		catch (Exception $e)
		{
			$model->returnError(500, $e->getMessage());
		}
	}

	/**
	 * Run jUpgradePro migrate
	 *
	 * @since   3.8.0
	 */
	public function migrate()
	{
		// Create container
		$this->createContainer();

		// Get the model for the view.
		$model = new Migrate($this->container);

		// Running the migrate
		try
		{
			$model->migrate();
		}
		catch (Exception $e)
		{
			$model->returnError(500, $e->getMessage());
		}
	}

	/**
	 * Run jUpgradePro extensions
	 *
	 * @since   3.8.0
	 */
	public function extensions()
	{
		// Create container
		$this->createContainer();

		// Get the model for the view.
		$model = new Extensions($this->container);

		// Running the extensions
		try
		{
			$model->extensions();
		}
		catch (Exception $e)
		{
			$model->returnError(500, $e->getMessage());
		}
	}

	/**
	 * Get the component params
	 *
	 * @since   3.8.0
	 */
	public function check()
	{
		$this->_db = Factory::getDbo();

		$app = Factory::getApplication();

		// Get a new DI container
		$this->createContainer();

		$model   = new Checks($this->container);
		$version = $model->checkSite();

		if ($version != false)
		{
			$this->returnError(400, Text::sprintf('COM_JUPGRADEPRO_CHECK_VERSION', $version));
		}
		else
		{
			$this->returnError(500, Text::sprintf('COM_JUPGRADEPRO_CHECK_VERSION_FAILED', $version));
		}

		$app->close();
	}

	/**
	 * Get the component params
	 *
	 * @since   3.8.0
	 */
	public function show()
	{
		$return    = '';
		$this->_db = Factory::getDbo();
		$app       = Factory::getApplication();

		$task = $app->input->get('command', false);

		if ($task == 'config')
		{
			$site = $app->input->get('site', false);

			// Create a new query object.
			$query = $this->_db->getQuery(true);

			// Select the required fields from the table.
			$query->select("*");
			$query->from('#__jupgradepro_sites AS s');
			$qSite = $this->_db->quote($site);
			$query->where("s.name = {$qSite}");
			$query->setLimit(1);

			// Set query
			$this->_db->setQuery($query);

			// Execute the query
			try
			{
				$item = $this->_db->loadObject();
			}
			catch (RuntimeException $e)
			{
				throw new RuntimeException($e->getMessage());
			}

			if (empty($item))
			{
				$return .= Text::_('COM_JUPGRADEPRO_CONFIG_SITE_NOT_FOUND');
				print($return);
				$app->close();
			}

			$method = $item->method;
			$json   = $item->$method;

			$return .= "\n" . Text::_('COM_JUPGRADEPRO_CONFIG_NAME') . ": [[g;grey;]{$item->name}]";
			$return .= "\n" . Text::_('COM_JUPGRADEPRO_TITLE_METHOD') . ": [[g;grey;]{$item->method}]";
			$return .= "\n" . Text::_('COM_JUPGRADEPRO_TITLE_LIMIT') . ": [[g;grey;]{$item->chunk_limit}]\n";
			$return .= $this->fixJSON($json);

		}
		elseif ($task == 'sites')
		{

			// Get the model for the view.
			$model = $this->getModel('Sites');

			$items = $model->getItems();

			$configTitle = "[[g;white;]|] " . Text::_('COM_JUPGRADEPRO_CONFIG_NAME');

			$return .= $configTitle . $this->getSpaces($configTitle, 30) . "|     " . Text::_('COM_JUPGRADEPRO_TITLE_METHOD');
			$return .= "\n|┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈┈\n";

			if (!empty($items))
			{
				foreach ($items as $key => $value)
				{
					$valTitle = "[[g;grey;]{$value->name}]";
					$return   .= "[[g;white;]|] " . $valTitle . $this->getSpaces($valTitle) . "|     " . $value->method . "\n";
				}
			}
			else
			{
				$return .= Text::_('COM_JUPGRADEPRO_SITES_NOT_FOUND') . "\n";
			}

			$return .= Text::_('COM_JUPGRADEPRO_HORIZONTAL_LIN3') . "\n";
		}

		print($return);
	}

	/**
	 * Get the spaces
	 *
	 * @param   string   $string  The string to set spaces
	 * @param   integer  $length  The length for this string
	 *
	 * @return  bool
	 * @since   3.8.0
	 */
	protected function getSpaces($string, $length = 27)
	{
		$len = $length - strlen($string);

		$return = "";
		for ($i = 0; $i < $len; $i++)
		{
			$return .= " ";
		}

		return $return;
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

		$return = print_r($decode, 1);

		$return = str_replace("stdClass Object", "", $return);

		return $return;
	}

	/**
	 * returnError
	 *
	 * @return void
	 * @since  3.8.0
	 */
	public function returnError($code, $message, $debug = false)
	{
		$response            = array();
		$response['code']    = $code;
		$response['message'] = Text::_($message);

		if ($debug != false)
		{
			$message['debug'] = $debug;
		}

		print(json_encode($response));
		exit;
	}

	/**
	 * Download composer installer
	 *
	 * @return  void
	 * @since  3.8.0
	 */
	function updateComposer()
	{
		set_time_limit(-1);

		$command  = "composer require matware-lab/jupgradenext";
		$explode  = explode(' ', $command);
		$command  = $explode[1];
		$command2 = isset($explode[2]) ? ' ' . $explode[2] : '';

		// Download composer.phar
		$this->downloadComposer();

		// Require composer bootstrap
		require_once "phar://" . JPATH_ROOT . "{$this->composer_data['bin']}/src/bootstrap.php";

		// Use root directory
		$composer_home = JPATH_ROOT . $this->composer_data['dir'];
		chdir($composer_home);
		putenv("COMPOSER_HOME={$composer_home}");

		// Force to use php://output instead of php://stdout
		putenv("OSTYPE=OS400");

		// Get the application console instance
		$app     = new \Composer\Console\Application();
		$factory = new \Composer\Factory();
		$output  = $factory->createOutput();

		// Build commands and arguments array
		$array            = array();
		$array['command'] = trim($command);

		if ($array['command'] == 'require' || $array['command'] == 'remove')
		{
			$array['packages'] = array(trim($command2));
		}

		// Set composer base root to Joomla! root
		$array['-d'] = $composer_home;

		// Get input
		$input = new ArrayInput($array);

		// Set interactive to false
		$input->setInteractive(true);

		// Run application
		$app->run($input, $output);
	}

	/**
	 * Get status
	 *
	 * @return  void
	 * @throws Exception
	 * @since  3.8.0
	 */
	function statusComposer()
	{
		if (Folder::exists(JPATH_COMPONENT_ADMINISTRATOR . '/vendor') == false)
		{
			$return = Text::_('COM_JUPGRADEPRO_COMPOSER_NOT_FOUND');
		}
		else
		{
			$return = Text::_('COM_JUPGRADEPRO_COMPOSER_FOUND');
		}

		print($return);
		Factory::getApplication()->close();
	}

	/**
	 * Download composer installer
	 *
	 * @return  void
	 * @since  3.8.0
	 */
	function downloadComposer()
	{
		$binfile = JPATH_ROOT . $this->composer_data['bin'];

		if (!file_exists($binfile))
		{
			copy($this->composer_data['url'], $binfile);
		}
	}

	/**
	 * Check for composer libraries
	 *
	 * @return  void
	 * @throws Exception
	 * @since  3.8.0
	 */
	function checkLibraries()
	{
		jimport('joomla.filesystem.folder');

		if (Folder::exists(JPATH_COMPONENT_ADMINISTRATOR . '/vendor') == false)
		{
			$return            = array();
			$return['code']    = 500;
			$return['message'] = Text::_('COM_JUPGRADEPRO_COMPOSER_NOT_FOUND');

			print(json_encode($return));
			Factory::getApplication()->close();
		}
	}

}
