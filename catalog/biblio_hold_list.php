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
  $nav = "holds";
  require_once("../shared/common.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/BiblioHold.php");
  require_once("../classes/BiblioHoldQuery.php");
  require_once("../classes/DmQuery.php");
  require_once("../functions/errorFuncs.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Get Status Message
  #****************************************************************************
  if (isset($_GET["msg"])) {
    $msg = "<font class=\"error\">".stripslashes($_GET["msg"])."</font><br><br>";
  } else {
    $msg = "";
  }

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

  #****************************************************************************
  #*  Show page
  #****************************************************************************
  require_once("../shared/header.php");
?>
<h1><?php print $loc->getText("biblioHoldListHead"); ?></h1>
<?php echo $msg ?>
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("biblioHoldListHdr1"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("biblioHoldListHdr2"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("biblioHoldListHdr3"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("biblioHoldListHdr4"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("biblioHoldListHdr5"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("biblioHoldListHdr6"); ?>
    </th>
  </tr>
<?php


  #****************************************************************************
  #*  Search database for BiblioHold data
  #****************************************************************************
  $holdQ = new BiblioHoldQuery();
  $holdQ->connect();
  if ($holdQ->errorOccurred()) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  if (!$holdQ->queryByBibid($bibid)) {
    $holdQ->close();
    displayErrorPage($holdQ);
  }
  if ($holdQ->getRowCount() == 0) {

?>
    <td class="primary" align="center" colspan="6">
      <?php print $loc->getText("biblioHoldListNoHolds"); ?>
    </td>
<?php
  } else {
    while ($hold = $holdQ->fetchRow()) {
?>
  <tr>
    <td class="primary" valign="top" nowrap="yes">
       <a href="../shared/hold_del_confirm.php?bibid=<?php echo $hold->getBibid();?>&copyid=<?php echo $hold->getCopyid();?>&holdid=<?php echo $hold->getHoldid();?>"><?php print $loc->getText("biblioHoldListdel"); ?></a>
    </td>
    <td class="primary" valign="top" >
      <?php echo $hold->getBarcodeNmbr();?>
    </td>
    <td class="primary" valign="top" nowrap="yes">
      <?php echo $hold->getHoldBeginDt();?>
    </td>
    <td class="primary" valign="top" >
      <a href="../circ/mbr_view.php?mbrid=<?php echo $hold->getMbrid();?>&reset=Y"><?php echo $hold->getFirstName();?>
      <?php echo $hold->getLastName();?></a>
    </td>
    <td class="primary" valign="top" >
      <?php echo $biblioStatusDm[$hold->getStatusCd()];?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $hold->getDueBackDt();?>
    </td>
  </tr>
<?php
    }
  }
  $holdQ->close();
?>

</table>
<?php include("../shared/footer.php"); ?>
