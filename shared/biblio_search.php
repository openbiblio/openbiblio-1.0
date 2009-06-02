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


  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************
  if (count($HTTP_POST_VARS) == 0) {
    header("Location: ../catalog/biblio_search_form.php");
    exit();
  }

  #****************************************************************************
  #*  Checking for tab name to show OPAC look and feel if searching from OPAC
  #****************************************************************************
  if (isset($HTTP_POST_VARS["tab"])) {
    $tab = $HTTP_POST_VARS["tab"];
  } else {
    $tab = "cataloging";
  }

  $nav = "search";
  require_once("../shared/read_settings.php");
  if ($tab != "opac") {
    require_once("../shared/logincheck.php");
  }
  require_once("../classes/Biblio.php");
  require_once("../classes/BiblioQuery.php");
  require_once("../functions/searchFuncs.php");
  require_once("../classes/DmQuery.php");

  #****************************************************************************
  #*  Function declaration only used on this page.
  #****************************************************************************
  function printResultPages($currPage, $pageCount, $sort) {
    echo "Result Pages: ";
    $maxPg = 21;
    if ($currPage > 1) {
      echo "<a href=\"javascript:changePage(".($currPage-1).",'".$sort."')\">&laquo;prev</a> ";
    }
    for ($i = 1; $i <= $pageCount; $i++) {
      if ($i < $maxPg) {
        if ($i == $currPage) {
          echo "<b>".$i."</b> ";
        } else {
          echo "<a href=\"javascript:changePage(".$i.",'".$sort."')\">".$i."</a> ";
        }
      } elseif ($i == $maxPg) {
        echo "... ";
      }
    }
    if ($currPage < $pageCount) {
      echo "<a href=\"javascript:changePage(".($currPage+1).",'".$sort."')\">next&raquo;</a> ";
    }
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
  $dmQ->execSelect("collection_dm");
  $collectionDm = $dmQ->fetchRows();
  $dmQ->execSelect("material_type_dm");
  $materialTypeDm = $dmQ->fetchRows();
  $dmQ->resetResult();
  $materialImageFiles = $dmQ->fetchRows("image_file");
  $dmQ->execSelect("biblio_status_dm");
  $biblioStatusDm = $dmQ->fetchRows();
  $dmQ->close();

  #****************************************************************************
  #*  Retrieving post vars and scrubbing the data
  #****************************************************************************
  if (isset($HTTP_POST_VARS["page"])) {
    $currentPageNmbr = $HTTP_POST_VARS["page"];
  } else {
    $currentPageNmbr = 1;
  }
  $searchType = $HTTP_POST_VARS["searchType"];
  $sortBy = $HTTP_POST_VARS["sortBy"];
  if ($sortBy == "default") {
    if ($searchType == "author") {
      $sortBy = "author";
    } else {
      $sortBy = "title";
    }
  }
  # remove slashes added by form post
  $searchText = stripslashes(trim($HTTP_POST_VARS["searchText"]));
  # remove redundant whitespace
  $searchText = eregi_replace("[[:space:]]+", " ", $searchText);
  if ($searchType == "barcodeNmbr") {
    $sType = OBIB_SEARCH_BARCODE;
    $words[] = $searchText;
  } else {
    $words = explodeQuoted($searchText);
    if ($searchType == "author") {
      $sType = OBIB_SEARCH_AUTHOR;
    } elseif ($searchType == "subject") {
      $sType = OBIB_SEARCH_SUBJECT;
    } else {
      $sType = OBIB_SEARCH_TITLE;
    }
  }

  #****************************************************************************
  #*  Search database
  #****************************************************************************
  $biblioQ = new BiblioQuery();
  $biblioQ->setItemsPerPage(OBIB_ITEMS_PER_PAGE);
  $biblioQ->connect();
  if ($biblioQ->errorOccurred()) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  if (!$biblioQ->execSearch($sType,$words,$currentPageNmbr,$sortBy)) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  #**************************************************************************
  #*  Show search results
  #**************************************************************************
  if ($tab == "opac") {
    require_once("../shared/header_opac.php");
  } else {
    require_once("../shared/header.php");
  }

  # Display no results message if no results returned from search.
  if ($biblioQ->getRowCount() == 0) {
    $biblioQ->close();
    echo "No results found.";
    require_once("../shared/footer.php");
    exit();
  }
?>

<!--**************************************************************************
    *  Javascript to post back to this page
    ************************************************************************** -->
<script language="JavaScript" type="text/javascript">
<!--
function changePage(page,sort)
{
  document.changePageForm.page.value = page;
  document.changePageForm.sortBy.value = sort;
  document.changePageForm.submit();
}
-->
</script>


<!--**************************************************************************
    *  Form used by javascript to post back to this page
    ************************************************************************** -->
<form name="changePageForm" method="POST" action="../shared/biblio_search.php">
  <input type="hidden" name="searchType" value="<?php echo $HTTP_POST_VARS["searchType"];?>">
  <input type="hidden" name="searchText" value="<?php echo $HTTP_POST_VARS["searchText"];?>">
  <input type="hidden" name="sortBy" value="<?php echo $HTTP_POST_VARS["sortBy"];?>">
  <input type="hidden" name="page" value="1">
  <input type="hidden" name="tab" value="<?php echo $tab;?>">
</form>

<!--**************************************************************************
    *  Printing result stats and page nav
    ************************************************************************** -->
<?php echo $biblioQ->getRowCount();?> results found sorted by <?php echo $sortBy;?>
<?php
  if ($sortBy == "author") {
    echo "(<a href=\"javascript:changePage(".$currentPageNmbr.",'title')\">sort by title</a>).<br>";
  } else {
    echo "(<a href=\"javascript:changePage(".$currentPageNmbr.",'author')\">sort by author</a>).<br>";
  }
?>
<?php printResultPages($currentPageNmbr, $biblioQ->getPageCount(), $sortBy); ?><br>
<br>

<!--**************************************************************************
    *  Printing result table
    ************************************************************************** -->
<table class="primary">
  <tr>
    <th valign="top" nowrap="yes" align="left" colspan="2">
      Search Results:
    </th>
  </tr>
  <?php
    while ($biblio = $biblioQ->fetchBiblio()) {
  ?>
  <tr>
    <td nowrap="true" class="primary" valign="top">
      <?php echo $biblioQ->getCurrentRowNmbr();?>.
    </td>
    <td nowrap="true" class="primary">
      <a href="../shared/biblio_view.php?bibid=<?php echo $biblio->getBibid();?>&tab=<?php echo $tab;?>">
      <img src="../images/<?php echo $materialImageFiles[$biblio->getMaterialCd()];?>" width="20" height="20" border="0" align="bottom" alt="<?php echo $materialTypeDm[$biblio->getMaterialCd()];?>"></a>
      <a href="../shared/biblio_view.php?bibid=<?php echo $biblio->getBibid();?>&tab=<?php echo $tab;?>"><?php echo $biblio->getTitle();?></a><br>
      <?php if ($biblio->getAuthor() != "") echo $biblio->getAuthor()."<br>";?>
      <?php if ($biblio->getPublicationDt() != "") echo $biblio->getPublicationDt()."<br>";?>
      <b>Type:</b> <?php echo $materialTypeDm[$biblio->getMaterialCd()];?>
      <b>Collection:</b> <?php echo $collectionDm[$biblio->getCollectionCd()];?>
      <b>Call #:</b> <?php echo $biblio->getCallNmbr();?>
      <b>Status:</b> <?php echo $biblioStatusDm[$biblio->getStatusCd()];?>
    </td>
  </tr>


  <?php
    }
    $biblioQ->close();
  ?>
  </table><br>
<?php printResultPages($currentPageNmbr, $biblioQ->getPageCount(), $sortBy); ?><br>
<?php require_once("../shared/footer.php"); ?>
