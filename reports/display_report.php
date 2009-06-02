<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../shared/common.php");
  $tab = "reports";
  $nav = "runreport";

  include("../shared/logincheck.php");

  $index = "outputType";
  if ($_POST[$index] == "reportCriteriaOutputHTML"){
    include("display_report_html.php");
  }
  else if($_POST[$index] == "reportCriteriaOutputCSV"){
    include("display_report_csv.php");
  }
  else {
    include("report_criteria.php");
  }
?>

