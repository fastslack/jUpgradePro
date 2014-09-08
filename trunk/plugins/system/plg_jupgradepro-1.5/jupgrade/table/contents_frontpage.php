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
 * Content table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JUpgradeTableContents_frontpage extends JUpgradeTable
{
	/** @var int Primary key */
	var $content_id					= null;
	/** @var int */
	var $ordering				= null;

	/**
	 * Table type
	 *
	 * @var string
	 */	
	var $_type = 'contents_frontpage';	

	/**
	* @param database A database connector object
	*/
	function __construct( &$db ) {
		parent::__construct( '#__content_frontpage', 'content_id', $db );
	}
}
