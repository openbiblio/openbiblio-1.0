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
  $nav = "runreport";

  include("../shared/read_settings.php");
  include("../shared/logincheck.php");
  require_once("../classes/Localize.php");
  $loc = new Localize(OBIB_LOCALE,$tab);

  include("../reports/run_report.php");
  include("../shared/header_no_nav.php");

?>

<font class="small">
<a href="../reports/report_criteria.php?rptid=<?php echo $rptid;?>&title=<?php echo $qStrTitle;?>&sql=<?php echo $qStrSql;?>"><?php print $loc->getText("runReportReturnLink1"); ?></a>
| <a href="../reports/report_list.php"><?php print $loc->getText("runReportReturnLink2"); ?></a></font>
<h1><?php echo $title;?>:</h1>

<table class="primary">
  <tr>
    <?php
      foreach($fieldIds as $fldid) {
        echo "<th class=\"rpt\">".$loc->getText($fldid)."</th>";
      }
    ?>
  </tr>
  <?php
    while ($array = $reportQ->fetchRow()) {
      echo "<tr>";
        foreach($array as $cell) {
          echo "<td class=\"rpt\">".$cell."</td>";
        }
      echo "</tr>";
    }
    $reportQ->close();
  ?>
  <tr><th class="rpt" colspan="<?php echo $colCount;?>"><?php echo $loc->getText("runReportTotal");?> <?php echo $rowCount;?></th></tr>
</table>
<br>
<font class="small">
<a href="../reports/report_criteria.php?rptid=<?php echo $rptid;?>&title=<?php echo $qStrTitle;?>&sql=<?php echo $qStrSql;?>"><?php print $loc->getText("runReportReturnLink1"); ?></a>
| <a href="../reports/report_list.php"><?php print $loc->getText("runReportReturnLink2"); ?></a></font>

<?php include("../shared/footer.php"); ?>
