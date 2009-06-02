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
  $nav = "hist";

  require_once("../shared/common.php");
  require_once("../functions/inputFuncs.php");
  require_once("../shared/logincheck.php");
  require_once("../classes/BiblioStatusHist.php");
  require_once("../classes/BiblioStatusHistQuery.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Checking for get vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_GET) == 0) {
    header("Location: ../circ/index.php");
    exit();
  }

  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  $mbrid = $_GET["mbrid"];
  if (isset($_GET["name"])) {
      $mbrName = urlencode($_GET["name"]);
  } else {
      $mbrName = "";
  }

  #****************************************************************************
  #*  Search database for member history
  #****************************************************************************
  $histQ = new BiblioStatusHistQuery();
  $histQ->connect();
  if ($histQ->errorOccurred()) {
    $histQ->close();
    displayErrorPage($histQ);
  }
  if (!$histQ->queryByMbrid($mbrid)) {
    $histQ->close();
    displayErrorPage($histQ);
  }

  #**************************************************************************
  #*  Show biblio checkout history
  #**************************************************************************
  require_once("../shared/header.php");
?>

<h1><?php print $loc->getText("mbrHistoryHead1"); ?></h1>
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrHistoryHdr1"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrHistoryHdr2"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrHistoryHdr3"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrHistoryHdr4"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrHistoryHdr5"); ?>
    </th>
    <th valign="top" nowrap="yes" align="left">
      <?php print $loc->getText("mbrHistoryHdr6"); ?>
    </th>
  </tr>

<?php
  if ($histQ->getRowCount() == 0) {
?>
  <tr>
    <td class="primary" align="center" colspan="6">
      <?php print $loc->getText("mbrHistoryNoHist"); ?>
    </td>
  </tr>
<?php
  } else {
    while ($hist = $histQ->fetchRow()) {
?>
  <tr>
    <td class="primary" valign="top" >
      <?php echo $hist->getBiblioBarcodeNmbr();?>
    </td>
    <td class="primary" valign="top" >
      <a href="../shared/biblio_view.php?bibid=<?php echo $hist->getBibid();?>&tab=cataloging"><?php echo $hist->getTitle();?></a>
    </td>
    <td class="primary" valign="top" >
      <?php echo $hist->getAuthor();?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $hist->getStatusCd();?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $hist->getStatusBeginDt();?>
    </td>
    <td class="primary" valign="top" >
      <?php echo $hist->getDueBackDt();?>
    </td>
  </tr>
<?php
    }
  }
  $histQ->close();

?>
</table>

<?php require_once("../shared/footer.php"); ?>
