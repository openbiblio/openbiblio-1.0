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

  $tab = "cataloging";
  $nav = "edit";
  require_once("../shared/read_settings.php");
  require_once("../shared/logincheck.php");

  require_once("../classes/Biblio.php");
  require_once("../classes/BiblioQuery.php");

    unset($HTTP_SESSION_VARS["postVars"]);
    unset($HTTP_SESSION_VARS["pageErrors"]);
    #****************************************************************************
    #*  Retrieving get var
    #****************************************************************************
    $bibid = $HTTP_GET_VARS["bibid"];

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

    #**************************************************************************
    #*  load up post vars
    #**************************************************************************
    $postVars["bibid"] = $bibid;
    $postVars["title"] = $biblio->getTitle();
    $postVars["collectionCd"] = $biblio->getCollectionCd();
    $postVars["materialCd"] = $biblio->getMaterialCd();
    $postVars["subtitle"] = $biblio->getSubtitle();
    $postVars["author"] = $biblio->getAuthor();
    $postVars["addAuthor"] = $biblio->getAddAuthor();
    $postVars["edition"] = $biblio->getEdition();
    $postVars["callNmbr"] = $biblio->getCallNmbr();
    $postVars["lccnNmbr"] = $biblio->getLccnNmbr();
    $postVars["isbnNmbr"] = $biblio->getIsbnNmbr();
    $postVars["lcCallNmbr"] = $biblio->getLcCallNmbr();
    $postVars["lcItemNmbr"] = $biblio->getLcItemNmbr();
    $postVars["udcNmbr"] = $biblio->getUdcNmbr();
    $postVars["udcEdNmbr"] = $biblio->getUdcEdNmbr();
    $postVars["publisher"] = $biblio->getPublisher();
    $postVars["publicationDt"] = $biblio->getPublicationDt();
    $postVars["publicationLoc"] = $biblio->getPublicationLoc();
    $postVars["summary"] = $biblio->getSummary();
    $postVars["pages"] = $biblio->getPages();
    $postVars["physicalDetails"] = $biblio->getPhysicalDetails();
    $postVars["dimensions"] = $biblio->getDimensions();
    $postVars["accompanying"] = $biblio->getAccompanying();
    $HTTP_SESSION_VARS["postVars"] = $postVars;
    header("Location: ../catalog/biblio_new_form.php");
    exit();
?>
