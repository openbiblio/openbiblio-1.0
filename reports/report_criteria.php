<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "reports";
  $focus_form_name = "reportcriteriaform";
  $focus_form_field = "fieldId1";

  include("../shared/logincheck.php");
  require_once("../functions/inputFuncs.php");
  require_once("../classes/ReportQuery.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  #****************************************************************************
  #*  page functions
  #****************************************************************************
  function getFullColumnName($fieldIds,$initialSort) {
    foreach($fieldIds as $fldid) {
      if (strpos($fldid,".".$initialSort)) {
        return $fldid;
      }
    }
    return "";
  }

  function printCriteriaFields($index,&$fieldIds,&$fieldNames,&$fieldTypes,&$fieldNumericFlgs,&$postVars,&$pageErrors,&$loc,&$fieldValuebVisibility){
    $fldIndex = "fieldId".$index;
    echo "<select name=\"".H($fldIndex)."\">";
    echo "<option value=\"\"";
    if ((isset($postVars[$fldIndex])) and ($postVars[$fldIndex] == "")) echo " selected";
    echo " ></option>";
    foreach($fieldIds as $fldid) {
      $fld = $fldid." ".$fieldTypes[$fldid]." ".$fieldNumericFlgs[$fldid];
      echo "<option value=\"".H($fld)."\"";
      if ((isset($postVars[$fldIndex])) and ($postVars[$fldIndex] == $fld)) echo " selected";
      echo " >".$loc->getText($fldid)."</option>";
    }
    echo "</select>";
    echo " <select name=\"comparitor".H($index)."\" onchange=\"comparitorOnChange(this,".H(addslashes($index)).")\";>";
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
    echo"<span id=\"and".H($index)."\" style=\"visibility:".H($fieldValuebVisibility[$index]).";\"> ".$loc->getText("reportCriteriaAnd")." </span>";
    printInputText("fieldValue".$index."b",15,80,$postVars,$pageErrors,$fieldValuebVisibility[$index]);
  }

  function printSortFields($index,&$fieldIds,&$postVars,&$pageErrors,&$loc){
    $fldIndex = "sortOrder".$index;
    echo "<select name=\"".H($fldIndex)."\">";
    echo "<option value=\"\"";
    if ((isset($postVars[$fldIndex])) and ($postVars[$fldIndex] == "")) echo " selected";
    echo " ></option>";
    foreach($fieldIds as $fldid) {
      echo "<option value=\"".H($fldid)."\"";
      if ((isset($postVars[$fldIndex])) and ($postVars[$fldIndex] == $fldid)) echo " selected";
      echo " >".$loc->getText($fldid)."</option>";
    }
    echo "</select>";
    
    $fldIndex = "sortDir".$index;
    echo "<input type=\"radio\" name=\"".H($fldIndex)."\" value=\"asc\"";
    if ((!isset($postVars[$fldIndex]) or
      (isset($postVars[$fldIndex])) and ($postVars[$fldIndex] == "asc"))) {
      echo " checked";
    }
    echo ">".$loc->getText("reportCriteriaAscending")."</input>";
    echo "<input type=\"radio\" name=\"".H($fldIndex)."\" value=\"desc\"";
    if ((isset($postVars[$fldIndex])) and ($postVars[$fldIndex] == "desc")) {
      echo " checked";
    }
    echo ">".$loc->getText("reportCriteriaDescending")."</input>";
  }

  function printOutputFields(&$fieldIds,&$postVars,&$pageErrors,&$loc){
    $fldIndex = "outputType";
    echo "<select name=\"".H($fldIndex)."\">";
    foreach($fieldIds as $fldid) {
      echo "<option value=\"".H($fldid)."\"";
      if ((isset($postVars[$fldIndex])) and ($postVars[$fldIndex] == $fldid)) echo " selected";
      echo " >".$loc->getText($fldid)."</option>";
    }
    echo "</select>";
  }

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
  $rptid = $_GET["rptid"];
  $title = $_GET["title"];
  $sql = $_GET["sql"];
  if (isset($_GET["label"]) and $_GET["label"]!="") {
    $nav = "labellist";
    $label = $_GET["label"];
    $letter = "";
    $initialSort = "";
    $okAction = "../reports/display_labels.php";
    $cancelAction = "../reports/label_list.php";
    $showStartLabelFld = TRUE;
  } elseif (isset($_GET["letter"]) and $_GET["letter"]!="") {
    $nav = "letterlist";
    $label = "";
    $letter = $_GET["letter"];
    $initialSort = $_GET["initialSort"];
    $okAction = "../reports/display_letters.php";
    $cancelAction = "../reports/letter_list.php";
    $showStartLabelFld = FALSE;
  } else {
    $nav = "reportlist";
    $label = "";
    $letter = "";
    $initialSort = "";
    $okAction = "../reports/display_report.php";
    $cancelAction = "../reports/report_list.php";
    $showStartLabelFld = FALSE;
  }
  if (isset($_GET["msg"])) {
    $msg = "<font class=\"error\">".H($_GET["msg"])."</font><br><br>";
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


  // load initial sort if passed as query string
  if (($initialSort != "") and !isset($postVars["sortOrder1"])) {
    $intialSortValue = getFullColumnName($fieldIds,$initialSort);
    $postVars["sortOrder1"] = $intialSortValue;
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
?>

<h1><?php echo H($title).":";?></h1>

<?php echo $msg ?>

<form name="reportcriteriaform" method="POST" action="<?php echo H($okAction); ?>">
<table class="primary">
  <tr>
    <th align="left" colspan="2" nowrap="yes">
      <?php echo $loc->getText("reportCriteriaHead1"); ?>
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
      <?php echo $loc->getText("reportCriteriaHead2"); ?>
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
<br>
<table class="primary">
  <tr>
    <th align="left" colspan="2" nowrap="yes">
      <?php echo $loc->getText("reportCriteriaHead3"); ?>
    </th>
  </tr>
  <tr>
    <td class="primary" align="left" valign="top" nowrap="yes">
      <?php echo $loc->getText("reportCriteriaOutput"); ?>
    </td>
    <td class="primary" align="left" valign="top" nowrap="yes">
      <?php
        $outputFields = array('reportCriteriaOutputHTML', 'reportCriteriaOutputCSV');
        printOutputFields($outputFields, $postVars,$pageErrors,$loc,$fieldValuebVisibility);
      ?>
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
  
  <input type="hidden" name="rptid" value="<?php echo H($rptid);?>">
  <input type="hidden" name="title" value="<?php echo H($title);?>">
  <input type="hidden" name="sql" value="<?php echo H($sql);?>">
  <input type="hidden" name="label" value="<?php echo H($label);?>">
  <input type="hidden" name="letter" value="<?php echo H($letter);?>">
  <input type="hidden" name="initialSort" value="<?php echo H($initialSort);?>">
<br>
  <center>
    <input type="submit" value="<?php echo $loc->getText("reportCriteriaRunReport"); ?>" class="button">
    <input type="button" onClick="self.location='<?php echo H(addslashes($cancelAction)); ?>'" value="<?php echo $loc->getText("reportsCancel"); ?>" class="button">
  </center>
</form>

<?php include("../shared/footer.php"); ?>
