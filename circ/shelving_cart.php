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

  $tab = "circulation";
  $nav = "checkin";
  $restrictInDemo = true;
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/BiblioStatus.php");
  require_once("../classes/BiblioStatusQuery.php");
  require_once("../functions/errorFuncs.php");

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($HTTP_POST_VARS) == 0) {
    header("Location: ../circ/checkin_form.php?reset=Y");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************

  $stat = new BiblioStatus();
  $stat->setBarcodeNmbr($HTTP_POST_VARS["barcodeNmbr"]);
  $HTTP_POST_VARS["barcodeNmbr"] = $stat->getBarcodeNmbr();
  $stat->setStatusCd("crt");
  $validData = $stat->validateData();
  if (!$validData) {
    $pageErrors["barcodeNmbr"] = $stat->getBarcodeNmbrError();
    $HTTP_SESSION_VARS["postVars"] = $HTTP_POST_VARS;
    $HTTP_SESSION_VARS["pageErrors"] = $pageErrors;
    header("Location: ../circ/checkin_form.php");
    exit();
  }

  #**************************************************************************
  #*  Insert new library member
  #**************************************************************************
  $statQ = new BiblioStatusQuery();
  $statQ->connect();
  if ($statQ->errorOccurred()) {
    $statQ->close();
    displayErrorPage($statQ);
  }
  if (!$statQ->update($stat)) {
    $statQ->close();
    displayErrorPage($statQ);
  }
  $statQ->close();

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($HTTP_SESSION_VARS["postVars"]);
  unset($HTTP_SESSION_VARS["pageErrors"]);

  #**************************************************************************
  #*  Go back to member view
  #**************************************************************************
  header("Location: ../circ/checkin_form.php?reset=Y");
?>
