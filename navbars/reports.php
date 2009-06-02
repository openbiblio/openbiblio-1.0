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

  require_once("../classes/Localize.php");
  $navLoc = new Localize(OBIB_LOCALE,"navbars");


 if (isset($HTTP_SESSION_VARS["userid"])) {
   $sess_userid = $HTTP_SESSION_VARS["userid"];
 } else {
   $sess_userid = "";
 }
 if ($sess_userid == "") { ?>
  <input type="button" onClick="parent.location='../shared/loginform.php?RET=<?php echo $HTTP_SERVER_VARS["PHP_SELF"];?>'" value="<?php echo $navLoc->getText("login");?>" class="navbutton">
<?php } else { ?>
  <input type="button" onClick="parent.location='../shared/logout.php'" value="<?php echo $navLoc->getText("logout");?>" class="navbutton">
<?php } ?>
<br /><br />

<?php if ($nav == "summary") { ?>
 &raquo; <?php echo $navLoc->getText("reportsSummary");?><br>
<?php } else { ?>
 <a href="../reports/index.php" class="alt1"><?php echo $navLoc->getText("reportsSummary");?></a><br>
<?php } ?>

<?php if ($nav == "reportlist") { ?>
 &raquo; <?php echo $navLoc->getText("reportsReportListLink");?><br>
<?php } else { ?>
 <a href="../reports/report_list.php" class="alt1"><?php echo $navLoc->getText("reportsReportListLink");?></a><br>
<?php } ?>

<?php if ($nav == "labellist") { ?>
 &raquo; <?php echo $navLoc->getText("reportsLabelsLink");?><br>
<?php } else { ?>
 <a href="../reports/label_list.php" class="alt1"><?php echo $navLoc->getText("reportsLabelsLink");?></a><br>
<?php } ?>

<?php if ($nav == "letterlist") { ?>
 &raquo; <?php echo $navLoc->getText("reportsLettersLink");?><br>
<?php } else { ?>
 <a href="../reports/letter_list.php" class="alt1"><?php echo $navLoc->getText("reportsLettersLink");?></a><br>
<?php } ?>

<a href="javascript:popSecondary('../shared/help.php<?php if (isset($helpPage)) echo "?page=".$helpPage; ?>')"><?php echo $navLoc->getText("help");?></a>
