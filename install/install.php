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
  include("../install/header.php");

  require_once("../classes/InstallQuery.php");
  require_once("../classes/SettingsQuery.php");
  require_once("../classes/Settings.php");
  require_once("../functions/errorFuncs.php");

  # table array
  $tables = array(
    "biblio"
    ,"biblio_hold"
    ,"biblio_status"
    ,"biblio_status_dm"
    ,"collection_dm"
    ,"material_type_dm"
    ,"mbr_classify_dm"
    ,"member"
    ,"session"
    ,"settings"
    ,"staff"
    ,"state_dm"
    ,"theme"
  );

?>
<br>
<h1>OpenBiblio Installation:</h1>

<!--This install module is still not complete.  Please follow the
<a href="../install_instructions.html">Install Instructions</a> to install OpenBiblio
instead of this module.<br><br-->
<?php

  # testing connection and current version
  $setQ = new SettingsQuery();
  $setQ->connect();
  if ($setQ->errorOccurred()) {
    $setQ->close();
    displayErrorPage($setQ);
    exit();
  }
  echo "Database connection is good.<br>\n";

  # show warning message if database exists.
  $setQ->execSelect();
  if ($setQ->errorOccurred()) {
    echo "Building OpenBiblio tables...<br>\n";
  } else {
    $set = $setQ->fetchRow();
    if (!isset($HTTP_GET_VARS["confirm"]) or ($HTTP_GET_VARS["confirm"] != "yes")){
      ?>
        <form method="POST" action="../install/install.php?confirm=yes">
        OpenBiblio (version <?php echo $set->getVersion();?>) is already installed.
        Are you sure you want to delete all library data and create new OpenBiblio         tables?<br>
        <input type="submit" value="Continue">
        <input type="button" onClick="parent.location='../install/cancel_msg.php'" value="Cancel">
        </form>
      <?php
      $setQ->close();
      include("../install/footer.php");
      exit();
    }
  }

  $setQ->close();

  # creating each table listed in the $tables array
  foreach($tables as $tableName) {
    # dropping the table if it exists
    $installQ = new InstallQuery();
    $installQ->connect();
    @$result = $installQ->dropTable($tableName);
    if ($installQ->errorOccurred()) {
      echo "\n<!-- db_errno = ".$installQ->getDbErrno()."-->\n";
      echo "<!-- db_error = ".$installQ->getDbError()."-->\n";
      echo "<!-- SQL = ".$installQ->getSQL()."-->\n";
      $installQ->clearErrors();
    } else {
      print "table ".$tableName." dropped.<br>";
      flush();
    }

    # reading through sql file executing SQL only when ";" is encountered
    $fp = fopen("sql/".$tableName.".sql", "r");
    $sqlStmt = "";
    while (!feof ($fp)) {
      $char = fgetc($fp);
      if ($char == ";") {
        #echo "process sql [".$sqlStmt."]<br>";
        $result = $installQ->exec($sqlStmt);
        if ($installQ->errorOccurred()) {
          $installQ->close();
          displayErrorPage($installQ);
          exit();
        }
        $sqlStmt = "";
      } else {
        $sqlStmt = $sqlStmt.$char;
      }
    }
    fclose($fp);
    $installQ->close();
    print "table ".$tableName." created.<br>";
    for ($i=0; $i<50; $i++) print ("                                                         ");
    flush();
  }

?>
<br>
OpenBiblio tables have been created successfully!<br>
<a href="../home/index.php">start using OpenBiblio</a>


<?php include("../install/footer.php"); ?>
