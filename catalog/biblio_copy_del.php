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

  $tab = "cataloging";
  $nav = "view";
  $restrictInDemo = true;
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/BiblioCopyQuery.php");
  require_once("../classes/BiblioStatusHistQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  $bibid = $HTTP_GET_VARS["bibid"];
  $copyid = $HTTP_GET_VARS["copyid"];
  $barcode = $HTTP_GET_VARS["barcode"];

  #**************************************************************************
  #*  Delete Bibliography Copy
  #**************************************************************************
  $copyQ = new BiblioCopyQuery();
  $copyQ->connect();
  if ($copyQ->errorOccurred()) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  if (!$copyQ->delete($bibid,$copyid)) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  $copyQ->close();

  #**************************************************************************
  #*  Delete Copy History
  #**************************************************************************
  $histQ = new BiblioStatusHistQuery();
  $histQ->connect();
  if ($histQ->errorOccurred()) {
    $histQ->close();
    displayErrorPage($histQ);
  }
  if (!$histQ->deleteByBibid($bibid,$copyid)) {
    $histQ->close();
    displayErrorPage($histQ);
  }
  $histQ->close();

  #**************************************************************************
  #*  Show success message
  #**************************************************************************
  $msg = $loc->getText("biblioCopyDelSuccess",array("barcode"=>$barcode));
  $msg = urlencode($msg);
  header("Location: ../shared/biblio_view.php?bibid=".$bibid."&msg=".$msg);
  exit();
?>
