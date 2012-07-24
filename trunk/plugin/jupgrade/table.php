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

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Abstract Table class
 *
 * Parent classes to all tables.
 *
 * @abstract
 * @package 	Joomla.Framework
 * @subpackage	Table
 * @since		1.0
 * @tutorial	Joomla.Framework/jtable.cls
 */
class JUpgradeTable extends JTable
{
	/**
	 * 
	 *
	 * @return  boolean  True if the user and pass are authorized
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function _getRequestID()
	{
		// Getting the database instance
		$db = JFactory::getDbo();	

		$query = 'SELECT `cid` FROM jupgrade_steps'
		. ' WHERE name = '.$db->quote($this->_type);
		$db->setQuery( $query );
		$result = $db->loadResult();

		if ($result == 0) {
			$query = "SELECT `{$this->getKeyName()}` FROM {$this->getTableName()}"
				." ORDER BY `{$this->getKeyName()}` ASC LIMIT 1";
			$db->setQuery( $query );
			$result = $db->loadResult();			

		} else {
			$query = "SELECT `{$this->getKeyName()}` FROM {$this->getTableName()}"
				." WHERE `{$this->getKeyName()}` > {$result} ORDER BY `{$this->getKeyName()}` ASC LIMIT 1";
			$db->setQuery( $query );
			$result = $db->loadResult();
		}

		$this->_updateRequestID($result);

		return $result;
	}

	/**
	 * 
	 *
	 * @return  boolean  True if the user and pass are authorized
	 *
	 * @since   1.0
	 * @throws  InvalidArgumentException
	 */
	public function _updateRequestID($id)
	{
		// Getting the database instance
		$db = JFactory::getDbo();	
	
		$query = "UPDATE `jupgrade_steps` SET `cid` = '{$id}' WHERE name = ".$db->quote($this->_type);
		$db->setQuery( $query );
		return $db->query();
	}

	/**
	 * Get total of the rows of the table
	 *
	 * @access	public
	 * @return	int	The total of rows
	 */
	public function total( )
	{
		$db =& $this->getDBO();

		$query = 'SELECT COUNT(*)'
		. ' FROM '.$this->_tbl;
		$db->setQuery( $query );

		$result = $db->loadResult( );

		if ($result) {
			return (int)$result;
		}
		else
		{
			$this->setError( $db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Export item list to json
	 *
	 * @access public
	 */
	public function toJSON ()
	{
		$array = array();

		foreach (get_object_vars( $this ) as $k => $v)
		{
			if (is_array($v) or is_object($v) or $v === NULL)
			{
				continue;
			}
			if ($k[0] == '_')
			{ // internal field
				continue;
			}
			
			$array[$k] = $v;
		}
		
		$json = json_encode($array);

		return $json;
	}

	/**
	 * Converts the params fields into a JSON string.
	 *
	 * @param	string	$params	The source text definition for the parameter field.
	 *
	 * @return	string	A JSON encoded string representation of the parameters.
	 * @since	0.4.
	 * @throws	Exception from the convertParamsHook.
	 */
	protected function convertParams($params)
	{
		$temp	= new JParameter($params);
		$object	= $temp->toObject();

		return json_encode($object);
	}
}
