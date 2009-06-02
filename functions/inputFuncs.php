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

require_once("../functions/errorFuncs.php");
require_once("../classes/Dm.php");
require_once("../classes/DmQuery.php");

/*********************************************************************************
 * Draws input html tag of type text.
 * @param string $fieldName name of input field
 * @param string $size size of text box
 * @param string $max max input length of text box
 * @param array_reference &$postVars reference to array containing all input values
 * @param array_reference &$pageErrors reference to array containing all input errors
 * @return void
 * @access public
 *********************************************************************************
 */
function printInputText($fieldName,$size,$max,&$postVars,&$pageErrors,$visibility = "visible"){
  if (!isset($postVars)) {
    $value = "";
  } elseif (!isset($postVars[$fieldName])) {
      $value = "";
  } else {
      $value = $postVars[$fieldName];
  }
  if (!isset($pageErrors)) {
    $error = "";
  } elseif (!isset($pageErrors[$fieldName])) {
      $error = "";
  } else {
      $error = $pageErrors[$fieldName];
  }

  echo "<input type=\"text\" id=\"".$fieldName."\" name=\"".$fieldName."\" size=\"".$size."\" maxlength=\"".$max."\"";
  if ($visibility != "visible") {
    echo " style=\"visibility:".$visibility."\"";
  }
  echo " value=\"".$value."\" >";
  if ($error != "") {
    echo "<br><font class=\"error\">";
    echo $error."</font>";
  }
}

/*********************************************************************************
 * Draws input html tag of type text.
 * @param string $fieldName name of input field
 * @param string $domainTable name of domain table to get values from
 * @param array_reference &$postVars reference to array containing all input values
 *********************************************************************************
 */
function printSelect($fieldName,$domainTable,&$postVars,$disabled=FALSE){
  $value = "";
  if (isset($postVars[$fieldName])) {
      $value = $postVars[$fieldName];
  }

  $dmQ = new DmQuery();
  $dmQ->connect();
  if ($dmQ->errorOccurred()) {
    $dmQ->close();
    displayErrorPage($dmQ);
  }
  $dmQ->execSelect($domainTable);
  if ($dmQ->errorOccurred()) {
    $dmQ->close();
    displayErrorPage($dmQ);
  }
  echo "<select id=\"".$fieldName."\" name=\"".$fieldName."\"";
  if ($disabled) {
    echo " disabled";
  }
  echo ">\n";
  while ($dm = $dmQ->fetchRow()) {
    echo "<option value=\"".$dm->getCode()."\"";
    if (($value == "") && ($dm->getDefaultFlg() == 'Y')) {
      echo " selected";
    } elseif ($value == $dm->getCode()) {
      echo " selected";
    }
    echo ">".$dm->getDescription()."\n";
  }
  echo "</select>\n";
  $dmQ->close();
}
?>
