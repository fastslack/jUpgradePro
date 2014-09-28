<?php
/**
* RESTfulTester
*
* @version $Id:
* @package jUpgradePro
* @copyright Copyright (C) 2004 - 2014 Matware. All rights reserved.
* @author Matias Aguirre
* @email maguirre@matware.com.ar
* @link http://www.matware.com.ar/
* @license GNU General Public License version 2 or later; see LICENSE
*/

// Prevent direct access to this file outside of a calling application.
defined('_JEXEC') or die;

/**
* RESTfulTester configuration class.
*
* @package jUpgradePro
* @since 1.0
*/
final class JConfig
{
	/**
	* The RESTful configuration and table pattern to search
	*
	* @var string
	* @since 1.0
	*/
	public $url = 'http://localhost/joomla15';
	public $username = 'admin';
	public $password = 'admin';
	public $restkey = 'beer';
}
