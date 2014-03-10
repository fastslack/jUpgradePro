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
 * Users table
 *
 * @package 	Joomla.Framework
 * @subpackage		Table
 * @since	1.0
 */
class JUpgradeTableUsers extends JUpgradeTable
{
	/**
	 * Unique id
	 *
	 * @var int
	 */
	var $id				= null;

	/**
	 * The users real name (or nickname)
	 *
	 * @var string
	 */
	var $name			= null;

	/**
	 * The login name
	 *
	 * @var string
	 */
	var $username		= null;

	/**
	 * The email
	 *
	 * @var string
	 */
	var $email			= null;

	/**
	 * MD5 encrypted password
	 *
	 * @var string
	 */
	var $password		= null;

	/**
	 * Description
	 *
	 * @var string
	 */
	var $usertype		= null;

	/**
	 * Description
	 *
	 * @var int
	 */
	var $block			= null;

	/**
	 * Description
	 *
	 * @var int
	 */
	var $sendEmail		= null;

	/**
	 * Description
	 *
	 * @var datetime
	 */
	var $registerDate	= null;

	/**
	 * Description
	 *
	 * @var datetime
	 */
	var $lastvisitDate	= null;

	/**
	 * Description
	 *
	 * @var string activation hash
	 */
	var $activation		= null;

	/**
	 * Description
	 *
	 * @var string
	 */
	var $params			= null;

	/**
	* @param database A database connector object
	*/
	function __construct ( &$db )
	{
		parent::__construct( '#__users', 'id', $db );

		$this->_type = 'users';
	}

	/**
	 * Migrate the data
	 *
	 * @access	public
	 * @param		Array	Result to migrate
	 * @return	Array	Migrated result
	 */
	function migrate ($rows)
	{
		foreach ($rows as $row)
		{
			// Fixing the params compatible with 2.5/3.0
			$row['params'] = $this->convertParams($row['params']);

		  // Chaging admin username and email
		  if ($row['id'] == 62) {
		    $row['username'] = $row['username'].'v15';
		    $row['email'] = $row['email'].'v15';
		  }
		}

		return $rows;
	}

	/**
	 * A hook to be able to modify params prior as they are converted to JSON.
	 *
	 * @param	object	$object	A reference to the parameters as an object.
	 *
	 * @return	void
	 * @since	0.4.
	 * @throws	Exception
	 */
	protected function convertParamsHook(&$object)
	{
		$object->timezone = 'UTC';
	}
}
