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

  $tab = "circulation";
  $nav = "search";
  require_once("../shared/common.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/Member.php");
  require_once("../classes/MemberQuery.php");
  require_once("../functions/searchFuncs.php");
  require_once("../classes/DmQuery.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  Function declaration only used on this page.
  #****************************************************************************
  function printResultPages($currPage, $pageCount) {
    global $loc;
    $maxPg = 21;
    if ($currPage > 1) {
      echo "<a href=\"javascript:changePage(".($currPage-1).")\">&laquo;".$loc->getText("mbrsearchprev")."</a> ";
    }
    for ($i = 1; $i <= $pageCount; $i++) {
      if ($i < $maxPg) {
        if ($i == $currPage) {
          echo "<b>".$i."</b> ";
        } else {
          echo "<a href=\"javascript:changePage(".$i.")\">".$i."</a> ";
        }
      } elseif ($i == $maxPg) {
        echo "... ";
      }
    }
    if ($currPage < $pageCount) {
      echo "<a href=\"javascript:changePage(".($currPage+1).")\">".$loc->getText("mbrsearchnext")."&raquo;</a> ";
    }
  }

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($_POST) == 0) {
    header("Location: ../circ/index.php");
    exit();
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
  $dmQ->execSelect("mbr_classify_dm");
  $mbrClassifyDm = $dmQ->fetchRows();
  $dmQ->close();

  #****************************************************************************
  #*  Retrieving post vars and scrubbing the data
  #****************************************************************************
  if (isset($_POST["page"])) {
    $currentPageNmbr = $_POST["page"];
  } else {
    $currentPageNmbr = 1;
  }
  $searchType = $_POST["searchType"];
  # remove slashes added by form post
  $searchText = stripslashes(trim($_POST["searchText"]));
  # remove redundant whitespace
  $searchText = eregi_replace("[[:space:]]+", " ", $searchText);

  if ($searchType == "barcodeNmbr") {
    $sType = OBIB_SEARCH_BARCODE;
  } else {
    $sType = OBIB_SEARCH_NAME;
  }

  #****************************************************************************
  #*  Search database
  #****************************************************************************
  $mbrQ = new MemberQuery();
  $mbrQ->setItemsPerPage(OBIB_ITEMS_PER_PAGE);
  $mbrQ->connect();
  if ($mbrQ->errorOccurred()) {
    $mbrQ->close();
    displayErrorPage($mbrQ);
  }
  if (!$mbrQ->execSearch($sType,$searchText,$currentPageNmbr)) {
    $mbrQ->close();
    displayErrorPage($mbrQ);
  }

  #**************************************************************************
  #*  Show member view screen if only one result from barcode query
  #**************************************************************************
  if (($sType == OBIB_SEARCH_BARCODE) && ($mbrQ->getRowCount() == 1)) {
    $mbr = $mbrQ->fetchMember();
    $mbrQ->close();
    header("Location: ../circ/mbr_view.php?mbrid=".$mbr->getMbrid()."&reset=Y");
    exit();
  }

  #**************************************************************************
  #*  Show search results
  #**************************************************************************
  require_once("../shared/header.php");

  # Display no results message if no results returned from search.
  if ($mbrQ->getRowCount() == 0) {
    $mbrQ->close();
    echo $loc->getText("mbrsearchNoResults");
    require_once("../shared/footer.php");
    exit();
  }
?>

<!--**************************************************************************
    *  Javascript to post back to this page
    ************************************************************************** -->
<script language="JavaScript" type="text/javascript">
<!--
function changePage(page)
{
  document.changePageForm.page.value = page;
  document.changePageForm.submit();
}
-->
</script>


<!--**************************************************************************
    *  Form used by javascript to post back to this page
    ************************************************************************** -->
<form name="changePageForm" method="POST" action="../circ/mbr_search.php">
  <input type="hidden" name="searchType" value="<?php echo $_POST["searchType"];?>">
  <input type="hidden" name="searchText" value="<?php echo $_POST["searchText"];?>">
  <input type="hidden" name="page" value="1">
</form>

<!--**************************************************************************
    *  Printing result stats and page nav
    ************************************************************************** -->
<?php echo $mbrQ->getRowCount(); echo $loc->getText("mbrsearchFoundResults");?><br>
<?php printResultPages($currentPageNmbr, $mbrQ->getPageCount()); ?><br>
<br>

<!--**************************************************************************
    *  Printing result table
    ************************************************************************** -->
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left" colspan="2">
      <?php echo $loc->getText("mbrsearchSearchResults");?>
    </th>
  </tr>
  <?php
    while ($mbr = $mbrQ->fetchMember()) {
  ?>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo $mbrQ->getCurrentRowNmbr();?>.
    </td>
    <td nowrap="true" class="primary">
      <a href="../circ/mbr_view.php?mbrid=<?php echo $mbr->getMbrid();?>&reset=Y"><?php echo $mbr->getLastName();?>, <?php echo $mbr->getFirstName();?></a><br>
      <?php
        if ($mbr->getAddress1() != "") echo $mbr->getAddress1()."<br>\n";
        if ($mbr->getAddress2() != "") echo $mbr->getAddress2()."<br>\n";
        if ($mbr->getCity() != "") {
          echo $mbr->getCity().", ".$mbr->getState()." ".$mbr->getZip();
          if ($mbr->getZipExt() != 0) {
            echo "-".$mbr->getZipExt()."<br>\n";
          } else {
            echo "<br>\n";
          }
        }
      ?>
      <b><?php echo $loc->getText("mbrsearchCardNumber");?></b> <?php echo $mbr->getBarcodeNmbr();?>
      <b><?php echo $loc->getText("mbrsearchClassification");?></b> <?php echo $mbrClassifyDm[$mbr->getClassification()];?>
    </td>
  </tr>


  <?php
    }
    $mbrQ->close();
  ?>
  </table><br>
<?php printResultPages($currentPageNmbr, $mbrQ->getPageCount()); ?><br>
<?php require_once("../shared/footer.php"); ?>
