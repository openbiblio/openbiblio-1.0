<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  $tab = "reports";
  $nav = "runreport";

  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);
  Header("Content-type: application/vnd.ms-excel; charset=".OBIB_CHARSET.";");
  Header("Content-disposition: attachment; filename=report_result.csv");
  include("../reports/run_report.php");
?>
<?php
    foreach($fieldIds as $fldid) {
       echo "\"".$loc->getText($fldid)."\",";
    }
    echo "
";
?>
<?php
    while ($array = $reportQ->fetchRow()) {
        foreach($array as $cell) {
            echo "\"".str_replace('"', '\\"', $cell)."\",";
        }
        echo "
";
    }
    $reportQ->close();
?>
