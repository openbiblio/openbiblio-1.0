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

#*********************************************************************************
#*  checklogin.php
#*  Description: Used to verify signon token on every secured page.
#*               Redirects to the login page if token not valid.
#*********************************************************************************

  require_once("../classes/SessionQuery.php");
  require_once("../functions/errorFuncs.php");

  #****************************************************************************
  #*  Temporarily disabling security for demo since sourceforge.net
  #*  seems to be using mirrored servers that do not share session info.
  #****************************************************************************
  if (!OBIB_DEMO_FLG) {

#  works in PHP 4.1
#  $returnPage = $_SERVER['PHP_SELF'];

# works in PHP 4.0
  $returnPage = $HTTP_SERVER_VARS['PHP_SELF'];
  session_register("returnPage");
#

  $HTTP_SESSION_VARS["returnPage"] = $returnPage;

  #****************************************************************************
  #*  Checking to see if session variables exist
  #****************************************************************************
  if (!isset($HTTP_SESSION_VARS["userid"]) or ($HTTP_SESSION_VARS["userid"] == "")) {
    header("Location: ../shared/loginform.php");
    exit();
  }
  if (!isset($HTTP_SESSION_VARS["token"]) or ($HTTP_SESSION_VARS["token"] == "")) {
    header("Location: ../shared/loginform.php");
    exit();
  }

  #****************************************************************************
  #*  Checking session table to see if session_id has timed out
  #****************************************************************************
  $sessQ = new SessionQuery();
  $sessQ->connect();
  if ($sessQ->errorOccurred()) {
    displayErrorPage($sessQ);
  }
  if (!$sessQ->validToken($HTTP_SESSION_VARS["userid"], $HTTP_SESSION_VARS["token"])) {
    if ($sessQ->errorOccurred()) {
      displayErrorPage($sessQ);
    }
    $sessQ->close();
    header("Location: ../shared/loginform.php?RET=".$returnPage);
    exit();
  }
  $sessQ->close();

  #****************************************************************************
  #*  Checking authorization for this tab
  #*  The session authorization flags were set at login in login.php
  #****************************************************************************
  if ($tab == "circulation"){
    if (!$HTTP_SESSION_VARS["hasCircAuth"]) {
      header("Location: ../circ/noauth.php");
      exit();
    } elseif (isset($restrictToMbrAuth) and !$HTTP_SESSION_VARS["hasCircMbrAuth"]) {
      header("Location: ../circ/noauth.php");
      exit();
    }
  } elseif ($tab == "cataloging") {
    if (!$HTTP_SESSION_VARS["hasCatalogAuth"]) {
      header("Location: ../catalog/noauth.php");
      exit();
    }
  } elseif ($tab == "admin") {
    if (!$HTTP_SESSION_VARS["hasAdminAuth"]) {
      header("Location: ../admin/noauth.php");
      exit();
    }
  } elseif ($tab == "reports") {
    if (!$HTTP_SESSION_VARS["hasReportsAuth"]) {
      header("Location: ../reports/noauth.php");
      exit();
    }
  }


  }

  #****************************************************************************
  #*  Checking to see if we are in demo mode and if we should not execute this
  #*  page.
  #****************************************************************************
  if (isset($restrictInDemo) && $restrictInDemo && OBIB_DEMO_FLG) {
    include("../shared/demo_msg.php");
  }

?>