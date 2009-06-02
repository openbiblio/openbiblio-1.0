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
  require_once("../shared/common.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/BiblioCopy.php");
  require_once("../classes/BiblioCopyQuery.php");
  require_once("../classes/DmQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);


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
  #*  Retrieving get var
  #****************************************************************************
  $bibid = $_GET["bibid"];
  $copyid = $_GET["copyid"];

  #****************************************************************************
  #*  Ready copy information
  #****************************************************************************
  $copyQ = new BiblioCopyQuery();
  $copyQ->connect();
  if ($copyQ->errorOccurred()) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  if (!$copy = $copyQ->query($bibid,$copyid)) {
    $copyQ->close();
    displayErrorPage($copyQ);
  }
  $copyQ->close();

  #****************************************************************************
  #*  Make sure copy is checked in before it is deleted
  #****************************************************************************
  if ($copy->getStatusCd() == OBIB_STATUS_OUT) {
    $msg = $loc->getText("biblioCopyDelConfirmErr1");
    $msg = urlencode($msg);
    header("Location: ../shared/biblio_view.php?bibid=".$bibid."&msg=".$msg);
    exit();
  }


  #**************************************************************************
  #*  Show confirm page
  #**************************************************************************
  require_once("../shared/header.php");
?>
<center>
<form name="delcopyform" method="POST" action="../catalog/biblio_copy_del.php?bibid=<?php echo $bibid;?>&copyid=<?php echo $copyid;?>&barcode=<?php echo $copy->getBarcodeNmbr();?>">
  <?php echo $loc->getText("biblioCopyDelConfirmMsg",array("barcodeNmbr"=>$copy->getBarcodeNmbr())); ?>
  <br><br>
  <input type="submit" value="Delete" class="button">
  <input type="button" onClick="parent.location='../shared/biblio_view.php?bibid=<?php echo $bibid;?>'" value="Cancel" class="button">
</form>
</center>
<?php include("../shared/footer.php"); ?>
