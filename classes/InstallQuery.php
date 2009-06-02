<?php
/**********************************************************************************
 *   Copyright(C) 2002 David Stevens
 *
 *   This file is part of OpenBiblio.
 *
 *   OpenBiblio is free software; you can redistribute it and/or modify
 *   it under the terms of the GNU General Public License as published by
 *   the Free Software Foundation; either version 2 of the License, or
 *   (at your option) any later version.
 *
 *   OpenBiblio is distributed in the hope that it will be useful,
 *   but WITHOUT ANY WARRANTY; without even the implied warranty of
 *   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *   GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *   along with OpenBiblio; if not, write to the Free Software
 *   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **********************************************************************************
 */

require_once("../shared/global_constants.php");
require_once("../classes/Query.php");

/******************************************************************************
 * InstallQuery data access component for install process
 *
 * @author David Stevens <dave@stevens.name>;
 * @version 1.0
 * @access public
 ******************************************************************************
 */
class InstallQuery extends Query {
  /****************************************************************************
   * Executes an sql statement
   * @param string $sql sql statement to execute
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function exec($sql) {
    return $this->_query($sql, "Error processing install sql.");
  }


  /****************************************************************************
   * Drops a table
   * @param string $tableName name of table to drop
   * @return boolean returns false, if error occurs
   * @access public
   ****************************************************************************
   */
  function dropTable($tableName) {
    $sql = $this->mkSQL("drop table %I ", $tableName);
    return $this->_query($sql, "Error dropping table $tableName");
  }

}

?>
