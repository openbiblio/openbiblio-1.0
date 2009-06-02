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
  $nav = "view";
  $restrictInDemo = true;
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/BiblioHold.php");
  require_once("../classes/BiblioHoldQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($HTTP_POST_VARS) == 0) {
    header("Location: ../circ/index.php");
    exit();
  }
  $barcode = trim($HTTP_POST_VARS["holdBarcodeNmbr"]);
  $mbrid = trim($HTTP_POST_VARS["mbrid"]);

  #****************************************************************************
  #*  Edit input
  #****************************************************************************
  if (!is_numeric($barcode)) {
    $pageErrors["holdBarcodeNmbr"] = $loc->getText("placeHoldErr1");
    $postVars["holdBarcodeNmbr"] = $barcode;
    $HTTP_SESSION_VARS["postVars"] = $postVars;
    $HTTP_SESSION_VARS["pageErrors"] = $pageErrors;
    header("Location: ../circ/mbr_view.php?mbrid=".$mbrid);
    exit();
  }

  #**************************************************************************
  #*  Insert hold
  #**************************************************************************
  // we need to also insert into status history table
  $holdQ = new BiblioHoldQuery();
  $holdQ->connect();
  if ($holdQ->errorOccurred()) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  $rc = $holdQ->insert($mbrid,$barcode);
  if (!$rc) {
    $holdQ->close();
    displayErrorPage($copyQ);
  }
  $holdQ->close();

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($HTTP_SESSION_VARS["postVars"]);
  unset($HTTP_SESSION_VARS["pageErrors"]);

  #**************************************************************************
  #*  Go back to member view
  #**************************************************************************
  header("Location: ../circ/mbr_view.php?mbrid=".$HTTP_POST_VARS["mbrid"]);
?>
