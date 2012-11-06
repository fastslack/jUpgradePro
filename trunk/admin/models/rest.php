<?php
/**
 * jUpgrade
 *
 * @version		$Id: ajax.php
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @copyright	Copyright 2006 - 2011 Matias Aguire. All rights reserved.
 * @license		GNU General Public License version 2 or later.
 * @author		Matias Aguirre <maguirre@matware.com.ar>
 * @link		http://www.matware.com.ar
 */

// No direct access.
defined('_JEXEC') or die;

require_once JPATH_COMPONENT_ADMINISTRATOR.'/models/jupgrade.model.php';

/**
 * Rest Model
 *
 * @package		MatWare
 * @subpackage	com_jupgrade
 */
class jUpgradeProModelRest extends jUpgradeProModel
{
	/**
	 * Get a single row
	 *
	 * @return   step object
	 */
	public function getRow() {

		// Getting the table
		$table = JRequest::getVar('table');

		// Getting the response
		$response = $this->requestRest('row', $table);

		if ($response != '') {
			$row = json_decode($response, true);
		}	
	
		$json = json_encode($row);

		return($json);
	}

	/**
	 * Get a list of images to migrate
	 *
	 * @return   step object
	 */
	public function getImageslist() {

		// Getting the response
		$response = $this->requestRest('imageslist', false, 'images');

		if ($response != '') {
			$row = json_decode($response, true);
		}	

		// Empty files_images table
		$query = "DELETE FROM jupgrade_files_images WHERE id >= 0";
		$this->runQuery ($query);

		// Saving the images list to db
		$images = $row['images'];
		$count = count($images);

		for($i=1;$i<$count;$i++) {

			$value = $images[$i];

			// Insert needed value
			$query = "INSERT INTO `jupgrade_files_images` ( `id`, `name`) VALUES ( {$i}, '{$value}')";
			$this->runQuery ($query);
		}

		// Return total
		//$return = array();
		//$return['total'] = $row['total'];
		echo json_encode($row);
	}

	/**
	 * Get a list of images to migrate
	 *
	 * @return   step object
	 */
	public function getImage() {

		// Getting the response
		$response = $this->requestRest('image', false, 'images');

		$id = $this->_getID('files_images');
		$id = $id + 1;
		$name =	$this->_getImageName($id);

		$write = JFile::write(JPATH_ROOT.'/images/'.$name, $response);

		$this->_updateID($id, 'files_images');
	}

	/**
	 * Get the current (image/media/template) id
	 *
	 * @return  int  The current id
	 *
	 * @since   3.0.0
	 */
	public function _getID($step)
	{
		// Getting the database instance
		$db = JFactory::getDbo();	

		$query = 'SELECT `cid` FROM jupgrade_steps'
		. ' WHERE name = '.$db->quote($step);
		$db->setQuery( $query );
		$fileid = (int) $db->loadResult();

		return $fileid;
	}

	/**
	 * Get the name of the image
	 *
	 * @return  int  The current id
	 *
	 * @since   3.0.0
	 */
	public function _getImageName($id)
	{
		// Getting the database instance
		$db = JFactory::getDbo();	

		$query = 'SELECT `name` FROM jupgrade_files_images'
		. ' WHERE id = '.$id;
		$db->setQuery( $query );
		$name = $db->loadResult();

		return $name;
	}

	/**
	 * 
	 *
	 * @return  boolean  True if the user and pass are authorized
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function _updateID($id, $step)
	{
		// Getting the database instance
		$db = JFactory::getDbo();	

		$query = "UPDATE `jupgrade_steps` SET `cid` = '{$id}' WHERE name = ".$db->quote($step);
		$db->setQuery( $query );
		return $db->query();
	}



	/**
	 * Migrate
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	function getExtensions() {

		// jUpgrade class
		$jupgrade = new jUpgrade;

		$step = $this->_getStep();

		// TODO: Error handler

		$this->_processExtensionStep($step);

		// Select the steps
		$query = "SELECT * FROM jupgrade_steps AS s WHERE s.extension = 1 ORDER BY s.id DESC LIMIT 1";
		$jupgrade->_db->setQuery($query);
		$lastid = $jupgrade->_db->loadResult();

		// Check for query error.
		$error = $jupgrade->_db->getErrorMsg();

		if ($error) {
			throw new Exception($error);
		}

		$message['status'] = "OK";
		$message['step'] = $step->id;
		$message['name'] = $step->name;
		$message['lastid'] = $lastid;
		$message['text'] = 'DONE';
		echo json_encode($message);

	}

	/**
	 * processStep
	 *
	 * @return	none
	 * @since	2.5.0
	 */
	public function _processExtensionStep ($step)
	{
		// Require the file
		require_once JPATH_COMPONENT.'/includes/jupgrade.extensions.class.php';	

		// Get jUpgradeExtensions instance
		$extension = jUpgradeExtensions::getInstance($step);
		$success = $extension->upgradeExtension();

		if ($extension->isReady())
		{
			$this->_updateStep($step);
		}
	}
}
