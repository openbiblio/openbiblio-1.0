<?php
/**********************************************************************************
 *   Copyright(C) 2002, 2003 David Stevens
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

  /**********************************************************************************
   * Function to read through an sql file executing SQL only when ";" is encountered
   **********************************************************************************/
  function executeSqlFile (&$installQ, $filename, $tablePrfx = "") {
    $fp = fopen($filename, "r");
    # show error if file could not be opened
    if ($fp == false) {
      echo "Error reading file ".$filename.".<br>\n";
      return false;
    } else {
      $sqlStmt = "";
      while (!feof ($fp)) {
        $char = fgetc($fp);
        if ($char == ";") {
          //replace table prefix
          $sql = str_replace("%prfx%",$tablePrfx,$sqlStmt);
          
          //echo "process sql [".$sqlStmt."]<br>";
          $result = $installQ->exec($sql);
          if ($installQ->errorOccurred()) {
            $installQ->close();
            displayErrorPage($installQ);
            fclose($fp);
            return false;
          }
          $sqlStmt = "";
        } else {
          $sqlStmt = $sqlStmt.$char;
        }
      }
      fclose($fp);
      return true;
    }
  }
?>
