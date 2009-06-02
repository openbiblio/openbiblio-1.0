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
  $staff->setUserid($_POST["userid"]);
  $staff->setPwd($_POST["pwd"]);
  $_POST["pwd"] = $staff->getPwd();
  $staff->setPwd2($_POST["pwd2"]);
  $_POST["pwd2"] = $staff->getPwd2();
  if (!$staff->validatePwd()) {
    $pageErrors["pwd"] = $staff->getPwdError();
    $_SESSION["postVars"] = $_POST;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../admin/staff_pwd_reset_form.php");
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
  if (!$staffQ->resetPwd($staff)) {
    $staffQ->close();
    displayErrorPage($staffQ);
  }
  $staffQ->close();

  #**************************************************************************
  #*  Show success page
  #**************************************************************************
  Page::header(array('nav'=>$tab.'/'.$nav, 'title'=>''));

  echo T("Password has been reset.").'<br /><br />';
  echo '<a href="../admin/staff_list.php">'.T("Return to staff list").'</a>';

  Page::footer();
