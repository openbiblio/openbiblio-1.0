<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "admin";
  $nav = "staff";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../classes/StaffQuery.php"));
  require_once(REL(__FILE__, "../functions/errorFuncs.php"));

  #****************************************************************************
  #*  Checking for query string.  Go back to staff list if none found.
  #****************************************************************************
  if (!isset($_GET["UID"])){
    header("Location: ../admin/staff_list.php");
    exit();
  }
  $uid = $_GET["UID"];
  $last_name = $_GET["LAST"];
  $first_name = $_GET["FIRST"];

  #**************************************************************************
  #*  Delete staff member
  #**************************************************************************
  $staffQ = new StaffQuery();
  $staffQ->connect();
  if ($staffQ->errorOccurred()) {
    $staffQ->close();
    displayErrorPage($staffQ);
  }
  if (!$staffQ->delete($uid)) {
    $staffQ->close();
    displayErrorPage($staffQ);
  }
  $staffQ->close();

  #**************************************************************************
  #*  Show success page
  #**************************************************************************
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

  echo T("Staff member, %name%, has been deleted.", array('name'=>$first_name.' '.$last_name)).'<br /><br />';
  echo '<a href="../admin/staff_list.php">'.T("Return to staff list").'</a>';

  Page::footer();
