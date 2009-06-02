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

  if (count($HTTP_POST_VARS) == 0) {
    header("Location: ../install/index.php");
    exit();
  }

  include("../install/header.php");

  require_once("../classes/InstallQuery.php");
  require_once("../classes/SettingsQuery.php");
  require_once("../classes/Settings.php");
  require_once("../functions/errorFuncs.php");

  $locale = $HTTP_POST_VARS["locale"];

  # table array
  $sqlDir="sql/";
  $domainDataDir="../locale/".$locale."/sql/";
  $tables = array(
    "biblio"
    ,"biblio_field"
    ,"biblio_copy"
    ,"biblio_hold"
    ,"biblio_status_dm"
    ,"biblio_status_hist"
    ,"collection_dm"
    ,"material_type_dm"
    ,"mbr_classify_dm"
    ,"member"
    ,"member_account"
    ,"session"
    ,"settings"
    ,"staff"
    ,"state_dm"
    ,"theme"
    ,"usmarc_block_dm"
    ,"usmarc_indicator_dm"
    ,"usmarc_subfield_dm"
    ,"usmarc_tag_dm"
    ,"transaction_type_dm"
  );
  $domainTables = array(
    "biblio_status_dm"
    ,"collection_dm"
    ,"material_type_dm"
    ,"mbr_classify_dm"
    ,"settings"
    ,"staff"
    ,"state_dm"
    ,"theme"
    ,"usmarc_block_dm"
    ,"usmarc_indicator_dm"
    ,"usmarc_subfield_dm"
    ,"usmarc_tag_dm"
    ,"transaction_type_dm"
  );

  if (isset($HTTP_POST_VARS["installTestData"])) {
    if ($HTTP_POST_VARS["installTestData"] == "yes") {
      $domainTables[] = "biblio";
      $domainTables[] = "biblio_copy";
      $domainTables[] = "member";
    }
  }
  
  /**********************************************************************************
   * Function to read through an sql file executing SQL only when ";" is encountered
   **********************************************************************************/
  function executeSqlFile (&$installQ, $filename) {
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
          #echo "process sql [".$sqlStmt."]<br>";
          $result = $installQ->exec($sqlStmt);
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

  #************************************************************************************
  #* show warning message if database exists.
  #************************************************************************************
  $setQ->execSelect();
  if ($setQ->errorOccurred()) {
    echo "Building OpenBiblio tables...<br>\n";
  } else {
    $set = @$setQ->fetchRow();
    if (!$set) {
      $version="unknown";
    } else {
      $version=$set->getVersion();
    }
    if (!isset($HTTP_POST_VARS["confirm"]) or ($HTTP_POST_VARS["confirm"] != "yes")){
      ?>
        <form method="POST" action="../install/install.php">
        OpenBiblio (version <?php echo $version;?>) is already installed.
        Are you sure you want to delete all library data and create new OpenBiblio         tables?<br>
        <input type="hidden" name="confirm" value="yes">
        <input type="hidden" name="locale" value="<?php echo $locale; ?>">
        <input type="hidden" name="installTestData" value="<?php if (isset($HTTP_POST_VARS["installTestData"])) echo "yes"; ?>">
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

  #************************************************************************************
  #* creating each table listed in the $tables array
  #************************************************************************************
  $installQ = new InstallQuery();
  $installQ->connect();
  foreach($tables as $tableName) {
    # dropping the table if it exists
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

    # creating table
    $filename = $sqlDir.$tableName.".sql";
    if (!executeSqlFile($installQ, $filename)) {
      $installQ->close();
      exit();
    }

    print "table ".$tableName." created.<br>";
    for ($i=0; $i<50; $i++) print ("                                                         ");
    flush();
  }

  foreach($domainTables as $tableName) {
    # inserting domain data
    $filename = $domainDataDir.$tableName.".sql";
    if (!executeSqlFile($installQ, $filename)) {
      $installQ->close();
      exit();
    }

    print "domain data for table ".$tableName." inserted.<br>";
    for ($i=0; $i<50; $i++) print ("                                                         ");
    flush();
  }

  $installQ->close();

?>
<br>
OpenBiblio tables have been created successfully!<br>
<a href="../home/index.php">start using OpenBiblio</a>


<?php include("../install/footer.php"); ?>
