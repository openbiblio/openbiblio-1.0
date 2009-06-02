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

  $tab = "admin";
  $nav = "staff";
  $restrictInDemo = true;
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/Staff.php");
  require_once("../classes/StaffQuery.php");
  require_once("../functions/errorFuncs.php");

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($HTTP_POST_VARS) == 0) {
    header("Location: ../admin/staff_list.php");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $staff = new Staff();
  $staff->setLastChangeUserid($HTTP_SESSION_VARS["userid"]);
  $staff->setUserid($HTTP_POST_VARS["userid"]);
  $staff->setLastName($HTTP_POST_VARS["last_name"]);
  $HTTP_POST_VARS["last_name"] = $staff->getLastName();
  $staff->setFirstName($HTTP_POST_VARS["first_name"]);
  $HTTP_POST_VARS["first_name"] = $staff->getFirstName();
  $staff->setUsername($HTTP_POST_VARS["username"]);
  $HTTP_POST_VARS["username"] = $staff->getUsername();
  $staff->setCircAuth(isset($HTTP_POST_VARS["circ_flg"]));
  $staff->setCircMbrAuth(isset($HTTP_POST_VARS["circ_mbr_flg"]));
  $staff->setCatalogAuth(isset($HTTP_POST_VARS["catalog_flg"]));
  $staff->setAdminAuth(isset($HTTP_POST_VARS["admin_flg"]));
  $staff->setReportsAuth(isset($HTTP_POST_VARS["reports_flg"]));
  $staff->setSuspended(isset($HTTP_POST_VARS["suspended_flg"]));
  if (!$staff->validateData()) {
    $pageErrors["last_name"] = $staff->getLastNameError();
    $pageErrors["username"] = $staff->getUsernameError();
    $HTTP_SESSION_VARS["postVars"] = $HTTP_POST_VARS;
    $HTTP_SESSION_VARS["pageErrors"] = $pageErrors;
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
  #*  Destroy form values and errors
  #**************************************************************************
  unset($HTTP_SESSION_VARS["postVars"]);
  unset($HTTP_SESSION_VARS["pageErrors"]);

  #**************************************************************************
  #*  Show success page
  #**************************************************************************
  require_once("../shared/header.php");
?>
Staff member, <?php echo $staff->getFirstName();?> <?php echo $staff->getLastName();?>, has been updated.<br><br>
<a href="../admin/staff_list.php">return to staff list</a>

<?php require_once("../shared/footer.php"); ?>
