<?php
/**
 * jUpgrade
 *
 * @version		$Id$
 * @package		MatWare
 * @subpackage	com_jupgrade
 * @copyright	Copyright 1999 - 2012 Matias Aguirre. All rights reserved.
 * @license		GNU General Public License version 2 or later.
 * @author		Matias Aguirre <maguirre@matware.com.ar>
 * @link		http://www.matware.com.ar
 */

/**
 * Database methods
 *
 * This class search for extensions to be migrated
 *
 * @since	3.0.0
 */
class jUpgradeDatabase extends JDatabaseDriverMysql
{
	/**
	 * Optimize the loadRow using mysql_data_seek
	 *
	 * @access	public
	 * @return	array	Assoc array
	 */
	public function loadRowDataSeek( $id )
	{
		$this->connect();

		$sql = $this->replacePrefix((string) $this->sql);

		$cursor = mysql_query($sql, $this->connection);

		// Optimize the search for the next row
		if (!mysql_data_seek($cursor, $id)) {
			echo "Cannot seek to row $id: " . mysql_error() . "\n";
			return false;
		}

		// Get the row from the result set.
		$row = mysql_fetch_assoc($cursor);

//print_r($row);

		return $row;
	}

}
