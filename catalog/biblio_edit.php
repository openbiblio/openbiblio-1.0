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
  $nav = "edit";
  $restrictInDemo = true;
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/Biblio.php");
  require_once("../classes/BiblioQuery.php");
  require_once("../functions/errorFuncs.php");

  #****************************************************************************
  #*  Checking for post vars.  Go back to form if none found.
  #****************************************************************************

  if (count($HTTP_POST_VARS) == 0) {
    header("Location: ../catalog/biblio_search_form.php");
    exit();
  }

  #****************************************************************************
  #*  Validate data
  #****************************************************************************
  $bibid = $HTTP_POST_VARS["bibid"];
  $title = urlencode($HTTP_POST_VARS["title"]);

  $biblio = new Biblio();
  $biblio->setBibid($HTTP_POST_VARS["bibid"]);
  $biblio->setBarcodeNmbr($HTTP_POST_VARS["barcodeNmbr"]);
  $HTTP_POST_VARS["barcodeNmbr"] = $biblio->getBarcodeNmbr();
  $biblio->setTitle($HTTP_POST_VARS["title"]);
  $HTTP_POST_VARS["title"] = $biblio->getTitle();
  $biblio->setCollectionCd($HTTP_POST_VARS["collectionCd"]);
  $biblio->setMaterialCd($HTTP_POST_VARS["materialCd"]);
  $biblio->setSubtitle($HTTP_POST_VARS["subtitle"]);
  $HTTP_POST_VARS["subtitle"] = $biblio->getSubtitle();
  $biblio->setAuthor($HTTP_POST_VARS["author"]);
  $HTTP_POST_VARS["author"] = $biblio->getAuthor();
  $biblio->setAddAuthor($HTTP_POST_VARS["addAuthor"]);
  $HTTP_POST_VARS["addAuthor"] = $biblio->getAddAuthor();
  $biblio->setEdition($HTTP_POST_VARS["edition"]);
  $HTTP_POST_VARS["edition"] = $biblio->getEdition();
  $biblio->setCallNmbr($HTTP_POST_VARS["callNmbr"]);
  $HTTP_POST_VARS["callNmbr"] = $biblio->getCallNmbr();
  $biblio->setLccnNmbr($HTTP_POST_VARS["lccnNmbr"]);
  $HTTP_POST_VARS["lccnNmbr"] = $biblio->getLccnNmbr();
  $biblio->setIsbnNmbr($HTTP_POST_VARS["isbnNmbr"]);
  $HTTP_POST_VARS["isbnNmbr"] = $biblio->getIsbnNmbr();
  $biblio->setLcCallNmbr($HTTP_POST_VARS["lcCallNmbr"]);
  $HTTP_POST_VARS["lcCallNmbr"] = $biblio->getLcCallNmbr();
  $biblio->setLcItemNmbr($HTTP_POST_VARS["lcItemNmbr"]);
  $HTTP_POST_VARS["lcItemNmbr"] = $biblio->getLcItemNmbr();
  $biblio->setUdcNmbr($HTTP_POST_VARS["udcNmbr"]);
  $HTTP_POST_VARS["udcNmbr"] = $biblio->getUdcNmbr();
  $biblio->setUdcEdNmbr($HTTP_POST_VARS["udcEdNmbr"]);
  $HTTP_POST_VARS["udcEdNmbr"] = $biblio->getUdcEdNmbr();
  $biblio->setPublisher($HTTP_POST_VARS["publisher"]);
  $HTTP_POST_VARS["publisher"] = $biblio->getPublisher();
  $biblio->setPublicationDt($HTTP_POST_VARS["publicationDt"]);
  $HTTP_POST_VARS["publicationDt"] = $biblio->getPublicationDt();
  $biblio->setPublicationLoc($HTTP_POST_VARS["publicationLoc"]);
  $HTTP_POST_VARS["publicationLoc"] = $biblio->getPublicationLoc();
  $biblio->setSummary($HTTP_POST_VARS["summary"]);
  $HTTP_POST_VARS["summary"] = $biblio->getSummary();
  $biblio->setPages($HTTP_POST_VARS["pages"]);
  $HTTP_POST_VARS["pages"] = $biblio->getPages();
  $biblio->setPhysicalDetails($HTTP_POST_VARS["physicalDetails"]);
  $HTTP_POST_VARS["physicalDetails"] = $biblio->getPhysicalDetails();
  $biblio->setDimensions($HTTP_POST_VARS["dimensions"]);
  $HTTP_POST_VARS["dimensions"] = $biblio->getDimensions();
  $biblio->setAccompanying($HTTP_POST_VARS["accompanying"]);
  $HTTP_POST_VARS["accompanying"] = $biblio->getAccompanying();
  $biblio->setPrice($HTTP_POST_VARS["price"]);
  $HTTP_POST_VARS["price"] = $biblio->getPrice();

  $validData = $biblio->validateData();
  if (!$validData) {
    $pageErrors["barcodeNmbr"] = $biblio->getBarcodeNmbrError();
    $pageErrors["title"] = $biblio->getTitleError();
    $pageErrors["callNmbr"] = $biblio->getCallNmbrError();
    $pageErrors["price"] = $biblio->getPriceError();
    $HTTP_SESSION_VARS["postVars"] = $HTTP_POST_VARS;
    $HTTP_SESSION_VARS["pageErrors"] = $pageErrors;
    header("Location: ../catalog/biblio_edit_form.php");
    exit();
  }

  #**************************************************************************
  #*  Update bibliography
  #**************************************************************************
  $biblioQ = new BiblioQuery();
  $biblioQ->connect();
  if ($biblioQ->errorOccurred()) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }
  if (!$biblioQ->update($biblio)) {
    $biblioQ->close();
    displayErrorPage($biblioQ);
  }

  $biblioQ->close();

  #**************************************************************************
  #*  Destroy form values and errors
  #**************************************************************************
  unset($HTTP_SESSION_VARS["postVars"]);
  unset($HTTP_SESSION_VARS["pageErrors"]);

  #**************************************************************************
  #*  Show success page
  #**************************************************************************
  require_once("../shared/header.php");
?>
Bibliography, <?php echo $biblio->getTitle();?>, has been updated.<br><br>
<a href="../shared/biblio_view.php?bibid=<?php echo $biblio->getBibid();?>">return to Biblio Info</a>

<?php require_once("../shared/footer.php"); ?>
