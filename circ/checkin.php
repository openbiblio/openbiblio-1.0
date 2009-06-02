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

  require_once("../classes/BiblioCopy.php");
  require_once("../classes/BiblioCopyQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($HTTP_POST_VARS) == 0) {
    header("Location: ../circ/checkin_form.php?reset=Y");
    exit();
  }
  $massCheckinFlg = $HTTP_POST_VARS["massCheckin"];
  if ($massCheckinFlg == "Y") {
    $massCheckin = TRUE;
  } else {
    $massCheckin = FALSE;
  }
  $bibids = "";
  $copyids = "";
  if (!$massCheckin) {
    foreach($HTTP_POST_VARS as $key => $value) {
      if ($value == "copyid") {
        parse_str($key,$output);
        $bibids[] = $output["bibid"];
        $copyids[] = $output["copyid"];
      }
    }
  }
  if ((!$massCheckin) and (!is_array($bibids))) {
    $msg = $loc->getText("checkinErr1");
    $msg = urlencode($msg);
    header("Location: ../circ/checkin_form.php?reset=Y&msg=".$msg);
    exit();
  }

  #**************************************************************************
  #*  Checkin bibliographies in bibidList
  #**************************************************************************
  $copyQ = new BiblioCopyQuery();
  $copyQ->connect();
  if ($copyQ->errorOccurred()) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  if (!$copyQ->checkin($massCheckin,$bibids,$copyids)) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  $copyQ->close();

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
