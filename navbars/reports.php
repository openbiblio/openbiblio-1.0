<?php
/* This file is part of a copyrighted work; it is distributed with NO WARRANTY.
 * See the file COPYRIGHT.html for more details.
 */
 
  require_once("../classes/Localize.php");
  $navLoc = new Localize(OBIB_LOCALE,"navbars");


 if (isset($_SESSION["userid"])) {
   $sess_userid = $_SESSION["userid"];
 } else {
   $sess_userid = "";
 }
 if ($sess_userid == "") { ?>
  <input type="button" onClick="self.location='../shared/loginform.php?RET=../reports/index.php'" value="<?php echo $navLoc->getText("login");?>" class="navbutton">
<?php } else { ?>
  <input type="button" onClick="self.location='../shared/logout.php'" value="<?php echo $navLoc->getText("logout");?>" class="navbutton">
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

<a href="javascript:popSecondary('../shared/help.php<?php if (isset($helpPage)) echo "?page=".H(addslashes(U($helpPage))); ?>')"><?php echo $navLoc->getText("help");?></a>
