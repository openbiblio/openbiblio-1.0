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

  $tab = "reports";
  $nav = "reportcriteria";
  $focus_form_name = "reportcriteriaform";
  $focus_form_field = "useCrit1";

  include("../shared/read_settings.php");
  include("../shared/logincheck.php");
  require_once("../functions/inputFuncs.php");
  require_once("../classes/ReportQuery.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  page functions
  #****************************************************************************
  function printCriteriaFields($index,&$fieldIds,&$fieldNames,&$fieldTypes,&$fieldNumericFlgs,&$postVars,&$pageErrors,&$loc,&$fieldValuebVisibility){
    $fldIndex = "fieldId".$index;
    echo "<select name=\"".$fldIndex."\">";
    echo "<option value=\"\"";
    if ((isset($postVars[$fldIndex])) and ($postVars[$fldIndex] == "")) echo " selected";
    echo " ></option>";
    foreach($fieldIds as $fldid) {
      $fld = $fldid." ".$fieldTypes[$fldid]." ".$fieldNumericFlgs[$fldid];
      echo "<option value=\"".$fld."\"";
      if ((isset($postVars[$fldIndex])) and ($postVars[$fldIndex] == $fld)) echo " selected";
      echo " >".$loc->getText($fldid)."</option>";
    }
    echo "</select>";
    echo " <select name=\"comparitor".$index."\" onchange=\"comparitorOnChange(this,".$index.")\";>";
    echo "<option value=\"eq\" ";
    if ($postVars["comparitor".$index] == "eq") echo "selected";
    echo ">".$loc->getText("reportCriteriaEQ")."</option>";
    echo "<option value=\"ne\" ";
    if ($postVars["comparitor".$index] == "ne") echo "selected";
    echo ">".$loc->getText("reportCriteriaNE")."</option>";
    echo "<option value=\"lt\" ";
    if ($postVars["comparitor".$index] == "lt") echo "selected";
    echo ">".$loc->getText("reportCriteriaLT")."</option>";
    echo "<option value=\"gt\" ";
    if ($postVars["comparitor".$index] == "gt") echo "selected";
    echo ">".$loc->getText("reportCriteriaGT")."</option>";
    echo "<option value=\"le\" ";
    if ($postVars["comparitor".$index] == "le") echo "selected";
    echo ">".$loc->getText("reportCriteriaLE")."</option>";
    echo "<option value=\"ge\" ";
    if ($postVars["comparitor".$index] == "ge") echo "selected";
    echo ">".$loc->getText("reportCriteriaGE")."</option>";
    echo "<option value=\"bt\" ";
    if ($postVars["comparitor".$index] == "bt") echo "selected";
    echo ">".$loc->getText("reportCriteriaBT")."</option>";
    echo "</select> ";
    printInputText("fieldValue".$index."a",15,80,$postVars,$pageErrors);
    echo"<span id=\"and".$index."\" style=\"visibility:".$fieldValuebVisibility[$index].";\"> ".$loc->getText("reportCriteriaAnd")." </span>";
    printInputText("fieldValue".$index."b",15,80,$postVars,$pageErrors,$fieldValuebVisibility[$index]);
  }

  function printSortFields($index,&$fieldIds,&$postVars,&$pageErrors,&$loc){
    $fldIndex = "sortOrder".$index;
    echo "<select name=\"".$fldIndex."\">";
    echo "<option value=\"\"";
    if ((isset($postVars[$fldIndex])) and ($postVars[$fldIndex] == "")) echo " selected";
    echo " ></option>";
    foreach($fieldIds as $fldid) {
      echo "<option value=\"".$fldid."\"";
      if ((isset($postVars[$fldIndex])) and ($postVars[$fldIndex] == $fldid)) echo " selected";
      echo " >".$loc->getText($fldid)."</option>";
    }
    echo "</select>";
    
    $fldIndex = "sortDir".$index;
    echo "<input type=\"radio\" name=\"".$fldIndex."\" value=\"asc\"";
    if ((!isset($postVars[$fldIndex]) or
      (isset($postVars[$fldIndex])) and ($postVars[$fldIndex] == "asc"))) {
      echo " checked";
    }
    echo ">".$loc->getText("reportCriteriaAscending")."</input>";
    echo "<input type=\"radio\" name=\"".$fldIndex."\" value=\"desc\"";
    if ((isset($postVars[$fldIndex])) and ($postVars[$fldIndex] == "desc")) {
      echo " checked";
    }
    echo ">".$loc->getText("reportCriteriaDescending")."</input>";
  }

?>

  <script language="JavaScript">
  <!--
  function comparitorOnChange(inputElem,critNmbr) {
    elem = document.getElementById("fieldValue" + critNmbr + "b")
    andElem = document.getElementById("and" + critNmbr)
    if (inputElem.value == "bt") {
      elem.style.visibility = "visible";
      andElem.style.visibility = "visible";
    } else {
      elem.style.visibility = "hidden";
      andElem.style.visibility = "hidden";
    }
  }
  -->
  </script>

<?php
  include("../shared/header.php");

  #****************************************************************************
  #*  getting form vars
  #****************************************************************************
  require("../shared/get_form_vars.php");
  for ($i = 1; $i <= 4; $i++) {
    if (!isset($postVars["comparitor".$i])) {
      $postVars["comparitor".$i] = "";
    }
    if ($postVars["comparitor".$i] == "bt") {
      $fieldValuebVisibility[$i] = "visible";
    } else {
      $fieldValuebVisibility[$i] = "hidden";
    }
  }

  #****************************************************************************
  #*  Read query string args
  #****************************************************************************
  $rptid = $HTTP_GET_VARS["rptid"];
  $title = $HTTP_GET_VARS["title"];
  $sql = stripslashes($HTTP_GET_VARS["sql"]);
  if (isset($HTTP_GET_VARS["label"]) and $HTTP_GET_VARS["label"]!="") {
    $label = $HTTP_GET_VARS["label"];
    $okAction = "../reports/display_labels.php";
    $cancelAction = "../reports/label_list.php";
    $showStartLabelFld = TRUE;
  } else {
    $label = "";
    $okAction = "../reports/display_report.php";
    $cancelAction = "../reports/report_list.php";
    $showStartLabelFld = FALSE;
  }
  if (isset($HTTP_GET_VARS["msg"])) {
    $msg = "<font class=\"error\">".stripslashes($HTTP_GET_VARS["msg"])."</font><br><br>";
  } else {
    $msg = "";
  }
  $metaSql = $sql." limit 1";

  #****************************************************************************
  #*  Run report with row limit = 1 to get field names
  #****************************************************************************
  $reportQ = new ReportQuery();
  $reportQ->connect();
  if ($reportQ->errorOccurred()) {
    $reportQ->close();
    displayErrorPage($reportQ);
  }
  $result = $reportQ->query($metaSql);
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
    $fieldNames[$fldid] = $fld->name;
    $fieldTypes[$fldid] = $fld->type;
    $fieldNumericFlgs[$fldid] = $fld->numeric;
  }

  $rowCount = $reportQ->getRowCount();
  $reportQ->close();

?>

<h1><?php echo $title.":";?></h1>

<?php echo $msg ?>

<form name="reportcriteriaform" method="POST" action="<?php echo $okAction; ?>">
<table class="primary">
  <tr>
    <th align="left" colspan="2" nowrap="yes">
      <?php print $loc->getText("reportCriteriaHead1"); ?>
    </th>
  </tr>
  <tr>
    <td class="primary" align="left" valign="top" nowrap="yes">
      <?php echo $loc->getText("reportCriteriaCrit1"); ?>
    </td>
    <td class="primary" align="left" valign="top" nowrap="yes">
      <?php printCriteriaFields(1,$fieldIds,$fieldNames,$fieldTypes,$fieldNumericFlgs,$postVars,$pageErrors,$loc,$fieldValuebVisibility); ?>
    </td>
  </tr>
  <tr>
    <td class="primary" align="left" valign="top" nowrap="yes">
      <?php echo $loc->getText("reportCriteriaCrit2"); ?>
    </td>
    <td class="primary" align="left" valign="top" nowrap="yes">
      <?php printCriteriaFields(2,$fieldIds,$fieldNames,$fieldTypes,$fieldNumericFlgs,$postVars,$pageErrors,$loc,$fieldValuebVisibility); ?>
    </td>
  </tr>
  <tr>
    <td class="primary" align="left" valign="top" nowrap="yes">
      <?php echo $loc->getText("reportCriteriaCrit3"); ?>
    </td>
    <td class="primary" align="left" valign="top" nowrap="yes">
      <?php printCriteriaFields(3,$fieldIds,$fieldNames,$fieldTypes,$fieldNumericFlgs,$postVars,$pageErrors,$loc,$fieldValuebVisibility); ?>
    </td>
  </tr>
  <tr>
    <td class="primary" align="left" valign="top" nowrap="yes">
      <?php echo $loc->getText("reportCriteriaCrit4"); ?>
    </td>
    <td class="primary" align="left" valign="top" nowrap="yes">
      <?php printCriteriaFields(4,$fieldIds,$fieldNames,$fieldTypes,$fieldNumericFlgs,$postVars,$pageErrors,$loc,$fieldValuebVisibility); ?>
    </td>
  </tr>

  
</table>
<br>
<table class="primary">
  <tr>
    <th align="left" colspan="2" nowrap="yes">
      <?php print $loc->getText("reportCriteriaHead2"); ?>
    </th>
  </tr>
  <tr>
    <td class="primary" align="left" valign="top" nowrap="yes">
      <?php echo $loc->getText("reportCriteriaSortCrit1"); ?>
    </td>
    <td class="primary" align="left" valign="top" nowrap="yes">
      <?php printSortFields(1,$fieldIds,$postVars,$pageErrors,$loc,$fieldValuebVisibility); ?>
    </td>
  </tr>
  <tr>
    <td class="primary" align="left" valign="top" nowrap="yes">
      <?php echo $loc->getText("reportCriteriaSortCrit2"); ?>
    </td>
    <td class="primary" align="left" valign="top" nowrap="yes">
      <?php printSortFields(2,$fieldIds,$postVars,$pageErrors,$loc,$fieldValuebVisibility); ?>
    </td>
  </tr>
  <tr>
    <td class="primary" align="left" valign="top" nowrap="yes">
      <?php echo $loc->getText("reportCriteriaSortCrit3"); ?>
    </td>
    <td class="primary" align="left" valign="top" nowrap="yes">
      <?php printSortFields(3,$fieldIds,$postVars,$pageErrors,$loc,$fieldValuebVisibility); ?>
    </td>
  </tr>
</table>

<?php if ($showStartLabelFld) {
    if (!isset($postVars["startOnLabel"])) {
      $postVars["startOnLabel"] = "1";
    }
    echo "<br>".$loc->getText("reportCriteriaStartOnLabel")." ";
    printInputText("startOnLabel",2,2,$postVars,$pageErrors);
  }
?>
  
  <input type="hidden" name="rptid" value="<?php echo $rptid;?>">
  <input type="hidden" name="title" value="<?php echo $title;?>">
  <input type="hidden" name="sql" value="<?php echo $sql;?>">
  <input type="hidden" name="label" value="<?php echo $label;?>">
<br>
  <center>
    <input type="submit" value="<?php echo $loc->getText("reportCriteriaRunReport"); ?>" class="button">
    <input type="button" onClick="parent.location='<?php echo $cancelAction; ?>'" value="<?php echo $loc->getText("reportsCancel"); ?>" class="button">
  </center>
</form>

<?php include("../shared/footer.php"); ?>
