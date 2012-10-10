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
		// Uncrypt the request
		$key = base64_decode($params['HTTP_KEY']);
		$parts	= explode( ':', $key );
		$key	= $parts[0];

		if (!isset($params['AUTH_USER']) && !isset($params['HTTP_USER']) ) {
			JResponse::setHeader('status', 404);
			JResponse::setBody('Username headers not found.');
			JResponse::sendHeaders();
			exit;
		}

		// Looking the username header
		if (isset($params['AUTH_USER'])) {
			$user_decode = base64_decode($params['AUTH_USER']);
		} else if (isset($params['HTTP_USER'])) {
			$user_decode = base64_decode($params['HTTP_USER']);
		}

		$parts	= explode( ':', $user_decode );
		$user	= $parts[0];

		// Looking the username header
		if (isset($params['AUTH_PW'])) {
			$password_decode = base64_decode($params['AUTH_PW']);
		} else if (isset($params['HTTP_PW'])) {
			$password_decode = base64_decode($params['HTTP_PW']);
		}

		$parts	= explode( ':', $password_decode );
		$password	= $parts[0];

		// Getting the local username and password
		$query = 'SELECT `id`, `password`, `gid`'
		. ' FROM #__users'
		. ' WHERE username = '.$db->quote($user);
		$db->setQuery( $query );
		$user_result = $db->loadObject();

		if (!is_object($user_result)) {
			JResponse::setHeader('status', 403);
			JResponse::setBody('Username not found.');
			JResponse::sendHeaders();
			exit;
		}

		if ($user_result->gid != 25) {
			JResponse::setHeader('status', 401);
			JResponse::setBody('Username is not Super Administrator');
			JResponse::sendHeaders();
			exit;
		}

		// Check the password
		$parts	= explode( ':', $user_result->password );
		$crypt	= $parts[0];
		$salt	= @$parts[1];
		$testcrypt = JUserHelper::getCryptedPassword($password, $salt);
	
		return ($crypt == $testcrypt) ? true : false;
	}
}
