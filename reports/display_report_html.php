<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  $tab = "reports";
  $nav = "runreport";

  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  include("../reports/run_report.php");
  include("../shared/header_no_nav.php");

?>

<font class="small">
<a href="../reports/report_criteria.php?rptid=<?php echo HURL($rptid);?>&amp;title=<?php echo HURL($title);?>&amp;sql=<?php echo HURL($baseSql);?>"><?php echo $loc->getText("runReportReturnLink1"); ?></a>
| <a href="../reports/report_list.php"><?php echo $loc->getText("runReportReturnLink2"); ?></a></font>
<h1><?php echo H($title);?>:</h1>

<table class="primary">
  <tr>
    <?php
      foreach($fieldIds as $fldid) {
        echo "<th class=\"rpt\">".$loc->getText($fldid)."</th>";
      }
    ?>
  </tr>
  <?php
    $bibidIndex = -1;
    $mbridIndex = -1;

    foreach($fieldIds as $key => $value) {
      if(stristr($value, ".bibid") != false) {
        $bibidIndex = $key;
      }
      else if(stristr($value, ".mbrid") != false) {
        $mbridIndex = $key;
      }
    }
    while ($array = $reportQ->fetchRow()) {
      echo "<tr>";
      
      foreach($array as $key => $value) {
        echo "<td class=\"rpt\">";

	if($key == $bibidIndex) {
          echo "<a href=\"../shared/biblio_view.php?bibid=".HURL($value)."&amp;tab=cataloging\">".H($value)."</a>";
        }
        else if($key == $mbridIndex) {
          echo "<a href=\"../circ/mbr_view.php?mbrid=".HURL($value)."\">".H($value)."</a>";
        }
        else {
          echo H($value);
        }

        echo "</td>";
      }
      echo "</tr>";
    }
    $reportQ->close();
  ?>
  <tr><th class="rpt" colspan="<?php echo H($colCount);?>"><?php echo $loc->getText("runReportTotal");?> <?php echo H($rowCount);?></th></tr>
</table>
<br>
<font class="small">
<a href="../reports/report_criteria.php?rptid=<?php echo HURL($rptid);?>&amp;title=<?php echo HURL($title);?>&amp;sql=<?php echo HURL($baseSql);?>"><?php echo $loc->getText("runReportReturnLink1"); ?></a>
| <a href="../reports/report_list.php"><?php echo $loc->getText("runReportReturnLink2"); ?></a></font>

<?php include("../shared/footer.php"); ?>
