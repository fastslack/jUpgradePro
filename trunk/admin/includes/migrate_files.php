<?php
/**
 * jUpgrade
 *
 * @version		$Id: templates_files.php 20605 2011-02-08 00:25:11Z eddieajau $
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @copyright	Copyright 2006 - 2011 Matias Aguire. All rights reserved.
 * @license		GNU General Public License version 2 or later.
 * @author		Matias Aguirre <maguirre@matware.com.ar>
 * @link		http://www.matware.com.ar
 */

/**
 * Upgrade class for 3rd party templates
 *
 * This class search for templates to be migrated
 *
 * @since	1.2.0
 */
class jUpgradeFiles extends jUpgrade
{
	/**
	 * Sets the data in the destination database.
	 *
	 * @return	void
	 * @since	1.2.0
	 * @throws	Exception
	 */
	protected function copyImagesDirectory()
	{
		$params = $this->getParams();

		if ($params->path != '') {
			$date = JFactory::getDate()->toFormat('%Y%m%d');

			$src = JPATH_SITE.DS.'images';
			$dest = JPATH_SITE.DS.'images-backup-'.$date;
			JFolder::move($src, $dest);

			$src = $params->path.DS.'images';
			$dest = JPATH_SITE.DS.'images';
			JFolder::copy($src, $dest);
		}
	}

	/**
	 * The public entry point for the class.
	 *
	 * @return	boolean
	 * @since	1.2.0
	 */
	public function upgrade()
	{
		try
		{
			$this->copyImagesDirectory();
		}
		catch (Exception $e)
		{
			echo JError::raiseError(500, $e->getMessage());

			return false;
		}

		return true;
	}
}
