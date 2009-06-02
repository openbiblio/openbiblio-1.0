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
  $nav = "deletedone";
  $restrictInDemo = true;
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/MemberQuery.php");
  require_once("../functions/errorFuncs.php");

  $mbrid = $HTTP_GET_VARS["mbrid"];
  $mbrName = $HTTP_GET_VARS["name"];

  #**************************************************************************
  #*  Delete library member
  #**************************************************************************
  $mbrQ = new MemberQuery();
  $mbrQ->connect();
  if ($mbrQ->errorOccurred()) {
    $mbrQ->close();
    displayErrorPage($mbrQ);
  }
  if (!$mbrQ->delete($mbrid)) {
    $mbrQ->close();
    displayErrorPage($mbrQ);
  }
  $mbrQ->close();

  #**************************************************************************
  #*  Show success page
  #**************************************************************************
  require_once("../shared/header.php");
?>
Member, <?php echo $mbrName;?>, has been deleted.<br><br>
<a href="../circ/mbr_search_form.php">return to Member Search</a>

<?php require_once("../shared/footer.php"); ?>
