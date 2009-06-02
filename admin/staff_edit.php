<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */

  require_once("../shared/common.php");

  $tab = "admin";
  $nav = "staff";
  $restrictInDemo = true;
  require_once(REL(__FILE__, "../shared/logincheck.php"));

  require_once(REL(__FILE__, "../classes/Staff.php"));
  require_once(REL(__FILE__, "../classes/StaffQuery.php"));
  require_once(REL(__FILE__, "../functions/errorFuncs.php"));

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_POST) == 0) {
    header("Location: ../admin/staff_list.php");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $staff = new Staff();
  $staff->setLastChangeUserid($_SESSION["userid"]);
  $staff->setUserid($_POST["userid"]);
  $staff->setLastName($_POST["last_name"]);
  $_POST["last_name"] = $staff->getLastName();
  $staff->setFirstName($_POST["first_name"]);
  $_POST["first_name"] = $staff->getFirstName();
  $staff->setUsername($_POST["username"]);
  $_POST["username"] = $staff->getUsername();
  $staff->setCircAuth(isset($_POST["circ_flg"]));
  $staff->setCircMbrAuth(isset($_POST["circ_mbr_flg"]));
  $staff->setCatalogAuth(isset($_POST["catalog_flg"]));
  $staff->setAdminAuth(isset($_POST["admin_flg"]));
  $staff->setReportsAuth(isset($_POST["reports_flg"]));
  $staff->setSuspended(isset($_POST["suspended_flg"]));
  if (!$staff->validateData()) {
    $pageErrors["last_name"] = $staff->getLastNameError();
    $pageErrors["username"] = $staff->getUsernameError();
    $_SESSION["postVars"] = $_POST;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../admin/staff_edit_form.php");
    exit();
  }

  #**************************************************************************
  #*  Update staff member
  #**************************************************************************
  $staffQ = new StaffQuery();
  $staffQ->connect();
  if ($staffQ->errorOccurred()) {
    $staffQ->close();
    displayErrorPage($staffQ);
  }
  if (!$staffQ->update($staff)) {
    $staffQ->close();
    displayErrorPage($staffQ);
  }
  $staffQ->close();

  #**************************************************************************
  #*  Show success page
  #**************************************************************************
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

  echo T("Staff member, %name%, has been updated.", array('name'=>$staff->getFirstName().' '.$staff->getLastName())).'<br /><br />';
  echo '<a href="../admin/staff_list.php">'.T("Return to staff list").'</a>';

  Page::footer();
