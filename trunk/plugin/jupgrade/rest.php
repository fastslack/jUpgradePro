<?php
/**
* @version $Id:
* @package Matware.jUpgradePro
* @copyright Copyright (C) 2005 - 2012 Matware. All rights reserved.
* @author Matias Aguirre
* @email maguirre@matware.com.ar
* @link http://www.matware.com.ar/
* @based on https://github.com/LouisLandry/joomla-platform/tree/oauth/libraries/joomla/oauth
* @license GNU General Public License version 2 or later; see LICENSE
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * REST Message class
 *
 * @package     Joomla
 * @subpackage  REST
 * @since       1.0
 */
class JRESTMessage
{
	/**
	 * @var    string  The HTTP request method for the message.
	 * @since  1.0
	 */
	public $_method;

	/**
	 * @var    array  Associative array of parameters for the REST message.
	 * @since  1.0
	 */
	public $_parameters = array();

	/**
	 * @var    array  List of OAuth possible parameters.
	 * @since  1.0
	 */
	private $_reserved = array(
		'PHP_AUTH_USER',
		'PHP_AUTH_PW',
		'HTTP_AUTH_USER',
		'HTTP_AUTH_PW',
		'HTTP_TASK',
		'HTTP_TYPE',
		'HTTP_TABLE',
		'HTTP_FILES',
		'HTTP_KEY',
		'HTTP_USER',
		'HTTP_ID',
		'AUTH_USER',
		'AUTH_PW'
	);

	/**
	 * @var    JURI  The request URI for the message.
	 * @since  1.0
	 */
	private $_uri;

	/**
	 * Method to get the REST parameters for the current request. Parameters are retrieved from these locations
	 * in the order of precedence as follows:
	 *
	 *   - Authorization header
	 *   - POST variables
	 *   - GET query string variables
	 *
	 * @return  boolean  True if an REST message was found in the request.
	 *
	 * @since   1.0
	 */
	public function loadFromRequest()
	{
		// Initialize variables.
		$found = false;

		// First we look and see if we have an appropriate Authorization header.
		$header = $this->_fetchAuthorizationHeader();

		// If we have an Authorization header it gets first dibs.
		if ($header && $this->_processAuthorizationHeader($header))
		{
			$found = true;
		}

		// If we didn't find an Authorization header or didn't find anything in it try the POST variables.
		if (!$found && $this->_processPostVars())
		{
			$found = true;
		}

		// If we didn't find anything in the POST variables either let's try the query string.
		if (!$found && $this->_processGetVars())
		{
			$found = true;
		}

		// If we found an REST message somewhere we need to set the URI and request method.
		if ($found)
		{
			$this->_uri = new JURI($this->_fetchRequestUrl());
			$this->_method = strtoupper($_SERVER['REQUEST_METHOD']);
		}

		return $found;
	}

	/**
	 * Method to set the REST message parameters.  This will only set valid REST message parameters.  If non-valid
	 * parameters are in the input array they will be ignored.
	 *
	 * @param   array  $parameters  The REST message parameters to set.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function setParameters($parameters)
	{
		// Ensure that only valid REST parameters are set if they exist.
		if (!empty($parameters))
		{
			foreach ($parameters as $k => $v)
			{
				if (in_array($k, $this->_reserved))
				{
					// Perform url decoding so that any use of '+' as the encoding of the space character is correctly handled.
					$this->_parameters[$k] = urldecode((string) $v);
				}
			}
		}
	}

	/**
	 * Encode a string according to the RFC3986
	 *
	 * @param   string  $s  string to encode
	 *
	 * @return  string encoded string
	 *
	 * @link    http://www.ietf.org/rfc/rfc3986.txt
	 * @since   1.0
	 */
	public function encode($s)
	{
		return str_replace('%7E', '~', rawurlencode((string) $s));
	}

	/**
	 * Decode a string according to RFC3986.
	 * Also correctly decodes RFC1738 urls.
	 *
	 * @param   string  $s  string to decode
	 *
	 * @return  string  decoded string
	 *
	 * @link    http://www.ietf.org/rfc/rfc1738.txt
	 * @link    http://www.ietf.org/rfc/rfc3986.txt
	 * @since   1.0
	 */
	public function decode($s)
	{
		return rawurldecode((string) $s);
	}

	/**
	 * Get the HTTP request headers.  Header names have been normalized, stripping
	 * the leading 'HTTP_' if present, and capitalizing only the first letter
	 * of each word.
	 *
	 * @return  string  The Authorization header if it has been set.
	 */
	private function _fetchAuthorizationHeader()
	{
		// The simplest case is if the apache_request_headers() function exists.
		if (function_exists('apache_request_headers'))
		{
			$headers = apache_request_headers();
			if (isset($headers['Authorization']))
			{
				return trim($headers['Authorization']);
			}
		}
		// Otherwise we need to look in the $_SERVER superglobal.
		elseif (isset($_SERVER['HTTP_AUTHORIZATION']))
		{
			return trim($_SERVER['HTTP_AUTHORIZATION']);
		}
		elseif (isset($_SERVER['HTTP_AUTH_USER']))
		{
			return trim($_SERVER['HTTP_AUTH_USER']);
		}
		elseif (isset($_SERVER['HTTP_USER']))
		{
			return trim($_SERVER['HTTP_USER']);
		}

	}

	/**
	 * Method to detect and return the requested URI from server environment variables.
	 *
	 * @return  string  The requested URI
	 *
	 * @since   11.3
	 */
	private function _fetchRequestUrl()
	{
		// Initialise variables.
		$uri = '';

		// First we need to detect the URI scheme.
		if (isset($_SERVER['HTTPS']) && !empty($_SERVER['HTTPS']) && (strtolower($_SERVER['HTTPS']) != 'off'))
		{
			$scheme = 'https://';
		}
		else
		{
			$scheme = 'http://';
		}

		/*
		 * There are some differences in the way that Apache and IIS populate server environment variables.  To
		 * properly detect the requested URI we need to adjust our algorithm based on whether or not we are getting
		 * information from Apache or IIS.
		 */

		// If PHP_SELF and REQUEST_URI are both populated then we will assume "Apache Mode".
		if (!empty($_SERVER['PHP_SELF']) && !empty($_SERVER['REQUEST_URI']))
		{
			// The URI is built from the HTTP_HOST and REQUEST_URI environment variables in an Apache environment.
			$uri = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
		}
		// If not in "Apache Mode" we will assume that we are in an IIS environment and proceed.
		else
		{
			// IIS uses the SCRIPT_NAME variable instead of a REQUEST_URI variable... thanks, MS
			$uri = $scheme . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

			// If the QUERY_STRING variable exists append it to the URI string.
			if (isset($_SERVER['QUERY_STRING']) && !empty($_SERVER['QUERY_STRING']))
			{
				$uri .= '?' . $_SERVER['QUERY_STRING'];
			}
		}

		return trim($uri);
	}

	/**
	 * 
	 *
	 * @param   string  $header  Authorization header.
	 *
	 * @return  boolean  True if REST PHP_AUTH_USER header found.
	 *
	 * @since   1.0
	 */
	private function _processAuthorizationHeader($header)
	{
		// Initialize variables.
		$parameters = array();

		// Iterate over the reserved parameters and look for them in the query string variables.
		foreach ($this->_reserved as $k)
		{
			if (isset($_SERVER[$k]))
			{
				if (strpos($k, 'AUTH_USER')) { 
					$parameters['AUTH_USER'] = trim($_SERVER[$k]);
				}else if (strpos($k, 'AUTH_PW')) {
					$parameters['AUTH_PW'] = trim($_SERVER[$k]);
				}else if (strpos($k, 'USER')) { 
					$parameters['USER'] = trim($_SERVER[$k]);
				}else if (strpos($k, 'PW')) {
					$parameters['PW'] = trim($_SERVER[$k]);
				}else{
					$parameters[$k] = trim($_SERVER[$k]);
				}
			}
		}

		// If we didn't find anything return false.
		if (empty($parameters) || ( empty($parameters['AUTH_USER']) || empty($parameters['AUTH_PW']) ) )
		{
			return false;
		}

		$this->setParameters($parameters);

		return true;
	}

	/**
	 * Parse the request query string for REST parameters.
	 *
	 * @return  boolean  True if REST parameters found.
	 *
	 * @since   1.0
	 */
	private function _processGetVars()
	{
		// Initialize variables.
		$parameters = array();

		// Iterate over the reserved parameters and look for them in the query string variables.
		foreach ($this->_reserved as $k)
		{
			if (isset($_GET[$k]))
			{
				$parameters[$k] = trim($_GET[$k]);
			}
		}

		// If we didn't find anything return false.
		if (empty($parameters))
		{
			return false;
		}

		$this->setParameters($parameters);

		return true;
	}

	/**
	 * Parse the request POST variables for REST parameters.
	 *
	 * @return  boolean  True if REST parameters found.
	 *
	 * @since   1.0
	 */
	private function _processPostVars()
	{
		// If we aren't handling a post request with urlencoded vars then there is nothing to do.
		if ((strtoupper($_SERVER['REQUEST_METHOD']) != 'POST') || (strtolower($_SERVER['CONTENT_TYPE']) != 'application/x-www-form-urlencoded'))
		{
			return;
		}

		// Initialize variables.
		$parameters = array();

		// Iterate over the reserved parameters and look for them in the POST variables.
		foreach ($this->_reserved as $k)
		{
			if (isset($_POST[$k]))
			{
				$parameters[$k] = trim($_POST[$k]);
			}
		}

		// If we didn't find anything return false.
		if (empty($parameters))
		{
			return false;
		}

		$this->setParameters($parameters);

		return true;
	}
}
