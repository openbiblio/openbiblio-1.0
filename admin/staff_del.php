<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "admin";
  $nav = "staff";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));
  require_once(REL(__FILE__, "../model/Staff.php"));

  if (!isset($_GET["UID"])){
    header("Location: ../admin/staff_list.php");
    exit();
  }
  $uid = $_GET["UID"];
  $last_name = $_GET["LAST"];
  $first_name = $_GET["FIRST"];

  $staff = new Staff;
  $staff->deleteOne($uid);

  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

  echo T("Staff member, %name%, has been deleted.", array('name'=>$first_name.' '.$last_name)).'<br /><br />';
  echo '<a href="../admin/staff_list.php">'.T("Return to staff list").'</a>';

  Page::footer();
