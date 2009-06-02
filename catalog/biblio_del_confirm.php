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
  $nav = "delete";
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/BiblioStatus.php");
  require_once("../classes/BiblioStatusQuery.php");
  require_once("../classes/DmQuery.php");

  $bibid = $HTTP_GET_VARS["bibid"];
  $title = $HTTP_GET_VARS["title"];

  #****************************************************************************
  #*  Loading a few domain tables into associative arrays
  #****************************************************************************
  $dmQ = new DmQuery();
  $dmQ->connect();
  if ($dmQ->errorOccurred()) {
    $dmQ->close();
    displayErrorPage($dmQ);
  }
  $dmQ->execSelect("biblio_status_dm");
  $biblioStatusDm = $dmQ->fetchRows();
  $dmQ->close();

  #****************************************************************************
  #*  Search database for BiblioStatus data
  #****************************************************************************
  $statQ = new BiblioStatusQuery();
  $statQ->connect();
  if ($statQ->errorOccurred()) {
    $statQ->close();
    displayErrorPage($statQ);
  }
  $statusCd = $statQ->getStatusCd($bibid);
  if (!$statusCd) {
    $statQ->close();
    displayErrorPage($statQ);
  }
  if ($statQ->getRowCount() > 0) {
    require_once("../shared/header.php");
    ?>
      Bibliography title "<?php echo $title;?>" is currently in status,
      <?php echo $biblioStatusDm[$statusCd];?>.
      This bibliography must be checked in before it can be deleted.
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
<form name="delbiblioform" method="POST" action="../catalog/biblio_del.php?bibid=<?php echo $bibid;?>&title=<?php echo urlencode($title);?>">
Are you sure you want to delete bibliography title "<?php echo $title;?>"?<br><br>
      <input type="submit" value="  Delete  ">
      <input type="button" onClick="parent.location='../shared/biblio_view.php?bibid=<?php echo $bibid;?>'" value="  Cancel  ">
</form>
</center>
<?php include("../shared/footer.php"); ?>
