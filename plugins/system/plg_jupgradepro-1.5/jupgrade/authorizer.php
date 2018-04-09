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
		// Getting the client key
		$plugin =& JPluginHelper::getPlugin('system', 'jupgrade');
		$pluginParams = new JParameter( $plugin->params );
		$client_key = trim($pluginParams->get('client_key'));

		// Uncrypt the request
		$key = isset($params['HTTP_KEY']) ? base64_decode($params['HTTP_KEY']) : false;

		if ($key === false) {
			JResponse::setHeader('status', 402);
			JResponse::setBody('Client key do not exists.');
			JResponse::sendHeaders();
			exit;
		}

		$parts	= explode( ':', $key );
		$key	= trim($parts[0]);

		if ($key != $client_key) {
			JResponse::setHeader('status', 402);
			JResponse::setBody('Client key do not match.');
			JResponse::sendHeaders();
			exit;
		}

		if (!isset($params['AUTH_USER']) && !isset($params['HTTP_USER']) && !isset($params['USER'])) {
			JResponse::setHeader('status', 405);
			JResponse::setBody('Username headers not found.');
			JResponse::sendHeaders();
			exit;
		}

		// Looking the username header
		if (isset($params['AUTH_USER'])) {
			$user_decode = base64_decode($params['AUTH_USER']);
		} else if (isset($params['HTTP_USER'])) {
			$user_decode = base64_decode($params['HTTP_USER']);
		} else if (isset($params['USER'])) {
			$user_decode = base64_decode($params['USER']);
		}

		$parts	= explode( ':', $user_decode );
		$user	= $parts[0];

		// Looking the username header
		if (isset($params['AUTH_PW'])) {
			$password_decode = base64_decode($params['AUTH_PW']);
		} else if (isset($params['HTTP_PW'])) {
			$password_decode = base64_decode($params['HTTP_PW']);
		} else if (isset($params['PW'])) {
			$password_decode = base64_decode($params['PW']);
		}

		$parts	= explode( ':', $password_decode );
		$password	= trim($parts[0]);

		// Getting the local username and password
		$query = 'SELECT `id`, `password`, `gid`'
		. ' FROM #__users'
		. ' WHERE username = '.$db->quote($user);
		$db->setQuery( $query );
		$user_result = $db->loadObject();

		// Check the password
		$parts	= explode( ':', $user_result->password );
		$crypt	= $parts[0];
		$salt	= @$parts[1];
		$testcrypt = JUserHelper::getCryptedPassword($password, $salt);

		if (!is_object($user_result)) {
			JResponse::setHeader('status', 403);
			JResponse::setBody('Username not found.');
			JResponse::sendHeaders();
			exit;
		}

		if ($crypt != $testcrypt) {
			JResponse::setHeader('status', 406);
			JResponse::setBody('Username or password do not match');
			JResponse::sendHeaders();
			exit;
		}

		if ($user_result->gid != 25) {
			JResponse::setHeader('status', 410);
			JResponse::setBody('Username is not Super Administrator');
			JResponse::sendHeaders();
			exit;
		}

		return true;
	}
}
