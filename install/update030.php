<?php
/**********************************************************************************
 *   Copyright(C) 2003-2004 David Stevens
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
  require_once("../install/installFuncs.php");
  require_once("../install/tableList.php");

  $sqlDir="sql/";
  // 0.3.0 was only available in english
  $domainDataDir="../locale/en/sql/";
  $tblPrfx = "obib040_";


?>
<br>
<h1>OpenBiblio Update:</h1>

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

  // check for database
  $setQ->execSelect();
  if ($setQ->errorOccurred()) {
    $setQ->close();
    echo "Version 0.3.0 of the OpenBiblio data could not be found.<br>\n";
    include("../install/footer.php");
    exit();
  }

  // check version number
  $set = @$setQ->fetchRow();
  $setQ->close();
  if (!$set) {
    $version="unknown";
  } else {
    $version=$set->getVersion();
  }
  if (substr($version,0,4) != "0.3.") {
    echo "Version 0.3.0 of the OpenBiblio data could not be found.<br>\n";
    include("../install/footer.php");
    exit();
  }

  // confirm update
  if (!isset($HTTP_POST_VARS["confirm"]) or ($HTTP_POST_VARS["confirm"] != "yes")){
    ?>
      <form method="POST" action="../install/update030.php">
      OpenBiblio (version <?php echo $version;?>) is currently installed.
      Are you sure you want to convert all library data to version 0.4.0?<br>
      <input type="hidden" name="confirm" value="yes">
      <input type="hidden" name="locale" value="<?php echo $locale; ?>">
      <input type="hidden" name="installTestData" value="<?php if (isset($HTTP_POST_VARS["installTestData"])) echo "yes"; ?>">
      <input type="submit" value="Continue">
      <input type="button" onClick="parent.location='../install/cancel_msg.php'" value="Cancel">
      </form>
    <?php
    include("../install/footer.php");
    exit();
  }

  #************************************************************************************
  #* creating each table listed in the $tables array
  #************************************************************************************
  $installQ = new InstallQuery();
  $installQ->connect();
  foreach($tables as $tableName) {
    $tempTableName = $tblPrfx.$tableName;
    # dropping the table if it exists
    @$result = $installQ->dropTable($tempTableName);
    if ($installQ->errorOccurred()) {
      echo "\n<!-- db_errno = ".$installQ->getDbErrno()."-->\n";
      echo "<!-- db_error = ".$installQ->getDbError()."-->\n";
      echo "<!-- SQL = ".$installQ->getSQL()."-->\n";
      $installQ->clearErrors();
    } else {
      print "table ".$tempTableName." dropped.<br>";
      flush();
    }

    # creating table
    $filename = $sqlDir.$tableName.".sql";
    if (!executeSqlFile($installQ, $filename, $tblPrfx)) {
      $installQ->close();
      exit();
    }

    print "table ".$tempTableName." created.<br>";
    for ($i=0; $i<50; $i++) print ("                                                         ");
    flush();
  }

  foreach($domainTables as $tableName) {
    # inserting domain data
    $filename = $domainDataDir.$tableName.".sql";
    if (!executeSqlFile($installQ, $filename, $tblPrfx)) {
      $installQ->close();
      exit();
    }

    print "domain data for table ".$tableName." inserted.<br>";
    for ($i=0; $i<50; $i++) print ("                                                         ");
    flush();
  }

  #************************************************************************************
  #* convert biblio data
  #************************************************************************************
  $sql = "insert into ".$tblPrfx."biblio ";
  $sql = $sql."select bibid, create_dt, last_updated_dt, 1, material_cd, ";
  $sql = $sql."collection_cd, call_nmbr, NULL, NULL, title, subtitle, trim(concat(author,' ',add_author)), author, ";
  $sql = $sql."NULL, NULL, NULL, null, null, 'Y' from biblio";
  $result = $installQ->exec($sql);
  if ($installQ->errorOccurred()) {
    $installQ->close();
    displayErrorPage($installQ);
  }
  print "biblio table converted.<br>";

  #************************************************************************************
  #* need to also populate marc table here
  #************************************************************************************
  function insertBiblioFields($installQ, $tag, $subFieldCd, $tableName, $colName){
    $sql = "insert into ".$tableName." select bibid, null, ".$tag.", null, null, '".$subFieldCd."', ".$colName." from biblio where ".$colName." is not null" ;
    $result = $installQ->exec($sql);
    if ($installQ->errorOccurred()) {
      $installQ->close();
      displayErrorPage($installQ);
    }
    print $tag.$subFieldCd." tags inserted.<br>";
  }

  insertBiblioFields($installQ, 250,'a',$tblPrfx."biblio_field","edition");  
  insertBiblioFields($installQ, 20,'a',$tblPrfx."biblio_field","isbn_nmbr");  
  insertBiblioFields($installQ, 10,'a',$tblPrfx."biblio_field","lccn_nmbr");  
  insertBiblioFields($installQ, 50,'a',$tblPrfx."biblio_field","lc_call_nmbr");
  insertBiblioFields($installQ, 50,'b',$tblPrfx."biblio_field","lc_item_nmbr");
  insertBiblioFields($installQ, 82,'a',$tblPrfx."biblio_field","udc_nmbr");
  insertBiblioFields($installQ, 82,'2',$tblPrfx."biblio_field","udc_ed_nmbr");
  insertBiblioFields($installQ, 260,'a',$tblPrfx."biblio_field","publication_loc");
  insertBiblioFields($installQ, 260,'b',$tblPrfx."biblio_field","publisher");
  insertBiblioFields($installQ, 260,'c',$tblPrfx."biblio_field","publication_dt");
  insertBiblioFields($installQ, 520,'a',$tblPrfx."biblio_field","summary");
  insertBiblioFields($installQ, 300,'a',$tblPrfx."biblio_field","pages");
  insertBiblioFields($installQ, 300,'b',$tblPrfx."biblio_field","physical_details");
  insertBiblioFields($installQ, 300,'c',$tblPrfx."biblio_field","dimensions");
  insertBiblioFields($installQ, 300,'e',$tblPrfx."biblio_field","accompanying");
  insertBiblioFields($installQ, 20,'c',$tblPrfx."biblio_field","price");

/*edition                   250 a
isbn                        20 a
lc control #                10 a
loc call # (classificaiton) 50 a
loc call # (item)           50 b
dewey dec classify #        82 a
dewey dec classify # (ed)   82 2
place of pub                260 a
name of pub                 260 b
date of pub                 260 c
summary                     520 a
phys desc (extent)          300 a
phys desc (other details)   300 b
phys desc (dimensions)      300 c
phys desc (accomp mat)      300 e
terms of avail (price)      20 c

*/
  #************************************************************************************
  #* populate biblio_copy data
  #************************************************************************************
  $sql = "insert into ".$tblPrfx."biblio_copy ";
  $sql = $sql."select biblio.bibid, null, null, biblio.barcode_nmbr, ifnull(biblio_status.status_cd,'in'), ifnull(biblio_status.status_begin_dt,biblio.create_dt), biblio_status.due_back_dt, biblio_status.mbrid from ";
  $sql = $sql."biblio left join biblio_status on biblio.bibid=biblio_status.bibid";
  $result = $installQ->exec($sql);
  if ($installQ->errorOccurred()) {
    $installQ->close();
    displayErrorPage($installQ);
  }
  $sql = "update ".$tblPrfx."biblio_copy set status_cd = 'hld' where status_cd = 'cll'";
  $result = $installQ->exec($sql);
  if ($installQ->errorOccurred()) {
    $installQ->close();
    displayErrorPage($installQ);
  }
  print "biblio_copy table converted.<br>";

  #************************************************************************************
  #* populate member data
  #************************************************************************************
  $sql = "insert into ".$tblPrfx."member ";
  $sql = $sql."select mbrid, barcode_nmbr, create_dt, sysdate(), 1, last_name, first_name, address1, address2, ";
  $sql = $sql."city, state, zip, zip_ext, home_phone, work_phone, null, classification, school_grade, school_teacher ";
  $sql = $sql." from member";
  $result = $installQ->exec($sql);
  if ($installQ->errorOccurred()) {
    $installQ->close();
    displayErrorPage($installQ);
  }
  print "member table converted.<br>";

  #************************************************************************************
  #* populate staff data
  #************************************************************************************
  $sql = "delete from ".$tblPrfx."staff ";
  $result = $installQ->exec($sql);
  if ($installQ->errorOccurred()) {
    $installQ->close();
    displayErrorPage($installQ);
  }
  print "new staff table rows deleted.<br>";
  
  $sql = "insert into ".$tblPrfx."staff ";
  $sql = $sql."select userid, create_dt, last_updated_dt, 1, username, pwd, last_name, first_name, ";
  $sql = $sql."suspended_flg, admin_flg, circ_flg, circ_flg, catalog_flg, admin_flg ";
  $sql = $sql." from staff";
  $result = $installQ->exec($sql);
  if ($installQ->errorOccurred()) {
    $installQ->close();
    displayErrorPage($installQ);
  }
  print "staff table converted.<br>";

  #************************************************************************************
  #* populate collection data
  #************************************************************************************
  $sql = "delete from ".$tblPrfx."collection_dm ";
  $result = $installQ->exec($sql);
  if ($installQ->errorOccurred()) {
    $installQ->close();
    displayErrorPage($installQ);
  }
  print "new collection table rows deleted.<br>";

  $sql = "insert into ".$tblPrfx."collection_dm ";
  $sql = $sql."select code, description, default_flg, days_due_back, 0.00 ";
  $sql = $sql." from collection_dm";
  $result = $installQ->exec($sql);
  if ($installQ->errorOccurred()) {
    $installQ->close();
    displayErrorPage($installQ);
  }
  print "collection_dm table converted.<br>";

  #************************************************************************************
  #* populate material type data
  #************************************************************************************

  $sql = "delete from ".$tblPrfx."material_type_dm ";
  $result = $installQ->exec($sql);
  if ($installQ->errorOccurred()) {
    $installQ->close();
    displayErrorPage($installQ);
  }
  print "new material_type table rows deleted.<br>";

  $sql = "insert into ".$tblPrfx."material_type_dm ";
  $sql = $sql."select * from material_type_dm";
  $result = $installQ->exec($sql);
  if ($installQ->errorOccurred()) {
    $installQ->close();
    displayErrorPage($installQ);
  }
  print "material_type_dm table converted.<br>";

  #************************************************************************************
  #* convert settings?
  #************************************************************************************
  $sql = "delete from ".$tblPrfx."settings ";
  $result = $installQ->exec($sql);
  if ($installQ->errorOccurred()) {
    $installQ->close();
    displayErrorPage($installQ);
  }
  print "new settings table rows deleted.<br>";

  $sql = "insert into ".$tblPrfx."settings ";
  $sql = $sql."select library_name, library_image_url, use_image_flg, ";
  $sql = $sql."library_hours, library_phone, library_url, opac_url, session_timeout, ";
  $sql = $sql."items_per_page, '0.5.0', 1, 6, 'Y', 'en', 'iso-8859-1', null from settings";
  
  $result = $installQ->exec($sql);
  if ($installQ->errorOccurred()) {
    $installQ->close();
    displayErrorPage($installQ);
  }
  print "settings table converted.<br>";

//exit('stopped here');

  #************************************************************************************
  #* dropping old tables
  #************************************************************************************
/*  $result = $installQ->dropTable("biblio");
  $result = $installQ->dropTable("biblio_hold");
*/
  $result = $installQ->dropTable("biblio_status");
  print "biblio_status have been dropped.<br>";
/*  $result = $installQ->dropTable("biblio_status_dm");
  $result = $installQ->dropTable("collection_dm");
  $result = $installQ->dropTable("material_type_dm");
  $result = $installQ->dropTable("mbr_classify_dm");
  $result = $installQ->dropTable("member");
  $result = $installQ->dropTable("session");
  $result = $installQ->dropTable("settings");
  $result = $installQ->dropTable("staff");
  $result = $installQ->dropTable("state_dm");
  $result = $installQ->dropTable("theme");
*/

  #************************************************************************************
  #* renaming temp tables to permenant table name
  #************************************************************************************
  foreach($tables as $tableName) {
    @$result = $installQ->dropTable($tableName);
    print $tableName." dropped.<br>";

    $tempTableName = $tblPrfx.$tableName;
    $sql = "rename table ".$tempTableName." to ".$tableName;
    $result = $installQ->exec($sql);
    if ($installQ->errorOccurred()) {
      $installQ->close();
      displayErrorPage($installQ);
    }
    print $tempTableName." renamed to ".$tableName.".<br>";
  }  

  $installQ->close();

?>
<br>
OpenBiblio tables have been created successfully!<br>
<a href="../home/index.php">start using OpenBiblio</a>


<?php include("../install/footer.php"); ?>
