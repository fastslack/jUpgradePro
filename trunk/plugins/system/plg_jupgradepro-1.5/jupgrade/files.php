<?php
/**
* jUpgradePro
*
* @version $Id:
* @package jUpgradePro
* @copyright Copyright (C) 2004 - 2013 Matware. All rights reserved.
* @author Matias Aguirre
* @email maguirre@matware.com.ar
* @link http://www.matware.com.ar/
* @license GNU General Public License version 2 or later; see LICENSE
*/
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Abstract Files class
 *
 * Parent classes to all tables.
 *
 * @abstract
 * @package 	Joomla.Framework
 * @subpackage	Table
 * @since		1.0
 * @tutorial	Joomla.Framework/jtable.cls
 */
class JUpgradeFiles
{
	/**
	 * @var    array  Lits of files to send
	 * @since  3.0
	 */
	protected $_images = array();

	/**
	 * @var    array  Lits of images to ignore on images dir
	 * @since  3.0
	 */
	protected $_ignore_images = array ('M_images/arrow.png', 'M_images/arrow_rtl.png', 'M_images/blank.png', 'M_images/con_address.png', 'M_images/con_fax.png', 'M_images/con_info.png', 'M_images/con_mobile.png', 'M_images/con_tel.png', 'M_images/edit.png', 'M_images/edit_unpublished.png', 'M_images/emailButton.png', 'M_images/icon_error.gif', 'M_images/indent.png', 'M_images/indent1.png', 'M_images/indent2.png', 'M_images/indent3.png', 'M_images/indent4.png', 'M_images/indent5.png', 'M_images/index.html', 'M_images/livemarks-rtl.png', 'M_images/livemarks.png', 'M_images/new.png', 'M_images/no_indent.png', 'M_images/pdf_button.png', 'M_images/printButton.png', 'M_images/rating_star.png', 'M_images/rating_star_blank.png', 'M_images/sort0.png', 'M_images/sort1.png', 'M_images/sort_asc.png', 'M_images/sort_desc.png', 'M_images/sort_none.png', 'M_images/weblink.png', 'apply_f2.png', 'archive_f2.png', 'back_f2.png', 'banners/index.html', 'banners/osmbanner1.png', 'banners/osmbanner2.png', 'banners/shop-ad-books.jpg', 'banners/shop-ad.jpg', 'blank.png', 'cancel.png', 'cancel_f2.png', 'css_f2.png', 'edit_f2.png', 'html_f2.png', 'index.html', 'joomla_logo_black.jpg', 'menu_divider.png', 'new_f2.png', 'powered_by.png', 'preview_f2.png', 'publish_f2.png', 'save.png', 'save_f2.png', 'smilies/biggrin.gif', 'smilies/index.html', 'smilies/sad.gif', 'smilies/shocked.gif', 'smilies/smile.gif', 'smilies/tongue.gif', 'smilies/wink.gif', 'sort_asc.png', 'sort_desc.png', 'stories/articles.jpg', 'stories/clock.jpg', 'stories/ext_com.png', 'stories/ext_lang.png', 'stories/ext_mod.png', 'stories/ext_plugin.png', 'stories/food/bread.jpg', 'stories/food/bun.jpg', 'stories/food/coffee.jpg', 'stories/food/index.html', 'stories/food/milk.jpg', 'stories/fruit/cherry.jpg', 'stories/fruit/index.html', 'stories/fruit/pears.jpg', 'stories/fruit/peas.jpg', 'stories/fruit/strawberry.jpg', 'stories/index.html', 'stories/joomla-dev_cycle.png', 'stories/key.jpg', 'stories/pastarchives.jpg', 'stories/powered_by.png', 'stories/taking_notes.jpg', 'stories/web_links.jpg', 'unarchive_f2.png', 'unpublish_f2.png', 'upload_f2.png');

	/**
	* Class constructor.
	*
	* @return void
	*
	* @since 2.5.0
	*/
	public function __construct()
	{
		$this->_processImages();
	}

	/**
	 * 
	 *
	 * @return  boolean  
	 *
	 * @since   3.0.0
	 */
	public function getImage()
	{
		$id = $this->_getID('files_images');

		$id = ($id == 0) ? 1 : $id;

		$image = JPATH_ROOT.'/images/'.$this->_images[$id];

		$read = JFile::read($image);

		$nextid = $id + 1;

		$this->_updateID( $nextid, 'files_images');
		
		return $read;
	}

	/**
	 * 
	 *
	 * @return  boolean  
	 *
	 * @since   3.0.0
	 */
	public function getImageslist()
	{
		$return = array();

		$return['images'] = $this->_images;
		$return['total'] = $this->getTotal();
		$return['name'] = 'images';
		

		//sort($this->_images);

		//print_r($this->_images);

		return json_encode($return);
	}


	/**
	 * _processImages()
	 *
	 * @return  array  The image files to migrate
	 *
	 * @since   3.0.0
	 */
	public function _processImages()
	{
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$path = $this->getImagesPath();

		$clean = array();
		foreach ( $this->_ignore_images as $value ) {
			$clean[] = preg_replace('/\//', '\\\/', $value);
		}

		$exclude = array('.svn', 'CVS', '.DS_Store', '__MACOSX');

		$files_array = JFolder::files($path, '.', true, true, $exclude, $clean);		

		$pattern = "#{$path}/#";
		$replace = "";

		$i = 1;
		foreach ( $files_array as $filename ) {

			$file = preg_replace($pattern, $replace, $filename);

			$extensions_list = array('png', 'jpg', 'gif', 'jpeg', 'GIF', 'JPG', 'PNG', 'JPEG');

			$extension = JFile::getExt($file);

			if (in_array($extension, $extensions_list)) {
				$this->_images[$i] = $file;
				$i++;
			}
		}

		return true;
	}

	/**
	 * getImagestotal()
	 *
	 * @return  int  The total of files to migrate
	 *
	 * @since   1.0
	 */
	public function getTotal()
	{
		return count($this->_images);
	}

	/**
	 * 
	 *
	 * @return  boolean 
	 *
	 * @since   3.0
	 */
	public function getCleanup()
	{
		$table = isset($this->_parameters['HTTP_TABLE']) ? $this->_parameters['HTTP_TABLE'] : '';

		// Getting the database instance
		$db = JFactory::getDbo();	

		$query = "UPDATE jupgrade_plugin_steps SET cid = 0"; 
		if ($table != false) {
			$query .= " WHERE name = '{$table}'";
		}

		$db->setQuery( $query );
		$result = $db->query();

		return true;
	}

	/**
	 * 
	 *
	 * @return  boolean
	 *
	 * @since   3.0
	 */
	public function getImagesPath()
	{
		return JPATH_ROOT."/images";
	}

	/**
	 * 
	 *
	 * @return  boolean
	 *
	 * @since   3.0
	 */
	public function getMediaPath()
	{
		return JPATH_ROOT."/media";
	}

	/**
	 * 
	 *
	 * @return  boolean
	 *
	 * @since   3.0
	 */
	public function getTemplatesPath()
	{
		return JPATH_ROOT."/templates";
	}

	/**
	 * Get the current (image/media/template) id
	 *
	 * @return  int  The current id
	 *
	 * @since   3.0.0
	 */
	public function _getID($table)
	{
		// Getting the database instance
		$db = JFactory::getDbo();	

		$query = 'SELECT `cid` FROM jupgrade_plugin_steps'
		. ' WHERE name = '.$db->quote($table);
		$db->setQuery( $query );
		$fileid = (int) $db->loadResult();

		return $fileid;
	}

	/**
	 * 
	 *
	 * @return  boolean  True if the user and pass are authorized
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function _updateID($id, $table)
	{
		// Getting the database instance
		$db = JFactory::getDbo();	

		$query = "UPDATE `jupgrade_plugin_steps` SET `cid` = '{$id}' WHERE name = ".$db->quote($table);

		$db->setQuery( $query );
		return $db->query();
	}
}
