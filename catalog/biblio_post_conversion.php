<?php
/**********************************************************************************
 *   Copyright(C) 2005 David Stevens
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

  $postVars["bibid"] = $biblio->getBibid();
  $postVars["collectionCd"] = $biblio->getCollectionCd();
  $postVars["materialCd"] = $biblio->getMaterialCd();
  $postVars["callNmbr1"] = $biblio->getCallNmbr1();
  $postVars["callNmbr2"] = $biblio->getCallNmbr2();
  $postVars["callNmbr3"] = $biblio->getCallNmbr3();
  if ($biblio->showInOpac()) {
    $postVars["opacFlg"] = "CHECKED";
  } else {
    $postVars["opacFlg"] = "";
  }
  $biblioFlds = $biblio->getBiblioFields();
  foreach ($biblioFlds as $key => $biblioFld) {
    // if we allow these tags with a fldid(added from marc screens), we'll get dup data.
    if ( !(($biblioFld->getTag() == 245) && ($biblioFld->getSubfieldCd() == "a") && ($biblioFld->getFieldid() != ""))
      && !(($biblioFld->getTag() == 245) && ($biblioFld->getSubfieldCd() == "b") && ($biblioFld->getFieldid() != ""))
      && !(($biblioFld->getTag() == 245) && ($biblioFld->getSubfieldCd() == "c") && ($biblioFld->getFieldid() != ""))
      && !(($biblioFld->getTag() == 100) && ($biblioFld->getSubfieldCd() == "a") && ($biblioFld->getFieldid() != ""))
      && !(($biblioFld->getTag() == 650) && ($biblioFld->getSubfieldCd() == "a") && ($biblioFld->getFieldid() != "")) ) {
      $postVars[$key] = $biblioFld->getFieldData();
      $fieldIds[$key] = $biblioFld->getFieldid();
    }
  }
?>
