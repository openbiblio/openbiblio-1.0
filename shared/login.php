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

  require_once("../shared/common.php");
  require_once("../classes/Staff.php");
  require_once("../classes/StaffQuery.php");
  require_once("../classes/SessionQuery.php");
  require_once("../functions/errorFuncs.php");

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  $pageErrors = "";
  if (count($_POST) == 0) {
    header("Location: ../shared/loginform.php");
    exit();
  }

  #****************************************************************************
  #*  Username edits
  #****************************************************************************
  $username = $_POST["username"];
  if ($username == "") {
    $error_found = true;
    $pageErrors["username"] = "Username is required.";
  }

  #****************************************************************************
  #*  Password edits
  #****************************************************************************
  $error_found = false;
  $pwd = $_POST["pwd"];
  if ($pwd == "") {
    $error_found = true;
    $pageErrors["pwd"] = "Password is required.";
  } else {


    $staffQ = new StaffQuery();
    $staffQ->connect();
    if ($staffQ->errorOccurred()) {
      displayErrorPage($staffQ);
    }
    $staffQ->verifySignon($username, $pwd);
    if ($staffQ->errorOccurred()) {
      displayErrorPage($staffQ);
    }
    $staff = $staffQ->fetchStaff();
    if ($staff == false) {
      # invalid password.  Add one to login attempts.
      $error_found = true;
      $pageErrors["pwd"] = "Invalid signon.";
      if (!isset($_SESSION["loginAttempts"]) || ($_SESSION["loginAttempts"] == "")) {
        $sess_login_attempts = 1;
      } else {
        $sess_login_attempts = $_SESSION["loginAttempts"] + 1;
      }
      # Suspend userid if login attempts >= 3
      if ($sess_login_attempts >= 3) {
        $staffQ->suspendStaff($username);
        $staffQ->close();
        header("Location: suspended.php");
        exit();
      }
    }
    $staffQ->close();
  }

  #****************************************************************************
  #*  Redirect back to form if error occured
  #****************************************************************************
  if ($error_found == true) {
    $_SESSION["postVars"] = $_POST;
    $_SESSION["pageErrors"] = $pageErrors;
    header("Location: ../shared/loginform.php");
    exit();
  }

  #****************************************************************************
  #*  Redirect to suspended message if suspended
  #****************************************************************************
  if ($staff->isSuspended()) {
    header("Location: ../shared/suspended.php");
    exit();
  }

  #**************************************************************************
  #*  Insert new session row with random token
  #**************************************************************************

  $sessionQ = new SessionQuery();
  $sessionQ->connect();
  if ($sessionQ->errorOccurred()) {
    $sessionQ->close();
    displayErrorPage($sessionQ);
  }
  $token = $sessionQ->getToken($staff->getUserid());
  if ($token == false) {
    $sessionQ->close();
    displayErrorPage($sessionQ);
  }
  $sessionQ->close();

  #**************************************************************************
  #*  Destroy form values and errors and reset signon variables
  #**************************************************************************
  unset($_SESSION["postVars"]);
  unset($_SESSION["pageErrors"]);

  $_SESSION["username"] = $staff->getUsername();
  $_SESSION["userid"] = $staff->getUserid();
  $_SESSION["token"] = $token;
  $_SESSION["loginAttempts"] = 0;
  $_SESSION["hasAdminAuth"] = $staff->hasAdminAuth();
  $_SESSION["hasCircAuth"] = $staff->hasCircAuth();
  $_SESSION["hasCircMbrAuth"] = $staff->hasCircMbrAuth();
  $_SESSION["hasCatalogAuth"] = $staff->hasCatalogAuth();
  $_SESSION["hasReportsAuth"] = $staff->hasReportsAuth();

  #**************************************************************************
  #*  Redirect to return page
  #**************************************************************************
  header("Location: ".$_SESSION["returnPage"]);
  exit();

?>
