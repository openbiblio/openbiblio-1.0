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
  $nav = "delete";
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/BiblioStatus.php");
  require_once("../classes/BiblioStatusQuery.php");

  $mbrid = $HTTP_GET_VARS["mbrid"];
  $mbrName = $HTTP_GET_VARS["name"];

  #****************************************************************************
  #*  Search database for BiblioStatus data
  #****************************************************************************
  $statQ = new BiblioStatusQuery();
  $statQ->connect();
  if ($statQ->errorOccurred()) {
    $statQ->close();
    displayErrorPage($statQ);
  }
  if (!$statQ->execSelect("out",$mbrid)) {
    $statQ->close();
    displayErrorPage($statQ);
  }
  if ($statQ->getRowCount() > 0) {
    $stat = $statQ->fetchBiblioStatus();
    require_once("../shared/header.php");
    ?>
      Library member, <?php echo $mbrName;?>, still has 
      <?php echo $statQ->getRowCount()?> bibliographies checked out.
      Please check-in all of these bibliographies before deleting this member.
    <?php 
    include("../shared/footer.php");
    exit();
  }
  $statQ->close();

  #**************************************************************************
  #*  Show confirm page
  #**************************************************************************
  require_once("../shared/header.php");
?>
<center>
<form name="delmbrform" method="POST" action="../circ/mbr_del.php?mbrid=<?php echo $mbrid;?>&name=<?php echo urlencode($mbrName);?>">
Are you sure you want to delete library member, <?php echo $mbrName;?>?<br><br>
      <input type="submit" value="  Delete  ">
      <input type="button" onClick="parent.location='../circ/mbr_view.php?mbrid=<?php echo $mbrid;?>&reset=Y'" value="  Cancel  ">
</form>
</center>
<?php include("../shared/footer.php"); ?>
