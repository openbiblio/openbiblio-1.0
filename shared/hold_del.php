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

  #****************************************************************************
  #*  Checking for get vars.
  #****************************************************************************
  $bibid = $_GET["bibid"];
  $copyid = $_GET["copyid"];
  $holdid = $_GET["holdid"];
  $mbrid = $_GET["mbrid"];
  if ($mbrid == "") {
    $tab = "cataloging";
    $nav = "holds";
    $returnNav = "../catalog/biblio_hold_list.php?bibid=".$bibid;
  } else {
    $tab = "circulation";
    $nav = "view";
    $returnNav = "../circ/mbr_view.php?mbrid=".$mbrid;
  }
  $restrictInDemo = TRUE;
  require_once("../shared/common.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/BiblioHoldQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,"shared");

  #**************************************************************************
  #*  Delete hold
  #**************************************************************************
  // we need to also insert into status history table
  $holdQ = new BiblioHoldQuery();
  $holdQ->connect();
  if ($holdQ->errorOccurred()) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  $rc = $holdQ->delete($bibid,$copyid,$holdid);
  if (!$rc) {
    $holdQ->close();
    displayErrorPage($copyQ);
  }
  $holdQ->close();

  #**************************************************************************
  #*  Go back to member view
  #**************************************************************************
  $msg = $loc->getText("holdDelSuccess");
  $msg = urlencode($msg);
  header("Location: ".$returnNav."&msg=".$msg);
?>
