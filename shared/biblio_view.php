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

  #****************************************************************************
  #*  Checking for get vars.  Go back to form if none found.
  #****************************************************************************
  if (count($HTTP_GET_VARS) == 0) {
    header("Location: ../catalog/biblio_search_form.php");
    exit();
  }

  #****************************************************************************
  #*  Checking for tab name to show OPAC look and feel if searching from OPAC
  #****************************************************************************
  if (isset($HTTP_GET_VARS["tab"])) {
    $tab = $HTTP_GET_VARS["tab"];
  } else {
    $tab = "cataloging";
  }

  $nav = "view";
  require_once("../shared/read_settings.php");
  if ($tab != "opac") {
    require_once("../shared/logincheck.php");
  }
  require_once("../classes/Biblio.php");
  require_once("../classes/BiblioQuery.php");
  require_once("../classes/DmQuery.php");


  #****************************************************************************
  #*  Retrieving get var
  #****************************************************************************
  $bibid = $HTTP_GET_VARS["bibid"];

  #****************************************************************************
  #*  Loading a few domain tables into associative arrays
  #****************************************************************************
  $dmQ = new DmQuery();
  $dmQ->connect();
  if ($dmQ->errorOccurred()) {
    $dmQ->close();
    displayErrorPage($dmQ);
  }
  $dmQ->execSelect("collection_dm");
  $collectionDm = $dmQ->fetchRows();
  $dmQ->execSelect("material_type_dm");
  $materialTypeDm = $dmQ->fetchRows();
  $dmQ->execSelect("biblio_status_dm");
  $biblioStatusDm = $dmQ->fetchRows();
  $dmQ->close();

  #****************************************************************************
  #*  Search database
  #****************************************************************************
  $biblioQ = new BiblioQuery();
  $biblioQ->connect();
  if ($biblioQ->errorOccurred()) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  if (!$biblioQ->execSelect($bibid)) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  $biblio = $biblioQ->fetchBiblio();
  $title = urlencode($biblio->getTitle());

  #**************************************************************************
  #*  Show search results
  #**************************************************************************
  if ($tab == "opac") {
    require_once("../shared/header_opac.php");
  } else {
    require_once("../shared/header.php");
  }

?>
<table class="primary">
  <tr>
    <th align="left" colspan="2" nowrap="yes">
      Bibliography Information:
    </th>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      Material Type:
    </td>
    <td valign="top" class="primary">
      <?php echo $materialTypeDm[$biblio->getMaterialCd()];?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      Title:
    </td>
    <td valign="top" class="primary">
      <?php echo $biblio->getTitle();?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      Subtitle:
    </td>
    <td valign="top" class="primary">
      <?php echo $biblio->getSubtitle();?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      Author:
    </td>
    <td valign="top" class="primary">
      <?php echo $biblio->getAuthor();?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      Additional Authors:
    </td>
    <td valign="top" class="primary">
      <?php echo $biblio->getAddAuthor();?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Edition:
    </td>
    <td valign="top" class="primary">
      <?php echo $biblio->getEdition();?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      Published:
    </td>
    <td valign="top" class="primary">
      <?php
        echo $biblio->getPublisher();
        if ($biblio->getPublicationLoc() != "") {
          echo ", ".$biblio->getPublicationLoc();
        }
        if ($biblio->getPublicationDt() != "") {
          echo ", ".$biblio->getPublicationDt();
        }
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Physical Details:
    </td>
    <td valign="top" class="primary">
      <?php
        echo $biblio->getPages();
        if ($biblio->getPhysicalDetails() != "") {
          echo ", ".$biblio->getPhysicalDetails();
        }
        if ($biblio->getDimensions() != "") {
          echo ", ".$biblio->getDimensions();
        }
        if ($biblio->getAccompanying() != "") {
          echo ", ".$biblio->getAccompanying();
        }
      ?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      Purchase Price:
    </td>
    <td valign="top" class="primary">
      $<?php echo $biblio->getPrice();?>
    </td>
  </tr>
  <tr>
    <td class="primary" valign="top">
      Summary:
    </td>
    <td valign="top" class="primary">
      <?php echo $biblio->getSummary();?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      ISBN:
    </td>
    <td valign="top" class="primary">
      <?php echo $biblio->getIsbnNmbr();?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      LCCN:
    </td>
    <td valign="top" class="primary">
      <?php echo $biblio->getLccnNmbr();?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Lib. of Congress Call #:
    </td>
    <td valign="top" class="primary">
      <?php
        echo $biblio->getLcCallNmbr();
        if ($biblio->getLcItemNmbr() != "") {
          echo ", ".$biblio->getLcItemNmbr();
        }
      ?>
    </td>
  </tr>
  <tr>
    <td nowrap="true" class="primary">
      Univ. Dec. Classification
    </td>
    <td valign="top" class="primary">
      <?php
        echo $biblio->getUdcNmbr();
        if ($biblio->getUdcEdNmbr() != "") {
          echo ", ".$biblio->getUdcEdNmbr();
        }
      ?>
    </td>
  </tr>
</table>

<br>
<table class="primary">
  <tr>
    <th align="left" colspan="6" nowrap="yes">
      Bibliography Call Information:
    </th>
  </tr>
  <tr>
    <th align="left" nowrap="yes">
      Barcode
    </th>
    <th align="left" nowrap="yes">
      Collection
    </th>
    <th align="left" nowrap="yes">
      Call #
    </th>
    <th align="left" nowrap="yes">
      Status
    </th>
    <th align="left" nowrap="yes">
      Due Back
    </th>
    <th align="left" nowrap="yes">
      On Hold
    </th>
  </tr>
  <tr>
    <td valign="top" class="primary">
      <?php echo $biblio->getBarcodeNmbr();?>
    </td>
    <td valign="top" class="primary">
      <?php echo $collectionDm[$biblio->getCollectionCd()];?>
    </td>
    <td valign="top" class="primary">
      <?php echo $biblio->getCallNmbr();?>
    </td>
    <td valign="top" class="primary">
      <?php
        if ($biblio->getStatusCd() == "out" && $tab != "opac") {
          echo "<a href=\"../circ/mbr_view.php?mbrid=".$biblio->getStatusMbrid()."&reset=Y\">";
          echo $biblioStatusDm[$biblio->getStatusCd()]."</a>";
        } else {
          echo $biblioStatusDm[$biblio->getStatusCd()];
        }
      ?>
    </td>
    <td valign="top" class="primary">
      <?php echo $biblio->getDueBackDt();?>
    </td>
    <td valign="top" class="primary">
      <?php
        if ($biblio->getHoldMbrid() != ""){
          if ($tab != "opac") {
            echo "<a href=\"../circ/mbr_view.php?mbrid=".$biblio->getHoldMbrid()."\">yes</a>";
          } else {
            echo "yes";
          }
        } else {
          echo "no";
        }
      ?>
    </td>
  </tr>
</table>

<br>
<table class="primary">
  <tr>
    <th align="left" colspan="2" nowrap="yes">
      Bibliography Topics:
    </th>
  </tr>
  <tr>
    <th align="left" nowrap="yes">
      Topic
    </th>
    <th align="left" nowrap="yes">
      General Subdivision
    </th>
  </tr>
  <tr>
    <td colspan="2" nowrap="true" class="primary">
      No topics have been defined.
    </td>
  </tr>
</table>

<?php require_once("../shared/footer.php"); ?>
