<?php
/**
* @version $Id:
* @package Matware.jUpgradePro
* @copyright Copyright (C) 2005 - 2012 Matware. All rights reserved.
* @author Matias Aguirre
* @email maguirre@matware.com.ar
* @link http://www.matware.com.ar/
* @license GNU General Public License version 2 or later; see LICENSE
*/

defined('_JEXEC') or die;

/**
 * REST Request Authorizer class
 *
 * @package     Joomla.Platform
 * @subpackage  REST
 * @since       1.0
 */
class JRESTAuthorizer
{
	/**
	 * Authorize an REST signed request for a protected resource.
	 *
	 * @return  boolean  True if the user and pass are authorized
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function authorize(&$db, $params)
	{
			$query = 'SELECT `id`, `password`, `gid`'
			. ' FROM #__users'
			. ' WHERE username = '.$db->quote($params['AUTH_USER']);
			$db->setQuery( $query );
			$user_result = $db->loadObject();

			if (!is_object($user_result)) {
				JResponse::setHeader('status', 400);
				JResponse::setBody('Username not found.');
				JResponse::sendHeaders();
				exit;
			}

			// Check the password
			$parts	= explode( ':', $user_result->password );
			$crypt	= $parts[0];
			$salt	= @$parts[1];
			$testcrypt = JUserHelper::getCryptedPassword($params['AUTH_PW'], $salt);
		
			return ($crypt == $testcrypt) ? true : false;
	}
}
