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
  $nav = "collections";
  $restrictInDemo = true;
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/DmQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for query string.  Go back to collection list if none found.
  #****************************************************************************
  if (!isset($HTTP_GET_VARS["code"])){
    header("Location: ../admin/collections_list.php");
    exit();
  }
  $code = $HTTP_GET_VARS["code"];
  $description = $HTTP_GET_VARS["desc"];

  #**************************************************************************
  #*  Delete row
  #**************************************************************************
  $dmQ = new DmQuery();
  $dmQ->connect();
  if ($dmQ->errorOccurred()) {
    $dmQ->close();
    displayErrorPage($dmQ);
  }
  if (!$dmQ->delete("collection_dm",$code)) {
    $dmQ->close();
    displayErrorPage($dmQ);
  }
  $dmQ->close();

  #**************************************************************************
  #*  Show success page
  #**************************************************************************
  require_once("../shared/header.php");
?>
<? echo $loc->getText("adminCollections_delStart"); ?>
<?php echo $description;?>
<? echo $loc->getText("adminCollections_delEnd"); ?><br><br>
<a href="../admin/collections_list.php"><? echo $loc->getText("adminCollections_delReturn"); ?></a>

<?php require_once("../shared/footer.php"); ?>
