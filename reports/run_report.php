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

  require_once("../classes/ReportCriteria.php");
  require_once("../classes/ReportQuery.php");

  #****************************************************************************
  #*  Page functions
  #****************************************************************************
  function getCriteria(&$vars,$index) {
    $crit = new ReportCriteria();
    $colData = explode(" ", $vars["fieldId".$index]);
    $fieldId = $colData[0];
    $fieldType = $colData[1];
    $fieldIsNumeric = $colData[2];
    $crit->setFieldid($fieldId);
    $crit->setType($fieldType);
    $crit->setNumeric($fieldIsNumeric);
    $crit->setComparitor($vars["comparitor".$index]);
    $vars["comparitor".$index] = $crit->getComparitor();
    $crit->setValue1($vars["fieldValue".$index."a"]);
    $vars["fieldValue".$index."a"] = $crit->getValue1();
    $crit->setValue2($vars["fieldValue".$index."b"]);
    $vars["fieldValue".$index."b"] = $crit->getValue2();
    return $crit;
  }

  #****************************************************************************
  #*  Read form variables
  #****************************************************************************
  $rptid = $_POST["rptid"];
  $title = $_POST["title"];
  $label = $_POST["label"];
  $letter = $_POST["letter"];
  $initialSort = $_POST["initialSort"];
  $baseSql = stripslashes($_POST["sql"]);

  #****************************************************************************
  #*  Validate selection criteria
  #****************************************************************************
  $crit = Array();
  $allCriteriaValid = TRUE;

  for ($i = 1; $i <= 4; $i++) {
    if ($_POST["fieldId".$i] != "") {
      $crit[$i] = getCriteria($_POST,$i);
      $critValid = $crit[$i]->validateData();
      $_POST["fieldValue".$i."a"] = $crit[$i]->getValue1();
      $_POST["fieldValue".$i."b"] = $crit[$i]->getValue2();
      if (!$critValid) {
        $allCriteriaValid = FALSE;
        $pageErrors["fieldValue".$i."a"] = $crit[$i]->getValue1Error();
        $pageErrors["fieldValue".$i."b"] = $crit[$i]->getValue2Error();
      }
    }
  }

  #****************************************************************************
  #*  Go back to criteria screen if any errors occurred
  #****************************************************************************
  $_SESSION["postVars"] = $_POST;
  if (!$allCriteriaValid) {
    $_SESSION["pageErrors"] = $pageErrors;
    $urlTitle = urlencode($title);
    $urlSql = urlencode($baseSql);
    header("Location: ../reports/report_criteria.php?rptid=".$rptid."&title=".$urlTitle."&sql=".$urlSql."&label=".$label."&letter=".$letter."&initialSort=".$initialSort);
    exit();
  }

  #****************************************************************************
  #*  add selection criteria to sql
  #****************************************************************************
  // checking for existing where clause.
  $hasWhereClause = stristr($baseSql,"where");
  if ($hasWhereClause == FALSE) {
    $clausePrefix = " where ";
  } else {
    $clausePrefix = " and ";
  }

  // add each selection criteria to the sql
  $splitResult = spliti("group by",$baseSql,2);
  if (count($splitResult) > 1) {
    $sql = $splitResult[0];
    $groupBy = $splitResult[1];
  } else {
    $sql = $baseSql;
    $groupBy = "";
  }

  foreach($crit as $c) {
    $sql = $sql.$clausePrefix.$c->getFieldid()." ".$c->getSqlComparitor();
    if ($c->isNumeric()) {
      $quote = "";
    } else {
      $quote = "'";
    }
    $sql = $sql." ".$quote.$c->getValue1().$quote;
    if ($c->getComparitor() == "bt") {
      $sql = $sql." and ".$quote.$c->getValue2().$quote;
    }
    $clausePrefix = " and ";
  }

  #****************************************************************************
  #*  add group by back in if it exists
  #****************************************************************************
  if ($groupBy != "") {
    $sql = $sql." group by ".$groupBy;
  }

  #****************************************************************************
  #*  add sort clause to sql
  #****************************************************************************
  $clausePrefix = " order by ";
  for ($i = 1; $i <= 3; $i++) {
    $sortOrderFldNm = "sortOrder".$i;
    $sortDirFldNm = "sortDir".$i;
    $sortCol = $_POST[$sortOrderFldNm];
    $sortDir = $_POST[$sortDirFldNm];
    if ($sortCol != ""){
      $sql = $sql.$clausePrefix.$sortCol;
      if ($sortDir == "desc") {
        $sql = $sql." DESC";
      }
      $clausePrefix = ", ";
    }
  }
  
  #****************************************************************************
  #*  run report
  #****************************************************************************
  $reportQ = new ReportQuery();
  $reportQ->connect();
  if ($reportQ->errorOccurred()) {
    $reportQ->close();
    displayErrorPage($reportQ);
  }
  $result = $reportQ->query($sql);
  if ($reportQ->errorOccurred()) {
    $reportQ->close();
    displayErrorPage($reportQ);
  }
  $fieldIds = array();
  $fieldNames = array();
  $fieldTypes = array();
  $fieldNumericFlgs = array();
  while ($fld = $reportQ->fetchField()) {
    $fldid = $fld->name;
    if ($fld->table != "") {
      $fldid = $fld->table.".".$fldid;
    }
    $fieldIds[] = $fldid;
    $fieldTypes[$fldid] = $fld->type;
    $fieldNumericFlgs[$fldid] = $fld->numeric;
  }

  $colCount = count($fieldIds);
  $rowCount = $reportQ->getRowCount();
  $qStrTitle = urlencode($title);
  $qStrSql = urlencode($baseSql);

?>