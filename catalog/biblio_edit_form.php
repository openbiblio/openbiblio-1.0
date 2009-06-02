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

  session_cache_limiter(null);

  $tab = "cataloging";
  $nav = "edit";
  $helpPage = "biblioEdit";
  $focus_form_name = "editbiblioform";
  $focus_form_field = "materialCd";
  require_once("../shared/common.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/Biblio.php");
  require_once("../classes/BiblioQuery.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  if (isset($_GET["bibid"])){
    unset($_SESSION["postVars"]);
    unset($_SESSION["pageErrors"]);
    #****************************************************************************
    #*  Retrieving get var
    #****************************************************************************
    $bibid = $_GET["bibid"];

    #****************************************************************************
    #*  Search database
    #****************************************************************************
    $biblioQ = new BiblioQuery();
    $biblioQ->connect();
    if ($biblioQ->errorOccurred()) {
      $biblioQ->close();
      displayErrorPage($biblioQ);
    }
    if (!$biblio = $biblioQ->query($bibid)) {
      $biblioQ->close();
      displayErrorPage($biblioQ);
    }

    #**************************************************************************
    #*  load up post vars
    #**************************************************************************
    include("biblio_post_conversion.php");
    $_SESSION["postVars"] = $postVars;
  } else {
    require("../shared/get_form_vars.php");
    $bibid = $postVars["bibid"];    
  }
  require_once("../shared/header.php");

  $cancelLocation = "../shared/biblio_view.php?bibid=".$postVars["bibid"];
  $headerWording="Edit";

?>

<form name="editbiblioform" method="POST" action="../catalog/biblio_edit.php">
<input type="hidden" name="bibid" value="<?php echo $postVars["bibid"];?>">
<?php include("../catalog/biblio_fields.php"); ?>
<?php include("../shared/footer.php"); ?>