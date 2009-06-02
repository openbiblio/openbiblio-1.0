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
            echo "\"".$cell."\",";
        }
        echo "
";
    }
    $reportQ->close();
?>
